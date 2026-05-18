<?php
require_once 'db.php';

// ASYNCHRONOUS FORM PROCESSING GATEWAY
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_inquiry'])) {
    $name    = trim($_POST['visitor_name'] ?? '');
    $email   = trim($_POST['visitor_email'] ?? '');
    $phone   = trim($_POST['visitor_phone'] ?? ''); // Added to match frontend input structural columns
    $subject = trim($_POST['visitor_subject'] ?? '');
    $message = trim($_POST['visitor_message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        // Integrated visitor_phone parameter seamlessly into production table structure
        $ins = $conn->prepare("INSERT INTO contact_requests (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($ins->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $ins->close();
    }
    
    // CRITICAL FIX: Terminate cleanly here so background fetch calls do not double-trigger trackers or view counts
    exit;
}

// STANDARD PAGE LOAD & MANUAL REFRESH TRACKING MATRIX
// This will now execute strictly when users access, load, or refresh the frontend layout manually.
trackLiveVisitor($conn); 

$feedback_status = ""; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peter W. Mayende</title>
    <link rel="icon" type="image/png" href="https://img.icons8.com/neon/96/shield.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
       body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin: 0;
    padding: 0;
}

.heading-font {
    font-family: 'Space Grotesk', sans-serif;
}

/* Fixed Background Image Layer - Crystal Clear and High Res */
.fixed-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* Upgraded to 1920x1080 for crisp rendering on desktops */
    background-image: url('https://picsum.photos/id/1031/1920/1080');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: -2;
}

