<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$page_url = $_SERVER['REQUEST_URI'] ?? '/index.php';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$session_id = session_id();

// Clean page url (remove project/ subdirectory or query page values if needed)
$page_url = str_ireplace('/project/', '/', $page_url);

// Check if we already logged this page visit for this session in the last 15 minutes
$time_threshold = date('Y-m-d H:i:s', time() - 900);
$stmt = $pdo->prepare("SELECT id FROM page_visits WHERE session_id = ? AND page_url = ? AND last_activity >= ? LIMIT 1");
$stmt->execute([$session_id, $page_url, $time_threshold]);
$existing = $stmt->fetch();

if ($existing) {
    // Just update the last activity time to track real-time active state
    $stmt = $pdo->prepare("UPDATE page_visits SET last_activity = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$existing['id']]);
} else {
    // Helper function for HTTP requests with timeout fallback
    function tracker_http_get($url, $timeout = 2) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => 1
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } else {
            // Fallback: use file_get_contents with stream context
            $context = stream_context_create([
                'http' => [
                    'timeout' => $timeout,
                    'ignore_errors' => true
                ]
            ]);
            return @file_get_contents($url, false, $context);
        }
    }

    // Get geolocation
    $country = 'Unknown';
    $city = 'Unknown';

    $is_local = ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0);
    $public_resolved = false;

    if ($is_local) {
        // Query api.ipify.org to get the real public IP of the machine
        try {
            $resolved_ip = tracker_http_get("https://api.ipify.org");
            if ($resolved_ip && filter_var(trim($resolved_ip), FILTER_VALIDATE_IP)) {
                $ip = trim($resolved_ip);
                $public_resolved = true;
            }
        } catch (Exception $e) {
            // ignore
        }
    }

    if ($is_local && !$public_resolved) {
        // Localhost/Private IP fallback when offline: Assign a random location for demonstration
        $locations = [
            ['Bangladesh', 'Dhaka'],
            ['Bangladesh', 'Chittagong'],
            ['Bangladesh', 'Sylhet'],
            ['United States', 'New York'],
            ['United States', 'San Francisco'],
            ['United Kingdom', 'London'],
            ['Japan', 'Tokyo'],
            ['Germany', 'Berlin'],
            ['Australia', 'Sydney']
        ];
        $rand_loc = $locations[array_rand($locations)];
        $country = $rand_loc[0];
        $city = $rand_loc[1];
    } else {
        // Public IP: Lookup geolocation via ip-api.com
        try {
            $response = tracker_http_get("http://ip-api.com/json/" . urlencode($ip));
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    $country = $data['country'] ?? 'Unknown';
                    $city = $data['city'] ?? 'Unknown';
                }
            }
        } catch (Exception $e) {
            // ignore
        }
    }

    $stmt = $pdo->prepare("INSERT INTO page_visits (page_url, ip_address, country, city, user_agent, session_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$page_url, $ip, $country, $city, $user_agent, $session_id]);
}
?>
