<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ၁။ Admin Login ဝင်ထားခြင်း ရှိမရှိ စစ်ဆေးခြင်း
if (!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}

require_once 'supabase.php';

// ၂။ PHPMailer Library ကို ချိတ်ဆက်ခြင်း
require 'PHPMailer/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // ၃။ ကျောင်းသားဒေတာကို Database မှ အရင်ဆွဲထုတ်ခြင်း (Course Name ပါ ပါလာမည်)
    $student = get_student_by_id($id); 

    // ၄။ ကျောင်းသားရှိလျှင် Database status ကို 'approved' သို့ Update လုပ်ခြင်း
    if ($student && approve_student($id)) {
        
        $mail = new PHPMailer(true);

        try {
            // ၅။ Gmail SMTP Server Settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zarnitun582180123@gmail.com'; 
            $mail->Password   = 'kqfv xeen uqje kbbp'; // သင်၏ App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // ၆။ ပို့သူနှင့် လက်ခံသူ သတ်မှတ်ခြင်း
            $mail->setFrom('zarnitun582180123@gmail.com', 'UCS Monywa Admin');
            $mail->addAddress($student['email'], $student['name']);

            // ၇။ Email Content (အကြောင်းအရာ)
            $mail->isHTML(true);
            $mail->Subject = 'Registration Approved - UCS Monywa';
            
            // Join ထားသော courses table မှ course_name ကို ယူသုံးခြင်း
            $course_name = $student['courses']['course_name'] ?? 'လျှောက်ထားသောသင်တန်း';

            $mail->Body = "
                <div style='font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
                    <h2 style='color: #3ecf8e;'>မင်္ဂလာပါ {$student['name']}၊</h2>
                    <p>သင်လျှောက်ထားသော <b>{$course_name}</b> ဘာသာရပ်အတွက် ကျောင်းဝင်ခွင့်မှတ်ပုံတင်ခြင်းကို Admin မှ <b>အတည်ပြု (Approved)</b> ပေးလိုက်ပြီဖြစ်ကြောင်း အကြောင်းကြားအပ်ပါသည်။</p>
                    
                    <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <p style='margin: 0;'>သင်၏ Student ID မှာ: <span style='color: #3ecf8e; font-weight: bold; font-size: 1.2rem;'>{$student['student_id']}</span> ဖြစ်ပါသည်။</p>
                    </div>

                    <p>လိုအပ်သည်များရှိပါက ကျောင်းသို့ လူကိုယ်တိုင်လာရောက်စုံစမ်းနိုင်ပါသည်။</p>
                    <hr style='border: 0; border-top: 1px solid #eee;'>
                    <p style='font-size: 0.9rem; color: #777;'>လေးစားစွာဖြင့်၊<br>University Database Team<br>UCS Monywa</p>
                </div>
            ";

            $mail->send();
            
            // ၈။ အောင်မြင်လျှင် Admin Dashboard သို့ ပြန်သွားရန်
            header("Location: admin.php?status=approved_and_emailed");
            exit();

        } catch (Exception $e) {
            // Database update ဖြစ်သွားသော်လည်း Email ပို့ခြင်း မအောင်မြင်ပါက
            header("Location: admin.php?status=approved_but_mail_failed&error=" . urlencode($mail->ErrorInfo));
            exit();
        }
    } else {
        // ID မှားယွင်းခြင်း သို့မဟုတ် Update လုပ်မရခြင်း
        header("Location: admin.php?status=error_not_found");
        exit();
    }
} else {
    // ID Parameter မပါလာပါက
    header("Location: admin.php");
    exit();
}