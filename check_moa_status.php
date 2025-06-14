<?php
include '../connection/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not authenticated.'
    ]);
    exit();
}

$supervisor_id = $_SESSION['auth_user']['supervisor_id'];
$current_date = new DateTime();
$three_months_later = (clone $current_date)->modify('+3 months');

try {
 
    $stmt = $conn->prepare("SELECT end_date_validity FROM moa_form WHERE supervisor_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$supervisor_id]);
    $moa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$moa || !$moa['end_date_validity']) {
        
        echo json_encode([
            'status' => 'expired',
            'message' => 'Are you sure you want to renew MOA validation?'
        ]);
        exit();
    }

    $end_date = new DateTime($moa['end_date_validity']);
    $days_remaining = $current_date->diff($end_date)->days;

    if ($end_date < $current_date) {
        
        echo json_encode([
            'status' => 'expired',
            'message' => 'Your MOA has expired. You need to renew your MOA.'
        ]);
    } elseif ($end_date <= $three_months_later) {
        
        $days_left = $current_date->diff($end_date)->days;
        $months_left = $current_date->diff($end_date)->m + ($current_date->diff($end_date)->y * 12);
        
        $message = "Your MOA will expire in ";
        if ($months_left > 0) {
            $message .= "$months_left month" . ($months_left > 1 ? 's' : '');
            if ($days_left % 30 > 0) {
                $remaining_days = $days_left % 30;
                $message .= " and $remaining_days day" . ($remaining_days > 1 ? 's' : '');
            }
        } else {
            $message .= "$days_left day" . ($days_left > 1 ? 's' : '');
        }
        $message .= ". Would you like to renew it now?";
        
        echo json_encode([
            'status' => 'expiring_soon',
            'message' => $message,
            'days_remaining' => $days_remaining
        ]);
    } else {
        
        $months_left = $current_date->diff($end_date)->m + ($current_date->diff($end_date)->y * 12);
        $days_left = $current_date->diff($end_date)->days;
        
        $message = "Your MOA is still valid for ";
        if ($months_left > 0) {
            $message .= "$months_left month" . ($months_left > 1 ? 's' : '');
            if ($days_left % 30 > 0) {
                $remaining_days = $days_left % 30;
                $message .= " and $remaining_days day" . ($remaining_days > 1 ? 's' : '');
            }
        } else {
            $message .= "$days_left day" . ($days_left > 1 ? 's' : '');
        }
        $message .= ". You can only renew when there are 3 months or less remaining.";
        
        echo json_encode([
            'status' => 'valid',
            'message' => $message,
            'days_remaining' => $days_remaining
        ]);
    }
} catch (PDOException $e) {
    error_log('Database error in check_moa_status.php: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while checking MOA status.'
    ]);
}
