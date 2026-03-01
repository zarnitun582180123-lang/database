<?php
// supabase.php
// define('SUPABASE_URL', 'https://afyuttkumaldgsdneutm.supabase.co');
// define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImFmeXV0dGt1bWFsZGdzZG5ldXRtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzIxNTI0NjAsImV4cCI6MjA4NzcyODQ2MH0.2wirSdNoxvb3wuu3zdUUukEhMnXeAiBw7d91qgeRub0'); 
define('SUPABASE_URL', getenv('https://afyuttkumaldgsdneutm.supabase.co'));
define('SUPABASE_KEY', getenv('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImFmeXV0dGt1bWFsZGdzZG5ldXRtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzIxNTI0NjAsImV4cCI6MjA4NzcyODQ2MH0.2wirSdNoxvb3wuu3zdUUukEhMnXeAiBw7d91qgeRub0'));
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
    curl_close($ch);
    return ['code' => $httpCode, 'data' => json_decode($response, true)];
}

// ၁။ ကျောင်းသားသစ် စာရင်းသွင်းရန်
function sendToSupabase($name, $email, $message, $student_id, $course_id) {
    $url = SUPABASE_URL . "/rest/v1/students";
    $payload = [
        'name' => $name, 'email' => $email, 'message' => $message, 
        'student_id' => $student_id, 'course_id' => (int)$course_id, 'status' => 'pending'
    ];
    $res = curl_request($url, 'POST', $payload);
    return ($res['code'] >= 200 && $res['code'] < 300);
}

// ၂။ ကျောင်းသားအားလုံးကို Course Name နှင့်တကွ ဆွဲထုတ်ရန်
function get_students() {
    $url = SUPABASE_URL . "/rest/v1/students?select=*,courses(course_name)&order=created_at.desc";
    $res = curl_request($url);
    return $res['data'] ?: [];
}

// ၃။ Course စာရင်းများကို ဆွဲထုတ်ရန်
function get_courses() {
    $url = SUPABASE_URL . "/rest/v1/courses?select=*&order=course_name.asc";
    $res = curl_request($url);
    return [
        ['id' => 1, 'course_name' => 'Computer Science'],
        ['id' => 2, 'course_name' => 'Computer Technology'],
        ['id' => 3, 'course_name' => 'Information Technology']
    ];
}

// ၄။ ID ဖြင့် ကျောင်းသားကို ရှာရန်
function get_student_by_id($id) {
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id . "&select=*,courses(course_name)";
    $res = curl_request($url);
    return (isset($res['data'][0])) ? $res['data'][0] : null;
}

// ၅။ ကျောင်းသားအရေအတွက် (Home Page အတွက်)
function get_student_count() {
    $url = SUPABASE_URL . "/rest/v1/students?status=eq.approved&select=id";
    $res = curl_request($url);
    return is_array($res['data']) ? count($res['data']) : 0;
}

// ၆။ Approve လုပ်ရန်
function approve_student($id) {
    $url = SUPABASE_URL . "/rest/v1/students?id=eq." . $id;
    $res = curl_request($url, 'PATCH', ['status' => 'approved']);
    return ($res['code'] >= 200 && $res['code'] < 300);
}

?>

