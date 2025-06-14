<?php
ob_start();
session_start();

// Log session for debugging
error_log("Session data: " . print_r($_SESSION, true));

// Include database connection
include '../connection/config.php';

// Check if user is logged in
if (!isset($_SESSION['auth_user']['unique_id']) || !isset($_SESSION['auth_user']['student_id'])) {
    error_log("Session 'unique_id' or 'student_id' not set: " . print_r($_SESSION, true));
    echo "<div style='text-align: center; color: red;'><h2>Session Error</h2><p>Please log in again.</p><p><a href='login.php'>Go to Login</a></p></div>";
    exit;
}

// Get unique_id and student_id from session
$unique_id = $_SESSION['auth_user']['unique_id'];
$student_id = $_SESSION['auth_user']['student_id'];

try {
    // Ensure PDO connection
    if (!isset($conn) || !($conn instanceof PDO)) {
        error_log("Invalid PDO connection in config.php");
        echo "<div style='text-align: center; color: red;'><h2>Database Error</h2><p>Database connection failed. Please try again later.</p><p><a href='login.php'>Go to Login</a></p></div>";
        exit;
    }

    // Query students_data for verification_status and is_working_student
    $stmt = $conn->prepare("SELECT verification_status, is_working_student FROM students_data WHERE uniqueID = ?");
    if ($stmt === false) {
        error_log("Prepare failed for uniqueID $unique_id: " . $conn->errorInfo()[2]);
        echo "<div style='text-align: center; color: red;'><h2>Database Error</h2><p>Query preparation failed. Please try again later.</p><p><a href='login.php'>Go to Login</a></p></div>";
        exit;
    }
    $stmt->execute([$unique_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $status = $student['verification_status'];
        $is_working_student = $student['is_working_student'];
        error_log("User $unique_id verification_status: $status, is_working_student: $is_working_student");

        // Check verification_status (case-insensitive)
        if (strtolower($status) === 'pending') {
            error_log("User $unique_id has verification_status 'pending', redirecting to pending.php");
            header("Location: pending.php");
            exit;
        } elseif (strtolower($status) === 'accept') {
            error_log("User $unique_id has verification_status 'accept', checking working student status");
            // Check if student is a working student
            if ($is_working_student === 'yes') {
                error_log("User $unique_id is a working student, redirecting to working_student/dashboard.php");
                header("Location: working_student/dashboard.php");
                exit;
            }
            // Continue to dashboard for non-working students
        } elseif (strtolower($status) === 'reject') {
            error_log("User $unique_id has verification_status 'reject', redirecting to reject.php");
            header("Location: reject.php");
            exit;
        } else {
            error_log("User $unique_id has invalid verification_status: $status");
            echo "<div style='text-align: center; color: red;'><h2>Account Error</h2><p>Your account status is invalid ($status). Please contact support.</p><p><a href='login.php'>Go to Login</a></p></div>";
            exit;
        }
    } else {
        error_log("User $unique_id not found in students_data");
        echo "<div style='text-align: center; color: red;'><h2>Error</h2><p>User not found.</p><p><a href='login.php'>Go to Login</a></p></div>";
        exit;
    }

    // Fetch Announcements
    $stmt = $conn->prepare("SELECT title, content, created_at FROM announcements WHERE portal IN ('Student', 'All') ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch FAQs
    $stmt = $conn->prepare("SELECT question, answer FROM faqs WHERE portal IN ('Student', 'All') ORDER BY created_at DESC");
    $stmt->execute();
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error for user $unique_id: " . $e->getMessage());
    echo "<div style='text-align: center; color: red;'><h2>Database Error</h2><p>An error occurred: " . htmlspecialchars($e->getMessage()) . ". Please try again later.</p><p><a href='login.php'>Go to Login</a></p></div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-name" content="focus" />
    <title>OJT Web Portal: Dashboard</title>
    <link rel="shortcut icon" href="images/Picture1.png">
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">
    <link href="css/lib/calendar2/pignose.calendar.min.css" rel="stylesheet">
    <link href="css/lib/chartist/chartist.min.css" rel="stylesheet">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/weather-icons.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
        * {box-sizing: border-box}

        .slideshow-container {
            max-width: 1000px;
            position: relative;
            margin: auto;
        }

        .mySlides {
            display: none;
        }

        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            margin-top: -22px;
            padding: 16px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
        }

        .dot {
            cursor: pointer;
            height: 10px;
            width: 10px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }

        .active, .dot:hover {
            background-color: #717171;
        }

        .fade {
            animation-name: fade;
            animation-duration: 1.5s;
        }

        @keyframes fade {
            from {opacity: .4}
            to {opacity: 1}
        }

        .faq-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .faq-item {
            background-color: white;
            margin: 20px 0;
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.8s ease;
        }

        .faq-header {
            padding: 15px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            background-color: rgb(104, 104, 104);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .faq-header:hover {
            background-color: rgb(152, 152, 152);
            color: #000;
        }

        .faq-header span {
            font-size: 24px;
            font-weight: 200;
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-header span {
            transform: rotate(180deg);
        }

        .faq-content {
            padding: 15px;
            display: block;
            background-color: rgb(255, 255, 255);
            font-size: 16px;
            color: #000;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 0.8s ease, opacity 0.8s ease;
        }

        .faq-item.active .faq-content {
            max-height: 1000px;
            opacity: 1;
        }

        .dashboard {
            max-width: 62.5rem;
            margin: 0 auto;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 100px;
        }

        .dashboard-content {
            min-width: 150px;
            min-height: 150px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .dashboard-content img {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .dashboard > :nth-child(1) {
            border: 3px solid #0054B2;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.8);
        }

        .dashboard > :nth-child(2) {
            border: 3px solid #DA7700;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.8);
        }

        .dashboard > :nth-child(3) {
            border: 3px solid #EAE100;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.8);
        }

        .dashboard > :nth-child(4) {
            border: 3px solid #419D00;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.8);
        }

        .quick-access {
            max-width: 70rem;
            margin: 0 auto;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 100px;
        }

        .quick-access-content {
            max-width: 150px;
            max-height: 75px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .quick-access-content img {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .quick-access > :nth-child(1) {
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;    
        }

        .quick-access > :nth-child(2) {
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;    
        }

        .quick-access > :nth-child(3) {
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;    
        }

        .quick-access > :nth-child(4) {
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;    
        }

        .quick-access-content button {
            width: 100%;
            height: 100%;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: rgb(79, 79, 79);
            font-weight: bold;
            text-align: center;
        }

        .stat {
            align-self: center;
            justify-self: center;
        }

        .deadline-container {
            width: 300px;
            border-radius: 10px;
            overflow: hidden;
            font-family: relative;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            background-color: #8B0000;
            color: #ffffff;
            padding: 15px 20px;
            font-size: 18px;
            position: relative;
            margin: 0 auto;
            text-align: center;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            width: 8px;
            height: 8px;
            background-color: black;
            border-radius: 50%;
        }

        .deadline-content {
            background-color: #f0f0f0;
            color: #000000;
            padding: 30px 20px;
            text-align: center;
            font-size: 24px;
        }

        .quick-access-content img {
            width: 50px;
            height: 50px;
            margin: 10px;
        }

        .alert-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php require_once 'templates/stud_navbar.php'; ?>

    <div class="content-wrap" style="height: 80%; width: 100%; margin: 0 auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <div>
                    <div class="page-header">
                        <div class="page-title">
                            <h1 style="font-size: 16px;"><b>HOME</b></h1>
                        </div>
                    </div>
                </div>
                <section id="main-content">
                    <br><br>
                    <div class="slideshow-container" style="position: relative;">
                        <div class="mySlides fade" style="position: relative;">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #700000; opacity: 0.5; z-index: 1; border-radius: 40px;"></div>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #FABC3F; font-size: 48px; z-index: 2; text-align: center; font-family: 'Source Serif 4', serif">
                                Iskolar ng Bayan!
                            </div>
                            <img src="images/pup-carousel.jpg" style="height: 50%; width: 100%; position: relative; z-index: 0; border-radius: 40px;">
                        </div>
                        <div class="mySlides fade" style="position: relative;">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #700000; opacity: 0.5; z-index: 1; border-radius: 40px;"></div>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #FABC3F; font-size: 48px; z-index: 2; text-align: center; font-family: 'Source Serif 4', serif">
                                Iskolar ng Bayan!
                            </div>
                            <img src="images/pup-carousel.jpg" style="height: 50%; width: 100%; position: relative; z-index: 0; border-radius: 40px;">
                        </div>
                        <div class="mySlides fade" style="position: relative;">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #700000; opacity: 0.5; z-index: 1; border-radius: 40px;"></div>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #FABC3F; font-size: 48px; z-index: 2; text-align: center; font-family: 'Source Serif 4', serif">
                                Iskolar ng Bayan!
                            </div>
                            <img src="images/pup-carousel.jpg" style="height: 50%; width: 100%; position: relative; z-index: 0; border-radius: 40px;">
                        </div>
                        <div style="position: absolute; bottom: 10%; left: 50%; transform: translateX(-50%); text-align: center;">
                            <span class="dot" onclick="currentSlide(1)"></span>
                            <span class="dot" onclick="currentSlide(2)"></span>
                            <span class="dot" onclick="currentSlide(3)"></span>
                        </div>
                    </div>
                    <br><br>
                    <div class="page-title">
                        <h1 style="font-size: 16px; color: #700000; margin-left: 5rem;"><b>DASHBOARD</b></h1>
                    </div>
                    <br><br>
                    <div class="row dashboard">
                        <div>
                            <div>
                                <div class="dashboard-content">
                                    <div>
                                        <img src="images/alarm.png" alt="">
                                    </div>
                                    <div>
                                        <div>Total Hours</div>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM ojt_hours");
                                        $stmt->execute();
                                        $count = $stmt->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <div class="stat"><?php echo $count['total_hours']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="dashboard-content">
                                    <div>
                                        <img src="images/user.png" alt="">
                                    </div>
                                    <div>
                                        <div>Hours Left</div>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM ojt_hours");
                                        $stmt->execute();
                                        $hours = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $studID = $_SESSION['auth_user']['unique_id'];
                                        $stmt = $conn->prepare("SELECT SUM(total_working_hours) AS completed_hours FROM stud_daily_time_records WHERE stud_id = ?");
                                        $stmt->execute([$studID]);
                                        $completedHOURS = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $hoursLEFT = $hours['total_hours'] - $completedHOURS['completed_hours'];
                                        ?>
                                        <div class="stat"><?php echo $hoursLEFT; ?> Hrs</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="dashboard-content">
                                    <div>
                                        <img src="images/done.png" alt="">
                                    </div>
                                    <div>
                                        <div>Completed Task</div>
                                        <?php
                                        $studID = $_SESSION['auth_user']['unique_id'];
                                        $taskSTATUS = 'Finished';
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM stud_task_list WHERE stud_id = ? AND task_status = ?");
                                        $stmt->execute([$studID, $taskSTATUS]);
                                        $count = $stmt->fetchColumn();
                                        ?>
                                        <div class="stat"><?php echo $count; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="dashboard-content">
                                    <div>
                                        <img src="images/pending.png" alt="">
                                    </div>
                                    <div>
                                        <div>Pending Task</div>
                                        <?php
                                        $studID = $_SESSION['auth_user']['unique_id'];
                                        $taskSTATUS = 'Pending';
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM stud_task_list WHERE stud_id = ? AND task_status = ?");
                                        $stmt->execute([$studID, $taskSTATUS]);
                                        $count = $stmt->fetchColumn();
                                        ?>
                                        <div class="stat"><?php echo $count; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="page-title">
                        <h1 style="font-size: 16px; color: #700000; margin-left: 5rem;"><b>QUICK ACCESS</b></h1>
                    </div>
                    <br><br>
                    <div class="row quick-access">
                        <div>
                            <div>
                                <div class="quick-access-content">
                                    <img src="images/dtr.png" alt="" style="height: 50px; width: 50px; display: block; margin: 10px auto; filter: hue-rotate(0deg) saturate(80%) brightness(40%);">
                                    <button onclick="window.location.href='dtr.php'">Daily Time Record</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="quick-access-content">
                                    <img src="images/calendar.png" alt="" style="height: 50px; width: 50px; display: block; margin: 10px auto; filter: hue-rotate(0deg) saturate(80%) brightness(40%);">
                                    <button onclick="window.location.href='weekly_accomplishment.php'">Weekly Accomplishment</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="quick-access-content">
                                    <img src="images/endorsement.png" alt="" style="height: 50px; width: 50px; display: block; margin: 10px auto; filter: hue-rotate(0deg) saturate(80%) brightness(40%);">
                                    <button onclick="window.location.href='internship_experience.php'">Internship Experience</button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="quick-access-content">
                                    <img src="images/working.png" alt="" style="height: 50px; width: 50px; display: block; margin: 10px auto; filter: hue-rotate(0deg) saturate(80%) brightness(40%);">
                                    <button onclick="window.location.href='working_student/dashboard.php'">Present as Working Student</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br><br>
                    <div class="page-title">
                        <h1 style="font-size: 16px; color: #700000; margin-left: 5rem;"><b>ANNOUNCEMENT</b></h1>
                    </div>
                    <br><br>
                    <div style="max-width: 62.5rem; margin: 0 auto; display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 80px;">
                        <?php if (empty($announcements)): ?>
                            <div class="alert-message">No announcements available at this time.</div>
                        <?php else: ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="deadline-container">
                                    <div class="header">
                                        <?php echo htmlspecialchars($announcement['title']); ?>
                                    </div>
                                    <div class="deadline-content">
                                        <?php echo htmlspecialchars($announcement['content']); ?>
                                        <br><small><?php echo date('F d, Y', strtotime($announcement['created_at'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <br><br>
                    <div class="page-title" style="display: flex; align-items: center; margin-left: 5rem;">
                        <img src="images/faqs.png" alt="faqs" style="margin-right: 10px;">
                        <h1 style="font-size: 16px; color: #700000; margin: 0;"><b>FAQs</b></h1>
                    </div>
                    <div class="faq-container">
                        <?php if (empty($faqs)): ?>
                            <div class="alert-message">No FAQs available at this time.</div>
                        <?php else: ?>
                            <?php foreach ($faqs as $faq): ?>
                                <div class="faq-item">
                                    <div class="faq-header"><?php echo htmlspecialchars($faq['question']); ?><span>v</span></div>
                                    <div class="faq-content">
                                        <?php echo htmlspecialchars($faq['answer']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/customAlert.js"></script>

    <script>
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) { slideIndex = 1 }
            slides[slideIndex - 1].style.display = "block";
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            dots[slideIndex - 1].className += " active";
            setTimeout(showSlides, 3000);
        }

        function plusSlides(n) {
            slideIndex += n - 1;
            showSlides();
        }

        function currentSlide(n) {
            slideIndex = n - 1;
            showSlides();
        }

        setInterval(showSlides, 3000);
    </script>

    <script>
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const header = item.querySelector('.faq-header');
            header.addEventListener('click', () => {
                item.classList.toggle('active');
            });
        });
    </script>

    <?php 
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
        <script>
        showSuccessAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>");
        </script>
    <?php
        unset($_SESSION['status']);
    }
    ob_end_flush();
    ?>
</body>
</html>