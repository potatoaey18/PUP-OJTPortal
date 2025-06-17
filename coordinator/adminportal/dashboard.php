<?php

include '../connection/config.php';
error_reporting(0);

session_start();

if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    
}else {
    // Assuming you have a variable $conn which is your database connection
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $query = "SELECT first_name FROM admin_account WHERE id = :admin_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $first_name = isset($result['first_name']) ? $result['first_name'] : "Guest";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- theme meta -->
    <meta name="theme-name" content="focus" />
    <title>OJT Web Portal: Admin Dashboard</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/Picture1.png">
    <!-- Retina iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <!-- Retina iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <!-- Standard iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <!-- Standard iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">
    <!-- Styles -->
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
</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->


    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-r-0 title-margin-right">
                        <div class="page-header">
                            <div class="page-title">
                                <h1 style="color: blue;">Welcome back, <span style="font-size: 18px; color: black;"><?php echo htmlspecialchars($first_name); ?></span></h1>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                    <div class="col-lg-4 p-l-0 title-margin-left">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Home</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                </div>
                <!-- /# row -->
                <section id="main-content">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-user color-primary border-primary"></i>
                                    </div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Trainees</div>
                                        <?php
                                        $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];
            
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM students_data WHERE stud_dept = ?");
                                        $stmt->execute([$course_handled]);
                                        
                                        $count = $stmt->fetchColumn(); // Fetch the count
                                        ?>

                                        <div class="stat-digit"><?php echo $count; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-user color-success border-success"></i>
                                    </div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Coordinators</div>
                                        <?php
                                        $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM coordinators_account WHERE coor_dept = ?");
                                        $stmt->execute([$course_handled]);
                                        $count = $stmt->fetchColumn(); // Fetch the count  
                                        

                                        ?>

                                        <div class="stat-digit"><?php echo $count; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-user color-primary border-primary"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Supervisors</div>
                                        <?php
                                        
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM supervisor");
                                        $stmt->execute();
                                        $count = $stmt->fetchColumn(); // Fetch the count  
                                        

                                        ?>

                                        <div class="stat-digit"><?php echo $count; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-user color-warning border-warning"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Admin</div>
                                        <?php
                                        
                                        $stmt = $conn->prepare("SELECT COUNT(*) FROM admin_account");
                                        $stmt->execute();
                                        $count = $stmt->fetchColumn(); // Fetch the count  
                                        

                                        ?>

                                        <div class="stat-digit"><?php echo $count; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-----------CHARTS-------------->
                    <div class="row">
                    <div class="col-lg-6">
                            <div class="card">
                            <div class="card-title">
                                <h4>Trainee Per Course </h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="pieChart"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                            <div class="card-title">
                                <h4>Trainee Status </h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="pieChart2"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                            <div class="card-title">
                                <h4>Overall Performance Rating</h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="singelBarChart"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                            <div class="card-title">
                                <h4>Performance Rating Frequency</h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="singelBarChart2"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                            <div class="card-title">
                                <h4>Task Accomplishments</h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="singelBarChart3"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>


                        <div class="col-lg-6">
                    <div class="card">
                        <div class="card-title">
                            <h4>Word Cloud</h4>
                        </div>
                        <div class="sales-chart">
                            <?php
                            $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];
                        

                            $stmt = $conn->prepare("SELECT comments_suggestions FROM stud_evaluation
                            LEFT JOIN students_data ON students_data.id = stud_evaluation.stud_id WHERE students_data.stud_dept = ?");
                            $stmt->execute([$course_handled]);

                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Get all comments and suggestions into a single string
                            $comments = '';
                            foreach ($rows as $row) {
                                $comments .= $row['comments_suggestions'] . ' ';
                            }

                            // Split the comments into an array of words
                            $words = str_word_count($comments, 1);

                            // Count occurrences of each word
                            $wordCounts = array_count_values($words);

                            // Generate the word cloud
                            foreach ($wordCounts as $word => $count) {
                                $fontSize = $count * 7; // Adjust the multiplier to change font size based on word frequency
                                $randomColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Generate a random color

                                echo "<span style='font-size: " . $fontSize . "px; color: " . $randomColor . ";'>" . $word . "</span> ";
                            }
                            ?>
                        </div>
                    </div>

                        <!-- /# card -->
                      </div>

                    </div>

                    <div class="col-lg-6">
                            <div class="card">
                            <div class="card-title">
                                <h4>Performance Rating per Criteria </h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="barChart"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>
                    


                    <!-- <div class="row">
                        <div class="col-lg-12">
                            <div class="footer">
                                <p>2024 ©  - <a href="#">Mabuhay</a></p>
                            </div>
                        </div>
                    </div> -->
                </section>
            </div>
        </div>
    </div>

    <!-- jquery vendor -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <!-- nano scroller -->
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <!-- sidebar -->

    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <!-- bootstrap -->


    <script src="js/lib/chart-js/Chart.bundle.js"></script>
    <!-- <script src="js/lib/chart-js/chartjs-init.js"></script> -->

    <!-- scripit init-->
    <script src="js/dashboard2.js"></script>

    
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>

    <?php
$course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];

