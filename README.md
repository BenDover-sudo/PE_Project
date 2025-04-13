# AI Video Assistant 🎥🤖

An intelligent video platform that allows users to:
- Upload videos with transcripts
- Generate summaries and quizzes using AI
- Ask transcript-related questions via chatbot

## 🚀 Features

- 🧠 Chat with AI about the video transcript
- 📄 Upload and view text transcripts (.txt)
- ✍️ Auto-generate summaries and quizzes
- 🗑️ Delete transcripts or videos
- 🔒 All data stored locally with PHP & MySQL backend

## 📦 Tech Stack

- **Frontend:** HTML, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **AI:** OpenRouter API using Mistral model
- **Style:** Dark-themed

Tool	Purpose
PHP	Backend scripting
MySQL	Database for storing video & transcript info
JavaScript	Frontend interactivity (chat, toggles)
OpenRouter API	AI chatbot using models like mistral-7b-instruct
XAMPP/WAMP	Local server environment for running PHP + MySQL
cURL	For making API requests to OpenRouter

1. 📦 Install XAMPP (or WAMP)
Download XAMPP from: https://www.apachefriends.org

Install and launch the XAMPP Control Panel

Start both Apache and MySQL services

2. 📁 Set Up Project Files
Clone or download this repository

Move the entire project folder to the following location:


Copy Edit
C:/xampp/htdocs/
Example: If your project folder is named ai-video-assistant, it will be accessible at


Copy Edit
http://localhost/ai-video-assistant
3. 🗄️ Set Up MySQL Database
Open phpMyAdmin in your browser

Create a new database named:


Copy Edit
ai_video_app
Inside the new database, run the following SQL to create the transcripts table:

sql
Copy Edit
CREATE TABLE transcripts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    content TEXT NOT NULL
);
(Optional) If you are managing videos via a table, create a videos table:

sql
Copy Edit
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

4. 🔐 Add Your OpenRouter API Key
 
Open the chat.php file
Locate the following line:

php
Copy Edit
$apiKey = 'sk-or-your-key-here';
Replace 'sk-or-your-key-here' with your actual API key from
https://openrouter.ai/keys

5. 🚀 Run the Project
Go to your browser and open:

Copy Edit
http://localhost/ai-video-assistant/index.php
Upload a video and its transcript

Use the AI features (chat, summary, quiz) to test functionality
