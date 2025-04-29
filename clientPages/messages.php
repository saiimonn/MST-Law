<?php 
session_start();
if (!isset($_SESSION["user_name"])) {
    header("Location: ../logins/login.php");
    exit();
}

require_once("../includes/db_connection.php");

// Get only attorneys with confirmed appointments
$attorney_query = "SELECT DISTINCT u.id, u.name, u.email 
                  FROM users u 
                  JOIN appointments a ON u.id = a.attorney_id
                  WHERE a.user_id = ? 
                  AND a.status = 'confirmed'
                  AND u.role = 'Lawyer'";

$stmt = $conn->prepare($attorney_query);
$stmt->execute([$_SESSION['user_id']]);
$attorneys = $stmt->fetchAll(PDO::FETCH_ASSOC);

$attorney_id = isset($_GET['attorney_id']) ? intval($_GET['attorney_id']) : 0;

if ($attorney_id) {
    // Verify if there's a confirmed appointment
    $appointment_check = "SELECT COUNT(*) FROM appointments 
                         WHERE user_id = ? AND attorney_id = ? 
                         AND status = 'confirmed'";
    $check_stmt = $conn->prepare($appointment_check);
    $check_stmt->execute([$_SESSION['user_id'], $attorney_id]);
    $has_appointment = $check_stmt->fetchColumn() > 0;

    if (!$has_appointment) {
        // Redirect if no confirmed appointment exists
        header("Location: messages.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MST LAW</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/messages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dashboard {
            position: relative;
            padding-bottom: 80px; /* Make room for message form */
        }

        .attorney-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
            max-width: 100%;
        }

        .attorney-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
        }

        .attorney-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .attorney-info h4 {
            margin: 0;
            color: #333;
        }

        .attorney-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.9em;
        }

        .chat-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .chat-button:hover {
            background: #0056b3;
        }

        .back-button {
            background: whitesmoke;
            border: none;
            margin-top: 15px;
            cursor: pointer;
            display: <?php echo $attorney_id ? 'block' : 'none'; ?>;
        }

        .chat-container {
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            margin-bottom: 20px;
        }

        .chat-input {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
        }

        .chat-input textarea {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            resize: none;
            min-height: 40px;
            max-height: 100px;
        }

        .chat-input button {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <?php include_once("../includedFiles/header.php")?>

    <main class="dashboard">
        <header>
            <h3>Messages</h3>
        </header>

        <?php if ($attorney_id): ?>
            <button class="back-button" onclick="location.href='messages.php'">
                ← Back to Attorney List
            </button>
        <?php endif; ?>

        <div class="attorney-list" <?php echo $attorney_id ? 'style="display:none"' : ''; ?>>
            <?php if (!empty($attorneys)): ?>
                <?php foreach($attorneys as $attorney): ?>
                    <div class="attorney-card">
                        <div class="attorney-info">
                            <h4><?php echo htmlspecialchars($attorney['name']); ?></h4>
                            <p><?php echo htmlspecialchars($attorney['email']); ?></p>
                        </div>
                        <button class="chat-button" onclick="location.href='messages.php?attorney_id=<?php echo $attorney['id']; ?>'">
                            Open Chat
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No attorneys available.</p>
            <?php endif; ?>
        </div>

        <div class="chat-container" <?php echo !$attorney_id ? 'style="display:none"' : ''; ?>>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            <form class="chat-input" id="message-form">
                <input type="hidden" name="receiver_id" value="<?php echo $attorney_id; ?>">
                <textarea 
                    name="message" 
                    placeholder="Type a message..." 
                    required
                    rows="1"
                    onkeydown="if(event.keyCode == 13 && !event.shiftKey){event.preventDefault(); this.form.dispatchEvent(new Event('submit'));}"
                ></textarea>
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </main>

    <script src = "../js/menuToggle.js"></script>
    <script>
        const chatMessages = document.getElementById('chat-messages');
        const messageForm = document.getElementById('message-form');
        const receiverId = <?php echo $attorney_id ?: 'null'; ?>;
        const hasAppointment = <?php echo isset($has_appointment) && $has_appointment ? 'true' : 'false'; ?>;
        let lastMessageId = 0;

        if(messageForm) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        if (receiverId && hasAppointment) {
            console.log('Chat initialized with receiver ID:', receiverId);
            console.log('Current user ID:', <?php echo $_SESSION['user_id']; ?>);

            function loadMessages() {
                console.log('Fetching messages...');
                fetch(`../includes/get_messages.php?receiver_id=${receiverId}&last_id=${lastMessageId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Received messages:', data);
                        if (data.error) {
                            console.error('Error:', data.message);
                            return;
                        }
                        
                        // Clear existing messages if this is the first load
                        if (lastMessageId === 0) {
                            chatMessages.innerHTML = '';
                        }

                        data.forEach(msg => {
                            // Only add message if it's new
                            if (msg.id > lastMessageId) {
                                const messageDiv = document.createElement('div');
                                messageDiv.className = `message ${msg.sender_id == <?php echo $_SESSION['user_id']; ?> ? 'sent' : 'received'}`;
                                messageDiv.innerHTML = `
                                    <div class="message-content">${msg.message}</div>
                                    <div class="message-time">${new Date(msg.timestamp).toLocaleTimeString()}</div>
                                `;
                                chatMessages.appendChild(messageDiv);
                                lastMessageId = msg.id;
                            }
                        });

                        // Scroll to bottom only if user is already at bottom or this is first load
                        const shouldScroll = lastMessageId === 0 || 
                            (chatMessages.scrollHeight - chatMessages.scrollTop === chatMessages.clientHeight);
                        
                        if (shouldScroll) {
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!hasAppointment) {
                    alert('You can only message attorneys with confirmed appointments.');
                    return;
                }
                const formData = new FormData(this);
                const submitButton = this.querySelector('button');
                const textarea = this.querySelector('textarea');
                
                // Disable form while sending
                submitButton.disabled = true;
                textarea.disabled = true;

                fetch('../includes/send_message.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        this.reset();
                        loadMessages();
                    } else {
                        alert('Failed to send message: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to send message');
                })
                .finally(() => {
                    // Re-enable form
                    submitButton.disabled = false;
                    textarea.disabled = false;
                    textarea.focus();
                });
            });

            // Auto-resize textarea
            const textarea = messageForm.querySelector('textarea');
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Initial message load and periodic refresh
            loadMessages();
            setInterval(loadMessages, 2000);
        } else if (receiverId && !hasAppointment) {
            chatMessages.innerHTML = '<div class="system-message">You can only message attorneys with confirmed appointments.</div>';
            messageForm.style.display = 'none';
        }
    </script>
</body>
</html>
