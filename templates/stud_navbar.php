<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP ITECH Navigation</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .header-icon {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .avatar-trigger {
            display: flex;
            align-items: center;
        }
        
        .user-name {
            font-weight: 500;
            color: #000;
        }
        
        .avatar-img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .nav-1 {
            font-family: 'Source Serif 4', serif;
            background: #fff;
            border-bottom: 2px solid rgba(68, 68, 68, 0.66);
            color: #D11010;
            text-align: left;
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
            background-clip: padding-box;
            z-index: 1000;
        }

        .nav-logo {
            height: 50px;
            margin-left: 20px;
        }

        .nav-title-caption-container {
            display: flex;
            margin-left: 20px;
        }

        .nav-title {
            font-size: 24px;
            font-weight: bold;
        }

        .sidenav {
            width: 15%;
            background: #fff;
            border-right: 2px solid rgba(68, 68, 68, 0.66);
            height: 100%;
            top: 0;
            position: fixed;
            padding-top: 20px;
            padding-right: 20px;
            margin-top: 75px;
            z-index: 1;
        }

        .sidenav img {
            height: 20px;
            margin-right: 10px;
            filter: brightness(0) invert(0);
        }
        
        .sidenav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        .sidenav a {
            padding: 10px 16px;
            text-decoration: none;
            font-size: 14px;
            color: rgb(0, 0, 0);
            background-color: #fff;
            display: flex;
            align-items: center;
        }

        .sidenav a:active {
            color: #f1f1f1 !important;
            background-color: #700000 !important;
        }

        .sidenav a:hover {
            color: #f1f1f1;
            background-color: #700000;
            border-radius: 0px 20px 20px 0px;
        }

        .sidenav a:hover img {
            filter: brightness(0) invert(1);
        }
        
        .sidenav a.active {
            color: #f1f1f1;
            background-color: #700000;
            border-radius: 0px 20px 20px 0px;
        }
        
        .sidenav a.active img {
            filter: brightness(0) invert(1);
        }
        
        .dropdown-toggle {
            align-items: left;
            width: 100%;
        }

        .dropdown-toggle.active {
            color: #f1f1f1;
            background-color: #700000;
        }

        .sidenav .dropdown-toggle.active {
            background-color: #700000 !important;
        }
        
        .dropdown-toggle.active img {
            filter: brightness(0) invert(1);
        }
        
        .dropdown-arrow {
            margin-left: 5px;
        }

        .user {
            margin-right: 20px;
            margin-left: auto;
        }
        
        .main {
            margin-left: 160px;
            padding: 0px 10px;
        }   

        .user-info {
            display: flex;
            flex-direction: column;
            color: #000;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
        }

        .user-name {
            margin-bottom: 10px;
        }

        .dropdown-content {
            display: none;
            min-width: 100%;
            padding: 0;
            z-index: 1;
        }
        
        .dropdown-content a {
            padding: 8px 16px 8px 24px;
            text-decoration: none;
            font-size: 13px;
            color: rgb(0, 0, 0);
            display: flex;
            align-items: center;
        }

        .dropdown-content a:hover {
            color: #f1f1f1;
            background-color: #700000;
        }

        .dropdown-content a:hover img {
            filter: brightness(0) invert(1);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            top: 100%;
            right: 0;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-content-body ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dropdown-content-body li {
            display: flex;
            align-items: center;
        }

        .dropdown-content-body a {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            text-decoration: none;
            color: #000;
            font-size: 14px;
            width: 100%;
        }

        .dropdown-content-body a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-content-body a:hover i.material-icons {
            color: #000;
        }

        .dropdown-icon {
            font-size: 20px;
            margin-right: 10px;
            color: #000;
        }
        
        @media screen and (max-width: 768px) {
            .sidenav {
                width: 100%;
                height: auto;
                position: relative;
                margin-top: 60px;
                padding-top: 10px;
                border-right: none;
                border-bottom: 2px solid rgba(68, 68, 68, 0.66);
            }
            
            .main {
                margin-left: 0;
                padding-top: 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="nav-1">
        <img src="images/pupLogo.png" alt="PUP Logo" class="nav-logo">
        <div class="nav-title-caption-container">
            <div class="nav-title">Polytechnic University of the Philippines-ITECH</div>
        </div>
        <div class="user">
            <div class="header-icon">
                <div class="avatar-trigger" data-toggle="dropdown">
                    <?php
                    if (isset($_SESSION['auth_user']['student_id'])) {
                        $studID = $_SESSION['auth_user']['student_id'];
                        $stmt = $conn->prepare("SELECT * FROM students_data WHERE id = ? ");
                        $stmt->execute([$studID]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $profileImage = $result['profile_picture'] ? $result['profile_picture'] : 'student/images/profile.png';
                    }
                    ?>
                    <div class="user-info">
                        <span class="user-name">
                            <?php echo $result['first_name']; ?>
                        </span>
                        <span class="schoolID">
                            <?php echo $result['student_ID']; ?>
                        </span>
                    </div>
                    <?php if (isset($profileImage)): ?>
                        <img src="<?php echo $profileImage; ?>" alt="User Avatar" class="avatar-img">
                    <?php else: ?>
                        <span>No Image</span>
                    <?php endif; ?>
                </div>
                <div class="drop-down dropdown-profile dropdown-menu dropdown-menu-right">
                    <div class="dropdown-content-body">
                        <ul>
                            <li>
                                <a href="#" onclick="profile();">
                                    <i class="material-icons dropdown-icon">person</i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="settings();">
                                    <i class="material-icons dropdown-icon">settings</i>
                                    <span>Setting</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" onclick="logout();">
                                    <i class="material-icons dropdown-icon">logout</i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div>
        <div>
            <div class="sidenav">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                ?>
                <ul>
                    <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>"><img src="images/home.png"> Home </a></li>
                    <li><a href="stud_profile.php" class="<?php echo ($current_page == 'stud_profile.php') ? 'active' : ''; ?>"><img src="images/profile.png"> Profile </a></li>
                    <li><a href="stud_notification.php" class="<?php echo ($current_page == 'stud_notification.php') ? 'active' : ''; ?>"><img src="images/notification.png"> Notifications </a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"><img src="images/message.png"> Messages</a>
                        <div class="dropdown-content" <?php echo (in_array($current_page, ['message_supervisor.php', 'message_coordinator.php', 'message_admin.php'])) ? 'style="display: block;"' : ''; ?>>
                            <a href="message_supervisor.php" class="<?php echo ($current_page == 'message_supervisor.php') ? 'active' : ''; ?>"><img src="images/supervisor.png"> HTE</a>
                            <a href="message_coordinator.php" class="<?php echo ($current_page == 'message_coordinator.php') ? 'active' : ''; ?>"><img src="images/faculty.png"> Adviser</a>
                            <a href="message_admin.php" class="<?php echo ($current_page == 'message_admin.php') ? 'active' : ''; ?>"><img src="images/admin.png"> Admin</a>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"><img src="images/todo.png"> To do</a>
                        <div class="dropdown-content" <?php echo (in_array($current_page, ['partner_companies.php', 'endorsement.php', 'documentation.php', 'evaluations.php', 'certification.php', 'portfolio.php'])) ? 'style="display: block;"' : ''; ?>>
                            <a href="partner_companies.php" class="<?php echo ($current_page == 'partner_companies.php') ? 'active' : ''; ?>"><img src="images/search.png"> Find HTE</a>
                            <a href="endorsement.php" class="<?php echo ($current_page == 'endorsement.php') ? 'active' : ''; ?>"><img src="images/endorsement.png"> Endorsement Process</a>
                            <a href="documentation.php" class="<?php echo ($current_page == 'documentation.php') ? 'active' : ''; ?>"><img src="images/internship_docs.png"> Internship Documentations</a>
                            <a href="evaluations.php" class="<?php echo ($current_page == 'evaluations.php') ? 'active' : ''; ?>"><img src="images/evaluation.png"> Evaluations</a>
                            <a href="coc.php" class="<?php echo ($current_page == 'coc.php') ? 'active' : ''; ?>"><img src="images/certificate.png"> Certification of Completion</a>
                            <a href="portfolio.php" class="<?php echo ($current_page == 'portfolio.php') ? 'active' : ''; ?>"><img src="images/portfolio.png"> Portfolio</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const $dropdownToggles = $(".dropdown-toggle");
            
            $dropdownToggles.click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $parent = $(this).parent();
                const $dropdownContent = $(this).siblings(".dropdown-content");
                const $arrow = $(this).find(".dropdown-arrow");
                const isActive = $parent.hasClass("active");
                
                if (isActive) {
                    $parent.removeClass("active");
                    $dropdownContent.slideUp(200);
                    $arrow.text("▼");
                } else {
                    $parent.addClass("active");
                    $dropdownContent.slideDown(200);
                    $arrow.text("▲");
                }
            });
            
            $(".dropdown-content").each(function() {
                if ($(this).find("a.active").length > 0) {
                    $(this).css("display", "block");
                    $(this).parent().addClass("active");
                }
            });

            $(".avatar-trigger").click(function(e) {
                e.stopPropagation();
                $(this).siblings(".dropdown-menu").toggleClass("show");
            });

            $(document).click(function() {
                $(".dropdown-menu").removeClass("show");
            });
        });
    </script>

    <script>
        var userId = <?php echo $_SESSION['auth_user']['student_id']; ?>;
        var logoutTimeout;

        function startLogoutTimer() {
            logoutTimeout = setTimeout(function() {
                $.ajax({
                    type: 'POST',
                    url: 'update_status_AutoLogOut.php',
                    data: { userId: userId },
                    success: function(response) {
                        window.location.href = 'index.php';
                    },
                    error: function(xhr, status, error) {
                        console.error('Logout error:', error);
                    }
                });
            }, 360000);
        }

        function resetLogoutTimer() {
            clearTimeout(logoutTimeout);
            startLogoutTimer();
        }

        startLogoutTimer();

        document.addEventListener('mousemove', resetLogoutTimer);
        document.addEventListener('keydown', resetLogoutTimer);
    </script>

    <script>
        function profile() {
            window.location.href = 'stud_profile.php';
        }
        function settings() {
            window.location.href = 'stud_settings.php';
        }
        function logout() {
            window.location.href = 'stud_logout.php';
        }
    </script>
</body>
</html>