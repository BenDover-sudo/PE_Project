<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ai_video_app"; // Change this to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for upload and read contents
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["transcript"])) {
    $video_id = $_POST["video_id"];
    $file_tmp_path = $_FILES["transcript"]["tmp_name"];

    // Read the file content
    $transcript_content = file_get_contents($file_tmp_path);

    if ($transcript_content === false) {
        die("Failed to read the uploaded file.");
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO transcripts (video_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $video_id, $transcript_content);
    
    if ($stmt->execute()) {
        header("Location: index.php"); // or wherever your main page is
        exit;
        } else {
        echo "Database error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
