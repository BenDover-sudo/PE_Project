<?php
require 'db_config.php';

$videoId = $_POST['videoId'] ?? null;
$question = $_POST['question'] ?? null;

if (!$videoId || !$question) {
    http_response_code(400);
    echo "Missing video ID or question.";
    exit;
}

// Fetch transcript for the given video
$stmt = $conn->prepare("SELECT content FROM transcripts WHERE video_id = ?");
$stmt->bind_param("i", $videoId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Transcript not found.";
    exit;
}

$transcript = $result->fetch_assoc()['content'];

// Use OpenRouter API
$apiKey = 'YOUR_OPENROUTER_API_KEY';
$url = 'https://openrouter.ai/api/v1/chat/completions';

$data = [
    'model' => 'openai/gpt-3.5-turbo', // or 'mistral' or any cheaper one you prefer
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful tutor answering questions based only on the transcript.'],
        ['role' => 'user', 'content' => "Transcript:\n$transcript\n\nQuestion: $question"]
    ]
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "Error: $err";
} else {
    $json = json_decode($response, true);
    echo $json['choices'][0]['message']['content'] ?? 'No response from AI.';
}
