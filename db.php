<?php
$conn = new mysqli("127.0.0.1", "root", "", "ubuntu_care", 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// "Keep me signed in": restore the session from a saved token
if (session_status() === PHP_SESSION_ACTIVE && !isset($_SESSION["user_id"]) && !empty($_COOKIE["uc_remember"])) {
    $parts = explode(":", $_COOKIE["uc_remember"], 2);
    if (count($parts) === 2) {
        [$selector, $validator] = $parts;
        $stmt = $conn->prepare("SELECT t.token_hash, u.userID, u.name, r.role_name FROM remember_tokens t JOIN users u ON t.userID = u.userID JOIN roles r ON u.roleID = r.roleID WHERE t.selector = ? AND t.expires_at > NOW()");
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row && hash_equals($row["token_hash"], hash("sha256", $validator))) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $row["userID"];
            $_SESSION["name"] = $row["name"];
            $_SESSION["role"] = $row["role_name"];
        }
    }
}