<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['video_id'])) {
    $video_id = $_POST['video_id'];

    // Delete transcript first (if any)
    $conn->query("DELETE FROM transcripts WHERE video_id = $video_id");

    // Delete video record
    $conn->query("DELETE FROM videos WHERE id = $video_id");

    // Optionally delete the video file from the server
    // Add file deletion logic here if needed

    header("Location: index.php");
    exit();
}
?>
