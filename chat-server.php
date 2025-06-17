<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/connection/config.php';

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!isset($data['action'])) return;

        switch ($data['action']) {
            case 'login':
                $this->users[$data['userId']] = $from;
                echo "User {$data['userId']} logged in\n";
                break;
            case 'message':
                $this->sendMessage($data['senderId'], $data['receiverId'], $data['message']);
                break;
            case 'fileShared':
                $this->broadcastFileShare($data['senderId'], $data['receiverId'], $data['fileType'], $data['filePath']);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        foreach ($this->users as $userId => $client) {
            if ($client === $conn) {
                unset($this->users[$userId]);
                echo "User $userId disconnected\n";
                break;
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function sendMessage($senderId, $receiverId, $message) {
        global $conn; // Your PDO connection from config.php
        $timestamp = date('Y-m-d H:i:s');
        $data = [
            'senderId' => $senderId,
            'message' => $message,
            'timestamp' => $timestamp
        ];

        // Send to receiver
        if (isset($this->users[$receiverId])) {
            $this->users[$receiverId]->send(json_encode($data));
        }
        // Send to sender for confirmation
        if (isset($this->users[$senderId])) {
            $this->users[$senderId]->send(json_encode($data));
        }

        // Save to database
        $stmt = $conn->prepare("INSERT INTO chat_system (sender_id, receiver_id, messages, date_only, time_only) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$senderId, $receiverId, $message, date('Y-m-d'), date('H:i:s')]);
    }

    public function broadcastFileShare($senderId, $receiverId, $fileType, $filePath) {
        $data = [
            'action' => 'fileShared',
            'senderId' => $senderId,
            'fileType' => $fileType,
            'filePath' => $filePath,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (isset($this->users[$receiverId])) {
            $this->users[$receiverId]->send(json_encode($data));
        }
        if (isset($this->users[$senderId])) {
            $this->users[$senderId]->send(json_encode($data));
        }
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();