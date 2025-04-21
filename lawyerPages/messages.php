<?php
session_start();
include '../logins/dbcon.php';

$lawyer_id = $_SESSION['user_id'];
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

// Simplified query to get all clients with messages
$query = "SELECT DISTINCT u.id, u.name, u.email
          FROM users u 
          INNER JOIN messages m 
          ON (m.sender_id = u.id OR m.receiver_id = u.id)
          WHERE (m.sender_id = ? OR m.receiver_id = ?)
          AND u.id != ?
          ORDER BY u.name";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $lawyer_id, $lawyer_id, $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/messages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dashboard {
            position: relative;
            padding-bottom: 80px; /* Make room for message form */
        }

        .client-list {
            padding: 20px;
            max-width: 100%;
        }

        .client-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .chat-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
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

        .message {
            max-width: 80%;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 10px;
            position: relative;
        }

        .message.sent {
            margin-left: auto;
            background: #007bff;
            color: white;
            border-radius: 15px 15px 0 15px;
        }

        .message.received {
            margin-right: auto;
            background: #e9ecef;
            color: #212529;
            border-radius: 15px 15px 15px 0;
        }

        .message span {
            display: block;
            font-size: 0.8em;
            margin-top: 5px;
            opacity: 0.7;
        }

        .message-form {
            position: fixed;
            bottom: 0;
            left: 0; /* Sidebar width */
            right: 0;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
        }

        .message-form textarea {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            resize: none;
            min-height: 40px;
            max-height: 100px;
        }

        .message-form button {
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

        .back-button {
            margin-top: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background: #f8f9fa;
        }

        .client-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    
    <?php include_once("../includedFiles/header.php")?>

    <main class="dashboard">
        <header>
            <h3>Messages</h3>
        </header>

        <?php if ($client_id): ?>
            <button class = "back-button" onclick="location.href='messages.php'">← Back to Client List</button>
        <?php endif; ?>

        <!-- Client List -->
        <div class="client-list" <?php echo $client_id ? 'style="display:none"' : ''; ?>>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($client = $result->fetch_assoc()): ?>
                    <div class="client-card">
                        <div class="client-info">
                            <h4><?php echo htmlspecialchars($client['name']); ?></h4>
                            <p><?php echo htmlspecialchars($client['email']); ?></p>
                        </div>
                        <button class = "chat-button" onclick="location.href='messages.php?client_id=<?php echo $client['id']; ?>'">
                            Open chat
                        </button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No conversations found.</p>
            <?php endif; ?>
        </div>

        <!-- Chat Area -->
        <?php if ($client_id): ?>
            <div class="chat-container">
                <div id="chat-messages" class="chat-messages">
                    <?php
                    $chat_query = "SELECT m.*, u.name as sender_name 
                                 FROM messages m 
                                 JOIN users u ON m.sender_id = u.id 
                                 WHERE (sender_id = ? AND receiver_id = ?) 
                                 OR (sender_id = ? AND receiver_id = ?) 
                                 ORDER BY timestamp ASC";
                    
                    $chat_stmt = $conn->prepare($chat_query);
                    $chat_stmt->bind_param("iiii", $lawyer_id, $client_id, $client_id, $lawyer_id);
                    $chat_stmt->execute();
                    $messages = $chat_stmt->get_result();

                    while ($msg = $messages->fetch_assoc()) {
                        $isMyMessage = $msg['sender_id'] == $lawyer_id;
                        echo '<div class="message ' . ($isMyMessage ? 'sent' : 'received') . '">';
                        echo '<p>' . htmlspecialchars($msg['message']) . '</p>';
                        echo '<span>' . date('g:i A', strtotime($msg['timestamp'])) . '</span>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <form id="message-form" class="message-form">
                    <input type="hidden" name="receiver_id" value="<?php echo $client_id; ?>">
                    <textarea name="message" 
                            placeholder="Type a message..." 
                            required
                            onkeydown="if(event.keyCode == 13 && !event.shiftKey) {
                                event.preventDefault();
                                this.form.dispatchEvent(new Event('submit'));
                            }"
                    ></textarea>
                    <button type="submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </main>
    <script src = "../js/menuToggle.js"></script>
    <script>
        const messageForm = document.getElementById('message-form');
        const chatMessages = document.getElementById('chat-messages');

        if (messageForm) {
            // Add this to scroll to bottom initially
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            messageForm.onsubmit = async (e) => {
                e.preventDefault();
                const formData = new FormData(messageForm);
                const textarea = messageForm.querySelector('textarea');
                
                // Don't send if message is empty or only whitespace
                if (!textarea.value.trim()) {
                    return;
                }
                
                try {
                    const response = await fetch('../includes/send_message.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        messageForm.reset();
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            };
        }
    </script>
</body>
</html>
