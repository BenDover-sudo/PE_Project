<?php
$video_id = $_POST['video_id'] ?? $_GET['video_id'] ?? null;

if (!$video_id) {
    die("Missing video ID");
}

$mysqli = new mysqli("localhost", "root", "", "ai_video_app");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT content FROM transcripts WHERE video_id = ?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transcript not found.");
}

$row = $result->fetch_assoc();
$transcript = $row['content'];

// Remove filler lines
$transcript = preg_replace('/\(Transcribed by .*?\)/i', '', $transcript);

// Check if summary already exists
$checkStmt = $mysqli->prepare("SELECT summary, generated_at FROM summaries WHERE video_id = ?");
$checkStmt->bind_param("i", $video_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $cached = $checkResult->fetch_assoc();
    $summary = $cached['summary'];
    $timestamp = $cached['generated_at'];
} else {
    // Generate summary using OpenRouter
    $apiKey = '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';
    $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

    $data = [
        "model" => "mistralai/mistral-7b-instruct",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant that summarizes transcripts."],
            ["role" => "user", "content" => "Summarize the following transcript:\n\n" . $transcript]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
    }
    curl_close($ch);

    $responseData = json_decode($response, true);
    $summary = $responseData['choices'][0]['message']['content'] ?? "Failed to generate summary.";
    $timestamp = date('Y-m-d H:i:s');

    // Save summary to DB
    $saveStmt = $mysqli->prepare("INSERT INTO summaries (video_id, summary, generated_at) VALUES (?, ?, ?)");
    $saveStmt->bind_param("iss", $video_id, $summary, $timestamp);
    $saveStmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Summary</title>
    <style>
        details summary {
            cursor: pointer;
            font-weight: bold;
        }
        details summary::-webkit-details-marker {
            display: none;
        }
        details[open] summary::after {
            content: "▲";
            float: right;
        }
        details summary::after {
            content: "▼";
            float: right;
        }
    </style>
</head>
<body>
    <h2>Summary for Video ID: <?= htmlspecialchars($video_id) ?></h2>
    <details>
        <summary>View Transcript</summary>
        <p><?= nl2br(htmlspecialchars($transcript)) ?></p>
    </details>
    <hr>
    <details open>
        <summary>Generated Summary (<?= htmlspecialchars($timestamp) ?>)</summary>
        <p><?= nl2br(htmlspecialchars($summary)) ?></p>
    </details>
    <form method="POST">
        <input type="hidden" name="video_id" value="<?= htmlspecialchars($video_id) ?>">
        <button type="submit">Regenerate Summary</button>
    </form>
</body>
</html>