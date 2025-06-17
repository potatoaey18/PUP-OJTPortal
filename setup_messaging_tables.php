<?php
// Database connection
include 'connection/config.php';

try {
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    echo "<h2>Setting up messaging tables...</h2>";
    
    // Create conversations table
    $sql = "CREATE TABLE IF NOT EXISTS `conversations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `student_id` int(11) NOT NULL,
        `supervisor_id` int(11) NOT NULL,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_conversation` (`student_id`, `supervisor_id`),
        KEY `student_id` (`student_id`),
        KEY `supervisor_id` (`supervisor_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    $conn->exec($sql);
    echo "<p>✅ Created 'conversations' table</p>";
    
    // Create messages table
    $sql = "CREATE TABLE IF NOT EXISTS `messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `conversation_id` int(11) NOT NULL,
        `sender_id` int(11) NOT NULL,
        `sender_type` enum('student','supervisor') NOT NULL,
        `content` text NOT NULL,
        `is_read` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `conversation_id` (`conversation_id`),
        KEY `sender_id` (`sender_id`),
        KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    $conn->exec($sql);
    echo "<p>✅ Created 'messages' table</p>";
    
    // Add foreign key constraint
    try {
        $sql = "ALTER TABLE `messages`
                ADD CONSTRAINT `fk_messages_conversation` 
                FOREIGN KEY (`conversation_id`) 
                REFERENCES `conversations` (`id`) 
                ON DELETE CASCADE";
        $conn->exec($sql);
        echo "<p>✅ Added foreign key constraint to 'messages' table</p>";
    } catch (PDOException $e) {
        // Ignore if foreign key already exists
        if (strpos($e->getMessage(), 'errno: 1553') === false) {
            throw $e;
        }
        echo "<p>ℹ️ Foreign key constraint already exists</p>";
    }
    
    echo "<h3>✅ Database setup completed successfully!</h3>";
    
} catch (PDOException $e) {
    echo "<h3>❌ Error setting up database:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . print_r($e->getTraceAsString(), true) . "</pre>";
}

// Add a button to go back to the messages page
echo '<p><a href="messagestudent1.php" style="display: inline-block; padding: 10px 20px; background-color: #8B0000; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;">Go to Messages</a></p>';
?>
