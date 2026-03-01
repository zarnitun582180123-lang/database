<?php 
require_once 'supabase.php'; 

// Database မှ ကျောင်းသားစာရင်းကို ဆွဲထုတ်ခြင်း
$students = get_approved_students(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Students List | UCS Monywa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* ... သင့်ရဲ့ မူလ CSS တွေ အကုန်ဒီမှာ ပြန်ထည့်ပါ ... */
        :root {
            --primary: #3ecf8e;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --text-light: #f8fafc;
            --accent: #38bdf8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1100px; /* ID column ပါလာလို့ width နည်းနည်းပိုချဲ့ထားပါတယ် */
            margin: 40px auto;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        h2 { color: var(--primary); margin: 0; font-size: 1.8rem; }

        .search-box { position: relative; }
        .search-box input {
            background: #0f172a;
            border: 1px solid #334155;
            color: white;
            padding: 10px 15px 10px 40px;
            border-radius: 8px;
            width: 250px;
            outline: none;
            transition: 0.3s;
        }
        .search-box input:focus { border-color: var(--accent); width: 300px; }
        .search-box::before {
            content: '🔍';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9rem;
            opacity: 0.5;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th {
            text-align: left;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            border-bottom: 2px solid #334155;
        }
        td { padding: 15px; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.9rem; }
        tr:hover { background: rgba(255,255,255,0.02); }

        .status-badge {
            background: rgba(62, 207, 142, 0.1);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .id-text { color: var(--accent); font-family: monospace; font-weight: 600; }

        .back-btn {
            text-decoration: none;
            color: #94a3b8;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .back-btn:hover { color: white; }
        .no-data { text-align: center; padding: 40px; color: #64748b; }

        @media (max-width: 600px) {
            .search-box input { width: 100%; }
            th, td { padding: 10px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        
        <div class="header-flex">
            <div>
                <h2>Active Students</h2>
                <p style="color: #64748b; margin: 5px 0 0; font-size: 0.85rem;">Official records from University Database.</p>
            </div>
            
            <div class="search-box">
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search students...">
            </div>
        </div>

        <table id="studentTable">
           <thead>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Major</th>
        <th>Registered Date</th> <th>Status</th>
    </tr>
</thead>

<tbody>
    <?php if (!empty($students)): ?>
        <?php foreach ($students as $row): ?>
            <tr>
                <td class="id-text">
                    <?php 
                        // student_id မရှိရင် 'TBA' (To Be Assigned) လို့ ပြမယ်
                        echo htmlspecialchars($row['student_id'] ?? 'TBA'); 
                    ?>
                </td>
                <td style="font-weight: 600; color: white;">
                    <?php echo htmlspecialchars($row['name'] ?? 'Unknown'); ?>
                </td>
                <td>
                    <span style="color: var(--accent);">
                        <?php echo htmlspecialchars($row['major'] ?? 'General'); ?>
                    </span>
                </td>
                <td>
                    <?php 
                        $date = new DateTime($row['created_at']);
                        echo $date->format('d M Y'); 
                    ?>
                </td>
                <td>
                    <?php if (($row['status'] ?? '') === 'approved'): ?>
                        <span class="status-badge">Registered</span>
                    <?php else: ?>
                        <span class="status-badge" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">Pending</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="no-data">အတည်ပြုပြီးသား ကျောင်းသားစာရင်း မရှိသေးပါ။</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>

    <script>
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("studentTable");
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