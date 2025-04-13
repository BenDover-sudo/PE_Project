<?php
include 'db_config.php';
if ($_FILES['video']['error'] == 0) {
    $target = 'uploads/' . basename($_FILES['video']['name']);
    if (move_uploaded_file($_FILES['video']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO videos (filename) VALUES (?)");
        $stmt->bind_param("s", $target);
        $stmt->execute();
        header("Location: index.php");
    } else {
        echo "Upload failed.";
    }
}
?>