/* 
   REMOVED full-screen .bg-overlay blur to keep the background completely clear.
   INSTEAD, use this class below on your content CARDS/CONTAINERS 
   so text is readable over the clear background.
*/
.glass-card {
    background: rgba(255, 255, 255, 0.15); /* Tinted transparent layer */
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    padding: 20px;
}
        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.08);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 24px 0 rgba(31, 38, 135, 0.04);
        }
        /* Custom Smooth Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.2);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="text-slate-800 antialiased selection:bg-cyan-200 selection:text-slate-900 overflow-x-hidden">

    <!-- Background Setup -->
    <div class="fixed-bg"></div>
    <div class="bg-overlay"></div>

    <!-- Navigation Header -->
    <header class="fixed top-0 left-0 w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#hero" class="heading-font text-2xl font-extrabold tracking-tight text-slate-900">
                PETER  MAYENDE <span class="text-cyan-600">.</span>
            </a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold tracking-wide text-slate-600">
                <a href="#hero" class="hover:text-cyan-600 transition-colors">Home</a>
                <a href="#profile" class="hover:text-cyan-600 transition-colors">Profile</a>
                <a href="#biography" class="hover:text-cyan-600 transition-colors">Biography</a>
                <a href="#education" class="hover:text-cyan-600 transition-colors">Education</a>
           
                <a href="#contact" class="hover:text-cyan-600 transition-colors">Contact</a>
                <a href="admin/index.php" class="hover:text-cyan-600 transition-colors">Administrator</a>
            </nav>

            <div class="hidden md:block">
                <a href="#contact" class="px-5 py-2.5 rounded-xl glass-card text-slate-900 font-bold text-sm hover:bg-white/80 transition-all duration-300 shadow-sm">
                    Get In Touch
                </a>
            </div>
            
            <!-- Mobile Menu Toggle Button -->
            <button id="menu-btn" class="md:hidden text-slate-800 hover:text-cyan-600 focus:outline-none">
                <i class="fa-solid fa-bars-staggered text-2xl"></i>
            </button>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden md:hidden glass-nav w-full absolute top-20 left-0 px-6 py-6 border-b border-white/20 flex flex-col space-y-4">
            <a href="#hero" class="mobile-link text-slate-700 hover:text-cyan-600 font-medium py-2">Home</a>
            <a href="#profile" class="mobile-link text-slate-700 hover:text-cyan-600 font-medium py-2">Profile</a>
            <a href="#biography" class="mobile-link text-slate-700 hover:text-cyan-600 font-medium py-2">Biography</a>
            <a href="#education" class="mobile-link text-slate-700 hover:text-cyan-600 font-medium py-2">Education</a>
        
            <a href="#contact" class="mobile-link text-slate-700 hover:text-cyan-600 font-medium py-2">Contact</a>
            <a href="admin/index.php" class="hover:text-cyan-600 transition-colors">Administrator</a>
        </div>
    </header>

    <main class="relative z-10 pt-20">
<!-- =========================================================================
     PREMIUM TRANSPARENT PYTHON LIVE-CODING HERO SECTION (SELF-CONTAINED)
     ========================================================================= -->
<section id="hero" class="relative min-h-[90vh] flex items-center justify-center py-20 px-6" style="display: flex; align-items: center; justify-content: center; overflow: hidden; background: transparent !important;">
    
    <!-- Cybernetic Ambient Background Grid & Subtle Glows -->
    <div class="cyber-bg-engine" style="position: absolute; inset: 0; pointer-events: none; z-index: 1;">
        <div class="cyber-grid"></div>
        <div class="neon-orb orb-1"></div>
        <div class="neon-orb orb-2"></div>
    </div>

    <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10" style="display: grid; width: 100%;">
        
        <!-- LEFT COLUMN: INTERACTIVE PYTHON LIVE CODING SIMULATOR -->
        <div class="lg:col-span-7 space-y-6" style="display: flex; flex-direction: column; opacity: 0; animation: heroSectionFadeIn 0.4s ease-out forwards;">
            
            <!-- Context Header Badge -->
            <div style="margin-bottom: 10px;">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-bold uppercase tracking-widest text-cyan-400">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span> Peter w. Mayende
                </span>
            </div>

            <!-- Terminal Window Interface -->
            <div class="terminal-container">
                <!-- Terminal Header Bar -->
                <div class="terminal-header">
                    <div class="terminal-buttons">
                        <span class="t-btn t-close"></span>
                        <span class="t-btn t-minimize"></span>
                        <span class="t-btn t-expand"></span>
                    </div>
                    <div class="terminal-title">app.py — Mayende-Core</div>
                </div>

                <!-- Terminal Live Content Body -->
                <div class="terminal-body">
                    <div class="code-line"><span class="c-keyword">def</span> <span class="c-func">initialize_portfolio</span>():</div>
                    <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;developer = <span class="c-str">"Peter Mayende"</span></div>
                    <div class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;role = <span class="c-str">"Full Stack Developer"</span></div>
                    
                    <!-- Simulating Typing Line -->
                    <div class="terminal-input-row">
                        <span class="terminal-prompt">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <span class="typing-text-animation"></span>
                    </div>

                    <!-- Live Dynamic Output Reveal Box -->
                    <div class="terminal-output-box">
                        <div class="output-row log-success">
                            <i class="fab fa-python"></i> >>> python3 app.py
                        </div>
                        <div class="output-row log-main">
                            <span class="pulse-bracket">>>> </span><span class="glow-text">WELCOME TO PETER MAYENDE WEBSITE</span>
                        </div>
                        <div class="output-row log-sub">
                            Process finished with exit code 0
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Call To Action Elements -->
         
            
            <div class="flex flex-wrap items-center gap-4 pt-4" style="display: flex; gap: 16px; flex-wrap: wrap;">
          
            </div>
        </div>

        <!-- RIGHT COLUMN: CYBER ROTATING BADGE FRAMEWORK WITH PROFILE IMAGE -->
        <div class="lg:col-span-5 flex justify-center relative" style="display: flex; justify-content: center; position: relative;">
            <div class="hero-circular-badge-wrapper" style="display: flex; justify-content: center; align-items: center; position: relative;">
                
                <!-- Infinite Rotating SVG Path -->
                <svg class="hero-rotating-text-svg" viewBox="0 0 100 100">
                    <defs>
                        <path id="heroTextCirclePath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" />
                    </defs>
                    <text class="hero-badge-text">
                        <textPath href="#heroTextCirclePath" startOffset="0%">
                            PETER MAYENDE • FULL STACK DEVELOPER • PETER MAYENDE • FULL STACK DEVELOPER •
                        </textPath>
                    </text>
                </svg>

                <!-- Central Glass Core holding profile picture 70.jpg -->
                <div class="hero-badge-core-hub" style="display: flex; justify-content: center; align-items: center; overflow: hidden; border-radius: 50%;">
                    <img src="70.jpg" alt="Peter Mayende Portrait Illustration" class="hero-avatar-img">
                </div>

            </div>
        </div>

    </div>

    <!-- FontAwesome Core Icons Hook -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scoped Style Engine protecting other layout DOM elements -->
    <style>
        /* Scoped Vars */
        :root {
            --term-bg: rgba(10, 16, 32, 0.65);
            --term-border: rgba(0, 242, 254, 0.2);
            --neon-cyan: #00f2fe;
            --matrix-blue: #4facfe;
            --python-blue: #306998;
            --python-yellow: #ffd43b;
        }

        /* Ambient Dynamic Background Framework */
        .cyber-grid {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(0, 242, 254, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 242, 254, 0.02) 1px, transparent 1px);
            background-size: 35px 35px;
            background-position: center center;
        }

        .neon-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(130px);
            opacity: 0.12;
            pointer-events: none;
        }
        .orb-1 { top: 10%; left: 15%; width: 350px; height: 350px; background: var(--neon-cyan); }
        .orb-2 { bottom: 10%; right: 10%; width: 400px; height: 400px; background: var(--matrix-blue); }

        /* Premium Terminal Window Panel Box */
        .terminal-container {
            width: 100%;
            background: var(--term-bg);
            border: 1px solid var(--term-border);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            overflow: hidden;
            font-family: 'Courier New', Courier, monospace;
        }

        .terminal-header {
            background: rgba(7, 11, 25, 0.7);
            padding: 12px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            position: relative;
        }

        .terminal-buttons {
            display: flex;
            gap: 7px;
        }

        .t-btn {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            display: inline-block;
        }
        .t-close { background: #ff5f56; }
        .t-minimize { background: #ffbd2e; }
        .t-expand { background: #27c93f; }

        .terminal-title {
            color: #64748b;
            font-size: 0.8rem;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .terminal-body {
            padding: 24px;
            color: #e2e8f0;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Python Style Syntax Highlighter Tokens */
        .code-line { margin-bottom: 6px; opacity: 0.95; }
        .c-keyword { color: #ff79c6; font-weight: bold; }
        .c-func { color: #50fa7b; }
        .c-str { color: #f1fa8c; }

        /* Dynamic Live Writing Pipeline */
        .terminal-input-row {
            display: flex;
            align-items: center;
            color: var(--neon-cyan);
            margin: 6px 0 16px 0;
            font-weight: bold;
        }

        .typing-text-animation::before {
            content: "";
            animation: simulatePythonTyping 6s linear infinite;
        }

        .typing-text-animation::after {
            content: "|";
            animation: terminalCursorPulse 0.8s infinite;
            margin-left: 2px;
            color: var(--neon-cyan);
        }

        /* Live Terminal Process Output Container */
        .terminal-output-box {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            padding: 16px;
            margin-top: 12px;
            border-left: 3px solid var(--python-yellow);
            opacity: 0;
            animation: triggerOutputReveal 6s step-end infinite;
        }

        .output-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        .log-success { color: var(--python-blue); font-size: 0.85rem; margin-bottom: 6px; font-weight: bold;}
        .log-success i { color: var(--python-yellow); }
        .log-sub { color: #64748b; font-size: 0.8rem; margin-top: 6px;}

        .log-main {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 4px 0;
        }
        .log-main .glow-text {
            color: #ffffff;
            text-shadow: 0 0 8px rgba(0, 242, 254, 0.8), 0 0 20px rgba(0, 242, 254, 0.4);
        }
        .log-main .pulse-bracket {
            color: var(--python-yellow);
        }

        /* Interactive Navigation Buttons Layout styles */
        .hero-btn {
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--neon-cyan), var(--matrix-blue));
            color: #070b19;
            box-shadow: 0 4px 20px rgba(0, 242, 254, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 26px rgba(0, 242, 254, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--neon-cyan);
            transform: translateY(-2px);
        }

        /* RIGHT SIDE CIRCULAR RADIAL ROTATING ENGINE CONTAINER */
        .hero-circular-badge-wrapper {
            width: 320px;
            height: 320px;
            opacity: 0;
            animation: heroSectionFadeIn 0.5s ease-out 0.15s forwards;
        }

        .hero-rotating-text-svg {
            position: absolute;
            width: 100%;
            height: 100%;
            animation: infiniteCircularRotation 22s linear infinite;
        }

        .hero-badge-text {
            font-size: 7.3px;
            font-weight: 900;
            letter-spacing: 2.1px;
            fill: #ffffff;
            text-transform: uppercase;
            text-shadow: 0 0 5px rgba(0, 242, 254, 0.7);
        }

        .hero-badge-core-hub {
            width: 180px;
            height: 180px;
            border: 2px solid var(--neon-cyan);
            box-shadow: 0 0 30px rgba(0, 242, 254, 0.35), inset 0 0 20px rgba(0, 242, 254, 0.2);
            z-index: 2;
        }

        .hero-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        .hero-circular-badge-wrapper:hover .hero-avatar-img {
            transform: scale(1.06);
        }

        /* PYTHON ANIMATION TIMELINE SCHEDULER KEYFRAMES */
        @keyframes simulatePythonTyping {
            0%, 100% { content: ""; }
            5% { content: "p"; }
            10% { content: "pr"; }
            15% { content: "pri"; }
            20% { content: "prin"; }
            25% { content: "print"; }
            30% { content: "print("; }
            35% { content: "print(f"; }
            40% { content: "print(f\"W"; }
            45% { content: "print(f\"Wel"; }
            50% { content: "print(f\"Welco"; }
            55% { content: "print(f\"Welcome"; }
            60% { content: "print(f\"Welcome d"; }
            65% { content: "print(f\"Welcome de"; }
            70% { content: "print(f\"Welcome dev"; }
            75% { content: "print(f\"Welcome dev\")"; }
            85%, 95% { content: "print(f\"Welcome dev\")"; }
        }

        @keyframes triggerOutputReveal {
            0%, 79%, 100% { opacity: 0; transform: translateY(4px); }
            80%, 95% { opacity: 1; transform: translateY(0); }
        }

        @keyframes terminalCursorPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        @keyframes infiniteCircularRotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes heroSectionFadeIn {
            0% { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ADAPTIVE MOBILE BREAKPOINT MAPPINGS */
        @media (max-width: 1023px) {
            #hero { padding-top: 140px; padding-bottom: 60px; }
            .hero-circular-badge-wrapper { width: 260px; height: 260px; margin-top: 20px;}
            .hero-badge-core-hub { width: 140px; height: 140px; }
            .hero-badge-text { font-size: 7.5px; }
            .log-main { font-size: 0.85rem; }
        }
    </style>
</section>
<!-- =========================================================================
     PREMIUM TRANSPARENT CYBER-OPS PROFILE SECTION (BORDERLESS EDITION)
     ========================================================================= -->
<div class="profile-deep-dive-section" style="display: flex; justify-content: center; align-items: center; min-height: 250px; background: transparent !important;">
    <div class="profile-panel-container" style="display: grid; opacity: 0; animation: profileFadeIn 0.3s ease-out forwards; background: transparent !important;">
        
        <!-- Left Column: Circular Rotating Text Badge & Profile Image -->
        <div class="profile-badge-column" style="display: flex; justify-content: center; align-items: center;">
            <div class="circular-badge-wrapper" style="display: flex; justify-content: center; align-items: center; position: relative;">
                <!-- Infinite Rotating SVG Engine -->
                <svg class="rotating-text-svg" viewBox="0 0 100 100">
                    <defs>
                        <!-- Define the exact circular track for the text mapping -->
                        <path id="textCirclePath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" />
                    </defs>
                    <text class="badge-text">
                        <textPath href="#textCirclePath" startOffset="0%">
                            WHO IS PETER MAYENDE • WHO IS PETER MAYENDE • WHO IS PETER MAYENDE • 
                        </textPath>
                    </text>
                </svg>
                <!-- Core Hub Image Container -->
                <div class="badge-core-hub" style="display: flex; justify-content: center; align-items: center; overflow: hidden; background: rgba(7, 11, 25, 0.9);">
                    <img src="speed.png" alt="Peter Mayende" class="profile-avatar-img">
                </div>
            </div>
        </div>

        <!-- Right Column: Context Information Architecture -->
        <div class="profile-content-column" style="display: flex; flex-direction: column; justify-content: center;">
            <h2 class="profile-title">Who is Peter?</h2>
            <div class="profile-description-block" style="display: flex; flex-direction: column;">
                <p class="highlight-text">
                    Automated software ecosystem engineer and architect.
                </p>
                <p class="body-text">
                    Building secure, high-performance management infrastructures, payment APIs and automated callback engines from complex business logic.
                </p>
            </div>
            
            <!-- Technical Architecture Meta Tags -->
            <div class="architecture-tags" style="display: flex; flex-wrap: wrap;">
                <span class="tech-tag"><i class="fas fa-layer-group"></i> Full-Stack</span>
                <span class="tech-tag"><i class="fas fa-cogs"></i> Automation</span>
                <span class="tech-tag"><i class="fas fa-network-wired"></i> System Architect</span>
            </div>
        </div>

    </div>

    <!-- FontAwesome Hooks -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Isolated Section Styling Layout -->
    <style>
        :root {
            --cyber-neon: #00f2fe;
            --matrix-blue: #4facfe;
            --pure-white: #ffffff;
            --ultra-readable-slate: #f8fafc;
        }

        /* Scope wrapper protecting outside global document trees */
        .profile-deep-dive-section {
            width: 100%;
            padding: 60px 20px;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Transparent Borderless Grid Container with Enhanced Text Visibility shadows */
        .profile-panel-container {
            max-width: 850px;
            width: 100%;
            border: none !important; /* Borders permanently dropped */
            border-radius: 20px;
            padding: 40px;
            box-sizing: border-box;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            align-items: center;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6),
                        0 0 30px rgba(0, 242, 254, 0.08);
        }

        /* Left Alignment Column Engine */
        .profile-badge-column {
            position: relative;
        }

        /* Circular Track & Wrapper Box */
        .circular-badge-wrapper {
            width: 240px;
            height: 240px;
        }

        /* Dynamic SVG Map rotation logic running forever */
        .rotating-text-svg {
            position: absolute;
            width: 100%;
            height: 100%;
            animation: infiniteCircularRotation 22s linear infinite;
        }

        .badge-text {
            font-size: 7.2px;
            font-weight: 900;
            letter-spacing: 1.8px;
            fill: var(--pure-white);
            text-transform: uppercase;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9), 0 0 5px rgba(0, 242, 254, 0.8);
        }

        /* Central Core Hub Sphere */
        .badge-core-hub {
            width: 130px;
            height: 130px;
            border: 2px solid var(--cyber-neon);
            border-radius: 50%;
            box-shadow: 0 0 25px rgba(0, 242, 254, 0.4),
                        inset 0 0 15px rgba(0, 242, 254, 0.2);
            z-index: 2;
        }

        /* Embedded Profile Avatar Image Component */
        .profile-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .circular-badge-wrapper:hover .profile-avatar-img {
            transform: scale(1.08);
        }

        /* High Contrast Typography Engine */
        .profile-title {
            font-size: 2.2rem;
            margin: 0 0 20px 0;
            font-weight: 800;
            color: var(--pure-white);
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
        }

        .profile-title span {
            background: linear-gradient(135deg, var(--cyber-neon), var(--matrix-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .profile-description-block {
            gap: 14px;
        }

        .highlight-text {
            font-size: 1.18rem;
            font-weight: 700;
            line-height: 1.5;
            color: var(--cyber-neon);
            margin: 0;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.7);
        }

        .body-text {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--ultra-readable-slate);
            margin: 0;
            font-weight: 500;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.8);
        }

        /* Technical Metadata Micro-Badges */
        .architecture-tags {
            gap: 12px;
            margin-top: 25px;
        }

        .tech-tag {
            background: rgba(7, 11, 25, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15); /* Keep small accent border on individual tags for architecture definition */
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            color: var(--pure-white);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .tech-tag i {
            color: var(--cyber-neon);
            font-size: 0.8rem;
        }

        .tech-tag:hover {
            border-color: var(--cyber-neon);
            background: rgba(0, 242, 254, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 242, 254, 0.2);
        }

        /* Anti-Flash Instant Smooth Reveal */
        @keyframes profileFadeIn {
            0% {
                opacity: 0;
                transform: translateY(8px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Infinite Animation Axis Rule */
        @keyframes infiniteCircularRotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Seamless Adaptive Media Queries */
        @media (max-width: 768px) {
            .profile-panel-container {
                grid-template-columns: 100% !important;
                gap: 30px;
                padding: 30px 20px;
            }
            .profile-content-column {
                text-align: center;
                align-items: center;
            }
            .architecture-tags {
                justify-content: center;
            }
            .profile-title {
                font-size: 1.9rem;
            }
        }
    </style>
</div>
<!-- CENTERING WRAPPER (Keeps the card perfectly centered in its parent container) -->
<div class="flex items-center justify-center w-full p-4 bg-transparent">
    
    <!-- AUTOMATIC AGE COUNTER CARD (BORDERLESS & TRANSPARENT EDITION) -->
    <div class="w-full max-w-sm p-6 bg-transparent flex flex-col items-center justify-center text-center shadow-none border-none">
        
 
        
        <!-- Profile Age Ring -->
        <div class="relative w-28 h-28 rounded-full bg-gradient-to-tr from-cyan-500 via-blue-500 to-emerald-400 p-[3px] shadow-xl flex items-center justify-center mb-3">
            <!-- Inner dark/transparent core optimized for neon readability -->
            <div class="w-full h-full rounded-full bg-slate-950/90 flex flex-col items-center justify-center p-2 backdrop-blur-sm">
                
                <!-- Shifting Color Age Number -->
                <span id="autoAgeDisplay" class="text-3xl font-black tracking-tight animate-color-shift leading-none drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]">
                    --
                </span>
                
                <!-- Label below number -->
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">
                    Years Old
                </span>
                
            </div>
        </div>

    </div>
</div>

<script>
    (function() {
        function calculateLiveAge(birthDateString) {
            const birthDate = new Date(birthDateString);
            const today = new Date();
            
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDifference = today.getMonth() - birthDate.getMonth();
            
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        const ageContainer = document.getElementById('autoAgeDisplay');
        if (ageContainer) {
            // Calculates automatically based on October 26, 2002
            ageContainer.textContent = calculateLiveAge('2002-10-26');
        }
    })();
</script>

<style>
    /* Continuous smooth text color shifting animation */
    @keyframes colorShift {
        0% { color: #06b6d4; }   /* Cyan-500 */
        33% { color: #3b82f6; }  /* Blue-500 */
        66% { color: #10b981; }  /* Emerald-500 */
        100% { color: #06b6d4; } /* Back to Cyan */
    }

    .animate-color-shift {
        animation: colorShift 6s ease-in-out infinite;
    }
</style>
<!-- SECTION 3: DETAILED BIOGRAPHY (BORDERLESS, TRANSPARENT & ULTRACLEAR TYPOGRAPHY) -->
<section id="biography" class="py-20 px-4 sm:px-6 scroll-mt-20 bg-transparent w-full overflow-hidden">
    <div class="max-w-7xl mx-auto bg-transparent rounded-none p-0 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
            
            <!-- LEFT COLUMN: PROTECTED IMAGE ARCHITECTURE -->
            <div class="lg:col-span-5 w-full flex justify-center lg:justify-start">
                <div class="w-full max-w-md lg:max-w-none min-h-[380px] sm:min-h-[520px] rounded-2xl overflow-hidden bg-transparent relative flex flex-col justify-between group select-none">
                    
                    <!-- Profile Image Card with Strict Copy/Save Protections -->
                    <div class="absolute inset-0 rounded-2xl overflow-hidden dynamic-img-protection shadow-2xl">
                        <img src="mp.jpeg" 
                             alt="Peter Mayende" 
                             class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105" 
                             style="pointer-events: none; -webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;"
                             oncontextmenu="return false;"
                             ondragstart="return false;" />
                    </div>

                </div>
            </div>
            
            <!-- RIGHT COLUMN: HIGH-CONTRAST TEXT NARRATIVE -->
            <div class="lg:col-span-7 w-full flex flex-col justify-center space-y-6 px-2 sm:px-4">
                
                <!-- Main Header Group -->
                <div class="space-y-3 text-center lg:text-left">
                    <span class="text-cyan-400 font-black tracking-widest uppercase text-xs heading-font px-4 py-1.5 rounded-full bg-slate-900/60 border border-cyan-500/30 inline-block backdrop-blur-sm shadow-md">
                        The Narrative Journey
                    </span>
                    <h2 class="heading-font text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">
                        Background Summary
                    </h2>
                    <div class="w-16 h-1.5 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full mx-auto lg:mx-0 shadow-lg"></div>
                </div>

                <!-- Core Overview Paragraphs (High Contrast Readability Engine) -->
                <div class="space-y-6 text-slate-200 leading-relaxed font-medium text-base sm:text-lg text-justify [text-shadow:0_2px_5px_rgba(0,0,0,0.95)]">
                    <p class="relative pl-0 lg:pl-4 border-l-0 lg:border-l-2 lg:border-cyan-500/40">
                        Born on <strong class="text-cyan-300 font-extrabold">October 26, 2002</strong>, in the Western Region of Kenya, specifically within the <strong class="text-white font-bold">Kabuchai Constituency of Bungoma County</strong>, Peter Mayende discovered his affinity for structured logic and computing systems at an early age. His foundational education was anchored at <strong class="text-white font-bold">Nairobi FYM</strong>, where he completed his KCPE in <strong class="text-white font-bold">2016</strong>. Propelled by an innate passion for technology, he joined <strong class="text-white font-bold">St. Joseph Nalondo Boys High School</strong> in January 2017, completing his secondary education in December 2020 and scoring a direct university entry grade.
                    </p>
                    <p class="relative pl-0 lg:pl-4 border-l-0 lg:border-l-2 lg:border-blue-500/40">
                        In pursuit of elite engineering paradigms, Peter transitioned to higher education at <strong class="text-cyan-300 font-extrabold">Kisii University</strong> on September 20, 2021. There, he pursued a rigorous dual-discipline <strong class="text-white font-bold">Bachelor of Science in Mathematics with Computer Science</strong>. Demonstrating exceptional technical execution, he completed his studies in April 2025 and officially graduated with his Bachelor's degree, specializing in <strong class="text-white font-bold">Computer Science</strong>, on December 17, 2025.
                    </p>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- IMAGE PROTECTION SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const protectedContainer = document.querySelector('.dynamic-img-protection');
        if (protectedContainer) {
            // Prevent right-click context menus on the container area
            protectedContainer.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Block image drag-and-drop operations
            const img = protectedContainer.querySelector('img');
            if (img) {
                img.addEventListener('dragstart', function(e) {
                    e.preventDefault();
                    return false;
                });
            }
        }
    });
</script>
<!-- SECTION 3: WORK EXPERIENCE & TECHNICAL SKILLS -->
<section id="experience" class="py-24 px-4 sm:px-6 max-w-7xl mx-auto scroll-mt-20 bg-transparent">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start bg-transparent">
        
        <!-- Left Side: Work Experience Timeline -->
        <div class="lg:col-span-7 space-y-8 bg-transparent">
            <div class="p-0 bg-transparent border-none shadow-none backdrop-blur-none transition-colors duration-300">
                <h2 class="heading-font text-3xl sm:text-4xl font-black mb-2 text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">Professional Experience</h2>
                <div class="w-16 h-1.5 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full mb-4 shadow-md"></div>
                <p class="text-white text-base sm:text-lg font-bold leading-relaxed [text-shadow:0_2px_5px_rgba(0,0,0,0.9)]">Operational trajectory within structural technical environments and systems deployment roles.</p>
            </div>

            <div class="relative pl-10 before:absolute before:inset-y-0 before:left-0 before:w-1 before:bg-gradient-to-b before:from-cyan-400 before:to-blue-500 bg-transparent">
                <!-- Experience Item -->
                <div class="relative group bg-transparent">
                    <div class="absolute -left-12 top-1.5 w-5 h-5 rounded-full bg-slate-950 border-4 border-cyan-400 z-10 transition-colors group-hover:bg-cyan-400 shadow-md"></div>
                    <div class="bg-transparent border-none p-0 rounded-none shadow-none hover:shadow-none transition-all duration-300 space-y-4">
                        <span class="inline-block text-xs font-black bg-slate-900/90 text-cyan-300 px-3 py-1 rounded-md uppercase tracking-widest border border-cyan-400/30 shadow-sm">May 2024 — August 2024</span>
                        <h4 class="heading-font text-2xl font-black text-white tracking-wide [text-shadow:0_2px_8px_rgba(0,0,0,0.95)]">IT Assistant</h4>
                        <span class="text-base text-cyan-200 font-extrabold block [text-shadow:0_2px_4px_rgba(0,0,0,0.9)]">Kisii County Ajiry Training Center</span>
                        
                        <ul class="space-y-4 text-white text-base sm:text-lg font-semibold leading-relaxed [text-shadow:0_2px_5px_rgba(0,0,0,0.95)]">
                            <li class="flex gap-3 items-start">
                                <i class="fa-solid fa-circle-check text-cyan-400 mt-1 flex-shrink-0 text-base shadow-sm"></i>
                                <span>Managed and maintained local network architectures, providing systematic troubleshooting for routing nodes and workspace client configurations.</span>
                            </li>
                            <li class="flex gap-3 items-start">
                                <i class="fa-solid fa-circle-check text-cyan-400 mt-1 flex-shrink-0 text-base shadow-sm"></i>
                                <span>Assisted in staging technical training infrastructure, managing user credentials, and validating hardware deployment compliance rules.</span>
                            </li>
                            <li class="flex gap-3 items-start">
                                <i class="fa-solid fa-circle-check text-cyan-400 mt-1 flex-shrink-0 text-base shadow-sm"></i>
                                <span>Delivered continuous technical support across software ecosystems, optimizing environment performance loops for student and staff interfaces.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Key Technical Skills Matrix -->
        <div class="lg:col-span-5 space-y-8 bg-transparent">
            <div class="p-0 bg-transparent border-none shadow-none backdrop-blur-none transition-colors duration-300">
                <h2 class="heading-font text-3xl sm:text-4xl font-black mb-2 text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">Technical Skills</h2>
                <div class="w-16 h-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full mb-4 shadow-md"></div>
                <p class="text-white text-base sm:text-lg font-bold leading-relaxed [text-shadow:0_2px_5px_rgba(0,0,0,0.9)]">Core technical stack matrices and system implementation frameworks.</p>
            </div>

            <div class="bg-transparent border-none p-0 rounded-none shadow-none space-y-6">
                <!-- Skill Group 1 -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center font-black text-white text-base sm:text-lg tracking-wide [text-shadow:0_2px_4px_rgba(0,0,0,0.9)]">
                        <span class="flex items-center gap-2.5"><i class="fa-solid fa-code text-cyan-400 text-xl"></i> Full-Stack Web Development</span>
                        <span class="text-xs font-black bg-slate-900/80 text-cyan-300 px-2.5 py-0.5 rounded border border-cyan-500/30">92%</span>
                    </div>
                    <div class="w-full bg-slate-900/60 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700 shadow-inner">
                        <div class="bg-gradient-to-r from-cyan-400 to-blue-500 h-full rounded-full shadow-md" style="width: 92%;"></div>
                    </div>
                    <p class="text-sm text-cyan-200 font-extrabold tracking-wide [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">PHP, MySQL, Tailwind CSS, Bootstrap, Responsive UI Design</p>
                </div>

                <!-- Skill Group 2 -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center font-black text-white text-base sm:text-lg tracking-wide [text-shadow:0_2px_4px_rgba(0,0,0,0.9)]">
                        <span class="flex items-center gap-2.5"><i class="fa-solid fa-network-wired text-blue-400 text-xl"></i> Network Engineering & Admin</span>
                        <span class="text-xs font-black bg-slate-900/80 text-blue-300 px-2.5 py-0.5 rounded border border-blue-500/30">88%</span>
                    </div>
                    <div class="w-full bg-slate-900/60 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700 shadow-inner">
                        <div class="bg-gradient-to-r from-blue-400 to-indigo-500 h-full rounded-full shadow-md" style="width: 88%;"></div>
                    </div>
                    <p class="text-sm text-blue-200 font-extrabold tracking-wide [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">MikroTik RouterOS, PPPoE Servers, Winbox, Port Forwarding, VirtualBox</p>
                </div>

                <!-- Skill Group 3 -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center font-black text-white text-base sm:text-lg tracking-wide [text-shadow:0_2px_4px_rgba(0,0,0,0.9)]">
                        <span class="flex items-center gap-2.5"><i class="fa-solid fa-credit-card text-indigo-400 text-xl"></i> API Integration & Payments</span>
                        <span class="text-xs font-black bg-slate-900/80 text-indigo-300 px-2.5 py-0.5 rounded border border-indigo-500/30">85%</span>
                    </div>
                    <div class="w-full bg-slate-900/60 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700 shadow-inner">
                        <div class="bg-gradient-to-r from-indigo-400 to-purple-500 h-full rounded-full shadow-md" style="width: 85%;"></div>
                    </div>
                    <p class="text-sm text-indigo-200 font-extrabold tracking-wide [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">M-Pesa STK Push Integration, PayHero API, RESTful Web Services</p>
                </div>

                <!-- Skill Group 4 -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center font-black text-white text-base sm:text-lg tracking-wide [text-shadow:0_2px_4px_rgba(0,0,0,0.9)]">
                        <span class="flex items-center gap-2.5"><i class="fa-solid fa-server text-emerald-400 text-xl"></i> Security & Architecture</span>
                        <span class="text-xs font-black bg-slate-900/80 text-emerald-300 px-2.5 py-0.5 rounded border border-emerald-500/30">78%</span>
                    </div>
                    <div class="w-full bg-slate-900/60 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700 shadow-inner">
                        <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-full rounded-full shadow-md" style="width: 78%;"></div>
                    </div>
                    <p class="text-sm text-emerald-200 font-extrabold tracking-wide [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">Authentication Portals, Access Control Constraints, Database Normalization</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 4: PROJECTS BLOCK WITH BLINKING CALL-TO-ACTIONS -->
<section id="projects" class="py-24 px-4 sm:px-6 max-w-7xl mx-auto scroll-mt-20 bg-transparent">
    
    <!-- Section Header Block -->
    <div class="text-center max-w-3xl mx-auto mb-16 p-0 bg-transparent border-none shadow-none backdrop-blur-none transition-colors duration-300">
        <h2 class="heading-font text-3xl sm:text-4xl lg:text-5xl font-black mb-4 text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">Strategic Projects</h2>
        <div class="w-16 h-1.5 bg-gradient-to-r from-emerald-400 to-teal-500 mx-auto rounded-full shadow-md"></div>
        <p class="text-white mt-5 text-base sm:text-lg font-bold leading-relaxed [text-shadow:0_2px_5px_rgba(0,0,0,0.9)]">Production deployments showcasing complex full-stack execution, secure interfaces, and local ecosystem integrations.</p>
    </div>

    <!-- Blinking Navigation Hub Node -->
    <div class="max-w-4xl mx-auto mb-16 p-0 bg-transparent border-none shadow-none flex flex-col sm:flex-row gap-6 justify-center items-center">
        <!-- Live Blinking Button 1 -->
        <a href="https://speedprinting01.infinityfree.me/" class="relative inline-flex items-center justify-center px-8 py-4 font-black text-white tracking-widest rounded-xl bg-cyan-600 hover:bg-cyan-700 shadow-xl hover:shadow-cyan-500/40 transition-all duration-300 transform hover:-translate-y-0.5 overflow-hidden group w-full sm:w-auto text-center text-sm sm:text-base uppercase">
            <span class="absolute inset-0 w-full h-full bg-cyan-400 opacity-0 group-hover:opacity-10 transition-opacity"></span>
            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-90"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-cyan-300"></span>
            </span>
            <i class="fa-solid fa-layer-group mr-2.5 text-base"></i>My Websites
        </a>

        <!-- Live Blinking Button 2 -->
        <a href="https://speedportfolio.infinityfreeapp.com/" class="relative inline-flex items-center justify-center px-8 py-4 font-black text-white tracking-widest rounded-xl bg-indigo-600 hover:bg-indigo-700 shadow-xl hover:shadow-indigo-500/40 transition-all duration-300 transform hover:-translate-y-0.5 overflow-hidden group w-full sm:w-auto text-center text-sm sm:text-base uppercase">
            <span class="absolute inset-0 w-full h-full bg-indigo-400 opacity-0 group-hover:opacity-10 transition-opacity"></span>
            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-90"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-indigo-300"></span>
            </span>
            <i class="fa-solid fa-briefcase mr-2.5 text-base"></i>My Portfolio
        </a>

   <a href="https://savenow254.my-board.org/" class="relative inline-flex items-center justify-center px-8 py-4 font-black text-white tracking-widest rounded-xl bg-emerald-600 hover:bg-emerald-700 shadow-xl hover:shadow-emerald-500/40 transition-all duration-300 transform hover:-translate-y-0.5 overflow-hidden group w-full sm:w-auto text-center text-sm sm:text-base uppercase">
    <span class="absolute inset-0 w-full h-full bg-emerald-400 opacity-0 group-hover:opacity-10 transition-opacity"></span>
    <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-90"></span>
        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-300"></span>
    </span>
       <i class="fa-solid fa-briefcase mr-2.5 text-base"></i>LIVE SUPPORT
</a>
    </div>

    <!-- Project Presentation Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto bg-transparent">
        <!-- Project 1: Speed Tech 254 -->
        <div class="bg-transparent border-none rounded-none overflow-hidden shadow-none hover:shadow-none transition-all duration-300 flex flex-col group">
            <div class="h-60 overflow-hidden relative rounded-2xl shadow-xl border border-slate-700">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=600&q=80" alt="Speed Tech 254 Portal" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <div class="absolute inset-0 bg-slate-950/20 group-hover:bg-slate-950/10 transition-colors"></div>
                <span class="absolute bottom-4 right-4 text-xs font-black bg-cyan-600 text-white px-3 py-1.5 rounded-md shadow-md tracking-widest uppercase">In Production</span>
            </div>
            <div class="pt-6 pb-2 flex-1 flex flex-col justify-between space-y-4">
                <div>
                    <h4 class="heading-font text-2xl font-black text-white tracking-wide [text-shadow:0_2px_6px_rgba(0,0,0,0.95)]">Speed Tech 254 Academy</h4>
                    <p class="text-white text-base font-semibold leading-relaxed mt-2 [text-shadow:0_2px_4px_rgba(0,0,0,0.85)]">An elite tech academy and security portal architecture featuring comprehensive student course registration modules, dynamic login systems, and protective security parameters.</p>
                </div>
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="text-xs font-black bg-slate-900/80 text-cyan-300 px-3 py-1 rounded-md border border-cyan-500/30 tracking-wider">PHP / MySQL</span>
                    <span class="text-xs font-black bg-slate-900/80 text-cyan-300 px-3 py-1 rounded-md border border-cyan-500/30 tracking-wider">Tailwind CSS</span>
                    <span class="text-xs font-black bg-slate-900/80 text-cyan-300 px-3 py-1 rounded-md border border-cyan-500/30 tracking-wider">Access Control</span>
                </div>
            </div>
        </div>

        <!-- Project 2: Speed Shoppers -->
        <div class="bg-transparent border-none rounded-none overflow-hidden shadow-none hover:shadow-none transition-all duration-300 flex flex-col group">
            <div class="h-60 overflow-hidden relative rounded-2xl shadow-xl border border-slate-700">
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=600&q=80" alt="Speed Shoppers Storefront" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <div class="absolute inset-0 bg-slate-950/20 group-hover:bg-slate-950/10 transition-colors"></div>
                <span class="absolute bottom-4 right-4 text-xs font-black bg-indigo-600 text-white px-3 py-1.5 rounded-md shadow-md tracking-widest uppercase">Integrated Ecosystem</span>
            </div>
            <div class="pt-6 pb-2 flex-1 flex flex-col justify-between space-y-4">
                <div>
                    <h4 class="heading-font text-2xl font-black text-white tracking-wide [text-shadow:0_2px_6px_rgba(0,0,0,0.95)]">Speed shoppers</h4>
                    <p class="text-white text-base font-semibold leading-relaxed mt-2 [text-shadow:0_2px_4px_rgba(0,0,0,0.85)]">A mobile-first e-commerce framework optimized for fashion storefront interactions. Built with fluid category metrics, request processing controls, and M-Pesa API integrations.</p>
                </div>
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="text-xs font-black bg-slate-900/80 text-indigo-300 px-3 py-1 rounded-md border border-indigo-500/30 tracking-wider">M-Pesa STK Push</span>
                    <span class="text-xs font-black bg-slate-900/80 text-indigo-300 px-3 py-1 rounded-md border border-indigo-500/30 tracking-wider">Bootstrap</span>
                    <span class="text-xs font-black bg-slate-900/80 text-indigo-300 px-3 py-1 rounded-md border border-indigo-500/30 tracking-wider">E-Commerce</span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- SECTION 5: EDUCATION BACKGROUND -->
<section id="education" class="py-24 px-4 sm:px-6 max-w-7xl mx-auto scroll-mt-20 bg-transparent">
    <div class="text-center max-w-3xl mx-auto mb-16 p-0 bg-transparent border-none shadow-none backdrop-blur-none transition-colors duration-300">
        <h2 class="heading-font text-3xl sm:text-4xl lg:text-5xl font-black mb-4 text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">Educational Journey</h2>
        <div class="w-16 h-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 mx-auto rounded-full shadow-md"></div>
        <p class="text-white mt-5 text-base sm:text-lg font-bold leading-relaxed [text-shadow:0_2px_5px_rgba(0,0,0,0.9)]">Academic foundation registers, engineering tracks, and advanced information technology research levels.</p>
    </div>

    <!-- Vertical Timeline Track Scheme -->
    <div class="relative max-w-4xl mx-auto before:absolute before:inset-y-0 before:left-0 md:before:left-1/2 before:w-1 before:bg-gradient-to-b before:from-cyan-400 before:via-blue-500 before:to-indigo-500 bg-transparent">
        
        <!-- Timeline Module Item 1: Masters -->
        <div class="relative pl-10 md:pl-0 md:w-1/2 md:ml-auto mb-16 group bg-transparent">
            <div class="absolute left-0 md:-left-4 top-2 w-8 h-8 rounded-full bg-slate-950 border-4 border-cyan-400 z-10 flex items-center justify-center transition-colors group-hover:bg-cyan-400 shadow-md"></div>
            <div class="md:pl-10 bg-transparent">
                <div class="bg-transparent border-none p-0 rounded-none relative shadow-none hover:shadow-none transition-all duration-300 space-y-3">
                    <span class="inline-block text-xs font-black bg-slate-900/90 text-cyan-300 px-2.5 py-1 rounded uppercase tracking-widest border border-cyan-400/30 shadow-sm">Present</span>
                    <h4 class="heading-font text-xl sm:text-2xl font-black text-white tracking-wide [text-shadow:0_2px_6px_rgba(0,0,0,0.9)]">MSc. in Information Technology</h4>
                    <span class="text-sm sm:text-base text-cyan-200 font-extrabold block [text-shadow:0_2px_4px_rgba(0,0,0,0.8)]">Kisii University</span>
                    <p class="text-white text-base font-semibold leading-relaxed [text-shadow:0_2px_4px_rgba(0,0,0,0.85)]">Focusing structural systems metrics on enterprise distributed infrastructure, secure transaction processing pathways, and advanced network optimization parameters.</p>
                </div>
            </div>
        </div>

        <!-- Timeline Module Item 2: Kisii University Undergrad -->
        <div class="relative pl-10 md:pl-0 md:w-1/2 md:mr-auto mb-16 md:text-right group bg-transparent">
            <div class="absolute left-0 md:left-auto md:-right-4 top-2 w-8 h-8 rounded-full bg-slate-950 border-4 border-blue-400 z-10 flex items-center justify-center transition-colors group-hover:bg-blue-400 shadow-md"></div>
            <div class="md:pr-10 bg-transparent">
                <div class="bg-transparent border-none p-0 rounded-none relative shadow-none hover:shadow-none transition-all duration-300 space-y-3">
                    <span class="inline-block text-xs font-black bg-slate-900/90 text-blue-300 px-2.5 py-1 rounded uppercase tracking-widest border border-blue-400/30 shadow-sm">2021 — 2025</span>
                    <h4 class="heading-font text-xl sm:text-2xl font-black text-white tracking-wide [text-shadow:0_2px_6px_rgba(0,0,0,0.9)]">BSc. in Computer Science</h4>
                    <span class="text-sm sm:text-base text-blue-200 font-extrabold block [text-shadow:0_2px_4px_rgba(0,0,0,0.8)]">Kisii University</span>
                    <p class="text-white text-base font-semibold leading-relaxed [text-shadow:0_2px_4px_rgba(0,0,0,0.85)]">Acquired core methodologies in algorithm design principles, relational database engine development, full-stack systems engineering, and automated environment scripts.</p>
                </div>
            </div>
        </div>

        <!-- Timeline Module Item 3: High School -->
        <div class="relative pl-10 md:pl-0 md:w-1/2 md:ml-auto mb-16 group bg-transparent">
            <div class="absolute left-0 md:-left-4 top-2 w-8 h-8 rounded-full bg-slate-950 border-4 border-indigo-400 z-10 flex items-center justify-center transition-colors group-hover:bg-indigo-400 shadow-md"></div>
            <div class="md:pl-10 bg-transparent">
                <div class="bg-transparent border-none p-0 rounded-none relative shadow-none hover:shadow-none transition-all duration-300 space-y-3">
                    <span class="inline-block text-xs font-black bg-slate-900/90 text-indigo-300 px-2.5 py-1 rounded uppercase tracking-widest border border-indigo-400/30 shadow-sm">2017 — 2020</span>
                    <h4 class="heading-font text-xl sm:text-2xl font-black text-white tracking-wide [text-shadow:0_2px_6px_rgba(0,0,0,0.9)]">Kenya Certificate of Secondary Education (KCSE)</h4>
                    <span class="text-sm sm:text-base text-indigo-200 font-extrabold block [text-shadow:0_2px_4px_rgba(0,0,0,0.8)]">St. Joseph's Nalondo Boys High School</span>
                    <p class="text-white text-base font-semibold leading-relaxed [text-shadow:0_2px_4px_rgba(0,0,0,0.85)]">Attained primary foundational certifications with structural focus metrics in mathematics, physics analysis, and initial computer logic tracking blocks.</p>
                </div>
            </div>
        </div>

        <!-- Timeline Module Item 4: Primary School -->
        <div class="relative pl-10 md:pl-0 md:w-1/2 md:mr-auto group bg-transparent">
            <div class="absolute left-0 md:left-auto md:-right-4 top-2 w-8 h-8 rounded-full bg-slate-950 border-4 border-blue-400 z-10 flex items-center justify-center transition-colors group-hover:bg-blue-400 shadow-md"></div>
            <div class="md:pr-10 bg-transparent">
                <div class="bg-transparent border-none p-0 rounded-none relative shadow-none hover:shadow-none transition-all duration-300 space-y-3">
                    <span class="inline-block text-xs font-black bg-slate-900/90 text-blue-300 px-2.5 py-1 rounded uppercase tracking-widest border border-blue-400/30 shadow-sm">2006 — 2016</span>
                    <h4 class="heading-font text-xl sm:text-2xl font-black text-white tracking-wide [text-shadow:0_2px_6px_rgba(0,0,0,0.9)]">Primary School Education</h4>
                    <span class="text-sm sm:text-base text-blue-200 font-extrabold block mb-4 [text-shadow:0_2px_4px_rgba(0,0,0,0.8)]">Marobo Primary School</span>
                    <p class="text-white text-base font-semibold leading-relaxed [text-shadow:0_2px_4px_rgba(0,0,0,0.85)]">Built a strong foundational knowledge in core subjects, developed critical thinking skills, and actively participated in co-curricular activities during early academic formation.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- =========================================================================
     STANDALONE CENTERING FAQ ACCORDION COMPONENT
     ========================================================================= -->
    <div class="standalone-faq-section-wrapper mt-24">
        <div class="faq-centered-container">
            
            <h2 class="faq-main-title heading-font text-3xl sm:text-4xl lg:text-5xl font-black mb-10 text-center text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">Frequently Asked Questions</h2>
        
            <div class="faq-accordion-container">
                
                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <button class="faq-trigger">
                        <span class="faq-question-text"><i class="far fa-compass faq-icon"></i> Where are these projects developed from?</span>
                        <span class="faq-chevron"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="faq-response">
                        <div class="faq-response-inner">
                            <p>I operate and work directly from <strong>Nairobi, Kenya</strong>. All deployment handling systems, database optimization routines, and technical configurations are built here to service scalable systems worldwide seamlessly.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <button class="faq-trigger">
                        <span class="faq-question-text"><i class="fas fa-laptop-code faq-icon"></i> What are sample projects for beginners and experts?</span>
                        <span class="faq-chevron"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="faq-response">
                        <div class="faq-response-inner">
                            <p>Projects span across two main tiers depending on architectural complexity:</p>
                            <ul class="project-links-list">
                                <li class="text-white">
                                    <span class="badge beginner">Beginner</span> 
                                    <strong>Interactive Interface Layouts:</strong> Responsive web modules built using clean structural styling and fluid visual transitions.
                                    <a href="https://speeddeveloper.infinityfreeapp.com/" class="project-access-link">Access Beginner Track <i class="fas fa-external-link-alt"></i></a>
                                </li>
                               
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item">
                    <button class="faq-trigger">
                        <span class="faq-question-text"><i class="far fa-id-card faq-icon"></i> Direct Business Contact</span>
                        <span class="faq-chevron"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="faq-response">
                        <div class="faq-response-inner">
                            <p>For instant deployment support, code reviews, staging server setups, or backend architectural consulting, you can get in touch directly via:</p>
                            <div class="contact-highlight-box">
                                <a href="tel:0754053038" class="phone-call-btn">
                                    <i class="fas fa-phone-volume"></i> 0754053038
                                </a>
                                <span class="loc-tag text-cyan-300">Available for project inquiries</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- FontAwesome Vector Icons Hook -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- CSS Styling Layout Engines -->
<style>
    :root {
        --neon-cyan-faq: #00f2fe;
        --glowing-blue-faq: #4facfe;
        --border-glow-faq: rgba(0, 242, 254, 0.3);
    }

    /* Perfectly centered standalone wrapper block without card frames */
    .standalone-faq-section-wrapper {
        background: transparent !important; 
        padding: 20px 0;
        font-family: inherit;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .faq-centered-container {
        width: 100%;
        max-width: 768px;
        box-sizing: border-box;
    }

    .faq-accordion-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }

    .faq-item {
        border-bottom: 1px solid var(--border-glow-faq);
        background: transparent;
        overflow: hidden;
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }

    /* Interactive Trigger Header Layout */
    .faq-trigger {
        width: 100%;
        background: transparent;
        border: none;
        padding: 18px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: left;
        cursor: pointer;
        outline: none;
        color: #ffffff !important;
        box-sizing: border-box;
        text-shadow: 0 2px 4px rgba(0,0,0,0.9);
    }

    .faq-question-text {
        font-size: 1.05rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 14px;
        letter-spacing: 0.2px;
        color: #ffffff !important;
    }

    .faq-icon {
        color: #22d3ee;
        font-size: 1.1rem;
    }

    .faq-chevron {
        color: #e2e8f0;
        font-size: 0.9rem;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Rotational State */
    .faq-item.open-active .faq-chevron {
        transform: rotate(180deg);
        color: #22d3ee;
    }

    /* Smooth Height Expansion */
    .faq-response {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .faq-response-inner {
        padding: 4px 0 24px 32px;
        color: #ffffff !important;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.7;
        box-sizing: border-box;
        text-shadow: 0 2px 4px rgba(0,0,0,0.9);
    }

    .faq-response-inner strong {
        color: #22d3ee !important;
    }

    .faq-response-inner p {
        margin: 0 0 12px 0;
        color: #ffffff !important;
    }
    
    .faq-response-inner p:last-child {
        margin-bottom: 0;
    }

    /* Inner Tracks Links Layout System */
    .project-links-list {
        list-style: none;
        padding: 0;
        margin: 16px 0 0 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .project-links-list li {
        position: relative;
        padding: 0;
        box-sizing: border-box;
        color: #ffffff !important;
    }

    .project-access-link {
        display: block;
        margin-top: 4px;
        color: #38bdf8 !important;
        font-weight: 800;
        transition: color 0.2s ease;
    }

    .project-access-link:hover {
        color: #22d3ee !important;
        text-decoration: underline;
    }

    .contact-highlight-box {
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .phone-call-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #06b6d4;
        color: #ffffff !important;
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 800;
        text-shadow: none;
        transition: background-color 0.2s ease;
    }

    .phone-call-btn:hover {
        background-color: #0891b2;
    }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 900;
        text-transform: uppercase;
        margin-right: 6px;
        letter-spacing: 0.5px;
    }

    .badge.beginner {
        background-color: #1e293b;
        color: #38bdf8 !important;
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    .badge.expert {
        background-color: #1e293b;
        color: #a78bfa !important;
        border: 1px solid rgba(167, 139, 250, 0.3);
    }
</style>

<script>
    // Self-contained Vanilla Accordion Logic Engine Engine
    document.querySelectorAll('.faq-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const currentItem = trigger.closest('.faq-item');
            const responseNode = currentItem.querySelector('.faq-response');
            const isOpen = currentItem.classList.contains('open-active');
            
            // Retract parallel nodes
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('open-active');
                item.querySelector('.faq-response').style.maxHeight = null;
            });
            
            // Toggle active segment 
            if (!isOpen) {
                currentItem.classList.add('open-active');
                responseNode.style.maxHeight = responseNode.scrollHeight + "px";
            }
        });
    });
</script>
<!-- =========================================================================
     COMPACT GLOWING MESH TESTIMONIAL ROTATOR (20 UNIQUE PROFILES) - ABSOLUTE FOUC GUARD
     ========================================================================= -->
<!-- FIXED: Added an inline style opacity guard to prevent the browser from drawing unstyled text -->
<div class="portfolio-testimonials-container" id="foucGuardContainer" style="opacity: 0; transition: opacity 0.3s ease;">
    <div class="container">
        <h2 class="section-title">What <span>Other People Say</span></h2>
        
        <!-- Shrunk, High-Impact Slider Container -->
        <div class="testimonial-slider-wrapper">
            <div class="testimonial-slider" id="testimonialSlider">
                
                <!-- Slide 1 -->
                <div class="testimonial-slide active">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-tie"></i></span>
                            <span class="user-name">David Ochieng</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 711 *** 432</span>
                        </div>
                        <p class="quote-text">"The automation module optimized our entire operations pipeline. Exceptional delivery on server path routing and overall database security architecture."</p>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-shield"></i></span>
                            <span class="user-name">Mercy Chepngetich</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 722 *** 891</span>
                        </div>
                        <p class="quote-text">"Flawless execution on the real-time financial logging gateway. The automated callbacks are bulletproof and handle high-volume spikes with ease."</p>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-cog"></i></span>
                            <span class="user-name">Evans Wafula</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 705 *** 314</span>
                        </div>
                        <p class="quote-text">"Incredible full-stack architectural precision. He translated complex, multi-tiered business logic into a lightweight, insanely fast web application."</p>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-ninja"></i></span>
                            <span class="user-name">Joy Wanjiku</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 733 *** 765</span>
                        </div>
                        <p class="quote-text">"Outstanding automation handling! The instant visual processing scripts he deployed completely eradicated our team's manual reconciliation backlogs."</p>
                    </div>
                </div>

                <!-- Slide 5 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-graduate"></i></span>
                            <span class="user-name">Kelvin Mwangi</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 791 *** 203</span>
                        </div>
                        <p class="quote-text">"A master class in modern system engineering. The dynamic dashboard transitions and core script processing execution are completely top-tier."</p>
                    </div>
                </div>

                <!-- Slide 6 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-md"></i></span>
                            <span class="user-name">Brenda Awuor</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 715 *** 982</span>
                        </div>
                        <p class="quote-text">"He swiftly engineered a secure system layout that instantly solved our multi-server transaction syncing issues. Highly reliable expert."</p>
                    </div>
                </div>

                <!-- Slide 7 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-astronaut"></i></span>
                            <span class="user-name">Collins Kiprono</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 720 *** 554</span>
                        </div>
                        <p class="quote-text">"The structured billing module he delivered runs flawlessly. Zero deployment errors, perfectly clean files, and immediate transactional feedback."</p>
                    </div>
                </div>

                <!-- Slide 8 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-secret"></i></span>
                            <span class="user-name">Agnes Mutua</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 740 *** 129</span>
                        </div>
                        <p class="quote-text">"Excellent documentation and crisp technical execution. He streamlined our network authentication engine while maximizing backend responsiveness."</p>
                    </div>
                </div>

                <!-- Slide 9 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-rocket"></i></span>
                            <span class="user-name">Moses Simiyu</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 752 *** 687</span>
                        </div>
                        <p class="quote-text">"The real-time automated status update tracking system works like a charm. Instant script execution speeds up our performance thresholds significantly."</p>
                    </div>
                </div>

                <!-- Slide 10 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-crown"></i></span>
                            <span class="user-name">Caroline Njoroge</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 718 *** 441</span>
                        </div>
                        <p class="quote-text">"His profound knowledge of robust backend automations and system scripting structures sets him completely apart from typical web developers."</p>
                    </div>
                </div>

                <!-- Slide 11 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-lightbulb"></i></span>
                            <span class="user-name">Patrick Onyango</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 729 *** 602</span>
                        </div>
                        <p class="quote-text">"Incredibly structured approach to deployment. He completely overhauled our user verification flows and dropped processing times to seconds."</p>
                    </div>
                </div>

                <!-- Slide 12 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-gem"></i></span>
                            <span class="user-name">Esther Kwamboka</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 702 *** 833</span>
                        </div>
                        <p class="quote-text">"The secure client registration panels and automated record updates are seamless. Impeccable attention to detail throughout development."</p>
                    </div>
                </div>

                <!-- Slide 13 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-clock"></i></span>
                            <span class="user-name">Brian Barasa</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 741 *** 194</span>
                        </div>
                        <p class="quote-text">"Flawless handling of automated transactional records. He resolved critical routing conflicts on our hosting space within record-breaking timelines."</p>
                    </div>
                </div>

                <!-- Slide 14 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-heart"></i></span>
                            <span class="user-name">Naomi Wambui</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 755 *** 915</span>
                        </div>
                        <p class="quote-text">"Highly functional components integrated perfectly with fluid animations. The automated system callbacks perform exceptionally well under high loads."</p>
                    </div>
                </div>

                <!-- Slide 15 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-bolt"></i></span>
                            <span class="user-name">Geoffrey Omwamba</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 708 *** 376</span>
                        </div>
                        <p class="quote-text">"Excellent systems engineer. The secure payment verification setup operates flawlessly, providing clean data logs with complete clarity."</p>
                    </div>
                </div>

                <!-- Slide 16 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-feather"></i></span>
                            <span class="user-name">Lillian Nafula</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 725 *** 217</span>
                        </div>
                        <p class="quote-text">"Professional, proactive, and exceptionally sharp. He automated our file management structures, bringing unmatched performance speeds to the platform."</p>
                    </div>
                </div>

                <!-- Slide 17 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-magic"></i></span>
                            <span class="user-name">Dominic Kimutai</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 712 *** 658</span>
                        </div>
                        <p class="quote-text">"Outstanding optimization on our complex application logic. The automated transaction flow runs seamlessly without any manual tracking bottlenecks."</p>
                    </div>
                </div>

                <!-- Slide 18 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-trophy"></i></span>
                            <span class="user-name">Fiona Atieno</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 734 *** 409</span>
                        </div>
                        <p class="quote-text">"He transformed vague processing requirements into a highly functional, automated architectural system. Truly top-tier engineering execution."</p>
                    </div>
                </div>

                <!-- Slide 19 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-key"></i></span>
                            <span class="user-name">Andrew Makori</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 701 *** 550</span>
                        </div>
                        <p class="quote-text">"The MikroTik API integrations and real-time database tracking modules he developed are incredibly responsive and secure. Brilliant work."</p>
                    </div>
                </div>

                <!-- Slide 20 -->
                <div class="testimonial-slide">
                    <div class="mesh-bg"></div>
                    <div class="card-content">
                        <div class="user-inline-meta">
                            <span class="user-icon"><i class="fas fa-user-star"></i></span>
                            <span class="user-name">Vero Nduku</span>
                            <span class="user-divider">|</span>
                            <span class="phone-number"><i class="fas fa-phone-alt"></i> +254 721 *** 991</span>
                        </div>
                        <p class="quote-text">"The billing card setups and notification modules are beautifully streamlined. His architectural clean-code structure is highly maintainable."</p>
                    </div>
                </div>

            </div>

            <!-- Minimal Dots Navigation -->
            <div class="slider-dots" id="sliderDots"></div>
        </div>
    </div>

    <!-- FontAwesome Vector Icons Hook -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSS Styling Layout Engines -->
    <style>
        :root {
            --neon-cyan: #00f2fe;
            --glowing-blue: #4facfe;
            --text-pure: #ffffff;
            --text-gray: #cbd5e1;
            --border-glow: rgba(0, 242, 254, 0.25);
        }

        .portfolio-testimonials-container {
            background: transparent !important; 
            padding: 40px 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--text-pure);
            width: 100%;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 35px;
            font-weight: 700;
        }

        .section-title span {
            background: linear-gradient(135deg, var(--neon-cyan), var(--glowing-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .testimonial-slider-wrapper {
            position: relative;
            width: 100%;
            margin: 0 auto;
        }

        /* Fixed Grid Architecture Stacking Context */
        .testimonial-slider {
            position: relative;
            width: 100%;
            display: grid;
            grid-template-columns: 100%;
            grid-template-rows: auto;
        }

        .testimonial-slide {
            grid-column: 1;
            grid-row: 1;
            width: 100%;
            opacity: 0;
            visibility: hidden;
            transform: scale(0.97);
            pointer-events: none;
            border: 1px solid var(--border-glow);
            border-radius: 12px;
            padding: 18px 22px;
            box-sizing: border-box;
            overflow: hidden;
            transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.5s ease;
        }

        .testimonial-slide.active {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
            pointer-events: auto;
            box-shadow: 0 8px 25px rgba(0, 242, 254, 0.15);
        }

        .mesh-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: #0d1527; 
            background-image: 
                radial-gradient(at 10% 20%, rgba(0, 242, 254, 0.14) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(79, 172, 254, 0.14) 0px, transparent 50%);
        }

        .card-content {
            position: relative;
            z-index: 2;
        }

        .user-inline-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .user-icon {
            color: var(--neon-cyan);
        }

        .user-name {
            font-weight: 700;
            color: var(--text-pure);
            letter-spacing: 0.3px;
        }

        .user-divider {
            color: rgba(255, 255, 255, 0.2);
        }

        .phone-number {
            color: var(--text-gray);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .phone-number i {
            color: var(--glowing-blue);
        }

        .quote-text {
            font-style: italic;
            color: #ffffff;
            line-height: 1.5;
            margin: 0;
            font-size: 0.98rem;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
            flex-wrap: wrap;
            max-width: 100%;
            padding: 0 10px;
        }

        .dot {
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: var(--neon-cyan);
            width: 16px;
            border-radius: 4px;
            box-shadow: 0 0 5px var(--neon-cyan);
        }

        @media (max-width: 550px) {
            .user-inline-meta {
                gap: 5px;
                font-size: 0.9rem;
            }
            .user-divider {
                display: none;
            }
            .testimonial-slide {
                padding: 14px 16px;
            }
        }
    </style>

    <!-- Slide Transition Controller Code -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const slides = document.querySelectorAll(".testimonial-slide");
            const dotsContainer = document.getElementById("sliderDots");
            const guardContainer = document.getElementById("foucGuardContainer");
            let currentIndex = 0;
            let slideTimer;

            // Generate Dot Nodes dynamically
            if(dotsContainer && dotsContainer.children.length === 0) {
                slides.forEach((_, index) => {
                    const dot = document.createElement("div");
                    dot.classList.add("dot");
                    if (index === 0) dot.classList.add("active");
                    dot.addEventListener("click", () => {
                        rotateActiveCard(index);
                        resetTimerSequence();
                    });
                    dotsContainer.appendChild(dot);
                });
            }

            const dots = document.querySelectorAll(".dot");

            function rotateActiveCard(index) {
                if(!slides[currentIndex] || !dots[currentIndex]) return;
                
                slides[currentIndex].classList.remove("active");
                dots[currentIndex].classList.remove("active");

                currentIndex = index;

                slides[currentIndex].classList.add("active");
                dots[currentIndex].classList.add("active");
            }

            function processNextSlide() {
                let nextTargetIndex = (currentIndex + 1) % slides.length;
                rotateActiveCard(nextTargetIndex);
            }

            function startTimerSequence() {
                slideTimer = setInterval(processNextSlide, 5000);
            }

            function resetTimerSequence() {
                clearInterval(slideTimer);
                startTimerSequence();
            }

            startTimerSequence();

            // FIXED: Instantly reveal the container smoothly only after styles and elements are bound
            if(guardContainer) {
                guardContainer.style.opacity = "1";
            }
        });
    </script>
</div>
    

    
<?php
// Place this processing script at the absolute top of your index.php file
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_inquiry'])) {
    // Capture data payloads smoothly from JavaScript fetch
    $name = $_POST['visitor_name'] ?? '';
    $email = $_POST['visitor_email'] ?? '';
    $phone = $_POST['visitor_phone'] ?? '';
    $subject = $_POST['visitor_subject'] ?? '';
    $message = $_POST['visitor_message'] ?? '';
    $status = 'pending';

    // Core execution string targeting structural columns cleanly
    $stmt = $conn->prepare("INSERT INTO contact_requests (name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    
    if ($stmt) {
        $stmt->bind_param("ssssss", $name, $email, $phone, $subject, $message, $status);
        $stmt->execute();
        $stmt->close();
    }
    // Exit immediately to prevent sending standard layout wrapper data during fetch responses
    exit;
}
?>
<!-- SECTION: EXPERIENCE METRICS NODES -->
<section id="experience-metrics" class="py-12 px-4 sm:px-6 max-w-7xl mx-auto scroll-mt-20 bg-transparent">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-transparent">
        
        <!-- Left Side: Title & Description Structure -->
        <div class="lg:col-span-5 space-y-3 bg-transparent">
            <div class="p-0 bg-transparent border-none shadow-none">
                <span class="inline-block text-xs font-black bg-slate-900/90 text-cyan-300 px-2.5 py-0.5 rounded uppercase tracking-widest border border-cyan-400/30 shadow-sm mb-2">LIVE</span>
                <h2 class="heading-font text-3xl sm:text-4xl font-black mb-2 text-white tracking-tight [text-shadow:0_3px_10px_rgba(0,0,0,0.9)]">Performance Registry</h2>
                <div class="w-16 h-1.5 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full mb-3 shadow-md"></div>
                <p class="text-white text-base font-black leading-relaxed [text-shadow:0_2px_5px_rgba(0,0,0,0.95)]">
                    Quantified implementation records across architecture deployment, full-stack builds and system optimizations.
                </p>
            </div>
        </div>

        <!-- Right Side: Compact Animated Metrics Counter Grid -->
        <div class="lg:col-span-7 bg-transparent">
            <div class="grid grid-cols-3 gap-4 bg-slate-950/40 border border-slate-800/60 rounded-xl p-6 backdrop-blur-sm shadow-xl">
                <!-- Counter Card 1: Years Experience -->
                <div class="text-center">
                    <span class="block text-3xl sm:text-4xl font-black text-cyan-400 tracking-tight [text-shadow:0_2px_8px_rgba(6,182,212,0.4)] font-mono" data-metric-target="5">0</span>
                    <span class="block text-xs sm:text-sm font-black text-white uppercase tracking-wider mt-1 [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">Years Exp</span>
                </div>
                <!-- Counter Card 2: Total Clients -->
                <div class="text-center border-x border-slate-800/80">
                    <span class="block text-3xl sm:text-4xl font-black text-blue-400 tracking-tight [text-shadow:0_2px_8px_rgba(59,130,246,0.4)] font-mono" data-metric-target="14000">0</span>
                    <span class="block text-xs sm:text-sm font-black text-white uppercase tracking-wider mt-1 [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">Total Clients</span>
                </div>
                <!-- Counter Card 3: Projects Done -->
                <div class="text-center">
                    <span class="block text-3xl sm:text-4xl font-black text-emerald-400 tracking-tight [text-shadow:0_2px_8px_rgba(16,185,129,0.4)] font-mono" data-metric-target="15000">0</span>
                    <span class="block text-xs sm:text-sm font-black text-white uppercase tracking-wider mt-1 [text-shadow:0_1px_3px_rgba(0,0,0,0.9)]">Projects Done</span>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Vanilla JS Intersection Observer for Fluid Metric Counting -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const metricCounters = document.querySelectorAll("[data-metric-target]");
        const animationDuration = 200; 

        const runMetricCounter = (counter) => {
            const executeIncrement = () => {
                const targetValue = +counter.getAttribute("data-metric-target");
                const currentValue = +counter.innerText;
                const incrementalStep = Math.ceil(targetValue / animationDuration);

                if (currentValue < targetValue) {
                    counter.innerText = currentValue + incrementalStep > targetValue ? targetValue : currentValue + incrementalStep;
                    setTimeout(executeIncrement, 15);
                } else {
                    counter.innerText = targetValue + "+";
                }
            };
            executeIncrement();
        };

        const observerConfiguration = {
            root: null,
            threshold: 0.15
        };

        const metricIntersectionObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    runMetricCounter(entry.target);
                    metricIntersectionObserver.unobserve(entry.target); 
                }
            });
        }, observerConfiguration);

        metricCounters.forEach(counter => metricIntersectionObserver.observe(counter));
    });
</script>
<!-- SECTION 6: INTERACTIVE CONTACT PORTAL -->
<section id="contact" class="py-24 px-6 max-w-7xl mx-auto scroll-mt-20 relative">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start relative z-10">
        
        <!-- Left Information Column Area -->
        <div class="lg:col-span-5 space-y-6 p-6 rounded-3xl glass-card">
            <h2 class="heading-font text-3xl sm:text-4xl font-extrabold text-slate-900">Let's Connect</h2>
            <p class="text-slate-700 font-medium text-sm sm:text-base leading-relaxed">
                Seeking collaboration channels on system network optimization, administrative solutions alignment, or academic analysis tracks? Reach out directly through the verified endpoints.
            </p>
            
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-4 bg-white/40 border border-white/60 p-4 rounded-xl">
                    <div class="w-10 h-10 rounded-lg bg-cyan-100/60 flex items-center justify-center text-cyan-700 shadow-inner">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-bold block">Email Address</span>
                        <a href="mailto:contact@petermayende.com" class="text-sm text-slate-800 font-bold hover:text-cyan-700 transition-colors">pmayende@gmail.com</a>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-white/40 border border-white/60 p-4 rounded-xl">
                    <div class="w-10 h-10 rounded-lg bg-blue-100/60 flex items-center justify-center text-blue-700 shadow-inner">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-bold block">Current Base Location</span>
                        <span class="text-sm text-slate-800 font-bold">Nairobi, Kenya</span>
                    </div>
                </div>
            </div>

            <!-- Connect Channels Linked Grid -->
           <!-- Font Awesome CDN for Live Icons (Add this inside your <head> if not already present) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Connect Channels Linked Grid -->
<div class="flex flex-wrap items-center gap-3 pt-2">
    
    <!-- LinkedIn -->
<!-- Facebook -->
<a href="https://web.facebook.com/codespeed" target="_blank" rel="noopener noreferrer" 
   class="w-10 h-10 rounded-lg bg-[#1877F2]/10 border border-[#1877F2]/30 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white hover:scale-105 transition-all" 
   title="Facebook">
    <i class="fa-brands fa-facebook-f text-lg"></i>
</a>
    
    <!-- GitHub -->
    <a href="https://github.com/shadowkenya" target="_blank" rel="noopener noreferrer" 
       class="w-10 h-10 rounded-lg bg-[#24292e]/10 border border-[#24292e]/30 flex items-center justify-center text-[#24292e] hover:bg-[#24292e] hover:text-white hover:scale-105 transition-all" 
       title="GitHub">
        <i class="fa-brands fa-github text-lg"></i>
    </a>
    
    <!-- X / Twitter -->
    <a href="https://x.com/Shadowkenya254" target="_blank" rel="noopener noreferrer" 
       class="w-10 h-10 rounded-lg bg-black/5 border border-black/20 flex items-center justify-center text-black hover:bg-black hover:text-white hover:scale-105 transition-all" 
       title="X (Twitter)">
        <i class="fa-brands fa-x-twitter text-lg"></i>
    </a>



    <!-- YouTube -->
    <a href="https://www.youtube.com/channel/UCRq2gsPwIB2YjKxmSuSe_cg" target="_blank" rel="noopener noreferrer" 
       class="w-10 h-10 rounded-lg bg-[#FF0000]/10 border border-[#FF0000]/30 flex items-center justify-center text-[#FF0000] hover:bg-[#FF0000] hover:text-white hover:scale-105 transition-all" 
       title="YouTube">
        <i class="fa-brands fa-youtube text-lg"></i>
    </a>



    <!-- Instagram -->
    <a href="https://www.instagram.com/mayende254/" target="_blank" rel="noopener noreferrer" 
       class="w-10 h-10 rounded-lg bg-[#E1306C]/10 border border-[#E1306C]/30 flex items-center justify-center text-[#E1306C] hover:bg-[#E1306C] hover:text-white hover:scale-105 transition-all" 
       title="Instagram">
        <i class="fa-brands fa-instagram text-lg"></i>
    </a>

</div>
        </div>

        <!-- Interactive Contact Form Panel Component -->
        <div class="lg:col-span-7 w-full">
            <form id="contactForm" method="POST" action="index.php#contact" class="glass-card p-6 sm:p-8 rounded-3xl space-y-6">
                
                <!-- Hidden Trigger value to let the PHP backend process entries natively -->
                <input type="hidden" name="submit_inquiry" value="1">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Your Name</label>
                        <input type="text" id="visitor_name" name="visitor_name" required class="w-full bg-white/40 border border-white/60 rounded-xl px-4 py-3 text-sm text-slate-900 font-medium focus:outline-none focus:border-cyan-500 focus:bg-white transition-all" placeholder="e.g. Peter Mayende">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Email Address</label>
                        <input type="email" id="visitor_email" name="visitor_email" required class="w-full bg-white/40 border border-white/60 rounded-xl px-4 py-3 text-sm text-slate-900 font-medium focus:outline-none focus:border-cyan-500 focus:bg-white transition-all" placeholder="peter@gmail.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Contact Number</label>
                        <input type="tel" id="visitor_phone" name="visitor_phone" required class="w-full bg-white/40 border border-white/60 rounded-xl px-4 py-3 text-sm text-slate-900 font-medium focus:outline-none focus:border-cyan-500 focus:bg-white transition-all" placeholder="+254 754053038">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Subject Message</label>
                        <input type="text" id="visitor_subject" name="visitor_subject" required class="w-full bg-white/40 border border-white/60 rounded-xl px-4 py-3 text-sm text-slate-900 font-medium focus:outline-none focus:border-cyan-500 focus:bg-white transition-all" placeholder="Enter message subject">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Message Details</label>
                    <textarea rows="4" id="visitor_message" name="visitor_message" required class="w-full bg-white/40 border border-white/60 rounded-xl px-4 py-3 text-sm text-slate-900 font-medium focus:outline-none focus:border-cyan-500 focus:bg-white transition-all resize-none" placeholder="Type your context here..."></textarea>
                </div>
                
                <!-- Submit Action Button Element with integrated Loader Layouts -->
                <button type="submit" id="submitBtn" class="w-full py-3.5 rounded-xl bg-slate-900 text-white font-bold text-sm tracking-wide hover:bg-slate-800 shadow-md transition-all duration-300 flex items-center justify-center gap-2">
                    <span id="btnText">Send  Message</span>
                    <i id="btnIcon" class="fa-solid fa-paper-plane text-xs"></i>
                    <!-- SVG Loading Spinner -->
                    <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

 <!-- GLASSMORPHIC SUCCESS STATUS MODAL POPUP -->
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm p-6 text-center rounded-3xl bg-slate-900/90 border border-white/10 shadow-2xl text-white transform scale-95 transition-transform duration-300" id="modalCard">
        <div class="w-16 h-16 mx-auto bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center text-2xl mb-4 border border-emerald-500/20">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h3 class="text-xl font-bold tracking-tight text-white mb-2">sent Successful</h3>
        <p class="text-slate-400 text-sm font-medium leading-relaxed">
            Your message have been processed and captured successfully.
        </p>
    </div>
</div>

<script>
    // Self-contained logic to auto-dismiss the modal 2 seconds after it becomes visible
    (function() {
        const modal = document.getElementById('successModal');
        const card = document.getElementById('modalCard');

        // MutationObserver to watch when your existing logic removes the 'hidden' class
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && !modal.classList.contains('hidden')) {
                    
                    // Trigger Tailwind fade-in transitions smoothly
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        card.classList.remove('scale-95');
                    }, 10);

                    // Auto-dismiss execution starts here (2000ms / 2 seconds)
                    setTimeout(() => {
                        // Start fade-out transitions
                        modal.classList.add('opacity-0');
                        card.classList.add('scale-95');
                        
                        // Hide from DOM completely once transitions finish
                        setTimeout(() => {
                            modal.classList.add('hidden');
                        }, 300); 
                    }, 2000);
                }
            });
        });

        observer.observe(modal, { attributes: true });
    })();