$stmt = $conn->prepare("SELECT stud_course, COUNT(*) as count FROM students_data WHERE stud_dept= ? GROUP BY stud_course");
$stmt->execute([$course_handled]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stud_course = array_column($rows, 'stud_course');
$counts = array_column($rows, 'count');
?>

<script>
    (function($) {
        "use strict";

        
        var stud_course = <?= json_encode($stud_course) ?>;
        var counts = <?= json_encode($counts) ?>;


        //pie chart
	var ctx = document.getElementById( "pieChart" );
	ctx.height = 300;
	var myChart = new Chart( ctx, {
		type: 'pie',
		data: {
			datasets: [ {
				data: counts,
				backgroundColor: [
                                    "rgba(0, 123, 255,0.9)",
                                    "rgba(0, 123, 255,0.7)",
                                    "rgba(0, 123, 255,0.5)",
                                    "rgba(0,0,0,0.07)",
                                    "rgba(255, 165, 0, 0.5)",
                                    "rgba(255, 206, 86, 0.5)",
                                    "rgba(255, 99, 132, 0.5)",
                                    "rgba(128, 0, 128, 0.5)"
                                ],
				hoverBackgroundColor: [
                                    "rgba(0, 123, 255,0.9)",
                                    "rgba(0, 123, 255,0.7)",
                                    "rgba(0, 123, 255,0.5)",
                                    "rgba(0,0,0,0.07)",
                                    "rgba(255, 165, 0, 0.5)",
                                    "rgba(255, 206, 86, 0.5)",
                                    "rgba(255, 99, 132, 0.5)",
                                    "rgba(128, 0, 128, 0.5)"

                                ]

                            } ],
			labels: stud_course,
		},
		options: {
			responsive: true
		}
	} );

    <?php
    $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];

    $stmt = $conn->prepare("SELECT week, AVG(job_knowledge) as job_knowledgeAVG, AVG(dependability) as dependabilityAVG,
    AVG(communication_skills) as communication_skillsAVG, AVG(conduct) as conductAVG, AVG(initiative_and_creativity) as initiative_and_creativityAVG, 
    AVG(cooperatives_and_relationship) as cooperatives_and_relationshipAVG, 
    AVG(attendance_and_punctuality) as attendance_and_punctualityAVG FROM stud_evaluation LEFT JOIN students_data ON students_data.id = stud_evaluation.stud_id
    WHERE students_data.stud_dept = ? GROUP BY week");
    $stmt->execute([$course_handled]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $weeks = array_map(function($week) {
        return "Week $week";
    }, array_column($rows, 'week'));


    $job_knowledgeAVG = array_column($rows, 'job_knowledgeAVG');

    $dependabilityAVG = array_column($rows, 'dependabilityAVG');

    $communication_skillsAVG = array_column($rows, 'communication_skillsAVG');

    $conductAVG = array_column($rows, 'conductAVG');

    $initiative_and_creativityAVG = array_column($rows, 'initiative_and_creativityAVG');

    $cooperatives_and_relationshipAVG = array_column($rows, 'cooperatives_and_relationshipAVG');

    $attendance_and_punctualityAVG = array_column($rows, 'attendance_and_punctualityAVG');
    ?>


        // Extract PHP arrays for JavaScript use
        var weekData = <?= json_encode($weeks) ?>;
        var job_knowledgeAVG = <?= json_encode($job_knowledgeAVG) ?>;
        var dependabilityAVG = <?= json_encode($dependabilityAVG) ?>;
        var communication_skillsAVG = <?= json_encode($communication_skillsAVG) ?>;
        var conductAVG = <?= json_encode($conductAVG) ?>;
        var initiative_and_creativityAVG = <?= json_encode($initiative_and_creativityAVG) ?>;
        var cooperatives_and_relationshipAVG = <?= json_encode($cooperatives_and_relationshipAVG) ?>;
        var attendance_and_punctualityAVG = <?= json_encode($attendance_and_punctualityAVG) ?>;

        //bar chart
	var ctx = document.getElementById( "barChart" );
	//    ctx.height = 200;
	var myChart = new Chart( ctx, {
		type: 'bar',
		data: {
			labels: weekData,
			datasets: [
				{
					label: "Job Knowledge",
					data: job_knowledgeAVG,
                    borderColor: "rgba(255, 99, 132, 1)", // Red color
					borderWidth: "0",
					backgroundColor: "rgba(255, 99, 132, 0.5)"
                            },
                {
					label: "Dependability",
					data: dependabilityAVG,
                    borderColor: "rgba(54, 162, 235, 1)", // Blue color
                    borderWidth: "0",
                    backgroundColor: "rgba(54, 162, 235, 0.5)"
                            },
                {
					label: "Communication Skills",
					data: communication_skillsAVG,
                    borderColor: "rgba(255, 206, 86, 1)", // Yellow color
                    borderWidth: "0",
                    backgroundColor: "rgba(255, 206, 86, 0.5)"
                            },
                            {
					label: "Conduct",
					data: conductAVG,
					borderColor: "rgba(255, 165, 0, 1)", // Orange color
                    borderWidth: "0",
                    backgroundColor: "rgba(255, 165, 0, 0.5)"
                            },
                {
					label: "Initiative & Creativity",
					data: initiative_and_creativityAVG,
					borderColor: "rgba(0, 255, 255, 1)", // Cyan color
                    borderWidth: "0",
                    backgroundColor: "rgba(0, 255, 255, 0.5)"

                            },
                {
					label: "Cooperatives & Relationship",
					data: cooperatives_and_relationshipAVG,
					borderColor: "rgba(128, 0, 128, 1)", // Purple color
                    borderWidth: "0",
                    backgroundColor: "rgba(128, 0, 128, 0.5)"


                            },
                {
					label: "Attendance & Punctuality",
					data: attendance_and_punctualityAVG,
					borderColor: "rgba(50, 205, 50, 1)", // Purple color
                    borderWidth: "0",
                    backgroundColor: "rgba(50, 205, 50, 0.5)"


                            }


                        ]
		},
        options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            xAxes: [{
                ticks: {
                    autoSkip: false,
                    maxRotation: 90,
                    minRotation: 45,
                    callback: function(value, index, values) {
                        // Change font size based on window width
                        var fontSize = window.innerWidth < 512 ? 10 : 14;
                        return Chart.helpers.isArray(value) ? value.join(' ') : value;
                    }
                }
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});

    <?php

$stmt = $conn->prepare("SELECT ojt_status, COUNT(*) as count FROM students_data GROUP BY ojt_status");
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ojt_status = array_column($rows, 'ojt_status');
$counts = array_column($rows, 'count');
?>

    var ojt_status = <?= json_encode($ojt_status) ?>;
    var counts = <?= json_encode($counts) ?>;

        //pie chart
	var ctx = document.getElementById( "pieChart2" );
	ctx.height = 300;
	var myChart = new Chart( ctx, {
		type: 'pie',
		data: {
			datasets: [ {
				data: counts,
				backgroundColor: [
                                    "rgba(0, 123, 255,0.9)",
                                    "rgba(0, 123, 255,0.7)",
                                    "rgba(0, 123, 255,0.5)",
                                    "rgba(0,0,0,0.07)",
                                    "rgba(255, 165, 0, 0.5)",
                                    "rgba(255, 206, 86, 0.5)",
                                    "rgba(255, 99, 132, 0.5)",
                                    "rgba(128, 0, 128, 0.5)"
                                ],
				hoverBackgroundColor: [
                                    "rgba(0, 123, 255,0.9)",
                                    "rgba(0, 123, 255,0.7)",
                                    "rgba(0, 123, 255,0.5)",
                                    "rgba(0,0,0,0.07)",
                                    "rgba(255, 165, 0, 0.5)",
                                    "rgba(255, 206, 86, 0.5)",
                                    "rgba(255, 99, 132, 0.5)",
                                    "rgba(128, 0, 128, 0.5)"

                                ]

                            } ],
			labels: ojt_status,
		},
		options: {
			responsive: true
		}
	} );


    <?php
     $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];

    $stmt = $conn->prepare("SELECT AVG(total_points) as total_pointsAVG FROM stud_evaluation
    LEFT JOIN students_data ON students_data.id = stud_evaluation.stud_id WHERE students_data.stud_dept = ?");
    $stmt->execute([$course_handled]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



    $total_pointsAVG = array_column($rows, 'total_pointsAVG');

    ?>


        var total_pointsAVG = <?= json_encode($total_pointsAVG) ?>;

    // single bar chart
	var ctx = document.getElementById( "singelBarChart" );
	ctx.height = 150;
	var myChart = new Chart( ctx, {
		type: 'bar',
		data: {
			datasets: [
				{
                    label: "Overall Performance Rating Percentage",
					data: total_pointsAVG,
					borderColor: "rgba(50, 205, 50, 1)",
                    borderWidth: "0",
                    backgroundColor: "rgba(50, 205, 50, 0.5)"
                            }
                        ]
		},
		options: {
			scales: {
				yAxes: [ {
					ticks: {
						beginAtZero: true
					}
                                } ]
			}
		}
	} );



    <?php
    $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];

    $stmt = $conn->prepare("SELECT total_points FROM stud_evaluation
        LEFT JOIN students_data ON students_data.id = stud_evaluation.stud_id WHERE students_data.stud_dept = ?");
    $stmt->execute([$course_handled]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extract total points
    $total_points = array_column($rows, 'total_points');

    // Count occurrences of each total point value
    $total_points_count = array_count_values($total_points);

    // Prepare data for chart
    $labels = array_keys($total_points_count); // Unique total points as labels
    $counts = array_values($total_points_count); // Frequency of each total point value
    ?>

var labels = <?= json_encode($labels) ?>;
    var counts = <?= json_encode($counts) ?>;

    var ctx = document.getElementById("singelBarChart2");
    ctx.height = 150;
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Performance Rating Frequency",
                data: counts,
                borderColor: "rgba(255, 165, 0, 1)", // Orange color
                borderWidth: "0",
                backgroundColor: "rgba(255, 165, 0, 0.5)"
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    <?php
    $course_handled = $_SESSION['auth_user']['coordinator_courseHANDLED'];
    
    $task_status = 'Finished';

    $stmt = $conn->prepare("SELECT COUNT(task_status) AS finished_tasks FROM stud_task_list
    LEFT JOIN students_data ON students_data.id = stud_task_list.stud_id WHERE students_data.stud_dept = ? AND stud_task_list.task_status = ?");
    $stmt->execute([$course_handled, $task_status]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



    $finished_tasks = array_column($rows, 'finished_tasks');

    ?>


        var finished_tasks = <?= json_encode($finished_tasks) ?>;

    // single bar chart
	var ctx = document.getElementById( "singelBarChart3" );
	ctx.height = 150;
	var myChart = new Chart( ctx, {
		type: 'bar',
		data: {
			datasets: [
				{
                    label: "Task Accomplishments",
					data: finished_tasks,
					borderColor: "rgba(0, 123, 255, 0.9)",
					borderWidth: "0",
					backgroundColor: "rgba(0, 123, 255, 0.5)"
                            }
                        ]
		},
		options: {
			scales: {
				yAxes: [ {
					ticks: {
						beginAtZero: true
					}
                                } ]
			}
		}
	} );





    })(jQuery);
</script>




    <?php 
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {

?>
    <script>
    sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
    </script>
<?php
unset($_SESSION['status']);
}
?>

</body>

</html>
