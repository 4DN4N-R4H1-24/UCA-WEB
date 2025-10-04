<?php
// PHP Script: sms_boom_api.php
session_start();
header('Content-Type: text/plain; charset=utf-8'); 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// 1. HTTP METHOD চেক: শুধু POST রিকোয়েস্ট অনুমোদিত
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo "❌ এরর (405): এই এন্ডপয়েন্টে শুধু POST রিকোয়েস্ট অনুমোদিত।";
    exit;
}

// 2. সেশন/লগইন চেক
// যদি আপনার লগইন প্রসেস এই সেশনটি সেট না করে, তাহলে এটি ব্যর্থ হবে
if (!isset($_SESSION['uca_auth'])) {
    http_response_code(403);
    echo "❌ এরর (403): আপনার লগইন সেশন বৈধ নয়। দয়া করে আবার লগইন করুন।";
    exit;
}

// 3. ইনপুট ডেটা চেক এবং ভ্যালিডেশন
if (!isset($_POST['number'])) {
    http_response_code(400);
    echo "⚠️ এরর (400): মোবাইল নাম্বার ইনপুটটি অনুপস্থিত।";
    exit;
}

$number = preg_replace('/\D/', '', $_POST['number']);

if (strlen($number) !== 11) {
    http_response_code(400);
    echo "⚠️ দয়া করে সঠিক ১১ সংখ্যার মোবাইল নাম্বার দিন।";
    exit;
}

// 4. External API কল প্রসেস
$url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// একটি সাধারণ, কিন্তু বৈধ ইউজার-এজেন্ট
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (GitHub Hosted SMS Boom Client)"); 

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. ফলাফল হ্যান্ডলিং
if ($response === false) {
    http_response_code(503); // সার্ভিস অনুপলব্ধ এরর
    echo "❌ সংযোগ ব্যর্থ! নেটওয়ার্ক বা সার্ভার সমস্যা: " . $error; 
} else {
    // External API এর রেসপন্সটি সরাসরি ক্লায়েন্টকে পাঠানো হলো।
    if ($http_code >= 200 && $http_code < 300) {
        // সফলতার ক্ষেত্রে External API-এর রেসপন্সটি দেখানো হলো
        echo "✅ " . $response;
    } else {
        // External API কোনো এরর কোড (যেমন: 400, 500) রিটার্ন করলে
        http_response_code($http_code); 
        echo "⚠️ External API এরর (HTTP " . $http_code . "): " . $response;
    }
}
?>
