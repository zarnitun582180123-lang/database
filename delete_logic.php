<?php
session_start();

// ၁။ Admin Login စစ်ဆေးခြင်း
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once 'supabase.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        // PHP Header အစား JavaScript Redirection ကို သုံးလိုက်ခြင်း (Error ကင်းစေရန်)
        echo "<script>window.location.href='admin.php?status=deleted';</script>";
        exit();
    } else {
        echo "<h3>Error: Unable to delete data.</h3>";
        echo "HTTP Status Code: " . $httpCode . "<br>";
        echo "<a href='admin.php'>Back to Dashboard</a>";
    }
} else {
    echo "<script>window.location.href='admin.php';</script>";
    exit();
}
