<?php
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SESSION['auth_user']['supervisor_id'] == 0) {
    echo "<script>window.location.href='index.php'</script>";
    exit;
} else {
    $supervisorID = $_SESSION['auth_user']['supervisor_id'];
    $query = "SELECT first_name FROM supervisor WHERE id = :supervisor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':supervisor_id', $supervisorID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $first_name = isset($result['first_name']) && $result !== false ? $result['first_name'] : "Guest";

    // Fetch announcements for HTE or All
    $stmt = $conn->prepare("SELECT title, content, created_at FROM announcements WHERE portal IN ('HTE', 'All') ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch FAQs for HTE or All
    $stmt = $conn->prepare("SELECT question, answer FROM faqs WHERE portal IN ('HTE', 'All') ORDER BY created_at DESC");
    $stmt->execute();
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>OJT Web Portal: Dashboard</title>
    <!-- ================= Favicon ================== -->
    <link rel="shortcut icon" href="images/pupLogo.png">
    
    <!-- Common -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body style="overflow-y: hidden;">
<!---------NAVIGATION BAR-------->
<?php require_once 'templates/supervisor_navbar.php'; ?>
<!---------NAVIGATION BAR ENDS-------->

    <div class="content-wrap" style="height: 100vh; width: 100%; margin: 0 auto; overflow-y: auto;">
        <div style="background-color: white; margin-top: 6rem; margin-left: 16rem; padding: 2rem;">
            <div>
                <div>
                    <div>
                        <div class="page-header">
                            <div class="page-title">
                                <h1 style="font-size: 16px;"><b>HOME</b></h1>
                            </div>
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
                    <div style="max-width: 62.5rem; margin: 0 auto; padding: 2rem 0;">
                        <?php
                        if (isset($_SESSION['auth_user']['supervisor_id'])) {
                            $supervisorID = $_SESSION['auth_user']['supervisor_id'];
                            $stmt = $conn->prepare("SELECT * FROM supervisor WHERE id = ?");
                            $stmt->execute([$supervisorID]);
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="stats-container" style="display: flex; justify-content: space-between; width: 100%;">
                            <div class="stats-box" style="border: 2px solid #FF1493; background: white; width: 250px; height: 150px; flex-direction: row; padding: 20px 30px;">
                                <div style="display: flex; align-items: center;">
                                    <img src="images/profile.png" alt="Profile Icon" style="width: 75px; height: 75px; filter: invert(27%) sepia(91%) saturate(2630%) hue-rotate(314deg) brightness(96%) contrast(97%);">
                                    <div class="stats-info" style="margin-left: 20px; align-items: flex-start;">
                                        <span class="stats-label" style="color: #333; font-size: 20px;">Applicants</span>
                                        <?php
                                        $stmt = $conn->prepare("SELECT company_name FROM supervisor WHERE id = ?");
                                        $stmt->execute([$supervisorID]);
                                        $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $company = ($supervisor !== false && isset($supervisor['company_name'])) ? $supervisor['company_name'] : '';

                                        $totalApplicants = 0;
                                        if ($company) {
                                            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM students_data WHERE stud_hte = ? AND (ojt_status IS NULL OR ojt_status = '')");
                                            $stmt->execute([$company]);
                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $totalApplicants = ($result !== false && isset($result['total'])) ? $result['total'] : 0;
                                        }
                                        ?>
                                        <span class="stats-number" style="color: #000000; font-size: 32px; margin-left: 30px;"><?php echo $totalApplicants; ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-box" style="border: 2px solid #32CD32; background: white; width: 250px; height: 150px; flex-direction: row; padding: 20px 30px;">
                                <div style="display: flex; align-items: center;">
                                    <img src="images/profile.png" alt="Profile Icon" style="width: 75px; height: 75px; filter: invert(55%) sepia(85%) saturate(385%) hue-rotate(84deg) brightness(97%) contrast(92%);">
                                    <div class="stats-info" style="margin-left: 20px; align-items: flex-start;">
                                        <span class="stats-label" style="color: #333; font-size: 20px;">Pending</span>
                                        <?php
                                        $totalPending = 0;
                                        if ($company) {
                                            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM students_data WHERE stud_hte = ? AND ojt_status = 'Completed'");
                                            $stmt->execute([$company]);
                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $totalPending = ($result !== false && isset($result['total'])) ? $result['total'] : 0;
                                        }
                                        ?>
                                        <span class="stats-number" style="color: #000000; font-size: 32px; margin-left: 30px;"><?php echo $totalPending; ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="stats-box" style="border: 2px solid #00BFFF; background: white; width: 250px; height: 150px; flex-direction: row; padding: 20px 30px;">
                                <div style="display: flex; align-items: center;">
                                    <img src="images/profile.png" alt="Profile Icon" style="width: 75px; height: 75px; filter: invert(67%) sepia(92%) saturate(4068%) hue-rotate(177deg) brightness(101%) contrast(102%);">
                                    <div class="stats-info" style="margin-left: 20px; align-items: flex-start;">
                                        <span class="stats-label" style="color: #333; font-size: 20px;">Trainees</span>
                                        <?php
                                        $totalTrainees = 0;
                                        if ($company) {
                                            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM students_data WHERE stud_hte = ? AND ojt_status = 'Deployed'");
                                            $stmt->execute([$company]);
                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $totalTrainees = ($result !== false && isset($result['total'])) ? $result['total'] : 0;
                                            $_SESSION['supervisor_company'] = $company;
                                        }
                                        ?>
                                        <span class="stats-number" style="color: #000000; font-size: 32px; margin-left: 30px;"><?php echo $totalTrainees; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div style="max-width: 62.5rem; margin: 0 auto; padding: 2rem 0;">
                        <div style="background: #f5f5f5; padding: 20px; border-radius: 10px; width: 100%; display: flex;">
                            <div style="flex: 1;">
                                <h2 style="color: #333; margin-bottom: -10px; font-size: 20px;">Students</h2>
                                <div style="width: 400px; height: 400px; margin: 0 auto; margin-left: 280px;">
                                    <canvas id="studentChart"></canvas>
                                </div>
                            </div>
                            <div style="padding: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <div style="width: 20px; height: 20px; background: #FF1493; margin-right: 10px;"></div>
                                    <span>Applicants</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <div style="width: 20px; height: 20px; background: #32CD32; margin-right: 10px;"></div>
                                    <span>Pending</span>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <div style="width: 20px; height: 20px; background: #00BFFF; margin-right: 10px;"></div>
                                    <span>Trainees</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br><br>
                    <div class="page-title">
                        <h1 style="font-size: 16px; color: #700000; margin-left: 5rem;"><b>ANNOUNCEMENTS</b></h1>
                    </div>
                    <br><br>
                    <div style="max-width: 62.5rem; margin: 0 auto; display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 80px;">
                        <?php if (!empty($announcements)): ?>
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="deadline-container">
                                    <div class="header">
                                        <?php echo htmlspecialchars($announcement['title']); ?>
                                    </div>
                                    <div class="content">
                                        <?php echo htmlspecialchars($announcement['content']); ?>
                                        <br><small style="font-size: 14px; color: #555;">Posted: <?php echo date('F d, Y', strtotime($announcement['created_at'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; color: #555;">No announcements available.</div>
                        <?php endif; ?>
                    </div>

                    <br><br>
                    <div class="page-title">
                        <h1 style="font-size: 16px; color: #700000; margin-left: 5rem;"><b>FREQUENTLY ASKED QUESTIONS</b></h1>
                    </div>
                    <br><br>
                    <div class="faq-container" style="max-width: 62.5rem; margin: 0 auto;">
                        <?php if (!empty($faqs)): ?>
                            <?php foreach ($faqs as $faq): ?>
                                <div class="faq-item">
                                    <div class="faq-header">
                                        <?php echo htmlspecialchars($faq['question']); ?>
                                        <span>↓</span>
                                    </div>
                                    <div class="faq-content">
                                        <?php echo htmlspecialchars($faq['answer']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; color: #555;">No FAQs available.</div>
                        <?php endif; ?>
                    </div>
                    <br><br>
                </section>
            </div>
        </div>
    </div>

    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    <script>
        const ctx = document.getElementById('studentChart').getContext('2d');
        
        const total = <?php echo $totalApplicants + $totalPending + $totalTrainees; ?>;
        const allZero = total === 0;
        
        const applicantsPercent = allZero ? 0 : (<?php echo $totalApplicants; ?> / total * 100).toFixed(1);
        const pendingPercent = allZero ? 0 : (<?php echo $totalPending; ?> / total * 100).toFixed(1);
        const traineesPercent = allZero ? 0 : (<?php echo $totalTrainees; ?> / total * 100).toFixed(1);

        const myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Applicants', 'Pending', 'Trainees'],
                datasets: [{
                    data: allZero ? [1] : [<?php echo $totalApplicants; ?>, <?php echo $totalPending; ?>, <?php echo $totalTrainees; ?>],
                    backgroundColor: allZero ? 
                        ['rgba(80, 80, 80, 0.8)'] : 
                        [
                            'rgba(255, 20, 147, 0.8)',
                            'rgba(50, 205, 50, 0.8)',
                            'rgba(0, 191, 255, 0.8)',
                        ],
                    borderColor: allZero ? 
                        ['rgba(60, 60, 60, 1)'] :
                        [
                            'rgba(255, 20, 147, 1)',
                            'rgba(50, 205, 50, 1)',
                            'rgba(0, 191, 255, 1)',
                        ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function() {
                                return '';
                            },
                            label: function(context) {
                                if (allZero) {
                                    return 'Applicants, Pending, and Trainees: 0.0%';
                                }
                                const label = context.label;
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${percentage}%`;
                            }
                        }
                    }
                }
            }
        });
    </script>

    <?php 
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
        $title = $_SESSION['alert'] ?? 'Success';
        $message = $_SESSION['status'];
        $type = $_SESSION['status-code'] ?? 'success';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '<?php echo addslashes($title); ?>',
            html: '<?php echo addslashes($message); ?>',
            icon: '<?php echo $type; ?>',
            confirmButtonText: 'OK',
            customClass: {
                popup: 'swal2-popup',
                icon: 'swal2-icon',
                title: 'swal2-title',
                confirmButton: 'swal2-confirm',
                htmlContainer: 'swal2-html-container'
            }
        });
    });
    </script>
    <?php
        unset($_SESSION['status']);
        unset($_SESSION['alert']);
        unset($_SESSION['status-code']);
    }
    ?>

</body>

</html>

<style>
    * {box-sizing:border-box}

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

    .content {
        background-color: #f0f0f0;   
        color: #000000; 
        padding: 30px 20px;
        text-align: center;
        font-size: 24px;
    }

    body {
        background-color: #f0f0f0;
    }
    
    .stats-container {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 20px;
    }

    .stats-box {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .stats-box:hover {
        transform: translateY(-5px);
    }

    .stats-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .stats-label {
        font-size: 18px;
        font-weight: 500;
    }

    .swal2-popup {
        border-radius: 40px !important;
        padding: 60px 30px 40px 30px !important;
        background-color: #700000 !important;
        border: 2px solid rgba(255, 193, 7, 0.3) !important;
    }

    .swal2-icon {
        position: absolute !important;
        left: 50% !important;
        top: 20px !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        z-index: 2 !important;
        background-color: #700000 !important;
        border: 3px solid #ffc107 !important;
        color: #ffc107 !important;
        animation: pulse 1.5s infinite !important;
        overflow: visible !important;
    }
      
    .swal2-success-circular-line-left,
    .swal2-success-circular-line-right,
    .swal2-success-fix {
        background-color: transparent !important;
    }
    
    .swal2-success-ring {
        border: 3px solid #ffc107 !important;
        border-radius: 50% !important;
    }
    
    .swal2-success-line-tip,
    .swal2-success-line-long {
        background-color: #ffc107 !important;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    .swal2-title {
        color: #ffc107 !important;
        font-size: 24px !important;
        font-weight: 600 !important;
        margin: 30px 0 20px 0 !important;
        position: relative;
        z-index: 3;
    }

    .swal2-html-container {
        color: #fff !important;
        font-size: 16px !important;
    }

    .swal2-confirm {
        background-color: #fff !important;
        color: #000 !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 10px 25px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }

    .swal2-confirm:hover {
        background-color: #ffc107 !important;
        color: #700000 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
    }
    
    .swal2-styled:focus {
        box-shadow: none !important;
    }

    .swal2-confirm:active {
        transform: translateY(0) !important;
    }
</style>