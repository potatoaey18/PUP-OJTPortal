
<?php
ob_start();
session_start();

// Log session for debugging
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['auth_user']['unique_id'])) {
    error_log("Session 'unique_id' not set: " . print_r($_SESSION, true));
    echo "<div style='text-align: center; color: red;'><h2>Session Error</h2><p>Please log in again.</p><p><a href='../pending/login.php'>Go to Login</a></p></div>";
    exit;
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Pending User Dashboard</title>
    <link rel="shortcut icon" href="images/Picture1.png">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .content-wrap {
            width: 100%;
            margin: 0 auto;
            padding-top: 80px;
        }
        .content {
            background-color: white;
            padding: 2rem;
            margin: 0 auto;
            max-width: 1000px;
        }
        .access-level-container {
            max-width: 62.5rem;
            margin: 0 auto;
            padding: 20px;
            border: 3px solid #700000;
            border-radius: 10px;
            text-align: center;
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.8);
        }
        .access-level-container p {
            font-size: 18px;
            color: #333;
            margin: 0;
        }
        .page-title {
            margin-left: 2rem;
        }
        .page-title h1 {
            font-size: 16px;
            color: #700000;
        }
    </style>
</head>
<body>
    <?php require_once 'templates/pending_navbar.php'; ?>

    <div class="content-wrap">
        <div class="content">
            <section id="main-content">
                <div class="page-title">
                    <h1><b>ACCESS LEVEL STATUS</b></h1>
                </div>
                <br><br>
                <div class="access-level-container">
                    <p>Wait for your Adviser to verify your status</p>
                </div>
            </section>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/customAlert.js"></script>
    <?php if (isset($_SESSION['status']) && $_SESSION['status'] !== ''): ?>
        <script>
        showSuccessAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>");
        </script>
        <?php unset($_SESSION['status']); ?>
    <?php endif; ?>
</body>
</html>