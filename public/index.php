<?php 
include_once '../config/db.php';

function generateShortCode($length = 6) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

if (isset($_POST['submit'])) {

    $original_url = $_POST['url'];
    $short_code = generateShortCode();

    // Use prepared statement to check if the URL already exists
    $stmt = $conn->prepare("SELECT * FROM urls WHERE original_url = :url");
    $stmt->bindParam(':url', $original_url);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // If URL exists, reuse the existing short code
        $short_code = $result['short_code'];
    } else {
        // Ensure the generated short code is unique
        do {
            $short_code = generateShortCode();
            $stmt = $conn->prepare("SELECT * FROM urls WHERE short_code = :short_code");
            $stmt->bindParam(':short_code', $short_code);
            $stmt->execute();
        } while ($stmt->rowCount() > 0);

        // Insert the new URL and short code into the database
        $stmt = $conn->prepare("INSERT INTO urls (original_url, short_code) VALUES (:url, :short_code)");
        $stmt->bindParam(':url', $original_url);
        $stmt->bindParam(':short_code', $short_code);
        $stmt->execute();
    }

    $short_url = "http://192.168.64.11/url-shortener/redirect.php?c=$short_code";
    echo "Shortened URL: <a href='$short_url'>$short_url</a>";
}

include_once '../views/header.php';
?>

<body>
    <h2>Enter a URL to shorten:</h2>
    <form method="post" action="">
        <input type="url" name="url" required placeholder="Enter a long URL" style="width: 300px;">
        <button type="submit" name="submit">Shorten</button>
    </form>
</body>

<?php include_once '../views/footer.php' ?>
