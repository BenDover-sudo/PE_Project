<?php
$conn = new mysqli("localhost", "root", "", "ai_video_app"); // Change this to your DB name

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM transcripts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting transcript.";
    }
    $stmt->close();
}
?>
