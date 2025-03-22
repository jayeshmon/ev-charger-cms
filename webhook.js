const express = require("express");
const { Pool } = require("pg");
const redis = require("redis");
require("dotenv").config();

const app = express();
app.use(express.json());

// PostgreSQL Connection
const pool = new Pool({
    connectionString: process.env.DATABASE_URL,
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

        // Store in Redis (Machine ID as key)
        if (mid) {
            await redisClient.set(`payment:${mid}`, JSON.stringify(payment), "EX", 86400); // Expire in 24 hours
            console.log(`✅ Stored in Redis with key: payment:${mid}`);
        } else {
            console.warn("⚠️ No Machine ID (mid) found. Skipping Redis storage.");
        }

        res.status(200).json({ success: true, message: "Webhook processed successfully" });
    } catch (error) {
        console.error("❌ Error processing webhook:", error);
        res.status(500).json({ success: false, message: "Server Error" });
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
