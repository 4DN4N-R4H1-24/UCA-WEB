<?php
session_start();
header('Content-Type: text/plain; charset=utf-8'); 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo "❌ এরর (405): শুধু POST রিকোয়েস্ট অনুমোদিত।";
    exit;
}

if (!isset($_SESSION['uca_auth'])) {
    http_response_code(403);
    echo "❌ এরর (403): আপনার লগইন সেশন বৈধ নয়।";
    exit;
}

if (!isset($_POST['number'])) {
    http_response_code(400);
    echo "⚠️ এরর (400): মোবাইল নাম্বার ইনপুটটি অনুপস্থিত।";
    exit;
}

$number = preg_replace('/\D/', '', $_POST['number']);
if (strlen($number) !== 11) {
    http_response_code(400);
    echo "⚠️ সঠিক ১১ সংখ্যার মোবাইল নাম্বার দিন।";
    exit;
}

$url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Custom API Client"); 

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

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
