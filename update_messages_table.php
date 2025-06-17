<?php
include 'connection/config.php';

try {
    // First, drop the foreign key constraint if it exists
    $sql = "
    ALTER TABLE messages 
    DROP FOREIGN KEY IF EXISTS fk_messages_conversation;
    
    -- Modify the sender_type column to include 'coordinator'
    ALTER TABLE messages 
    MODIFY COLUMN sender_type ENUM('student', 'supervisor', 'coordinator') NOT NULL;
    
    -- Re-add the foreign key constraint
    ALTER TABLE messages 
    ADD CONSTRAINT fk_messages_conversation 
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE;
    
    -- Update the conversations table to handle coordinator conversations
    ALTER TABLE conversations
    ADD COLUMN IF NOT EXISTS coordinator_id INT NULL AFTER supervisor_id,
    ADD CONSTRAINT fk_conversations_coordinator 
    FOREIGN KEY (coordinator_id) REFERENCES coordinators_account(id) ON DELETE CASCADE,
    DROP INDEX unique_conversation,
    ADD UNIQUE KEY unique_conversation (student_id, supervisor_id, coordinator_id);
    ";
    
    // Execute the SQL statements
    $conn->exec($sql);
    
    echo "Database schema updated successfully to support coordinator messages.";
    
} catch (PDOException $e) {
    die("Error updating database schema: " . $e->getMessage());
}
?>