</script>
</section>

<!-- ENGINE INTERACTION LOGIC SCRIPT -->
<script>
document.getElementById('contactForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent instantaneous processing redirect loop

    const form = this;
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const btnSpinner = document.getElementById('btnSpinner');
    const successModal = document.getElementById('successModal');
    const modalCard = document.getElementById('modalCard');

    // 1. Initialize 3-Second UI Loading Matrix
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
    btnText.textContent = "Processing Transmission...";
    btnIcon.classList.add('hidden');
    btnSpinner.classList.remove('hidden');

    // Prepare standard form payloads to pass along quietly to PHP
    const formData = new FormData(form);

    // 2. Perform background server execution submission
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Enforce the 3 seconds load time delay block requested before highlighting successful state
        setTimeout(() => {
            // Reset button structure styles back to normal
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
            btnText.textContent = "Transmit Message";
            btnIcon.classList.remove('hidden');
            btnSpinner.classList.add('hidden');

            // Show Glassmorphic Modal with animated smooth transition fade
            successModal.classList.remove('hidden');
            setTimeout(() => {
                successModal.classList.remove('opacity-0');
                modalCard.classList.remove('scale-95');
            }, 50);

            // 3. Clear all input fields automatically
            form.reset();

            // 4. Automated closing sequence to smoothly hide the modal without clicking
            setTimeout(() => {
                successModal.bg-slate-950/40;
                successModal.classList.add('opacity-0');
                modalCard.classList.add('scale-95');
                setTimeout(() => {
                    successModal.classList.add('hidden');
                }, 300);
            }, 2500); // Closes automatically after 2.5 seconds

        }, 3000); // 3000ms = Exactly 3 Seconds delayed window
    })
    .catch(error => {
        console.error('Transmission Error Node detected:', error);
        
        // Reset button state safely on server communication failures
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
        btnText.textContent = "Transmit Message";
        btnIcon.classList.remove('hidden');
        btnSpinner.classList.add('hidden');
    });
});
</script>

    </main>

    <!-- Footer System Log Signoff -->
    <footer class="border-t border-white/40 glass-nav py-8 px-6 text-center text-xs text-slate-600 relative z-10">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="font-semibold">&copy; 2026 Peter Mayende . All Rights Reserved.</p>
            <p class="font-medium">Engineered for absolute scannable alignment across high performance responsive configurations.</p>
        </div>
    </footer>

    <!-- Script Implementations -->
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        // Toggle mobile sub-navigation links view
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Auto collapse menu stack upon link select
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });

    
    </script>
</body>
</html>