const https = require('https');
const http = require('http');

module.exports = (req, res) => {
    // CORS সুরক্ষা bypass
    res.setHeader('Access-Control-Allow-Origin', '*'); 
    res.setHeader('Content-Type', 'text/plain');

    const targetUrl = req.query.target;

    if (!targetUrl) {
        res.status(400).send('Error: Target API URL is missing.');
        return;
    }

    const protocol = targetUrl.startsWith('https') ? https : http;

    // সার্ভার-সাইড থেকে আসল API-কে কল করা
    protocol.get(targetUrl, (apiResponse) => {
        let data = '';

        apiResponse.on('data', (chunk) => {
            data += chunk;
        });

        apiResponse.on('end', () => {
            res.status(200).send("Success! API call initiated."); 
        });

    }).on('error', (e) => {
        // API সার্ভারের সাথে সংযোগ স্থাপন করতে ব্যর্থ হলে
        res.status(500).send(`Proxy Error: Could not connect to the target API.`);
    });
};
