<?php
require 'vendor/autoload.php';
require_once 'supabase.php';

// Redis Configuration
$redis_url = getenv('REDIS_URL');

while (true) {
    try {
        $client = new Predis\Client($redis_url);
        echo "Worker: Connected to Redis. Waiting for data...\n";

        while (true) {
            // blpop ကို စက္ကန့် ၃၀ ပဲ စောင့်ခိုင်းပါမယ် (Timeout error မတက်စေရန်)
            $data = $client->blpop(['registration_queue'], 30);

            if ($data) {
                $payload = json_decode($data[1], true);
                $name = $payload['name'];
                echo "Worker: Processing registration for: $name\n";

                $success = sendToSupabase(
                    $payload['name'],
                    $payload['email'],
                    $payload['message'],
                    $payload['student_id'],
                    $payload['course_id']
                );

                if ($success) {
                    echo "Worker: Successfully sent to Supabase!\n";
                } else {
                    echo "Worker: Failed to send to Supabase.\n";
                }
            }
        }
    } catch (Exception $e) {
        // Timeout သို့မဟုတ် Connection ပြတ်သွားလျှင် ၅ စက္ကန့်နားပြီး ပြန်ချိတ်မည်
        echo "Worker Connection Error: " . $e->getMessage() . "\n";
        echo "Worker: Reconnecting in 5 seconds...\n";
        sleep(5);
    }
}
