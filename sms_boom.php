<?php
// session_start(); // সেশন চেক বন্ধ করা হলো, শুধু পরীক্ষার জন্য

// POST ছাড়া অন্য রিকোয়েস্ট এলে Method Not Allowed এরর দেবে
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    header('Allow: POST'); // ব্রাউজারকে জানিয়ে দেওয়া হলো যে শুধু POST অনুমোদিত
    echo "Method Not Allowed";
    exit;
}

// লগইন চেকটি আপাতত বন্ধ আছে। কাজ সফল হলে নিচের কমেন্টগুলো মুছে ব্যবহার করুন:
/*
if(!isset($_SESSION['uca_auth'])){
    http_response_code(403);
    echo "❌ এরর: আপনার লগইন সেশন বৈধ নয়।";
    exit;
}
*/

// ডেটা ইনপুট চেক
if(!isset($_POST['number'])){
    http_response_code(400); // Bad Request
    echo "⚠️ এরর: মোবাইল নাম্বার ইনপুটটি অনুপস্থিত।";
    exit;
}

$number = preg_replace('/\D/', '', $_POST['number']);

// নম্বর চেক: শুধুমাত্র ১১ সংখ্যার নম্বর অনুমোদন
if(strlen($number) !== 11){ 
    http_response_code(400); 
    echo "⚠️ দয়া করে সঠিক ১১ সংখ্যার মোবাইল নাম্বার দিন।";
    exit;
}

// মূল API কল প্রসেস
$url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

$ch = curl_init();
// ... (বাকি cURL সেটিংস আপনার আগের কোড থেকে নেওয়া হলো)
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36"); 

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ফলাফল হ্যান্ডলিং
if($response === false){
    http_response_code(503);
    echo "❌ সংযোগ ব্যর্থ! সার্ভার সংযোগে সমস্যা হয়েছে: " . $error; 
} else {
    // এখানে সফল বা ব্যর্থ যাই হোক, External API এর রেসপন্সটি দেখানো হচ্ছে।
    echo $response;
}
?>
