const express = require("express");
const Redis = require("ioredis");
const moment = require("moment"); // Optional: for date calculations if needed
require("dotenv").config();

const app = express();
app.use(express.json());

// Initialize ioredis client
const redisClient = new Redis({
  host: process.env.REDIS_HOST || "127.0.0.1",
  port: process.env.REDIS_PORT || 6379,
});

// Log connection events
redisClient.on("connect", () => console.log("✅ Connected to Redis (ioredis)"));
redisClient.on("error", (err) => console.error("❌ Redis Error:", err));

// Helper: Get current timestamp in milliseconds
const getCurrentTimestampMs = () => Date.now();

// Helper: Retrieve all key-value pairs from a given Redis DB using pipeline
const getAllKeyValues = async (dbIndex) => {
  // Switch to the desired DB
  await redisClient.select(dbIndex);
  const keys = await redisClient.keys("*");
  if (!keys.length) return [];
  
  const pipeline = redisClient.pipeline();
  keys.forEach((key) => pipeline.get(key));
  const results = await pipeline.exec();
  
  // results is an array where each element is [error, value]
  return keys.map((key, idx) => {
    let value = results[idx][1];
    try {
      value = JSON.parse(value);
    } catch (e) {
      // If not valid JSON, keep as is
    }
    return { key, value };
  });
};

// 1) Get online devices (sent data in the last 2 minutes) from Redis DB 3
app.get("/online-devices", async (req, res) => {
  try {
    const keyValues = await getAllKeyValues(3); // DB 3 stores device packets
    const currentTime = getCurrentTimestampMs();
    const onlineDevices = keyValues.filter(({ value: packet }) => {
      if (packet && packet.T) {
        // Assuming "T" is a timestamp string in a format recognized by Date
        const telemetryTimeMs = new Date(packet.T).getTime();
        return currentTime - telemetryTimeMs <= 120000; // within 2 minutes
      }
      return false;
    }).map(({ value }) => value);
    
    res.json({ status: "success", onlineDevices });
  } catch (error) {
    console.error("❌ Error fetching online devices:", error);
    res.status(500).json({ status: "error", message: "Server Error" });
  }
});

// 2) Get currently charging devices (i = 1) from Redis DB 3
app.get("/charging-devices", async (req, res) => {
  try {
    const keyValues = await getAllKeyValues(3);
    const chargingDevices = keyValues.filter(({ value: packet }) => packet && packet.i === 1)
                                     .map(({ value }) => value);
    res.json({ status: "success", chargingDevices });
  } catch (error) {
    console.error("❌ Error fetching charging devices:", error);
    res.status(500).json({ status: "error", message: "Server Error" });
  }
});

// 3) Get free devices (sending data within 2 minutes but i = 0) from Redis DB 3
app.get("/free-devices", async (req, res) => {
  try {
    const keyValues = await getAllKeyValues(3);
    const currentTime = getCurrentTimestampMs();
    const freeDevices = keyValues.filter(({ value: packet }) => {
      if (packet && packet.T && typeof packet.i === "number") {
        const telemetryTimeMs = new Date(packet.T).getTime();
        return currentTime - telemetryTimeMs <= 120000 && packet.i === 0;
      }
      return false;
    }).map(({ value }) => value);
    
    res.json({ status: "success", freeDevices });
  } catch (error) {
    console.error("❌ Error fetching free devices:", error);
    res.status(500).json({ status: "error", message: "Server Error" });
  }
});

// 4) Get power disconnected devices (p = 0) from Redis DB 3
app.get("/disconnected-devices", async (req, res) => {
  try {
    const keyValues = await getAllKeyValues(3);
    const disconnectedDevices = keyValues.filter(({ value: packet }) => packet && packet.p === 0)
                                         .map(({ value }) => value);
    res.json({ status: "success", disconnectedDevices });
  } catch (error) {
    console.error("❌ Error fetching disconnected devices:", error);
    res.status(500).json({ status: "error", message: "Server Error" });
  }
});

// 5) Get count of payment received devices (entries in Redis DB 0)
app.get("/payment-received-count", async (req, res) => {
  try {
    await redisClient.select(0);
    const keys = await redisClient.keys("*");
    res.json({ status: "success", paymentCount: keys.length });
  } catch (error) {
    console.error("❌ Error fetching payment count:", error);
    res.status(500).json({ status: "error", message: "Server Error" });
  }
});

// 6) Monitor disconnections from Redis DB 2
// If current time is greater than the stored disconnection time, send an SMS and delete the key.
const monitorDisconnections = async () => {
  try {
    await redisClient.select(2);
    const keys = await redisClient.keys("*");
    if (!keys.length) {
      console.log("✅ No disconnections pending.");
      return;
    }
    
    const pipeline = redisClient.pipeline();
    keys.forEach((key) => pipeline.get(key));
    const results = await pipeline.exec();
    
    const currentTime = getCurrentTimestampMs();
    keys.forEach(async (key, idx) => {
      const value = results[idx][1];
      if (value) {
        const disconnectTime = parseInt(value, 10);
        if (currentTime > disconnectTime) {
          console.log(`⚠️ Device ${key} exceeded disconnection time! Sending SMS...`);
          await sendSms(key);
          await redisClient.del(key); // Remove the entry after sending SMS
        }
      }
    });
  } catch (error) {
    console.error("❌ Error monitoring disconnections:", error);
  }
};

// Placeholder function for sending SMS
const sendSms = async (imei) => {
  console.log(`📲 Sending SMS to disconnect device with IMEI: ${imei}`);
  // TODO: Integrate with your GSM/SMS API here
};

// Run monitorDisconnections every minute
setInterval(monitorDisconnections, 60000);

// Start the server
const PORT = process.env.PORT || 3002;
app.listen(PORT, () => {
  console.log(`🚀 Server running on http://localhost:${PORT}`);
});
