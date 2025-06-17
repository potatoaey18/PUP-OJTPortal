
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP ITECH Navigation</title>
    <style>
        .nav-1 {
            font-family: 'Source Serif 4', serif;
            background: #fff;
            border-bottom: 2px solid rgba(68, 68, 68, 0.66);
            color: #D11010;
            align-items: center;
            font-size: 20px;
            font-weight: 400;
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            z-index: 1000;
            padding: 20px;
        }

        .nav-logo {
            height: 50px;
            margin-left: 40px;
        }

        .nav-title-caption-container {
            display: flex;
            margin-left: 20px;
        }

        .nav-title {
            font-size: 24px;
            font-weight: bold;
        }

        .logout-button {
            margin-right: 20px;
            margin-left: auto;
            padding: 8px 20px;
            background-color: #700000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .logout:hover {
            background-color: #333;
        }
    </style>
</head>
<body>
    <nav class="nav-1">
        <img src="images/pupLogo.png" alt="PUP Logo" class="nav-logo">
        <div class="nav-title-caption-container">
            <div class="nav-title">Polytechnic University of the Philippines - ITECH</div>
        </div>
        <a href="stud_logout.php" class="logout-button">Logout</a>
    </div>
</nav>

<script>
    var userId = <?php echo isset($_SESSION['auth_user']['id']) ? $_SESSION['auth_user']['id'] : 0; ?>;
    var logoutTimeout;

    function startLogoutTimer() {
        logoutTimeout = setTimeout(function() {
            fetch('/stud_logout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json'},
                body: JSON.stringify({ userId: userId })
            }).then(response => {
                window.location.href = 'index.php';
            }).catch(error => {
                console.error('Logout error:', error);
            });
        }, 360000); // 6 minutes
    }

    function resetLogoutTimer() {
        clearTimeout(logoutTimeout);
        startLogoutTimer();
    }

    startLogoutTimer();

    document.addEventListener('mousemove', resetLogoutTimer);
    document.addEventListener('keydown', resetLogoutTimer);
</script>
</body>
</html>