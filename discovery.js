const express = require("express");
const bodyParser = require("body-parser");
const { Pool } = require("pg");
const Ajv = require("ajv");
const addFormats = require("ajv-formats");
require("dotenv").config();
const redis = require("redis");
const app = express();

// Middleware to parse x-www-form-urlencoded and JSON
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

// PostgreSQL Configuration
const pool = new Pool({
    user: process.env.PG_USER || "myuser",
    password: process.env.PG_PASSWORD || "mypassword",
    host: process.env.PG_HOST || 'localhost',
    port: process.env.PG_PORT || 5432,
    database: process.env.PG_DATABASE || 'mydatabase',

});

const redisClient = redis.createClient({
    socket: {
        host: process.env.REDIS_HOST || "127.0.0.1",
        port: process.env.REDIS_PORT || 6379,
    }
});

redisClient.on("error", (err) => console.error("❌ Redis Error:", err));
redisClient.on("connect", () => console.log("✅ Connected to Redis"));

(async () => {
    try {
        await redisClient.connect();
    } catch (err) {
        console.error("❌ Redis Connection Failed:", err);
    }
})();
// AJV JSON Schema Validator
const ajv = new Ajv();
addFormats(ajv);

// Add custom format for date-time in "YYYY-MM-DD HH:mm:ss"
ajv.addFormat("custom-datetime", /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/);

const schema = {
    type: "object",
    properties: {
        t: { type: "string" },
        VD: { type: "string" },
        FV: { type: "string" },
        ak: { type: "integer" },
        AD: { type: "integer" },
        PS: { type: "string" },
        v: { type: "string" },
        T: { type: "string", format: "custom-datetime" },
        l: { type: "string" },
        g: { type: "string" },
        s: { type: "string" },
        CG: { type: "string" },
        LC: { type: "string" },
        CD: { type: "string" },
        SN: { type: "string" },
        HD: { type: "string" },
        o: { type: "string" },
        MC: { type: "string" },
        MN: { type: "string" },
        MV: { type: "string" },
        DI: { type: "string" },
        DO: { type: "string" },
        AIN: { type: "array", items: { type: "integer" } },
        ms: { type: "string" },
        SS: { type: "string" },
        is: { type: "integer" },
        i: { type: "integer" },
        p: { type: "integer" },
        FN: { type: "string" },
        VM: { type: "string" },
    },
    required: ["AD", "PS", "v", "T", "l", "g", "FN", "VM"],
};

// Helper function to validate and insert data into PostgreSQL
const processPacket = async (packet) => {
    try {
        // Validate the packet using AJV
        const isValid = ajv.validate(schema, packet);
        console.log("Validation result:", isValid);

        if (!isValid) {
            console.error("Validation failed for packet:", packet);
            console.error("Validation errors:", ajv.errors);
        }

        // Insert the packet into the charger_data table
        const query = `
            INSERT INTO charger_data (
                imei, VD, FV, ak, AD, PS, v, T, l, g, s, CG, LC, CD, SN, HD, o, MC, MN, MV, DIN, DOUT, AIN, ms, SS, ins, i, p, FN, VM
            ) VALUES (
                $1, $2, $3, $4, $5, $6, $7, to_timestamp($8, 'YYYY-MM-DD HH24:MI:SS'), $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19, $20, $21, $22, $23, $24, $25, $26, $27, $28, $29, $30
            ) RETURNING id;
        `;

        const values = [
            packet.t, packet.VD, packet.FV, packet.ak, packet.AD, packet.PS, packet.v,
            packet.T, packet.l, packet.g, packet.s, packet.CG, packet.LC, packet.CD,
            packet.SN, packet.HD, packet.o, packet.MC, packet.MN, packet.MV,
            packet.DI, packet.DO, packet.AIN || [], packet.ms, packet.SS,
            packet.is, packet.i, packet.p, packet.FN, packet.VM
        ];

        const result = await pool.query(query, values);
        console.log(`✅ Inserted packet with ID: ${result.rows[0].id}`);
    } catch (error) {
        console.error("❌ Error processing packet:", error);
    }
};
function convertTimestampToMs(timestamp) {
    return new Date(timestamp).getTime();
}
async function clearRedisKeys(imei) {
    try {
        await redisClient.select(0);
        await redisClient.del(imei); // Remove payment data

      

        await redisClient.select(2);
        await redisClient.del(imei); // Remove charge finish time

        console.log(`✅ Cleared all Redis keys for IMEI: ${imei}`);
    } catch (error) {
        console.error(`❌ Error clearing Redis keys for IMEI ${imei}:`, error);
    }
}
async function getChargeFinishTime(imei) {
    try {
        await redisClient.select(2); // Switch to DB 2
        let chargeFinishTime = await redisClient.get(imei);
        return chargeFinishTime ? parseInt(chargeFinishTime) : null;
    } catch (error) {
        console.error(`❌ Error fetching charge finish time for ${imei}:`, error);
        return null;
    }
}
// Endpoint to handle incoming packets
app.post("/parsedata", async (req, res) => {
    try {
        const rawJson = req.body.vltjson;

        if (!rawJson) {
            return res.status(400).send({ error: "Missing vltjson in request body" });
        }

        let packets;
        try {
            packets = JSON.parse(rawJson);
        } catch (err) {
            return res.status(400).send({ error: "Invalid JSON format in vltjson" });
        }

        if (!Array.isArray(packets)) {
            return res.status(400).send({ error: "Invalid input format. Expected an array." });
        }

        try {
            console.log(rawJson);
            packets = JSON.parse(rawJson);
            
        } catch (err) {
            return res.status(400).send({ error: "Invalid JSON format in vltjson" });
        }

        if (!Array.isArray(packets)) {
            return res.status(400).send({ error: "Invalid input format. Expected an array." });
        }

        for (const packet of packets) {
            processPacket(packet);
            const imei = packet.t;
            const telemetryTimeMs = convertTimestampToMs(packet.T);

            // Get charge finish time from Redis DB 2
            const chargeFinishTime = await getChargeFinishTime(imei);
             console.log(chargeFinishTime)
            if (chargeFinishTime !== null) {
                if (chargeFinishTime > telemetryTimeMs) {
                    return res.status(200).send({
                        "status": 1,
                        "command": "SET CUIMOB:OFF"
                    });
                } else {
                    await clearRedisKeys(imei);
                    return res.status(200).send({
                        "status": 1,
                        "command": "SET CUIMOB:ON"
                    });
                }
            }
        }

      



        res.status(200).send({ message: "Data processed successfully" });
    } catch (err) {
        console.error("❌ Error processing data:", err);
        res.status(500).send({ error: "Internal server error" });
    }
});

// Start server
const PORT = 3001;
app.listen(PORT, () => {
    console.log(`🚀 Server is running on http://localhost:${PORT}`);
});
