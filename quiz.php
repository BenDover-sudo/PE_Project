<?php
$video_id = $_POST['video_id'] ?? null;

if (!$video_id) {
    die("Missing video ID");
}

$mysqli = new mysqli("localhost", "root", "", "ai_video_app");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get transcript
$stmt = $mysqli->prepare("SELECT content FROM transcripts WHERE video_id = ?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transcript not found.");
}
$row = $result->fetch_assoc();
$transcript = preg_replace('/\(Transcribed by .*?\)/i', '', $row['content']); // remove filler

// Check if quiz exists
$stmt = $mysqli->prepare("SELECT quiz, generated_at FROM quizzes WHERE video_id = ?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $quiz = $row['quiz'];
    $generated_at = $row['generated_at'];
} else {
    // Generate quiz
    $apiKey = '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'; // replace with your OpenRouter key
    $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

    $data = [
        "model" => "mistralai/mistral-7b-instruct",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant that creates multiple-choice quizzes from transcripts."],
            ["role" => "user", "content" => "Generate a multiple-choice quiz based on the transcript. For each question, provide 4 options (a, b, c, d) and clearly mention the correct answer after each question in the format: 'Answer: <correct option>'. Here is the transcript:\n\n" . $transcript]
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
    $quiz = $responseData['choices'][0]['message']['content'] ?? "Failed to generate quiz.";
    $generated_at = date('Y-m-d H:i:s');

    // Store in DB
    $stmt = $mysqli->prepare("INSERT INTO quizzes (video_id, quiz, generated_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quiz = VALUES(quiz), generated_at = VALUES(generated_at)");
    $stmt->bind_param("iss", $video_id, $quiz, $generated_at);
    $stmt->execute();
}

?>

<h2>Quiz for Video ID: <?= htmlspecialchars($video_id) ?></h2>
<p><strong>Transcript:</strong> <?= nl2br(htmlspecialchars($transcript)) ?></p>

<details style="margin-top: 20px;">
    <summary style="font-weight: bold; font-size: 1.1em;">Generated Quiz (<?= $generated_at ?>)</summary>
    <div style="margin-top: 10px;"><?= nl2br(htmlspecialchars($quiz)) ?></div>
</details>
