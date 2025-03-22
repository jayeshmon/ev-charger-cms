const express = require("express");
const bodyParser = require("body-parser");
const { Pool } = require("pg");
const Ajv = require("ajv");
const addFormats = require("ajv-formats");

const app = express();

// Middleware to parse x-www-form-urlencoded and JSON
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

// PostgreSQL Configuration
const pool = new Pool({
    connectionString: process.env.DATABASE_URL,
});

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
                imei, VD, FV, ak, AD, PS, v, T, l, g, s, CG, LC, CD, SN, HD, o, MC, MN, MV, DIN, DOUT, AIN, ms, SS, is, i, p, FN, VM
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

        for (const packet of packets) {
            await processPacket(packet);
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
