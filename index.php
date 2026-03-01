<?php 
require_once 'supabase.php';
$student_count = get_student_count();

// တကယ်လို့ 0 ပဲ ပေါ်နေရင် ဒါကိုသုံးပြီး စစ်ကြည့်ပါ
// var_dump($student_count); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of Computer Studies, Monywa | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables & Global Styles */
        :root {
            --primary: #3ecf8e;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --text-light: #f8fafc;
            --accent: #38bdf8;
        }

        /* Live Pulse Animation */
        .pulse-dot {
            width: 8px; height: 8px; background: var(--primary); 
            border-radius: 50%; display: inline-block; 
            box-shadow: 0 0 10px var(--primary); 
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.7; }
            70% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.7; }
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            line-height: 1.6;
        }

        /* Nav Layout Consistency */
        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 8%; background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .logo { font-weight: 800; font-size: 1.2rem; color: var(--primary); letter-spacing: 1px; }

        /* Hero - Fully Centered */
        .hero {
            height: 100vh; 
            display: flex; 
            flex-direction: column;
            justify-content: center; 
            align-items: center; 
            text-align: center;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.8), var(--bg-dark)),
                        url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=2070&auto=format&fit=crop');
            background-size: cover; 
            background-position: center; 
            padding: 0 20px;
            box-sizing: border-box;
        }

        .hero h1 { 
            font-size: clamp(2rem, 5vw, 3.5rem); 
            margin-bottom: 15px; 
            line-height: 1.2;
        }

        .hero h1 span { color: var(--primary); }

        .hero p { 
            max-width: 750px; 
            color: #94a3b8; 
            font-size: 1.1rem; 
            margin: 0 auto;
        }

        /* Sections */
        .section { padding: 80px 10%; }
        .section-title { color: var(--primary); font-size: 2rem; margin-bottom: 30px; text-align: center; }

        .grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
        .mission-box { 
            background: rgba(62, 207, 142, 0.05); border-left: 4px solid var(--primary);
            padding: 30px; border-radius: 0 12px 12px 0;
            margin-top: 20px;
        }

        .history-card {
            background: var(--card-bg); padding: 25px; border-radius: 12px;
            margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.05);
            transition: 0.3s;
        }
        .history-card:hover { border-color: var(--accent); transform: translateX(10px); }
        .date { color: var(--accent); font-weight: 800; font-size: 1.1rem; display: block; margin-bottom: 5px; }

        /* Buttons */
        .btn { padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: 0.3s; display: inline-block; }
        .btn-primary { background: var(--primary); color: #0f172a; }
        .btn-outline { border: 1px solid var(--primary); color: var(--primary); margin-left: 10px; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }

        footer { text-align: center; padding: 40px; background: #070b14; color: #64748b; font-size: 0.8rem; }

        @media (max-width: 768px) {
            .grid-info { grid-template-columns: 1fr; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
</head>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div id="success-toast" style="position: fixed; top: 20px; right: 20px; background: #3ecf8e; color: #0f172a; padding: 15px 25px; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; animation: slideIn 0.5s ease-out;">
        ✅ စာရင်းသွင်းမှု အောင်မြင်ပါသည်!
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('success-toast').style.display = 'none';
        }, 4000);
    </script>
<?php endif; ?>
<body>

    <?php include 'header.php'; ?> 

    <header class="hero">
        <div style="background: rgba(62, 207, 142, 0.1); border: 1px solid var(--primary); padding: 6px 16px; border-radius: 50px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
            <span class="pulse-dot"></span>
            <span style="font-size: 0.85rem; font-weight: 600; color: var(--primary); letter-spacing: 0.5px;">
    <?php echo number_format($student_count); ?> ACTIVE STUDENTS ENROLLED
</span>
        </div>

        <h1>University of Computer Studies, <span>Monywa</span></h1>
        <p>Developing skilled IT professionals to drive Myanmar’s digital transformation from an agrarian to an industrialized nation.</p>
        
        <div style="margin-top: 35px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="register.php" class="btn btn-primary">Start Registration</a>
            <a href="#about" class="btn btn-outline">Learn More</a>
        </div>
    </header>

    <section class="section" id="about">
        <div class="grid-info">
            <div>
                <h2 style="color: var(--primary);">Nurturing IT Professionals</h2>
                <p>
                    The Government of the Republic of the Union of Myanmar is actively working to transform the nation from an agrarian economy to an industrialized one...
                </p>
                
                <div id="extra-content" style="display: none;">
                    <p>The University of Computer Studies, Monywa, plays a vital role in producing competent young IT professionals. Our aim is to equip students with the knowledge and skills necessary to secure employment in the rapidly evolving IT sector.</p>
                    
                    <p>UCS (Monywa) produces IT professionals and technicians each year, addressing the critical need for skilled human resources not only in the Sagaing Region but also nationwide. To provide access to internationally standardized education, we offer undergraduate B.C.Sc./B.C.Tech degrees, postgraduate Diplomas (D.C.Sc.), and Master’s degrees (M.C.Sc./M.C.Tech and M.I.Sc.).</p>

                    <div class="mission-box">
                        <h3 style="margin-top: 0; color: var(--primary);">Our Mission</h3>
                        <p>“Our university’s mission is to produce skilled software and hardware professionals, innovative researchers, applications and system developers in Information Technology. These graduates will apply their expertise to real-world situations, ensuring Myanmar keeps pace with international standards.”</p>
                    </div>
                </div>

                <button onclick="toggleReadMore()" id="readMoreBtn" class="btn btn-outline" style="margin-top: 20px; cursor: pointer; background: transparent;">
                    Read More
                </button>
            </div>

            <div style="text-align: center;">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=500" 
                     alt="Tech Education" 
                     style="border-radius: 12px; width: 100%; max-width: 400px; border: 1px solid rgba(255,255,255,0.1);">
            </div>
        </div>
    </section>

    <section class="section" style="background: #111b2d;">
        <h2 class="section-title">Background History</h2>
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="history-card">
                <span class="date">September 4, 2000</span>
                Founding of Government Computer College, Monywa at No. 260, Bogyoke Aung San Road.
            </div>
            <div class="history-card">
                <span class="date">September 3, 2001</span>
                Relocated to a new site near Moehnyinthanbokedae Temple.
            </div>
            <div class="history-card">
                <span class="date">February 5, 2006</span>
                Moved to the premises of the former Government Technical College in Myothit Ward.
            </div>
            <div class="history-card">
                <span class="date">January 20, 2007</span>
                Officially upgraded to the <b>University of Computer Studies, Monywa</b>.
            </div>
            <div class="history-card" style="border-left: 4px solid var(--primary);">
                <span class="date">Current Campus</span>
                Located on Monywa-Yargyi-Kalaywa Road, Aung Zeya Myothit, Yinmarbin Township.
            </div>
        </div>
    </section>

    <footer>
        University of Computer Studies, Monywa<br>
        Sagaing Region, Myanmar. &copy; 2026
    </footer>

    <script>
    function toggleReadMore() {
        var extraText = document.getElementById("extra-content");
        var btnText = document.getElementById("readMoreBtn");

        if (extraText.style.display === "none") {
            extraText.style.display = "block";
            btnText.innerHTML = "Show Less";
            extraText.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            extraText.style.display = "none";
            btnText.innerHTML = "Read More";
        }
    }
    </script>

</body>
</html>