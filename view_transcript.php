<?php
$conn = new mysqli("localhost", "root", "", "ai_video_app");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT content FROM transcripts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($content);
    if ($stmt->fetch()) {
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    } else {
        echo "Transcript not found.";
    }
    $stmt->close();
}
?>
