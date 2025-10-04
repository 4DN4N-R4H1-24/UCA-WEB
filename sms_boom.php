<?php
session_start();
header('Content-Type: text/plain; charset=utf-8'); // নিশ্চিত করা হলো রেসপন্স প্লেইন টেক্সট

// ⚠️ লগইন চেক: আপনার লগইন প্রসেস যদি এই সেশনটি সেট না করে, তাহলে এটি কাজ করবে না।
if(!isset($_SESSION['uca_auth'])){
    http_response_code(403);
    echo "❌ এরর: আপনার লগইন সেশন বৈধ নয়।";
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['number'])){
    $number = preg_replace('/\D/', '', $_POST['number']);
    
    // নম্বর চেক: শুধুমাত্র ১১ সংখ্যার নম্বর অনুমোদন
    if(strlen($number) !== 11){ 
        http_response_code(400); // Bad Request
        echo "⚠️ দয়া করে সঠিক ১১ সংখ্যার মোবাইল নাম্বার দিন।";
        exit;
    }

    $url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // টাইমআউট ১৫ সেকেন্ডে বাড়ানো হলো
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // উন্নত ইউজার-এজেন্ট ব্যবহার করা হলো
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36"); 

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // cURL সংযোগ ব্যর্থ হলে
    if($response === false){
        http_response_code(503); // Service Unavailable
        echo "❌ সংযোগ ব্যর্থ! সার্ভার সংযোগে সমস্যা হয়েছে: " . $error; 
    } 
    // External API থেকে রেসপন্স সফলভাবে পেলে
    else if ($http_code >= 200 && $http_code < 300) {
        // External API সফল রেসপন্স দিলেও, তার টেক্সটটি দেখাবে।
        echo "✅ " . $response;
    } 
    // External API কোনো এরর কোড (যেমন: 400, 500) রিটার্ন করলে
    else {
        http_response_code($http_code);
        echo "⚠️ API কল ব্যর্থ হয়েছে। HTTP কোড: " . $http_code . "। রেসপন্স: " . $response;
    }

} else {
    http_response_code(405);
    echo "❌ এরর: ভুল রিকোয়েস্ট পদ্ধতি বা ডেটা অনুপস্থিত।";
}
?>
