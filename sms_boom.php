<?php
// লগইন চেক (session)
session_start();
if(!isset($_SESSION['uca_auth'])){
    header("Location: index.html");
    exit;
}

// API কল করার অংশ
if(isset($_POST['number'])){
    $number = $_POST['number'];
    $url = "http://mahfuz-boom.gt.tc/?number={$number}&cycles=3";

    // API কল
    $response = @file_get_contents($url);

    if($response !== false){
        echo "আপনার কলিজা কে ভালোবাসা পাঠানো হচ্ছে";
    } else {
        echo "কোনো সমস্যা হয়েছে, পরে আবার চেষ্টা করুন।";
    }
    exit;
}
?>
