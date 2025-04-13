<?php
require 'db_config.php';
session_start();

if (!isset($_SESSION['summaryData'])) $_SESSION['summaryData'] = [];
if (!isset($_SESSION['quizData'])) $_SESSION['quizData'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['generate_summary']) && isset($_POST['video_id'])) {
        $videoId = $_POST['video_id'];
        ob_start();
        include 'summary.php';
        $_SESSION['summaryData'][$videoId] = ob_get_clean();
    }

    if (!empty($_POST['generate_quiz']) && isset($_POST['video_id'])) {
        $videoId = $_POST['video_id'];
        ob_start();
        include 'quiz.php';
        $_SESSION['quizData'][$videoId] = ob_get_clean();
    }
}

$summaryData = $_SESSION['summaryData'];
$quizData = $_SESSION['quizData'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>AI Video Doubt Solver</title>
    <style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #000; /* Set entire background to black */
    color: #fff; /* Default text color white */
    margin: 0;
    padding: 20px;
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #ffcc00;
  }

  form {
    background: #1a1a1a;
    color: #fff;
    padding: 20px;
    max-width: 600px;
    margin: 0 auto 40px auto;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255,255,255,0.1);
    text-align: center;
  }

  input[type="file"] {
    padding: 10px;
    margin-bottom: 10px;
    color: #fff;
    background-color: #333;
    border: 1px solid #555;
  }

  button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #0056b3;
  }

  .video-box {
    background: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    margin: 20px auto;
    max-width: 600px;
    box-shadow: 0 0 10px rgba(255,255,255,0.05);
  }

  video {
    width: 100%;
    max-height: 350px;
    border-radius: 8px;
    margin-bottom: 15px;
  }

  .chat-box {
    margin-top: 10px;
  }

  .chat-messages {
    border: 1px solid #444;
    background-color: #222;
    color: #eee;
    border-radius: 6px;
    padding: 10px;
    height: 120px;
    overflow-y: auto;
    margin-bottom: 10px;
    font-size: 14px;
  }

  .chat-box input[type="text"] {
    width: 70%;
    padding: 8px;
    border: 1px solid #555;
    background-color: #333;
    color: #fff;
    border-radius: 6px;
  }

  .chat-box button {
    padding: 8px 12px;
    margin-left: 8px;
  }

  .header {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  background-color: black;
  padding: 10px 20px;
}

.categories {
  justify-self: start;
}

.categories select {
  background-color: #e68e1cff;
  color: black;
  font-size: 16px;
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  appearance: none; /* removes default arrow */
  -webkit-appearance: none;
  -moz-appearance: none;
  cursor: pointer;
}

.categories select:focus {
  outline: none;
}

.vu-title {
  font-size: 32px;
  text-align: center;
  margin: 20px 0;
  font-weight: bold;
  font-family: Arial, sans-serif;
}

.vish {
  color: white;
}

.uni {
  background-color: #e68e1cff;
  color: black;
  padding: 4px 10px;
  margin-left: 5px;
  border-radius: 6px;
}

a, .transcript-btn {
  background-color: #ffcc00;
  color: black;
  padding: 6px 12px;
  margin: 5px;
  border-radius: 6px;
  text-decoration: none;
  display: inline-block;
  font-weight: bold;
  transition: 0.3s;
}

.transcript-btn:hover, a:hover {
  background-color: #e6b800;
}

.upload-form input[type="file"] {
  margin: 10px 0;
  padding: 10px;
  background-color: #222;
  border: 1px solid #444;
  color: #eee;
}

.upload-form button, .danger-btn {
  background-color: #d9534f;
  color: white;
  padding: 10px 20px;
  border: none;
  margin-top: 10px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.upload-form button:hover, .danger-btn:hover {
  background-color: #c9302c;
}

.danger-btn.small {
  padding: 10px 10px;
  font-size: 14px;
  margin-bottom: 10px;
  vertical-align: middle;
  background-color: #ff4444;
}

.danger-btn.small:hover {
  background-color: #cc0000;
}

.result-box {
    background: #1e1e1e;
    color: #f1f1f1;
    padding: 10px;
    margin-top: 10px;
    border-radius: 8px;
    font-size: 14px;
}

.result-header {
    font-weight: bold;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.result-content {
    padding-top: 5px;
}
.chat-section {
  margin-top: 15px;
  background-color: #1f1f1f;
  padding: 15px;
  border-radius: 8px;
}
.chat-log {
  max-height: 200px;
  overflow-y: auto;
  background-color: #2b2b2b;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
  font-size: 14px;
}
.chat-section input[type="text"] {
  width: 70%;
  padding: 8px;
  border: none;
  border-radius: 4px;
}
.chat-section button {
  padding: 8px 12px;
  margin-left: 10px;
  border: none;
  background-color: #ff5c5c;
  color: white;
  border-radius: 4px;
  cursor: pointer;
}
.chat-section button:hover {
  background-color: #e74c3c;
}


  </style>

<script>
  function toggleBox(header) {
    const content = header.nextElementSibling;
    const arrow = header.querySelector(".arrow");
    if (content.style.display === "none" || !content.style.display) {
      content.style.display = "block";
      arrow.innerText = "↑";
    } else {
      content.style.display = "none";
      arrow.innerText = "↓";
    }
  }

  function sendMessage(videoId) {
    const input = document.getElementById(`chat-input-${videoId}`);
    const chatLog = document.getElementById(`chat-log-${videoId}`);
    const userMessage = input.value.trim();

    if (!userMessage) return;

    // Add user message to chat
    chatLog.innerHTML += `<div><strong>You:</strong> ${userMessage}</div>`;
    input.value = '';

    fetch('chat.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        video_id: videoId,
        message: userMessage
      })
    })
    .then(response => response.json())
    .then(data => {
      const botReply = data.response || 'No response.';
      chatLog.innerHTML += `<div><strong>AI:</strong> ${botReply}</div>`;
      chatLog.scrollTop = chatLog.scrollHeight;
    })
    .catch(error => {
      console.error('Chat Error:', error);
      chatLog.innerHTML += `<div style="color:red;"><strong>Error:</strong> Failed to get response.</div>`;
    });
    console.log('Sending:', { videoId, userMessage });
  }
