<?php
include '../../connection/config.php';
session_start();

if (!isset($_SESSION['auth_user']['student_id'])) {
    header("Location: login.php?error=Please log in to access the chat.");
    exit();
}

if (!isset($_POST['userUNIQUEid_receiver'])) {
    echo "Error: No receiver specified";
    exit();
}

$stud_uniqueId_receiver = $_POST['userUNIQUEid_receiver'];
$senderId = $_SESSION['auth_user']['student_uniqueID'];

// Fetch receiver info
$stmt = $conn->prepare("SELECT * FROM students_data WHERE uniqueID = ?");
$stmt->execute([$stud_uniqueId_receiver]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receiver) {
    echo "Error: User not found.";
    exit();
}

// Pagination for message history
$limit = 20;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare("
    SELECT * FROM chat_system 
    WHERE (sender_id = ? AND receiver_id = ?) 
    OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY date_only DESC, time_only DESC 
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $senderId, PDO::PARAM_STR);
$stmt->bindValue(2, $stud_uniqueId_receiver, PDO::PARAM_STR);
$stmt->bindValue(3, $stud_uniqueId_receiver, PDO::PARAM_STR);
$stmt->bindValue(4, $senderId, PDO::PARAM_STR);
$stmt->bindValue(5, $limit, PDO::PARAM_INT);
$stmt->bindValue(6, $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="chat-header">
    <div class="position-relative">
        <img src="<?= htmlspecialchars($receiver['profile_picture'] ?? 'images/placeholder.png') ?>" class="rounded-circle mr-2" alt="User" width="40" height="40">
        <span class="fas fa-circle <?= $receiver['online_offlineStatus'] === 'Online' ? 'chat-online' : 'chat-offline' ?>" style="position: absolute; bottom: 0; right: 0;"></span>
    </div>
    <div class="flex-grow-1">
        <strong><?= htmlspecialchars("{$receiver['first_name']} {$receiver['middle_name']} {$receiver['last_name']}") ?></strong>
        <div class="text-muted small">INSTITUTE OF TECHNOLOGY - PUP PUREZA, MANILA</div>
    </div>
</div>

<div class="chat-messages" id="chatMessages">
    <?php if ($page === 1 && count($messages) === $limit) { ?>
        <button id="loadMore" class="btn btn-link mx-auto d-block mb-3" data-page="2">Load More</button>
    <?php } ?>
    <?php foreach (array_reverse($messages) as $message) { // Reverse to show oldest first
        $messageClass = ($message['sender_id'] === $senderId) ? 'chat-message-right' : 'chat-message-left';
        $content = htmlspecialchars($message['messages'] ?? '');
        if (!empty($message['images']) || !empty($message['documents'])) {
            $filePath = $message['images'] ?? $message['documents'];
            $fileType = $message['images'] ? 'image' : 'document';
            $content .= $fileType === 'image' 
                ? "<br><img src='".htmlspecialchars($filePath)."' alt='Image' style='max-width: 200px; border-radius: 8px;'>"
                : "<br><a href='".htmlspecialchars($filePath)."' target='_blank' class='btn btn-link'>Download {$fileType}</a>";
        }
    ?>
        <div class="<?= $messageClass ?>">
            <div class="chat-message-text">
                <?= $content ?>
                <small class="d-block text-muted"><?= htmlspecialchars("{$message['date_only']} {$message['time_only']}") ?></small>
            </div>
        </div>
    <?php } ?>
</div>

<div class="chat-input">
    <form id="messageForm">
        <div class="input-group">
            <button type="button" data-toggle="modal" data-target="#imageModal" aria-label="Send image"><i class="fas fa-image"></i></button>
            <button type="button" data-toggle="modal" data-target="#documentModal" aria-label="Send document"><i class="fas fa-paperclip"></i></button>
            <textarea class="form-control" name="message" placeholder="Type message here..." aria-label="Message input"></textarea>
            <button type="submit" aria-label="Send message"><i class="fas fa-paper-plane"></i></button>
        </div>
    </form>
</div>

<script>
$('#loadMore').click(function() {
    const page = $(this).data('page');
    $.ajax({
        url: 'stud_messageLIVECHAT.php',
        method: 'POST',
        data: { userUNIQUEid_receiver: '<?= $stud_uniqueId_receiver ?>', page: page },
        success: (response) => {
            const $newContent = $(response).find('#chatMessages').children();
            $('#chatMessages').prepend($newContent);
            if ($newContent.filter('#loadMore').length) {
                $('#loadMore').data('page', page + 1);
            } else {
                $('#loadMore').remove();
            }
        }
    });
});
</script>