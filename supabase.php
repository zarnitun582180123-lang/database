<?php
// supabase.php

// Render Dashboard ရဲ့ Environment Variables ထဲက Key နာမည်တွေကိုပဲ သုံးရပါမယ်
define('SUPABASE_URL', getenv('SUPABASE_URL'));
define('SUPABASE_KEY', getenv('SUPABASE_KEY'));

function curl_request($url, $method = 'GET', $data = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST') curl_setopt($ch, CURLOPT_POST, true);
    if ($method === 'PATCH' || $method === 'DELETE') curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Render Logs မှာ အမှားစစ်နိုင်ရန် Log ထုတ်ခြင်း
    if ($httpCode < 200 || $httpCode >= 300) {
        error_log("Supabase API Error: Code $httpCode | Response: $response");
    }
    
    curl_close($ch);
    return ['code' => $httpCode, 'data' => json_decode($response, true)];
}

// ကျောင်းသားသစ် စာရင်းသွင်းရန်
function sendToSupabase($name, $email, $message, $student_id, $course_id) {
    $url = SUPABASE_URL . "/rest/v1/students";
    $payload = [
        'name' => $name, 
        'email' => $email, 
        'message' => $message, 
        'student_id' => $student_id, 
        'course_id' => (int)$course_id, 
        'status' => 'pending'
    ];
    $res = curl_request($url, 'POST', $payload);
    return ($res['code'] >= 200 && $res['code'] < 300);
}

// Course စာရင်းများကို ဆွဲထုတ်ရန်
function get_courses() {
    $url = SUPABASE_URL . "/rest/v1/courses?select=*&order=course_name.asc";
    $res = curl_request($url);
    
    // API မှ data ရလျှင် ၎င်းကိုသုံးမည်၊ မရလျှင် Default စာရင်းကို ပြန်ပေးမည်
    if (!empty($res['data']) && is_array($res['data'])) {
        return $res['data'];
    }

    return [
        ['id' => 1, 'course_name' => 'Computer Science'],
        ['id' => 2, 'course_name' => 'Computer Technology'],
        ['id' => 3, 'course_name' => 'Information Technology']
    ];
}

// Approved ဖြစ်ပြီးသား ကျောင်းသားအရေအတွက်
function get_student_count() {
    $url = SUPABASE_URL . "/rest/v1/students?status=eq.approved&select=id";
    $res = curl_request($url);
    return is_array($res['data']) ? count($res['data']) : 0;
}
// ၄။ ကျောင်းသားအားလုံးကို ဆွဲထုတ်ရန် (admin.php အတွက်)
function get_students() {
    // courses table နဲ့ join ထားပြီး ကျောင်းသားစာရင်းကို ဆွဲထုတ်ခြင်း
    $url = SUPABASE_URL . "/rest/v1/students?select=*,courses(course_name)&order=created_at.desc";
    $res = curl_request($url);
    
    if (!empty($res['data']) && is_array($res['data'])) {
        return $res['data'];
    }
    
    return []; // Data မရှိရင် Empty Array ပြန်ပေးမယ်
}
?>


