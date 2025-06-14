<?php

header('Content-Type: application/json');

function match_any($message, $keywords) {
    foreach ($keywords as $kw) {
        if (strpos($message, $kw) !== false) return true;
    }
    return false;
}

$userMessage = isset($_POST['message']) ? strtolower(trim($_POST['message'])) : '';
$response = '';

$greetings = ['hello', 'hi', 'good morning', 'good afternoon', 'hey'];
$thanks = ['thank', 'thanks', 'thank you'];
$help = ['help', 'assist', 'support'];
$filter = ['filter', 'filters', 'filtering'];
$save = ['save', 'saving', 'saved'];
$date = ['date format', 'date', 'format'];
$week = ['week', 'weeks', 'week number'];
$account = ['account', 'profile', 'login', 'logout', 'password'];
$nav = ['navigate', 'navigation', 'menu', 'where', 'find', 'locate'];
$system = ['system', 'about', 'version', 'info', 'information'];
$dashboard = ['dashboard', 'statistics', 'summary', 'home'];
$profileSection = ['profile', 'update profile', 'view profile', 'company info'];
$notifications = ['notification', 'notifications', 'alerts'];
$messages = ['message', 'messages', 'chat', 'contact', 'conversation'];
$appointment = ['appointment', 'appointments', 'meeting', 'schedule'];
$moa = ['moa', 'agreement', 'moa status', 'memorandum'];
$interns = ['interns', 'applicants', 'list', 'trainee', 'students'];
$updates = ['updates', 'intern updates', 'progress', 'activity'];
$evaluations = ['evaluation', 'evaluations', 'review', 'rate'];

if ($userMessage === '__init__') {
    $response = "\nHello there! 👋\n\n Welcome to the OJT Web Portal – PUP ITECH.\n\nI'm your new chatbot assistant. I'm still learning and can only help with basic questions for now.\n\nTo get the best help, please try to be as clear and specific as possible when asking your questions.\n\n<strong>How can I assist you today? 😊</strong>";
} elseif (!$userMessage) {
    $response = "Sorry, I didn't get your message.";
} elseif (match_any($userMessage, $greetings)) {
    $greetReplies = [
        "Hello! How can I help you today?",
        "Hi there! Need assistance?",
        "Hey! How can I assist you?"
    ];
    $response = $greetReplies[array_rand($greetReplies)];
} elseif (match_any($userMessage, $thanks)) {
    $thanksReplies = [
        "You're welcome!",
        "Glad to help!",
        "Anytime! Let me know if you have more questions."
    ];
    $response = $thanksReplies[array_rand($thanksReplies)];
} elseif (match_any($userMessage, $help)) {
    $response = "Sure! Please tell me what you need help with. You can ask about filtering, saving, or using the system.";
} elseif (match_any($userMessage, $filter)) {
    $response = "To use the filter, select your desired week or date, and the table will update automatically.";
} elseif (match_any($userMessage, $save)) {
    $response = "The save button becomes active when you use a filter. Click it to save your filtered results.";
} elseif (match_any($userMessage, $date)) {
    $response = "Dates are displayed as dd/mm/yyyy (e.g., 10/04/2024).";
} elseif (match_any($userMessage, $week)) {
    $response = "Week numbers are shown as 'Week X' (e.g., 'Week 1').";
} elseif (match_any($userMessage, $account)) {
    $response = "For account issues (login, password, profile), please use the account menu at the top right or contact your administrator if you have trouble.";
} elseif (match_any($userMessage, $nav)) {
    $response = "You can use the navigation bar at the top to access different sections such as Dashboard, MOA Status, and more. If you can't find something, let me know!";
} elseif (match_any($userMessage, $system)) {
    $response = "This system is designed to help you manage weekly accomplishments and MOA processes. For more details, contact your system administrator.";
} elseif (match_any($userMessage, $dashboard)) {
    $response = "\nThe dashboard provides an overview of applicants, trainees, and completed interns. \n\nYou’ll also find summary charts and announcements like deadlines from here.";
} elseif (match_any($userMessage, $profileSection)) {
    $response = "Go to the 'Profile' section from the left menu. Here you can see your personal or company information, contact details, and MOA validity.";
} elseif (match_any($userMessage, $notifications)) {
    $response = "Click on 'Notifications' in the sidebar to view recent messages, requests, and system updates relevant to your account.";
} elseif (match_any($userMessage, $messages)) {
    $response = "Use the 'Messages' section to chat with students, faculty, HTE, or admin. Select a contact to start a conversation, view message history, and share files.";
} elseif (match_any($userMessage, $appointment)) {
    $response = "Click on 'Appointment Meetings' under the 'To do' menu to manage or check your scheduled meetings. You can also click the link and it will automatically redirects you to the respective platforms";
} elseif (match_any($userMessage, $moa)) {
    $response = "Use the 'Check MOA Status' link in the 'To do' section to see the validation, expiration, and validity period of your Memorandum of Agreement.";
} elseif (match_any($userMessage, $interns)) {
    $response = "Go to 'Interns List and Applicants' to view all current applicants and interns, along with their status.";
} elseif (match_any($userMessage, $updates)) {
    $response = "Use the 'Check Intern’s updates' link to monitor the progress and recent activities of your interns.";
} elseif (match_any($userMessage, $evaluations)) {
    $response = "The 'Evaluations' section lets you review and submit evaluations for students or view evaluations you’ve received.";
} else {
    $fallbacks = [
        "Sorry, I am a simple assistant. Please ask about filtering, saving, or week/date formats.",
        "I'm not sure how to answer that. Try asking about filters, saving, or navigation.",
        "If you need further help, contact your supervisor or system administrator."
    ];
    $response = $fallbacks[array_rand($fallbacks)];
}

echo json_encode(['reply' => $response]);
