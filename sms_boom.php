<?php
// PHP Script: sms_boom_api.php (GET Request Optimized)
session_start();
header('Content-Type: text/plain; charset=utf-8'); 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// 1. HTTP METHOD চেক: শুধুমাত্র GET রিকোয়েস্ট অনুমোদিত
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo "❌ এরর (405): এই এন্ডপয়েন্টে শুধু GET রিকোয়েস্ট অনুমোদিত।";
    exit;
}

// 2. সেশন/লগইন চেক
if (!isset($_SESSION['uca_auth'])) {
    http_response_code(403);
    echo "❌ এরর (403): আপনার লগইন সেশন বৈধ নয়।";
    exit;
}

// 3. ইনপুট ডেটা চেক (URL প্যারামিটার থেকে)
if (!isset($_GET['number'])) {
    http_response_code(400);
    echo "⚠️ এরর (400): মোবাইল নাম্বার ইনপুটটি অনুপস্থিত।";
    exit;
}

$number = preg_replace('/\D/', '', $_DGET['number']);
if (strlen($number) !== 11) {
    http_response_code(400);
    echo "⚠️ সঠিক ১১ সংখ্যার মোবাইল নাম্বার দিন।";
    exit;
}

// 4. External API কল
$external_url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $external_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Custom GET API Client"); 

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. ফলাফল হ্যান্ডলিং
if ($response === false) {
    http_response_code(503);
    echo "❌ সংযোগ ব্যর্থ! নেটওয়ার্ক বা সার্ভার সমস্যা: " . $error; 
} else {
    if ($http_code >= 200 && $http_code < 300) {
        echo "✅ " . $response;
    } else {
        http_response_code($http_code); 
        echo "⚠️ External API এরর (HTTP " . $http_code . "): " . $response;
    }
}
?>
