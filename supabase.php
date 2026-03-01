<?php
// supabase.php
// Note: File အဆုံးတွင် ?> ပိတ်ရန်မလိုပါ။ ၎င်းသည် 'Headers already sent' error ကို ကာကွယ်ပေးသည်။

// ၁။ Render Environment Variables များ
define('SUPABASE_URL', getenv('SUPABASE_URL'));
define('SUPABASE_KEY', getenv('SUPABASE_KEY'));

// ၂။ General CURL Request Function
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
    
    if ($httpCode < 200 || $httpCode >= 300) {
        error_log("Supabase API Error: Code $httpCode | Response: $response");
    }
    
    curl_close($ch);
    return ['code' => $httpCode, 'data' => json_decode($response, true)];
}

// ၃။ ကျောင်းသားသစ် စာရင်းသွင်းရန် (Used in Worker/Register)
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

// ၄။ Course စာရင်းများကို ဆွဲထုတ်ရန်
function get_courses() {
    $url = SUPABASE_URL . "/rest/v1/courses?select=*&order=course_name.asc";
    $res = curl_request($url);
    
    if (!empty($res['data']) && is_array($res['data'])) {
        return $res['data'];
    }

    return [
        ['id' => 1, 'course_name' => 'Computer Science'],
        ['id' => 2, 'course_name' => 'Computer Technology'],
        ['id' => 3, 'course_name' => 'Information Technology']
    ];
}

// ၅။ Approved ဖြစ်ပြီးသား ကျောင်းသားအရေအတွက် (Dashboard အတွက်)
function get_student_count() {
    $url = SUPABASE_URL . "/rest/v1/students?status=eq.approved&select=id";
    $res = curl_request($url);
    return is_array($res['data']) ? count($res['data']) : 0;
}

// ၆။ ကျောင်းသားအားလုံးကို ဆွဲထုတ်ရန် (admin.php အတွက်)
function get_students() {
    $url = SUPABASE_URL . "/rest/v1/students?select=*,courses(course_name)&order=created_at.desc";
    $res = curl_request($url);
    
    if (!empty($res['data']) && is_array($res['data'])) {
        return $res['data'];
    }
    return [];
}

// ၇။ ID ဖြင့် ကျောင်းသားဒေတာ ရှာရန် (Email ပို့ရန်အတွက်)
function get_student_by_id($id) {
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id . "&select=*,courses(course_name)";
    $res = curl_request($url);
    
    if (!empty($res['data']) && is_array($res['data'])) {
        return $res['data'][0];
    }
    return null;
}

// ၈။ ကျောင်းသားကို Approve လုပ်ရန် (Status Update)
function approve_student($id) {
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id;
    $payload = ['status' => 'approved'];
    $res = curl_request($url, 'PATCH', $payload);
    return ($res['code'] >= 200 && $res['code'] < 300);
}

// ၉။ ကျောင်းသားကို ဖျက်ရန် (Delete Logic တွင် သုံးနိုင်ရန်)
function delete_student($id) {
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id;
    $res = curl_request($url, 'DELETE');
    return ($res['code'] >= 200 && $res['code'] < 300);
}
