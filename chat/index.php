<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.html");
    exit();
}
$currentUser = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Chat</title>
    <style>
        body {
            background-color: #121212;
            color: #ccc;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #chat {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background-color: #1e1e1e;
            box-sizing: border-box;
        }
        .message {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .username {
            color: #6cc9ff;
            font-weight: bold;
            cursor: pointer;
        }
        .msg-text {
            color: #32cd32;
        }
        .time {
            color: #888;
            font-size: 0.8em;
            margin-left: 6px;
        }
        #sendForm {
            display: flex;
            padding: 10px;
            background-color: #222;
        }
        #messageInput {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #333;
            color: #eee;
            font-size: 1em;
        }
        #sendBtn {
            margin-left: 8px;
            padding: 8px 16px;
            background-color: #6cc9ff;
            border: none;
            border-radius: 4px;
            color: #121212;
            font-weight: bold;
            cursor: pointer;
        }
        #sendBtn:hover {
            background-color: #4aa8ff;
        }
        #logoutBtn {
            background: none;
            border: none;
            color: #6cc9ff;
            font-weight: bold;
            cursor: pointer;
            padding: 10px;
            font-size: 1em;
            align-self: flex-end;
        }
        #logoutBtn:hover {
            color: #4aa8ff;
        }
        /* Özel sohbet penceresi */
        .private-chat {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 300px;
            height: 400px;
            background-color: #222;
            border: 1px solid #555;
            display: flex;
            flex-direction: column;
            margin: 10px;
            box-shadow: 0 0 10px #000;
        }
        .private-chat-header {
            background-color: #333;
            padding: 8px;
            color: #6cc9ff;
            font-weight: bold;
            cursor: default;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .private-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            color: #eee;
        }
        .private-chat-form {
            display: flex;
            padding: 8px;
            background-color: #111;
        }
        .private-chat-input {
            flex: 1;
            padding: 6px;
            border: none;
            border-radius: 4px;
            background-color: #333;
            color: #eee;
            font-size: 0.9em;
        }
        .private-chat-send-btn {
            margin-left: 6px;
            padding: 6px 12px;
            background-color: #6cc9ff;
            border: none;
            border-radius: 4px;
            color: #121212;
            font-weight: bold;
            cursor: pointer;
        }
        .private-chat-send-btn:hover {
            background-color: #4aa8ff;
        }
        .close-btn {
            background: none;
            border: none;
            color: #6cc9ff;
            cursor: pointer;
            font-size: 1.2em;
            font-weight: bold;
            line-height: 1;
        }
    </style>
</head>
<body>
    <button id="logoutBtn" onclick="location.href='logout.php'">Çıkış Yap</button>
    <div id="chat"></div>
    <form id="sendForm">
        <input type="text" id="messageInput" autocomplete="off" placeholder="Mesajınızı yazın..." required />
        <button type="submit" id="sendBtn">Gönder</button>
    </form>

    <script>
        const currentUser = "<?php echo addslashes($currentUser); ?>";
        const chatDiv = document.getElementById('chat');
        const form = document.getElementById('sendForm');
        const input = document.getElementById('messageInput');

        // Genel sohbet mesajlarını alır ve gösterir
        function fetchMessages() {
            fetch('fetch.php')
                .then(res => res.text())
                .then(data => {
                    chatDiv.innerHTML = data;
                    chatDiv.scrollTop = chatDiv.scrollHeight;
                    attachUsernameClicks();
                });
        }

        // Mesaj gönderme (genel sohbet)
        form.addEventListener('submit', e => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;
            fetch('send.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'message=' + encodeURIComponent(message) + '&to=ALL'
            })
            .then(res => res.text())
            .then(() => {
                input.value = '';
                fetchMessages();
            });
        });

        // Kullanıcı adına çift tıklayınca özel sohbet aç
        function attachUsernameClicks() {
            document.querySelectorAll('.username').forEach(el => {
                el.ondblclick = () => {
                    const username = el.dataset.username;
                    if (username && username !== currentUser) {
                        openPrivateChat(username);
                    }
                }
            });
        }

        // Açık olan özel sohbet pencereleri
        const privateChats = {};

        function openPrivateChat(username) {
            if (privateChats[username]) return; // Zaten açık

            // Create container div
            const chatBox = document.createElement('div');
            chatBox.className = 'private-chat';
            chatBox.id = 'private-chat-' + username;

            // Header
            const header = document.createElement('div');
            header.className = 'private-chat-header';
            header.textContent = username;

            // Kapatma butonu
            const closeBtn = document.createElement('button');
            closeBtn.className = 'close-btn';
            closeBtn.textContent = '×';
            closeBtn.onclick = () => {
                clearInterval(privateChats[username].interval);
                delete privateChats[username];
                chatBox.remove();
            };
            header.appendChild(closeBtn);
            chatBox.appendChild(header);

            // Mesajlar container
            const messagesDiv = document.createElement('div');
            messagesDiv.className = 'private-chat-messages';
            chatBox.appendChild(messagesDiv);

            // Form
            const form = document.createElement('form');
            form.className = 'private-chat-form';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'private-chat-input';
            input.placeholder = 'Mesaj yaz...';
            input.autocomplete = 'off';
            input.required = true;

            const sendBtn = document.createElement('button');
            sendBtn.type = 'submit';
            sendBtn.className = 'private-chat-send-btn';
            sendBtn.textContent = 'Gönder';

            form.appendChild(input);
            form.appendChild(sendBtn);
            chatBox.appendChild(form);

            document.body.appendChild(chatBox);

            // Özel mesajları periyodik çekme
            function fetchPrivateMessages() {
                fetch('fetch_private.php?user=' + encodeURIComponent(username))
                    .then(res => res.text())
                    .then(data => {
                        messagesDiv.innerHTML = data;
                        messagesDiv.scrollTop = messagesDiv.scrollHeight;
                    });
            }

            fetchPrivateMessages();
            const interval = setInterval(fetchPrivateMessages, 1000);

            form.onsubmit = e => {
                e.preventDefault();
                const msg = input.value.trim();
                if (!msg) return;
                fetch('send.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'message=' + encodeURIComponent(msg) + '&to=' + encodeURIComponent(username)
                })
                .then(res => res.text())
                .then(() => {
                    input.value = '';
                    fetchPrivateMessages();
                });
            };

            privateChats[username] = {interval};
        }

        fetchMessages();
        setInterval(fetchMessages, 2000);
    </script>
</body>
</html>
