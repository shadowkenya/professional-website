<?php
// index.php - Admin Authentication Node
require_once 'db.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

$error = "";
$login_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        
        // Hardcoded bypass checking or proper hash checking verification
        if ($password === 'developer' || password_verify($password, $hashed_password)) { 
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $username;
            $login_success = true; // Flag for firing the modal before redirecting
        } else {
            $error = "Invalid structural credentials.";
        }
    } else {
        $error = "Admin node registry not found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin login</title>
    <link rel="icon" type="image/png" href="https://img.icons8.com/neon/96/shield.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Space Grotesk', sans-serif; 
            overflow: hidden;
        }
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .glass-panel { 
            background: rgba(15, 23, 42, 0.7); 
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08); 
        }
    </style>
</head>
<body class="min-h-screen w-screen flex items-center justify-center p-4 text-white relative">

    <!-- Seamlessly Looped Background Video with hidden controls -->
    <video class="video-bg" autoplay loop muted playsinline>
        <source src="../playy.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Compact Glass Card optimized for all screens -->
    <div class="w-full max-w-sm glass-panel p-6 rounded-2xl shadow-2xl z-10 transition-all duration-300 mx-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black tracking-tight text-cyan-400">Peter Mayende</h2>
      
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500/40 text-red-200 text-xs py-2.5 px-3 rounded-xl mb-4 text-center font-semibold">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" class="space-y-4">
            <div>
                <label class="text-[10px] uppercase tracking-wider font-bold text-slate-300 block mb-1.5">Username</label>
                <input type="text" name="username" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500 transition-all" placeholder="e.g. admin">
            </div>
            <div>
                <label class="text-[10px] uppercase tracking-wider font-bold text-slate-300 block mb-1.5">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500 transition-all" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold tracking-wide transition-all shadow-lg shadow-cyan-900/40 mt-2">
                LOGIN
            </button>
            <!-- Return to Home Button -->
    <a href="/" class="block w-full py-3 rounded-xl bg-gradient-to-r from-slate-700 to-zinc-700 hover:from-slate-600 hover:to-zinc-600 text-white text-xs font-bold tracking-wide transition-all shadow-lg shadow-slate-900/40 mt-3 text-center uppercase">
        Return to Home
    </a>
        </form>
    </div>

    <!-- Success Feedback Modal Layer -->
    <?php if ($login_success): ?>
    <div id="successModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="glass-panel max-w-xs w-full p-6 rounded-2xl text-center border border-emerald-500/30 shadow-2xl transform scale-100 transition-all">
            <div class="w-12 h-12 bg-emerald-500/20 border border-emerald-500/40 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-emerald-400 mb-1">Access Granted</h3>
            <p class="text-slate-300 text-xs mb-3">Node handshake initialized successfully.</p>
            <div class="text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 text-cyan-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Redirecting to dashboard...
            </div>
        </div>
    </div>

    <script>
        // Hold the user visually on the modal for 2.5 seconds before launching dashboard
        setTimeout(function() {
            window.location.href = "dashboard.php";
        }, 2500);
    </script>
    <?php endif; ?>

</body>
</html>