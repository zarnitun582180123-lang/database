<?php
require 'vendor/autoload.php';
require_once 'supabase.php';

// ၁။ Redis Configuration
$redis_url = getenv('REDIS_URL');

echo "Worker စတင်နေပါပြီ... ဒေတာများကို စောင့်ဆိုင်းနေသည်...\n";

while (true) {
    try {
        $client = new Predis\Client($redis_url);
        
        // Redis ချိတ်ဆက်မှု အောင်မြင်ကြောင်း Log ပြခြင်း
        $start_msg = "Worker: Connected to Redis at " . date('Y-m-d H:i:s');
        $client->rpush('worker_logs', $start_msg);
        $client->ltrim('worker_logs', -50, -1); // နောက်ဆုံး Log အကြောင်း ၅၀ ပဲ သိမ်းမယ်

        while (true) {
            // ၂။ Redis Queue ထဲမှ ဒေတာကို စက္ကန့် ၃၀ စောင့်ပြီး ဆွဲထုတ်ခြင်း
            $data = $client->blpop(['registration_queue'], 30);

            if ($data) {
                $payload = json_decode($data[1], true);
                $name = $payload['name'];
                
                $max_retries = 3; 
                $is_success = false;

                // ၃။ Retry Logic (အကြိမ်ကြိမ် ကြိုးစားခြင်း)
                for ($i = 0; $i <= $max_retries; $i++) {
                    $attempt = $i + 1;
                    $status_msg = "ကျောင်းသား $name ၏ ဒေတာကို သိမ်းဆည်းနေပါသည် (ကြိုးစားမှု - $attempt)";
                    
                    // Terminal နှင့် Redis နှစ်နေရာလုံးမှာ ပြသခြင်း
                    echo $status_msg . "\n";
                    $client->rpush('worker_logs', $status_msg);

                    // Supabase Database ထဲသို့ ထည့်ခြင်း
                    $success = sendToSupabase(
                        $payload['name'],
                        $payload['email'],
                        $payload['message'],
                        $payload['student_id'],
                        $payload['course_id']
                    );

                    if ($success) {
                        $success_msg = "✅ $name: အောင်မြင်စွာ သိမ်းဆည်းပြီးပါပြီ။";
                        echo $success_msg . "\n";
                        $client->rpush('worker_logs', $success_msg);
                        $is_success = true;
                        break; 
                    } else {
                        $remaining = $max_retries - $i;
                        if ($remaining > 0) {
                            $retry_msg = "❌ $name: အမှားရှိနေပါသည်။ ၅ စက္ကန့်အကြာတွင် ပြန်ကြိုးစားပါမည် (ကျန်ရှိသည့်အကြိမ်ရေ: $remaining)";
                            echo $retry_msg . "\n";
                            $client->rpush('worker_logs', $retry_msg);
                            sleep(5); 
                        } else {
                            $fail_msg = "⚠️ $name: အကြိမ်ကြိမ် ကြိုးစားသော်လည်း မအောင်မြင်ပါ။ ဒေတာကို စာရင်းမှ ဖယ်ထုတ်လိုက်ပါပြီ။";
                            echo $fail_msg . "\n";
                            $client->rpush('worker_logs', $fail_msg);
                            
                            // ၄။ (Optional) မအောင်မြင်သော ဒေတာများကို Failed Queue ထဲ သီးသန့်သိမ်းခြင်း
                            $client->rpush('failed_registrations', json_encode($payload));
                        }
                    }
                    // Log အရေအတွက်ကို ထိန်းညှိခြင်း
                    $client->ltrim('worker_logs', -50, -1);
                }
            }
        }
    } catch (Exception $e) {
        $error_msg = "Worker Connection Error: " . $e->getMessage();
        echo $error_msg . "\n";
        echo "Worker: Reconnecting in 5 seconds...\n";
        sleep(5);
    }
}
