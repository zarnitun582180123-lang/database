<?php
session_start();

// ၁။ Admin Login စစ်ဆေးခြင်း
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once 'supabase.php';

// ၂။ URL မှ ID စစ်ဆေးခြင်း
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // ၃။ Supabase API URL
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id;

    $ch = curl_init($url);
    
    // ၄။ CURL Options (DELETE Method)
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // ၅။ Headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
        "Content-Type: application/json"
    ]);

    // ၆။ Execute
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ၇။ Redirect လုပ်ခြင်း
    if ($httpCode >= 200 && $httpCode < 300) {
        header("Location: admin.php?status=deleted");
        exit();
    } else {
        // Error တက်ရင် output မထုတ်ခင် header error မတက်အောင် 
        // JavaScript နဲ့ redirect လုပ်တာ ဒါမှမဟုတ် message ပြတာ ပိုကောင်းပါတယ်
        echo "<h3>Error: Unable to delete data.</h3>";
        echo "HTTP Status Code: " . $httpCode . "<br>";
        echo "<a href='admin.php'>Back to Dashboard</a>";
    }
} else {
    header("Location: admin.php");
    exit();
}
