<?php
session_start();
require '../connection/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/logs/php_errors.log');

// Generate CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html style="font-size: 16px;" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title>Sign Up - PUP ITECH OJT Portal</title>
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link rel="stylesheet" href="css/nicepage.css" media="screen">
    <link rel="stylesheet" href="css/Page-2.css" media="screen">
    <script class="u-script" type="text/javascript" src="js/jquery.js" defer=""></script>
    <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap');
        body {
            font-family: 'Arial', sans-serif;
        }

        .back-button {
            display: flex;
            align-items: center;
            background: none;
            margin-left: -3rem;
            color: rgba(128, 128, 128, 0.5);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 400;
            transition: 0.3s;
        }

        .back-button img {
            height: 40px;
            filter: grayscale(100%);
            opacity: 0.3;
            transition: filter 0.3s ease, opacity 0.3s ease;
        }

        .back-button:hover,
        .back-button:hover img {
            color: #9B0C0C;
            filter: grayscale(0%) sepia(100%) hue-rotate(330deg) saturate(500%);
            opacity: 1;
        }
        
        .nav-1 {
            font-family: 'Source Serif 4', serif;
            background: linear-gradient(to left, rgba(155, 12, 12, 1), rgba(255, 255, 255, 1));
            color: #D11010;
            padding: 15px 0;
            text-align: left;
            font-size: 20px;
            font-weight: 400;
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            display: flex;
            align-items: center;
            background-clip: padding-box;
            z-index: 1000;
        }

        .nav-logo {
            height: 50px;
            margin-left: 20px;
        }

        .nav-title-caption-container {
            display: flex;
            flex-direction: column;
            margin-left: 20px;
        }

        .nav-title {
            font-size: 24px;
            font-weight: bold;
        }

        .nav-caption {
            font-size: 16px;
            color: #000;
            font-weight: normal;
        }

        .register-section {
            padding: 10px 80px;
            flex-direction: column;
            gap: 10px;
            line-height: 1.5;
            margin-top: 100px;
            width: 100%;
        }

        h5 {
            font-size: 18px;
            font-weight: 600;
            align-items: center;
            margin-top: -10px;
            justify-content: flex-start;
            display: flex;
            line-height: 1;
            color: #9B0C0C;
        }

        h5 img {
            margin-right: 10px;
            filter: invert(10%) sepia(88%) saturate(5144%) hue-rotate(356deg) brightness(97%) contrast(106%);
        }

        input, select {
            width: 100%;
            max-width: 100%;
            height: 60px;
            padding: 20px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            outline: none;
            color: #333;
            font-size: 14px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        input[type="file"] {
            padding: 5px;
            height: auto;
        }

        label {
            font-size: 14px;
            font-weight: 400;
            color: #000;
            margin-left: 0;
            display: block;
            text-align: left;
        }

        .register-button {
            background: #9B0C0C;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            margin-top: 20px;
            transition: 0.3s;
            width: 100%;
            height: 50px;
        }

        .error-message {
            color: red;
            margin-bottom: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            width: 100%;
        }

        .form-grid > div {
            display: flex;
            flex-direction: column;
        }

        .form-grid .full-width {
            grid-column: span 3;
        }

        .required {
            color: red;
            font-weight: bold;
            margin-left: 5px;
        }

        #sis-document-field, #course-field, #year-field, #section-field, #department-field, #guardian-name-field, #guardian-phone-field, #custom-gender-field {
            display: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .nav-logo {
                height: 40px;
            }

            .nav-title {
                font-size: 20px;
            }

            .nav-caption {
                font-size: 14px;
            }

            .register-section {
                margin-top: 150px;
                width: 95%;
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-grid .full-width {
                grid-column: span 1;
            }

            input, select {
                height: 38px;
                font-size: 13px;
            }

            label {
                font-size: 13px;
            }

            .register-button {
                height: 45px;
                font-size: 16px;
            }

            .back-button {
                margin-left: 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .nav-logo {
                height: 35px;
            }

            .nav-title {
                font-size: 18px;
            }

            .nav-caption {
                font-size: 12px;
            }

            .register-section {
                margin-top: 130px;
                width: 100%;
                padding: 10px;
            }

            input, select {
                height: 36px;
                font-size: 12px;
            }

            label {
                font-size: 12px;
            }

            .register-button {
                height: 40px;
                font-size: 14px;
            }
        }
    </style>
    <script>
        function updateFormAction() {
            const role = document.getElementById('role').value;
            const form = document.getElementById('register-form');
            form.action = '../php/pending_registerCode.php';
        }

        document.addEventListener('DOMContentLoaded', updateFormAction);

        function toggleSisDocument() {
            const role = document.getElementById('role').value;
            const isStudent = role === 'student';
            document.getElementById('sis-document-field').style.display = isStudent ? 'block' : 'none';
            document.getElementById('course-field').style.display = isStudent ? 'block' : 'none';
            document.getElementById('year-field').style.display = isStudent ? 'block' : 'none';
            document.getElementById('section-field').style.display = isStudent ? 'block' : 'none';
            document.getElementById('department-field').style.display = isStudent ? 'block' : 'none';
            document.getElementById('guardian-name-field').style.display = isStudent ? 'block' : 'none';
            document.getElementById('guardian-phone-field').style.display = isStudent ? 'block' : 'none';
        }

        function toggleCustomGender() {
            const gender = document.getElementById('gender').value;
            document.getElementById('custom-gender-field').style.display = gender === 'Other' ? 'block' : 'none';
        }

        function validateForm() {
            const role = document.getElementById('role').value;
            const firstName = document.getElementById('first-name').value;
            const email = document.getElementById('email').value;
            const idNumber = document.getElementById('id-number').value;
            const errorMessage = document.getElementById('error-message');

            // Debug: Log form data before submission
            console.log('Form Submission Data:', {
                role,
                first_name: firstName,
                email,
                id_number: idNumber
            });

            errorMessage.textContent = '';

            // Minimal validation for first name
            if (!firstName.trim()) {
                errorMessage.textContent = 'Please enter your first name.';
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <nav class="nav-1">
        <img src="images/pupLogo.png" alt="PUP Logo" class="nav-logo">
        <div class="nav-title-caption-container">
            <div class="nav-title">Polytechnic University of the Philippines-ITECH</div>
            <div class="nav-caption">On the Job Training Portal</div>
        </div>
    </nav>

    <section>
        <div class="register-section">
            <header class="header">
                <a href="/pup/index.php" class="back-button">
                    <span class="back-icon"><img src="images/less-than.png" alt="Back"></span>
                    Back
                </a><br>
                <h5>
                    <img src="images/pencil.svg" alt="Edit Icon" height="27">
                    REGISTRATION
                </h5>
            </header>
            <p id="error-message" class="error-message"><?php echo isset($_SESSION['status']) ? htmlspecialchars($_SESSION['status']) : ''; ?></p>
            <form id="register-form" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-grid">
                    <div>
                        <label for="role">Role <span class="required">*</span></label>
                        <select id="role" name="role" onchange="updateFormAction(); toggleSisDocument()" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="admin">Administrator</option>
                            <option value="coordinator">Adviser</option>
                            <option value="supervisor">Supervisor (H.T.E)</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div>
                        <label for="id-number">ID Number <span class="required">*</span></label>
                        <input type="text" id="id-number" name="id_number" placeholder="Enter ID Number" required maxlength="50">
                    </div>
                    <div>
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Enter Email" required maxlength="200">
                    </div>
                    <div>
                        <label for="first-name">First Name <span class="required">*</span></label>
                        <input type="text" id="first-name" name="first_name" placeholder="Enter First Name" required maxlength="70">
                    </div>
                    <div>
                        <label for="middle-name">Middle Name <span class="required">*</span></label>
                        <input type="text" id="middle-name" name="middle_name" placeholder="Enter Middle Name" required maxlength="70">
                    </div>
                    <div>
                        <label for="last-name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last-name" name="last_name" placeholder="Enter Last Name" required maxlength="70">
                    </div>
                    <div>
                        <label for="address">Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" placeholder="Enter Address" required maxlength="200">
                    </div>
                    <div>
                        <label for="age">Age <span class="required">*</span></label>
                        <input type="number" id="age" name="age" placeholder="Enter Age" min="18" required>
                    </div>
                    <div>
                        <label for="phone-number">Phone Number <span class="required">*</span></label>
                        <input type="text" id="phone-number" name="phone_number" placeholder="Enter Your Phone Number" required maxlength="12">
                    </div>
                    <div>
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select id="gender" name="gender" onchange="toggleCustomGender()" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Prefer Not to Say">Prefer Not to Say</option>
                            <option value="Other">Other</option>
                        </select>
                        <div id="custom-gender-field">
                            <label for="custom-gender">Specify Gender <span class="required">*</span></label>
                            <input type="text" id="custom-gender" name="custom_gender" placeholder="Enter Your Gender" maxlength="100">
                        </div>
                    </div>
                    <div>
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Enter Password" required>
                    </div>
                    <div>
                        <label for="confirm-password">Confirm Password <span class="required">*</span></label>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <!-- Student Fields -->
                    <div id="sis-document-field">
                        <label for="sis-document">SIS Document (PDF, Students Only) <span class="required">*</span></label>
                        <input type="file" id="sis-document" name="sis_document" accept="application/pdf">
                    </div>
                    <div id="course-field">
                        <label for="course">Course <span class="required">*</span></label>
                        <select id="course" name="stud_course">
                            <option value="" disabled selected>Select Course</option>
                            <option value="DCvET">Diploma in Civil Engineering Technology (DCvET)</option>
                            <option value="DCET">Diploma in Computer Engineering Technology (DCET)</option>
                            <option value="DEET">Diploma in Electrical Engineering Technology (DEET)</option>
                            <option value="DECET">Diploma in Electronics Engineering Technology (DECET)</option>
                            <option value="DIT">Diploma in Information Technology (DIT)</option>
                            <option value="DMET">Diploma in Mechanical Engineering Technology (DMET)</option>
                            <option value="DOMT">Diploma in Office Management Technology (DOMT)</option>
                            <option value="DRET">Diploma in Railway Engineering Technology (DRET)</option>
                        </select>
                    </div>
                    <div id="year-field">
                        <label for="year">Year <span class="required">*</span></label>
                        <select id="year" name="year_level">
                            <option value="" disabled selected>Select Year</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div id="section-field">
                        <label for="section">Section <span class="required">*</span></label>
                        <select id="section" name="section">
                            <option value="" disabled selected>Select Section</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div id="department-field">
                        <label for="department">Department <span class="required">*</span></label>
                        <input type="text" id="department" name="department" value="ITECH" readonly>
                    </div>
                    <div id="guardian-name-field">
                        <label for="guardian-name">Guardian's Name <span class="required">*</span></label>
                        <input type="text" id="guardian-name" name="guardian_name" placeholder="Enter Guardian's Name" maxlength="200">
                    </div>
                    <div id="guardian-phone-field">
                        <label for="guardian-phone">Guardian's Phone Number <span class="required">*</span></label>
                        <input type="text" id="guardian-phone" name="guardian_phone" placeholder="Enter Guardian's Phone Number" maxlength="20">
                    </div>
                </div>
                <button type="submit" name="register" class="register-button full-width">Register</button>
            </form>
        </div>
    </section>

    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>

    <?php 
    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
    <script>
    sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
    <?php
    unset($_SESSION['status'], $_SESSION['alert'], $_SESSION['status-code']);
    ?>
    </script>
    <?php
    }
    ?>
</body>
</html>