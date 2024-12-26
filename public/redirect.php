<?php
include '../config/db.php';

if (isset($_GET['c'])) {
    $code = $_GET['c'];

    error_log($code);

    // Use prepared statement to query for the original URL
    $stmt = $conn->prepare("SELECT original_url FROM urls WHERE short_code = :short_code");
    $stmt->bindParam(':short_code', $code);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Redirect to the original URL
        header("Location: " . $result['original_url']);
        exit();
    } else {
        echo "Short URL not found!";
    }
} else {
    echo "No short code provided!";
}

