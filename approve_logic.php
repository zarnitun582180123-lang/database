<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ၁။ Admin Login စစ်ဆေးခြင်း
if (!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}

require_once 'supabase.php';
// Composer ရဲ့ autoload ကို သုံးခြင်း (PHPMailer ကို အလိုအလျောက် ချိတ်ပေးပါသည်)
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // ၂။ ကျောင်းသားဒေတာကို ဆွဲထုတ်ခြင်း
    $student = get_student_by_id($id); 

    // ၃။ Status ကို 'approved' သို့ Update လုပ်ခြင်း
    if ($student && approve_student($id)) {
        
        $mail = new PHPMailer(true);

        try {
            // ၄။ Gmail SMTP Server Settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'zarnitun582180123@gmail.com'; 
            $mail->Password   = 'kqfvxeenuqjekbbp'; // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // ၅။ ပို့သူနှင့် လက်ခံသူ
            $mail->setFrom('zarnitun582180123@gmail.com', 'UCS Monywa Admin');
            $mail->addAddress($student['email'], $student['name']);

            // ၆။ Email Content
            $mail->isHTML(true);
            $mail->Subject = 'Registration Approved - UCS Monywa';
            
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
            
            header("Location: admin.php?status=approved_and_emailed");
            exit();

        } catch (Exception $e) {
            header("Location: admin.php?status=approved_but_mail_failed&error=" . urlencode($mail->ErrorInfo));
            exit();
        }
    } else {
        header("Location: admin.php?status=error_not_found");
        exit();
    }
} else {
    header("Location: admin.php");
    exit();
}


