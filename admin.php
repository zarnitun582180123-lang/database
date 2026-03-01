<?php
// ဘာစာသားမှ မရှိစေရ (Space တောင် မပါရပါ)
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once 'supabase.php';

// ကျောင်းသားစာရင်းကို ဆွဲထုတ်ခြင်း
$students = get_students();

// Status အရ 'pending' ကို ထိပ်ဆုံးပို့ရန် Sort လုပ်ခြင်း
if (!empty($students) && is_array($students)) {
    usort($students, function($a, $b) {
        // pending ကို အရင်ပြမည်
        if ($a['status'] === 'pending' && $b['status'] !== 'pending') return -1;
        if ($a['status'] !== 'pending' && $b['status'] === 'pending') return 1;
        
        // status တူနေရင် ရက်စွဲအလိုက် အသစ်ဆုံးကို ထိပ်မှာထားမည်
        $dateA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
        $dateB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
        return $dateB - $dateA;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | UCS Monywa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3ecf8e;
            --danger: #ef4444;
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
            --accent: #38bdf8;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text); margin: 0; padding: 40px 8%; }
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 20px; flex-wrap: wrap; }
        h2 { color: var(--primary); margin: 0; font-size: 1.8rem; }

        .search-box { position: relative; }
        .search-box input {
            background: #1e293b;
            border: 1px solid #334155;
            color: white;
            padding: 10px 15px 10px 40px;
            border-radius: 8px;
            width: 250px;
            outline: none;
            transition: 0.3s;
        }
        .search-box input:focus { border-color: var(--primary); width: 300px; }
        .search-box::before {
            content: '🔍';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
            opacity: 0.5;
        }

        .table-container { background: var(--card); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: rgba(62, 207, 142, 0.1); color: var(--primary); padding: 18px; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
        td { padding: 15px 18px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; color: #cbd5e1; }
        tr:hover { background: rgba(255,255,255,0.02); }

        .id-text { color: var(--accent); font-family: 'Courier New', monospace; font-weight: bold; }
        .badge { padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        
        .btn-action { text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: 0.2s; padding: 5px 10px; border-radius: 4px; display: inline-block; }
        .btn-approve { color: var(--primary); background: rgba(62, 207, 142, 0.1); }
        .btn-delete { color: var(--danger); background: rgba(239, 68, 68, 0.1); margin-left: 10px; }
        .btn-action:hover { opacity: 0.8; transform: translateY(-1px); }

        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }

        @media (max-width: 768px) { .table-container { overflow-x: auto; } .header-area { flex-direction: column; align-items: flex-start; } }
    </style>
</head>
<body>
    <div class="header-area">
        <h2>Admin Dashboard</h2>
        <div class="search-box">
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by ID, Name or Email...">
        </div>
        <a href="logout.php" 
           style="background: #ef4444; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: bold;"
           onclick="return confirm('Logout ထွက်မှာ သေချာပါသလား?')">
            Logout
        </a>
    </div>

    <div class="table-container">
        <table id="adminTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name & Email</th>
                    <th>Major</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $row): ?>
                    <tr>
                        <td class="id-text"><?php echo htmlspecialchars($row['student_id'] ?? 'TBA'); ?></td>
                        <td>
                            <div style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($row['email']); ?></div>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(56, 189, 248, 0.1); color: #38bdf8;">
                                <?php echo htmlspecialchars($row['courses']['course_name'] ?? 'General'); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <?php if ($row['status'] === 'pending'): ?>
                                    <a href="approve_logic.php?id=<?php echo $row['id']; ?>" 
                                       class="btn-action btn-approve"
                                       onclick="return confirm('ဤကျောင်းသားကို Approve လုပ်မှာ သေချာပါသလား?')">
                                       Approve ✅
                                    </a>
                                <?php else: ?>
                                    <span class="status-pill" style="background: rgba(62, 207, 142, 0.2); color: #3ecf8e;">Approved</span>
                                <?php endif; ?>

                                <a href="delete_logic.php?id=<?php echo $row['id']; ?>" 
                                   class="btn-action btn-delete"
                                   onclick="return confirm('ဤကျောင်းသားကို စာရင်းမှ ဖျက်ပစ်မှာ သေချာပါသလား?')">
                                   Delete 🗑️
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">ဒေတာ မရှိသေးပါ။</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("adminTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let rowText = tr[i].innerText.toUpperCase();
                if (rowText.indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
