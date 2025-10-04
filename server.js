// server.js
// Node.js (Express) Proxy Server

const express = require('express');
const axios = require('axios');
const cors = require('cors');
const app = express();
// যদি আপনি হোস্টিং প্ল্যাটফর্মে (যেমন Heroku, Railway) ডিপ্লয় করেন,
// তবে পরিবেশের পোর্ট ব্যবহার করুন, না হলে 3000 ব্যবহার করুন।
const PORT = process.env.PORT || 3000; 

// --- মিডলওয়্যার ---
// 1. CORS সক্ষম করা: এটি আপনার HTML পেজকে সার্ভারের সাথে যোগাযোগ করতে দেবে।
app.use(cors()); 

// 2. ক্লায়েন্ট থেকে আসা JSON ডেটা পার্স করার জন্য
app.use(express.json());

// 3. --- SMS BOOM PROXY ENDPOINT ---
// ক্লায়েন্ট-সাইড JavaScript এই POST এন্ডপয়েন্টকে কল করবে।
app.post('/api/boom-proxy', async (req, res) => {
    // ক্লায়েন্ট থেকে আসা ডেটা
    const { number, cycles } = req.body;

    // ভ্যালিডেশন
    if (!number || !cycles || cycles < 1 || cycles > 5) {
        return res.status(400).json({ status: 'error', message: 'Invalid number or cycle count (max 5).' });
    }
    
    // টার্গেট API এর URL তৈরি করা
    const TARGET_API_URL = `http://mahfuz-boom.gt.tc/?number=${number}&cycles=${cycles}`;

    console.log(`Proxying request: ${TARGET_API_URL}`);

    try {
        // টার্গেট API-কে কল করা
        // সার্ভার-টু-সার্ভার কলের ক্ষেত্রে CORS সমস্যা হয় না।
        await axios.get(TARGET_API_URL, { timeout: 15000 }); // ১৫ সেকেন্ডের টাইমআউট

        // সফলভাবে কল করা গেছে ধরে নিয়ে ক্লায়েন্টকে সফলতার বার্তা দেওয়া হলো।
        return res.json({ 
            status: 'success', 
            message: 'Boom request forwarded successfully to the API.',
            target: number,
            cycles: cycles
        });

    } catch (error) {
        console.error('API Call Error:', error.message);
        return res.status(502).json({
            status: 'error',
            message: 'Target API communication failed.',
            details: error.message
        });
    }
});

// সার্ভার চালু করা
app.listen(PORT, () => {
    console.log(`Proxy Server running on http://localhost:${PORT}`);
});
