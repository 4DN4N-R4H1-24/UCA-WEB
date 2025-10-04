// server.js
// আপনার Node.js সার্ভারের জন্য (GitHub-এ আপলোড করতে হবে)

const express = require('express');
const axios = require('axios');
const cors = require('cors');
const app = express();
const PORT = process.env.PORT || 3000; 

// --- মিডলওয়্যার ---
// CORS সক্ষম করা (ক্লায়েন্টকে কল করার অনুমতি দেয়)
app.use(cors()); 
// JSON ডেটা পার্স করার জন্য
app.use(express.json());

// 3. --- SMS BOOM PROXY ENDPOINT ---
app.post('/api/boom-proxy', async (req, res) => {
    const { number, cycles } = req.body;

    // ভ্যালিডেশন
    if (!number || !cycles || cycles < 1 || cycles > 5) {
        return res.status(400).json({ status: 'error', message: 'Invalid number or cycle count (max 5).' });
    }
    
    // টার্গেট API (যেটিকে কল করতে চান)
    const TARGET_API_URL = `http://mahfuz-boom.gt.tc/?number=${number}&cycles=${cycles}`;

    try {
        // টার্গেট API-কে কল করা
        await axios.get(TARGET_API_URL, { timeout: 15000 }); 

        // সফল হলে ক্লায়েন্টকে সফলতার বার্তা দেওয়া
        return res.json({ 
            status: 'success', 
            message: 'Boom request forwarded successfully to the API.',
            target: number,
            cycles: cycles
        });

    } catch (error) {
        console.error('Target API Call Error:', error.message);
        return res.status(502).json({
            status: 'error',
            message: 'Target API communication failed. (Check external API status)',
            details: error.message
        });
    }
});

// সার্ভার চালু করা
app.listen(PORT, () => {
    console.log(`Proxy Server running on port ${PORT}`);
});
