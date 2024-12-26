<?php
include '../config/db.php';

if (isset($_GET['c'])) {
    $code = $_GET['c'];

    // Use prepared statement to query for the original URL
    $stmt = $conn->prepare("SELECT original_url, expiration_dt, creation_dt FROM urls WHERE short_code = :short_code");
    $stmt->bindParam(':short_code', $code);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // if query returns a result, check expiration date
    if($result){
        $expiration_dt = strtotime($result['expiration_dt']);
        $now = strtotime('NOW');

        if ($expiration_dt > $now) {
            // Redirect to the original URL
            header("Location: " . $result['original_url']);
            exit();
        } elseif($expiration_dt < $now){
            echo "URL has expired.";
        }else {
            echo "Short URL not found!";
        }
    }
} else {
    echo "No short code provided!";
}