</script>


</head>
<body>

<div class="header">
  <div class="categories">
    <select>
      <option disabled selected>Categories</option>
      <option>Computer Science</option>
      <option>Electronics</option>
      <option>Mechanical</option>
      <option>Civil</option>
      <option>AI & ML</option>
    </select>
  </div>
  <h1 class="vu-title">
    <span class="vish">Vishwakarma</span><span class="uni">University</span>
  </h1>
</div>

<h2>Upload Video</h2>
<form action="upload.php" method="POST" enctype="multipart/form-data">
  <input type="file" name="video" accept="video/*" required><br>
  <button type="submit">Upload</button>
</form>

<h2>Latest Videos</h2>
<?php
$result = $conn->query("SELECT * FROM videos ORDER BY uploaded_at DESC");
while ($row = $result->fetch_assoc()) {
    echo '<div class="video-box">';
    echo '<video controls src="' . $row['filename'] . '"></video>';

    $video_id = $row['id'];
    $check_query = "SELECT * FROM transcripts WHERE video_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $video_id);
    $check_stmt->execute();
    $transcript_result = $check_stmt->get_result();

    if ($transcript_result->num_rows > 0) {
        $transcript = $transcript_result->fetch_assoc();
        echo "<p><strong>Transcript uploaded.</strong></p>";
        echo '<a class="transcript-btn" href="view_transcript.php?id=' . $transcript['id'] . '" target="_blank">View Transcript</a> | ';
        echo '<a class="transcript-btn" href="delete_transcript.php?id=' . $transcript['id'] . '" onclick="return confirm(\'Are you sure you want to delete the transcript?\')">Delete Transcript</a>';

        echo '<div class="chat-section">';
        echo '<h3>Ask a Doubt (Transcript-based)</h3>';
        echo '<div class="chat-log" id="chat-log-' . $video_id . '"></div>';
        echo '<input type="text" id="chat-input-' . $video_id . '" placeholder="Ask a question..." />';
        echo '<button onclick="sendMessage(' . $video_id . ')">Send</button>';
        echo '</div>';
    } else {
        echo '<form class="upload-form" action="upload_transcript.php" method="POST" enctype="multipart/form-data">';
        echo '<input type="hidden" name="video_id" value="' . $row['id'] . '">';
        echo '<input type="file" name="transcript" accept=".txt" required>';
        echo '<button type="submit">Upload Transcript</button>';
        echo '</form>';
    }

    // Generate Summary button
    echo '<form action="" method="POST" style="display:inline-block; margin-right:10px;">';
    echo '<input type="hidden" name="video_id" value="' . $row['id'] . '">';
    echo '<input type="hidden" name="generate_summary" value="1">';
    echo '<button type="submit" class="transcript-btn">Generate Summary</button>';
    echo '</form>';

    // Generate Quiz button
    echo '<form action="" method="POST" style="display:inline-block;">';
    echo '<input type="hidden" name="video_id" value="' . $row['id'] . '">';
    echo '<input type="hidden" name="generate_quiz" value="1">';
    echo '<button type="submit" class="transcript-btn">Generate Quiz</button>';
    echo '</form>';

    // Display summary
    if (isset($summaryData[$row['id']])) {
        echo '<div class="result-box">';
        echo '<div class="result-header" onclick="toggleBox(this)">Summary <span class="arrow">↑</span></div>';
        echo '<div class="result-content">' . nl2br($summaryData[$row['id']]) . '</div>';
        echo '</div>';
    }

    // Display quiz
    if (isset($quizData[$row['id']])) {
        echo '<div class="result-box">';
        echo '<div class="result-header" onclick="toggleBox(this)">Quiz <span class="arrow">↑</span></div>';
        echo '<div class="result-content">' . nl2br($quizData[$row['id']]) . '</div>';
        echo '</div>';
    }

    // Delete video button
    echo '<form action="delete_video.php" method="POST" style="display:inline;">';
    echo '<input type="hidden" name="video_id" value="' . $row['id'] . '">';
    echo '<button class="danger-btn small" type="submit" onclick="return confirm(\'Are you sure you want to delete this video?\')">Delete Video</button>';
    echo '</form>';

    echo '</div>';
}
?>

</body>
</html>
