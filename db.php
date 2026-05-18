<?php
// db.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// InfinityFree Database Connection Settings
$host   = "sql308.infinityfree.com"; 
$user   = "if0_41945444";      
$pass   = "xhYpcRRABvxT8";          
$dbname = "if0_41945444_bio";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Node Failed: " . $conn->connect_error);
}

/**
 * Global Tracking Matrix Function
 * Logs detailed real-time active visitor traffic metrics, handles session binding,
 * and increments total hit views uniformly on page entry and browser reloads.
 */
function trackLiveVisitor($conn) {
    $session_id = session_id();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // 1. UPDATE REAL-TIME TRAFFIC MATRIX LOG ROW
    // Fixed to match your actual SQL schema: binding session_id and ip_address.
    $stmt = $conn->prepare("INSERT INTO traffic_log (ip_address, session_id, last_activity) 
                            VALUES (?, ?, NOW()) 
                            ON DUPLICATE KEY UPDATE last_activity = NOW(), ip_address = ?");
    if ($stmt) {
        // "sss" means three strings: ip_address, session_id, and the fallback ip_address for the update statement
        $stmt->bind_param("sss", $ip_address, $session_id, $ip_address);
        $stmt->execute();
        $stmt->close();
    }

    // 2. INCREMENT RAW SYSTEM VIEW COUNTER 
    // Triggers cleanly on every single manual frontend browser refresh and unique page request node
    $conn->query("UPDATE site_analytics SET view_count = view_count + 1 WHERE id = 1");
}
?>