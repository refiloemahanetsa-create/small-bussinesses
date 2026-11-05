<?php
session_start();

// Only logged-in business owners can use the assistant.
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'business_owner'
) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Business Assistant | Small Business Shop</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7f4;
            color: #222;
        }

        header {
            background: #00ad2e;
            color: white;
            padding: 16px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        main {
            max-width: 850px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .chat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 14px rgba(0,0,0,.08);
            overflow: hidden;
        }

        .chat-heading {
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .chat-heading h1 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .controls {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        select, textarea, button {
            font: inherit;
        }

        select {
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        #messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background: #fafcfa;
        }

        .message {
            max-width: 85%;
            padding: 12px 14px;
            margin-bottom: 14px;
            border-radius: 10px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.5;
        }

        .user {
            background: #dcfce4;
            margin-left: auto;
        }

        .assistant {
            background: #e9ecef;
            margin-right: auto;
        }

        .sender {
            display: block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .chat-form {
            display: flex;
            gap: 10px;
            padding: 15px;
            border-top: 1px solid #ddd;
        }

        textarea {
            flex: 1;
            resize: vertical;
            min-height: 48px;
            max-height: 140px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            background: #00ad2e;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 12px 18px;
            cursor: pointer;
        }

        button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .notice {
            color: #555;
            font-size: 13px;
            padding: 0 20px 18px;
        }

        @media (max-width: 550px) {
            .chat-form {
                flex-direction: column;
            }

            .chat-form button {
                width: 100%;
            }

            #messages {
                height: 350px;
            }
        }
    </style>
</head>

<body>

<header>
    <strong>Small Business Shop</strong>
    <a href="business_owner_dashboard.php">Back to Dashboard</a>
</header>

<main>
    <section class="chat-card">
        <div class="chat-heading">
            <h1>AI Business Assistant</h1>
            <p>Ask questions to help you manage and grow your business.</p>
        </div>

        <div class="controls">
            <label for="language"><strong>Choose language:</strong></label>

            <select id="language">
                <option value="English">English</option>
                <option value="Sesotho">Sesotho</option>
            </select>
        </div>

        <div id="messages" aria-live="polite">
            <div class="message assistant">
                <span class="sender">AI Assistant</span>
                Hello! How can I help you improving your business.
            </div>
        </div>

        <form id="chatForm" class="chat-form">
            <textarea
                id="question"
                maxlength="2000"
                placeholder="Type your business question here..."
                required
            ></textarea>

            <button id="sendButton" type="submit">Send</button>
        </form>
    </section>
</main>

<script>
const form = document.getElementById('chatForm');
const questionBox = document.getElementById('question');
const languageSelect = document.getElementById('language');
const messagesBox = document.getElementById('messages');
const sendButton = document.getElementById('sendButton');

// Keep a short conversation history in this browser page.
const history = [];

function addMessage(sender, text) {
    const bubble = document.createElement('div');
    bubble.className = 'message ' + sender;

    const label = document.createElement('span');
    label.className = 'sender';
    label.textContent =
        sender === 'user' ? 'You' : 'AI Assistant';

    const content = document.createElement('div');
    content.textContent = text;

    bubble.appendChild(label);
    bubble.appendChild(content);
    messagesBox.appendChild(bubble);
    messagesBox.scrollTop = messagesBox.scrollHeight;
}

form.addEventListener('submit', async function (event) {
    event.preventDefault();

    const question = questionBox.value.trim();

    if (!question) {
        return;
    }

    addMessage('user', question);
    history.push({ role: 'user', content: question });

    // Send only the last 10 messages to keep requests manageable.
    const conversation = history.slice(-10);

    questionBox.value = '';
    sendButton.disabled = true;
    sendButton.textContent = 'Thinking...';

    try {
        const response = await fetch('ai_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                language: languageSelect.value,
                messages: conversation
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'The AI request failed.');
        }

        addMessage('assistant', data.reply);
        history.push({
            role: 'assistant',
            content: data.reply
        });

    } catch (error) {
        addMessage(
            'assistant',
            'Sorry, I could not answer your question. ' + error.message
        );
    } finally {
        sendButton.disabled = false;
        sendButton.textContent = 'Send';
        questionBox.focus();
    }
});
</script>

</body>
</html>