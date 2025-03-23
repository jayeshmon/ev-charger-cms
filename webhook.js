const express = require("express");
const { Pool } = require("pg");
const redis = require("redis");
require("dotenv").config();

const app = express();
app.use(express.json());

// PostgreSQL Connection
const pool = new Pool({
    user: process.env.PG_USER || "myuser",
    password: process.env.PG_PASSWORD || "mypassword",
    host: process.env.PG_HOST || 'localhost',
    port: process.env.PG_PORT || 5432,
    database: process.env.PG_DATABASE || 'mydatabase',
});

// Redis Connection
const redisClient = redis.createClient({
    socket: {
        host: process.env.REDIS_HOST || "127.0.0.1",
        port: process.env.REDIS_PORT || 6379,
    }
});

redisClient.on("error", (err) => console.error("❌ Redis Error:", err));
redisClient.on("connect", () => console.log("✅ Connected to Redis"));

// Ensure Redis is connected before use
(async () => {
    try {
        await redisClient.connect();
    } catch (err) {
        console.error("❌ Redis Connection Failed:", err);
    }
})();

/**
 * Get the tariff for a given Machine ID (mid) from Redis (DB 1)
 * @param {string} mid 
 * @returns {Promise<number>} Tariff amount
 */
async function getTariff(mid) {
    try {
        await redisClient.select(1); // Switch to DB 1
        let tariff = await redisClient.get(mid);
        return tariff ? parseFloat(tariff) : 1500; // Default tariff = 1500/hour
    } catch (error) {
        console.error(`❌ Error fetching tariff for ${mid}:`, error);
        return 1500; // Default fallback
    }
}

/**
 * Set the charge finish time in Redis (DB 2)
 * @param {string} mid 
 * @param {number} chargeFinishTime Timestamp in milliseconds
 */
async function setFinishTime(mid, chargeFinishTime) {
    try {
        await redisClient.select(2); // Switch to DB 2
        await redisClient.set(mid, chargeFinishTime);
        console.log(`✅ Charge finish time set for ${mid}: ${new Date(chargeFinishTime)}`);
    } catch (error) {
        console.error(`❌ Error setting charge finish time for ${mid}:`, error);
    }
}

app.post("/webhook", async (req, res) => {
    console.log(req.body);
    try {
        const payload = req.body;
        const payment = (payload && payload.payload && payload.payload.payment) ? payload.payload.payment.entity : null;

        if (!payment) {
            console.error("❌ Invalid Webhook Payload:", payload);
            return res.status(400).json({ success: false, message: "Invalid webhook data" });
        }

        // Extract necessary fields
        const { id, order_id, amount, currency, status, method, email, contact, created_at } = payment;
        const card_last4 = (payment.card && payment.card.last4) ? payment.card.last4 : null;
        const card_network = (payment.card && payment.card.network) ? payment.card.network : null;
        const card_type = (payment.card && payment.card.type) ? payment.card.type : null;
        const issuer = (payment.card && payment.card.issuer) ? payment.card.issuer : null;

        // Extract Machine ID (mid) safely
        let mid = null;
        if (payment.notes && Array.isArray(payment.notes)) {
            for (const note of payment.notes) {
                if (note.mid) {
                    mid = note.mid;
                    break; // Stop after finding the first valid mid
                }
            }
        }
        console.log("🔹 Machine ID (mid):", mid);

        // Store in PostgreSQL
        const query = `
            INSERT INTO payments (payment_id, order_id, amount, currency, status, method, card_last4, card_network, card_type, issuer, email, contact, created_at)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, to_timestamp($13))
            ON CONFLICT (payment_id) DO NOTHING;
        `;
        await pool.query(query, [id, order_id, amount, currency, status, method, card_last4, card_network, card_type, issuer, email, contact, created_at]);

        // Store payment in Redis (DB 0)
        if (mid) {
            await redisClient.select(0);
            await redisClient.set(mid, JSON.stringify(payment));
            console.log(`✅ Stored payment in Redis (DB 0) with key: payment:${mid}`);

            // Get the tariff from Redis DB 1
            const tariff = await getTariff(mid);

            // Calculate charge finish time
            const durationInMs = (amount / tariff) * 3600000; // Convert to milliseconds
            const chargeFinishTime = Date.now() + durationInMs;

            // Store charge finish time in Redis DB 2
            await setFinishTime(mid, chargeFinishTime);
        } else {
            console.warn("⚠️ No Machine ID (mid) found. Skipping Redis storage.");
        }

        res.status(200).json({ success: true, message: "Webhook processed successfully" });
    } catch (error) {
        console.error("❌ Error processing webhook:", error);
        res.status(500).json({ success: false, message: "Server Error" });
    }
});
// Endpoint to set tariff for a given IMEI in Redis DB 1
app.post("/settariff", async (req, res) => {
    try {
        const { imei, tariff } = req.body;

        // Validate input
        if (!imei || !tariff || isNaN(tariff)) {
            return res.status(400).json({ success: false, message: "Invalid IMEI or tariff value" });
        }

        await redisClient.select(1); // Switch to Redis DB 1
        await redisClient.set(imei, tariff);

        console.log(`✅ Tariff set for IMEI ${imei}: ${tariff}`);
        res.status(200).json({ success: true, message: `Tariff set successfully for IMEI ${imei}` });

    } catch (error) {
        console.error("❌ Error setting tariff:", error);
        res.status(500).json({ success: false, message: "Internal server error" });
    }
});

// Gracefully handle server shutdown
process.on("SIGINT", async () => {
    await redisClient.quit();
    console.log("🛑 Redis client closed. Exiting...");
    process.exit(0);
});

app.listen(3000, () => {
    console.log("🚀 Server is running on port 3000");
});
