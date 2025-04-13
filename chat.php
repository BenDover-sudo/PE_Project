<?php
header('Content-Type: application/json');

// Parse raw JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Step 1: Validate input
$video_id = $input['video_id'] ?? null;
$message = $input['message'] ?? null;

if (!$video_id || !$message) {
    echo json_encode(['error' => 'Missing video_id or message']);
    exit;
}

// Step 2: Connect to DB
$mysqli = new mysqli("localhost", "root", "", "ai_video_app");
if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Step 3: Get transcript
$stmt = $mysqli->prepare("SELECT content FROM transcripts WHERE video_id = ?");
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Transcript not found']);
    exit;
}

$row = $result->fetch_assoc();
$transcript = preg_replace('/\(Transcribed by .*?\)/i', '', $row['content']);

// Step 4: Prepare request to OpenRouter
$apiKey = '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';
$apiUrl = "https://openrouter.ai/api/v1/chat/completions";

$data = [
    "model" => "mistralai/mistral-7b-instruct",
    "messages" => [
        ["role" => "system", "content" => "You are a helpful AI assistant who answers questions based on the transcript."],
        ["role" => "user", "content" => "Transcript:\n" . $transcript . "\n\nQuestion:\n" . $message]
    ],
    "temperature" => 0.7
];

// Step 5: Send cURL request
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
    echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// Step 6: Parse and return
$responseData = json_decode($response, true);

if (!isset($responseData['choices'][0]['message']['content'])) {
    echo json_encode(['error' => 'Invalid response from OpenRouter', 'responce_raw' => $response]);
    exit;
}

$ai_reply = $responseData['choices'][0]['message']['content'];
echo json_encode(['response' => $ai_reply]);

file_put_contents("debug_log.txt", $response);

file_put_contents("input_debug.txt", json_encode($_POST));
file_put_contents("php_input_debug.txt", file_get_contents("php://input"));


?>
