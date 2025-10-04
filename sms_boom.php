<?php
session_start();

// লগইন চেক
if(!isset($_SESSION['uca_auth'])){
    http_response_code(403);
    echo "আপনার লগইন প্রয়োজন।";
    exit;
}

// শুধু POST মেথড গ্রহণ করা হবে
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['number'])){
    // নাম্বার sanitize
    $number = preg_replace('/\D/', '', $_POST['number']); 
    if(strlen($number) < 11){
        echo "সঠিক নাম্বার দিন।";
        exit;
    }

    $url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

    // cURL setup
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if($response !== false){
        echo "আপনার কলিজা কে ভালোবাসা পাঠানো হচ্ছে";
    } else {
        echo "কোনো সমস্যা হয়েছে, পরে আবার চেষ্টা করুন। " . $error;
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
?>
