<?php
// worker.php
require_once 'supabase.php';
require 'vendor/autoload.php';

// Redis ချိတ်ဆက်ခြင်း
try {
    $redis_url = getenv('REDIS_URL');
    $redis = new Predis\Client($redis_url);
    echo "Worker started. Waiting for data...\n";
} catch (Exception $e) {
    die("Redis Connection Error: " . $e->getMessage());
}

// အမြဲတမ်း Run နေမည့် loop (Infinite Loop)
while (true) {
    // Queue ထဲက data ကို ဆွဲထုတ်ခြင်း (BLPOP သည် data မရှိမချင်း စောင့်နေမည်)
    // 0 ဆိုသည်မှာ data ရောက်လာသည်အထိ အချိန်အကန့်အသတ်မရှိ စောင့်ခိုင်းခြင်းဖြစ်သည်
    $result = $redis->blpop('registration_queue', 0);

    if ($result) {
        $data_json = $result[1];
        $student = json_decode($data_json, true);

        echo "Processing registration for: " . $student['name'] . "\n";

        // Supabase ထဲသို့ ထည့်ခြင်း (supabase.php ထဲက function ကို သုံးသည်)
        $success = sendToSupabase(
            $student['name'],
            $student['email'],
            $student['message'],
            $student['student_id'],
            $student['course_id']
        );

        if ($success) {
            echo "Successfully sent to Supabase!\n";
        } else {
            echo "Failed to send to Supabase. Data: " . $data_json . "\n";
            // မအောင်မြင်လျှင် Queue ထဲ ပြန်ထည့်ချင်က ထည့်နိုင်သည် (Optional)
        }
    }
    
    // Server ဝန်မပိစေရန် ခေတ္တနားခြင်း
    usleep(500000); // 0.5 စက္ကန့်
}
