<?php
// register.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'supabase.php';
require 'vendor/autoload.php'; 

$message_sent = false;
$error = false;
$redis_error_msg = "";

// ၁။ Redis ချိတ်ဆက်ရန်
try {
    $redis_url = getenv('REDIS_URL'); 
    if ($redis_url) {
        // rediss:// protocol သုံးထားလျှင် TLS အတွက် အဆင်ပြေပါသည်
        $redis = new Predis\Client($redis_url);
        $redis->connect();
        error_log("Redis Connection: Success!"); 
    } else {
        $redis = new Predis\Client('redis://127.0.0.1:6379');
    }
} catch (Exception $e) {
    error_log("Redis Connection Error: " . $e->getMessage());
    $redis_error_msg = "Redis ချိတ်ဆက်မှု မအောင်မြင်ပါ - " . $e->getMessage();
    $error = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_data = [
        'name'       => $_POST['name'] ?? '',
        'email'      => $_POST['email'] ?? '',
        'student_id' => $_POST['student_id'] ?? '',
        'course_id'  => $_POST['course_id'] ?? '',
        'message'    => $_POST['message'] ?? '',
        'timestamp'  => time()
    ];

    try {
        if (isset($redis)) {
            // Redis Queue (registration_queue) ထဲသို့ ထည့်ခြင်း
            $redis->rpush('registration_queue', json_encode($student_data));
            $message_sent = true;
        } else {
            $error = true;
        }
    } catch (Exception $e) {
        error_log("Queue Error: " . $e->getMessage());
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CU - Student Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3ecf8e;
            --primary-dark: #2eb87b;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --text-light: #f8fafc;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-dark); color: var(--text-light); margin: 0; min-height: 100vh; }
        nav { display: flex; padding: 15px 8%; background: rgba(15, 23, 42, 0.9); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo { font-weight: 800; font-size: 1.2rem; color: var(--primary); text-decoration: none; }
        .wrapper { display: flex; justify-content: center; align-items: center; padding: 60px 20px; }
        .container { background: var(--card-bg); padding: 2.5rem; border-radius: 16px; width: 100%; max-width: 450px; border: 1px solid rgba(255,255,255,0.1); }
        h2 { margin-top: 0; color: var(--primary); text-align: center; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 5px; font-size: 0.85rem; color: #94a3b8; }
        input, textarea, select { width: 100%; padding: 12px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white; box-sizing: border-box; }
        .submit-btn { width: 100%; padding: 14px; background: var(--primary); color: #0f172a; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 10px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .success { background: rgba(62, 207, 142, 0.15); color: #3ecf8e; border: 1px solid var(--primary); }
        .error { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid #ef4444; }
    </style>
</head>
<body>

<nav><a href="index.php" class="logo">CU PORTAL</a></nav>

<div class="wrapper">
    <div class="container">
        <h2>CU Registration</h2>
        
        <?php if ($message_sent): ?>
            <div class="alert success">✓ စာရင်းသွင်းမှု အောင်မြင်ပါသည်။ Data များကို စစ်ဆေးနေပါသည်။</div>
        <?php endif; ?>

        <?php if ($error || !empty($redis_error_msg)): ?>
            <div class="alert error">✕ <?php echo $redis_error_msg ?: "စနစ်အတွင်း အမှားတစ်ခုရှိနေပါသည်။"; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>အမည် (Full Name)</label>
                <input type="text" name="name" placeholder="Mg Mg" required>
            </div>
            
            <div class="form-group">
                <label>ကျောင်းသားကတ် နံပါတ် (Student ID)</label>
                <input type="text" name="student_id" placeholder="CU-12345" required>
            </div>

            <div class="form-group">
                <label>အီးမေးလ် (Email Address)</label>
                <input type="email" name="email" placeholder="name@cu.edu.mm" required>
            </div>

            <div class="form-group">
                <label>အထူးပြုဘာသာ (Major)</label>
                <select name="course_id" required>
                    <option value="">-- သင်တန်းရွေးချယ်ပါ --</option>
                    <?php 
                    $courses = get_courses(); 
                    foreach($courses as $c): 
                    ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>မှတ်ချက် (Message)</label>
                <textarea name="message" rows="3"></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Register Now</button>
        </form>
    </div>
</div>

</body>
</html>
