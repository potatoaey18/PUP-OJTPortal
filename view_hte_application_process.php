<?php
include '../connection/config.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['auth_user']['userid']) || $_SESSION['auth_user']['userid'] == 0) {
    echo "<script>window.location.href='../pending/login.php'</script>";
    exit;
}

// Fetch company data from the supervisor table
$supervisor_id = isset($_GET['supervisor_id']) ? mysqli_real_escape_string($conn, $_GET['supervisor_id']) : null;

if ($supervisor_id) {
    $query = "SELECT company_name, company_address, supervisor_email, phone_number, supervisor_profile_picture 
              FROM supervisor 
              WHERE uniqueID = '$supervisor_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $company_data = mysqli_fetch_assoc($result);
        // Map database fields to the expected keys
        $company_data['logo'] = !empty($company_data['supervisor_profile_picture']) 
                               ? htmlspecialchars($company_data['supervisor_profile_picture']) 
                               : 'images/cambridge-logo.png';
        $company_data['email'] = htmlspecialchars($company_data['supervisor_email']);
        $company_data['company_email'] = htmlspecialchars($company_data['supervisor_email']);
        $company_data['contact_number'] = htmlspecialchars($company_data['phone_number']);
        $company_data['address'] = htmlspecialchars($company_data['company_address']);
    } else {
        // Fallback data if no supervisor is found
        $company_data = [
            'company_name' => 'Not Found',
            'address' => 'N/A',
            'logo' => 'images/cambridge-logo.png',
            'email' => 'N/A',
            'company_email' => 'N/A',
            'contact_number' => 'N/A'
        ];
    }
} else {
    // Fallback if no supervisor_id is provided
    $company_data = [
        'company_name' => 'Digital Services Cambridge Limited',
        'address' => 'Blk 18 Lt 22 Caliya Nasin Norte, Quezon',
        'logo' => 'images/cambridge-logo.png',
        'email' => 'jamesrobledo@gmail.com',
        'company_email' => 'careers.pexelwebsolution@gmail.com',
        'contact_number' => '0955346790'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: HTE Application Process</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/Picture1.png">
    <!-- Styles -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="endorsement-css/endorsement-moa.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            color: #000;
            overflow-x: hidden;
        }
        .content-wrap {
            height: auto;
            min-height: 100vh;
            width: 100%;
            margin: 0 auto;
        }
        .main-content {
            background-color: white;
            margin-top: 6rem;
            margin-left: 16rem;
            padding: 2rem;
            min-height: calc(100vh - 6rem);
        }
        .page-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        .back-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }
        .back-icon img {
            width: 16px;
            height: 16px;
        }
        .page-title {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .company-profile-card {
            max-width: 100%;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .company-header {
            display: flex;
            align-items: center;
            padding: 20px;
            gap: 20px;
        }
        .company-logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .company-info {
            flex: 1;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        .company-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        .hte-badge {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .requirements-section {
            padding: 20px;
            font-family: source sans pro, sans-serif;
        }
        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
            font-family: source sans pro, sans-serif;
        }
        .requirements-list li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
        }
        .requirements-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #333;
            font-weight: bold;
        }
        .contact-section {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .contact-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #8B0000;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .contact-title:before {
            content: "📞";
            font-size: 18px;
        }
        .contact-info {
            margin-bottom: 20px;
        }
        .contact-row {
            display: flex;
            margin-bottom: 8px;
            align-items: center;
        }
        .contact-label {
            font-weight: 500;
            min-width: 150px;
            color: #333;
        }
        .contact-value {
            font-weight: bold;
            color: #333;
        }
        .email-confirmation {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .confirmation-question {
            font-size: 16px;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .confirmation-question:before {
            content: "❓";
            font-size: 18px;
        }
        .confirmation-buttons {
            display: flex;
            gap: 10px;
        }
        .btn-no {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-yes {
            background-color: #8B0000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-no:hover {
            background-color: #5a6268;
        }
        .btn-yes:hover {
            background-color: #a00000;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <?php require_once 'templates/stud_navbar.php'; ?>

    <div class="content-wrap">
        <div class="main-content">
            <div class="page-header">
                <div>
                    <a href="partner_companies.php" class="back-button">
                        <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                        Back
                    </a>
                </div>
                <div class="page-title">
                    <h1><b>Apply Internship</b></h1>
                </div>
            </div>
            
            <div class="company-profile-card">
                <div class="company-header">
                    <div class="company-logo">
                        <?php if (!empty($company_data['logo']) && file_exists($company_data['logo'])): ?>
                            <img src="<?php echo $company_data['logo']; ?>" alt="Company Logo">
                        <?php else: ?>
                            <img src="images/cambridge-logo.png" alt="Default Logo" style="width: 60px; height: auto;">
                        <?php endif; ?>
                    </div>
                    <div class="company-info">
                        <div class="company-name"><?php echo htmlspecialchars($company_data['company_name']); ?></div>
                        <div class="company-address">address : <?php echo htmlspecialchars($company_data['address']); ?></div>
                        <div class="hte-badge">HTE</div>
                    </div>
                </div>
                
                <div class="requirements-section">
                    <div>Note : Please make sure to send us your CV together with the :</div>
                    <ul class="requirements-list">
                        <li>Memorandum of Agreement</li>
                        <li>Internship Agreement</li>
                        <li>Consent Form</li>
                    </ul>
                </div>
                
                <div class="contact-section">
                    <div class="contact-title"><?php echo htmlspecialchars($company_data['company_name']); ?> Contact/s</div>
                    <div class="contact-info">
                        <div style="margin-bottom: 15px; font-weight: 500;">Here's our contact/s :</div>
                        <div class="contact-row">
                            <span class="contact-label">Email</span>
                            <span>: <?php echo htmlspecialchars($company_data['email']); ?></span>
                        </div>
                        <div class="contact-row">
                            <span class="contact-label">Company Email</span>
                            <span>: <?php echo htmlspecialchars($company_data['company_email']); ?></span>
                        </div>
                        <div class="contact-row">
                            <span class="contact-label">Contact Number</span>
                            <span>: <?php echo htmlspecialchars($company_data['contact_number']); ?></span>
                        </div>
                    </div>
                    
                    <div class="email-confirmation">
                        <div class="confirmation-question">Did you already send an email to us?</div>
                        <div class="confirmation-buttons">
                            <button class="btn-no" onclick="handleResponse('no')">No</button>
                            <button class="btn-yes" onclick="handleResponse('yes')">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>

    <script>
        function handleResponse(response) {
            if (response === 'yes') {
                sweetAlert("Success!", "Thank you for your application. We will review your submission and get back to you soon.", "success");
            } else {
                sweetAlert("Information", "Please send your CV and required documents to the provided email addresses before proceeding.", "info");
            }
        }
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