<?php
ob_start(); // Start output buffering
include '../connection/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/moa_errors.log');
session_start();

// Check if user is logged in
if (!isset($_SESSION['auth_user']['supervisor_id']) || $_SESSION['auth_user']['supervisor_id'] == 0) {
    ob_end_clean();
    echo "<script>window.location.href='index.php'</script>";
    exit();
}

$supervisorID = $_SESSION['auth_user']['supervisor_id'];
$supervisor = null;
$error = '';

// Load most recent recruitment form data for display
$existingForm = null;
try {
    $stmt = $conn->prepare("SELECT * FROM recruitment_form WHERE supervisor_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$supervisorID]);
    $existingForm = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existingForm) {
        error_log('Found existing recruitment form data: ' . print_r($existingForm, true));
    }
} catch (Exception $e) {
    error_log('Error loading existing recruitment form: ' . $e->getMessage());
}

// Fetch supervisor details
try {
    $stmt = $conn->prepare("SELECT * FROM supervisor WHERE id = ?");
    $stmt->execute([$supervisorID]);
    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$supervisor) {
        throw new Exception("Supervisor with ID $supervisorID not found in database");
    }
    error_log('Supervisor found: ID ' . $supervisorID);
} catch (Exception $e) {
    $error = 'Error fetching supervisor: ' . $e->getMessage();
    error_log($error);
}

// Function to display errors
function displayError($message, $details = '') {
    echo '<div style="background: #ffebee; border-left: 4px solid #f44336; padding: 10px; margin: 10px 0; color: #b71c1c;">';
    echo '<strong>Error:</strong> ' . htmlspecialchars($message);
    if ($details) {
        echo '<div style="margin-top: 5px; font-family: monospace; font-size: 12px;">';
        echo htmlspecialchars($details);
        echo '</div>';
    }
    echo '</div>';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['moa-renew-submit'])) {
    error_log('POST data: ' . print_r($_POST, true));
    error_log('SESSION data: ' . print_r($_SESSION, true));

    try {
        // Get form data
        $internship_department = trim($_POST['internship_department'] ?? '');
        $supervisor_name = trim($_POST['supervisor_name'] ?? '');
        $nature_of_duties = $_POST['nature_of_duties'] ?? '';
        $modality = $_POST['modality'] ?? '';
        $supervisor_email = trim($_POST['supervisor_email'] ?? '');
        $agree_terms = $_POST['agree_terms'] ?? 'no';

        // Validate required fields
        if (empty($internship_department) || empty($supervisor_name) || empty($nature_of_duties) || empty($modality) || empty($supervisor_email)) {
            throw new Exception('Please fill in all required fields.');
        }

        // Validate JSON for nature_of_duties
        if (!json_decode($nature_of_duties, true)) {
            throw new Exception('Invalid data format for Nature of Duties.');
        }

        // Validate email format
        if (!filter_var($supervisor_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address (e.g., example@gmail.com)');
        }

        // Verify terms were agreed to
        if ($agree_terms !== 'yes') {
            throw new Exception('You must agree to the terms and conditions to submit the form.');
        }

        // Verify supervisor exists
        $stmt = $conn->prepare("SELECT id FROM supervisor WHERE id = ?");
        if (!$stmt->execute([$supervisorID])) {
            throw new Exception('Error checking supervisor in database.');
        }
        if ($stmt->rowCount() === 0) {
            throw new Exception('Supervisor not found in database. ID: ' . $supervisorID);
        }

        // Begin transaction
        $conn->beginTransaction();

        // Insert new record
        $sql = "INSERT INTO recruitment_form (
            supervisor_id,
            internship_department,
            supervisor_name,
            nature_of_duties,
            modality,
            supervisor_email,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $params = [
            $supervisorID,
            $internship_department,
            $supervisor_name,
            $nature_of_duties,
            $modality,
            $supervisor_email
        ];

        $stmt = $conn->prepare($sql);
        error_log('Executing query: ' . $sql);
        error_log('With params: ' . print_r($params, true));

        if (!$stmt->execute($params)) {
            throw new Exception('Failed to save form data: ' . ($stmt->errorInfo()[2] ?? 'Unknown error'));
        }

        $lastInsertId = $conn->lastInsertId();
        error_log('Last insert ID: ' . $lastInsertId);

        // Store debug data in session instead of outputting
        $debugData = $_POST;
        if (!empty($debugData['nature_of_duties'])) {
            $dutiesArray = json_decode($debugData['nature_of_duties'], true);
            $debugData['nature_of_duties'] = is_array($dutiesArray) ? implode(', ', $dutiesArray) : $debugData['nature_of_duties'];
        }
        $_SESSION['form_debug'] = $debugData;

        // Commit transaction
        $conn->commit();
        error_log('Transaction committed successfully');

        // Set success message
        $_SESSION['success'] = 'Recruitment form submitted successfully!';
        $_SESSION['alert'] = 'Success';
        $_SESSION['status_code'] = 'success';

        // Redirect to the same page to show success message
        ob_end_clean();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error = $e->getMessage();
        error_log('Form submission error: ' . $error);
        displayError($error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJT Web Portal: Appointment</title>
    <link rel="shortcut icon" href="images/pupLogo.png">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap');
        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            margin-top: -30rem;
        }
        .content-wrap {
            width: 100%;
            margin: 0 auto;
            position: relative;
            padding-left: 19.5rem;
            padding-top: 7rem;
        }
        .profile-container {
            background-color: #fff;
            position: fixed;
            top: 7rem;
            left: 19.5rem;
            right: 0;
            bottom: 0;
            padding: 2rem;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .page-title p {
            color: #000;
            margin-bottom: 4px;
        }
        .form-label {
            font-family: 'Source Sans Pro', Arial, sans-serif !important;
            font-size: 1.1rem !important;
            color: #000 !important;
            font-weight: 100 !important;
        }
        .form-control {
            background: #d9d9d9 !important;
            border: 1px solid #d9d9d9 !important;
        }
        .btn-agree-choice {
            padding: 6px 0 !important;
            border-radius: 10px !important;
            font-size: 18px !important;
            font-weight: 250 !important;
            transition: all 0.3s ease;
        }
        .btn-agree-choice:hover {
            cursor: pointer;
        }
        .force-underline {
            text-decoration: underline !important;
        }
        .swal2-popup {
            border-radius: 40px !important;
            padding: 60px 30px 40px 30px !important;
            background-color: #700000 !important;
        }
        .swal2-icon {
            position: absolute !important;
            left: 50% !important;
            top: 20px !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
            background-color: #700000 !important;
            border: 3px solid #ffc107 !important;
            color: #ffc107 !important;
        }
        .swal2-icon.swal2-warning .swal2-icon-content {
            color: #ffc107 !important;
        }
        .swal2-icon.swal2-warning {
            margin-top: -20px !important;
        }
        .swal-confirm-proceed {
            background-color: #fff !important;
            color: #000 !important;
            padding: 12px 50px !important;
            margin-top: 1rem;
        }
        .swal-confirm-proceed:hover {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
        .swal-text-white {
            color: #fff !important;
        }
        .move-text-up {
            margin-top: -20px !important;
        }
        .title-color {
            color: #ffc107 !important;
        }
    </style>
</head>
<body>
    <?php require_once 'templates/supervisor_navbar.php'; ?>

    <div class="content-wrap">
        <div class="profile-container">
            <div class="page-header">
                <div class="page-title">
                    <br>
                    <h1 style="font-size: 17px;"><b>Launch Recruitment</b></h1>
                    <br><br>

                    <form method="post" action="">
                        <input type="hidden" name="moa-renew-submit" value="1">
                        <div class="form-group mb-3">
                            <label for="internship_department" class="form-label"><b>Internship Department <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="internship_department" name="internship_department" required autocomplete="off" placeholder="Ex. Information Technology" value="<?php echo isset($existingForm['internship_department']) ? htmlspecialchars($existingForm['internship_department']) : ''; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="supervisor_name" class="form-label"><b>Supervisor's Name <span style='color:red'>*</span></b></label>
                            <input type="text" class="form-control" id="supervisor_name" name="supervisor_name" required autocomplete="off" value="<?php echo htmlspecialchars($supervisor['name'] ?? ''); ?>">
                        </div>
                        <?php
                        $duties = [];
                        try {
                            $stmt = $conn->query("SELECT duty_name FROM nature_of_duties WHERE is_active = TRUE ORDER BY duty_name");
                            $duties = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        } catch (PDOException $e) {
                            error_log("Error fetching duty options: " . $e->getMessage());
                        }
                        $dutiesJson = htmlspecialchars(json_encode($duties), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="form-group mb-3">
                            <label class="form-label"><b>Nature of Duties <span style='color:red'>*</span></b></label>
                            <div class="selected-tags-container mb-2 p-2" style="min-height: 44px;" id="selectedTags">
                                <div id="selectedTagsDisplay" class="d-flex flex-wrap gap-2">No duties selected</div>
                            </div>
                            <div class="searchable-dropdown">
                                <input type="text" class="form-control" id="nature_of_duties" placeholder="Search or type a duty..." autocomplete="off" data-options="<?php echo $dutiesJson; ?>">
                                <input type="hidden" name="nature_of_duties" id="nature_of_duties_hidden" required value="<?php echo isset($existingForm['nature_of_duties']) ? htmlspecialchars($existingForm['nature_of_duties']) : ''; ?>">
                                <div class="dropdown-options"></div>
                            </div>
                        </div>
                        <style>
                            .searchable-dropdown {
                                position: relative;
                                width: 100%;
                            }
                            .dropdown-options {
                                display: none;
                                position: absolute;
                                width: 100%;
                                max-height: 200px;
                                overflow-y: auto;
                                background: white;
                                border: 1px solid #ced4da;
                                border-top: none;
                                border-radius: 0 0 0.25rem 0.25rem;
                                z-index: 1000;
                                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                            }
                            .dropdown-options.show {
                                display: block;
                            }
                            .dropdown-options div {
                                padding: 8px 12px;
                                cursor: pointer;
                                transition: background-color 0.2s;
                            }
                            .dropdown-options div:hover {
                                background-color: #f8f9fa;
                            }
                            .selected-tag {
                                display: inline-flex;
                                align-items: center;
                                background-color: rgb(131, 131, 131);
                                border-radius: 16px;
                                padding: 4px 12px;
                                margin: 2px;
                                font-size: 14px;
                            }
                            .selected-tag .remove-tag {
                                margin-left: 6px;
                                cursor: pointer;
                                font-weight: bold;
                                color: rgb(22, 22, 22) !important;
                            }
                            .selected-tag .remove-tag:hover {
                                color: #dc3545;
                            }
                        </style>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            try {
                                const input = document.getElementById('nature_of_duties');
                                const hiddenInput = document.getElementById('nature_of_duties_hidden');
                                const optionsContainer = document.querySelector('.dropdown-options');
                                const selectedTagsContainer = document.getElementById('selectedTags');
                                const options = JSON.parse(input.getAttribute('data-options'));
                                let selectedOptions = [];

                                // Clear any existing values on page load
                                selectedOptions = [];
                                renderSelectedTags();
                                updateHiddenInput();

                                input.addEventListener('focus', showOptions);
                                input.addEventListener('input', showOptions);

                                input.addEventListener('keydown', function(e) {
                                    if ((e.key === 'Enter' || e.key === ',') && input.value.trim()) {
                                        e.preventDefault();
                                        addTag(input.value.trim().replace(/,+$/, ''));
                                    }
                                });

                                document.addEventListener('click', function(e) {
                                    if (!input.contains(e.target) && !optionsContainer.contains(e.target)) {
                                        optionsContainer.classList.remove('show');
                                    }
                                });

                                function showOptions() {
                                    const searchTerm = input.value.toLowerCase().trim();
                                    let filteredOptions = options.filter(opt => 
                                        !selectedOptions.includes(opt) && 
                                        (searchTerm === '' || opt.toLowerCase().includes(searchTerm))
                                    );

                                    if (searchTerm && !options.includes(searchTerm) && !selectedOptions.includes(searchTerm)) {
                                        filteredOptions.unshift(`Add "${searchTerm}"`);
                                    }

                                    renderOptions(filteredOptions);
                                }

                                function renderOptions(optionsToShow) {
                                    optionsContainer.innerHTML = '';
                                    if (optionsToShow.length === 0) {
                                        optionsContainer.style.display = 'none';
                                        return;
                                    }

                                    optionsToShow.forEach(option => {
                                        const isCustomAdd = option.startsWith('Add "');
                                        const optionElement = document.createElement('div');
                                        optionElement.textContent = option;
                                        optionElement.style.color = isCustomAdd ? '#0d6efd' : '';
                                        optionElement.style.fontStyle = isCustomAdd ? 'italic' : '';

                                        optionElement.addEventListener('mousedown', function(e) {
                                            e.preventDefault();
                                            const selectedOption = isCustomAdd ? option.replace(/^Add \"|\"$/g, '') : option;
                                            addTag(selectedOption);
                                        });

                                        optionsContainer.appendChild(optionElement);
                                    });

                                    optionsContainer.classList.add('show');
                                }

                                function addTag(tag) {
                                    if (!tag || selectedOptions.includes(tag)) return;
                                    selectedOptions.push(tag);
                                    updateHiddenInput();
                                    renderSelectedTags();
                                    input.value = '';
                                    input.focus();
                                    showOptions();
                                }

                                function removeTag(tagToRemove) {
                                    selectedOptions = selectedOptions.filter(tag => tag !== tagToRemove);
                                    updateHiddenInput();
                                    renderSelectedTags();
                                    showOptions();
                                }

                                function renderSelectedTags() {
                                    const tagsDisplay = document.getElementById('selectedTagsDisplay');
                                    tagsDisplay.innerHTML = '';

                                    if (selectedOptions.length === 0) {
                                        tagsDisplay.innerHTML = '<span class="text-muted">No duties selected</span>';
                                        updateHiddenInput();
                                        return;
                                    }

                                    const tagContainer = document.createElement('div');
                                    tagContainer.className = 'd-flex flex-wrap gap-2';

                                    selectedOptions.forEach((tag, index) => {
                                        const tagElement = document.createElement('span');
                                        tagElement.className = 'selected-tag d-inline-flex align-items-center bg-light rounded-pill px-3 py-1';
                                        tagElement.innerHTML = `
                                            ${tag}
                                            <span class="remove-tag ms-2 fw-bold" style="cursor:pointer;" data-index="${index}">×</span>
                                        `;
                                        tagContainer.appendChild(tagElement);
                                    });

                                    tagsDisplay.appendChild(tagContainer);

                                    document.querySelectorAll('.remove-tag').forEach(btn => {
                                        btn.addEventListener('click', function(e) {
                                            e.stopPropagation();
                                            const index = parseInt(this.getAttribute('data-index'));
                                            removeTag(selectedOptions[index]);
                                        });
                                    });

                                    updateHiddenInput();
                                }

                                function updateHiddenInput() {
                                    hiddenInput.value = JSON.stringify(selectedOptions);
                                    hiddenInput.required = selectedOptions.length === 0;
                                }

                                renderSelectedTags();
                            } catch (e) {
                                console.error('Error in nature_of_duties script:', e);
                            }
                        });

                        document.addEventListener('DOMContentLoaded', function() {
                            try {
                                const form = document.querySelector('form');
                                const submitBtn = document.getElementById('moa-renew-submit');

                                form.addEventListener('submit', function(e) {
                                    const emailInput = document.getElementById('supervisor_email');
                                    const email = emailInput.value.trim();
                                    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                                    if (!emailRegex.test(email)) {
                                        e.preventDefault();
                                        Swal.fire({
                                            icon: 'error',
                                            title: '<span style="color: #ffc107;">Invalid Email</span>',
                                            html: '<span style="color: #ffffff;">Please enter a valid email address (e.g., example@gmail.com)</span>',
                                            customClass: { confirmButton: 'swal-confirm-proceed' }
                                        });
                                        emailInput.focus();
                                        return false;
                                    }

                                    const requiredFields = [
                                        document.getElementById('internship_department'),
                                        document.getElementById('supervisor_name'),
                                        document.getElementById('nature_of_duties_hidden'),
                                        document.getElementById('modality'),
                                        document.getElementById('supervisor_email')
                                    ];
                                    let emptyField = false;
                                    requiredFields.forEach(f => {
                                        if (!f.value.trim()) emptyField = true;
                                    });

                                    const agreeInput = document.getElementById('agree_terms');
                                    if (!agreeInput.value || agreeInput.value === 'no') {
                                        e.preventDefault();
                                        Swal.fire({
                                            title: 'Cannot Proceed',
                                            html: '<span class="swal-text-white move-text-up">You cannot proceed if you do not agree to the University\'s Terms of Agreement.</span>',
                                            icon: 'warning',
                                            customClass: {
                                                popup: 'swal2-popup',
                                                icon: 'swal2-icon',
                                                title: 'title-color',
                                                confirmButton: 'swal-confirm-proceed'
                                            }
                                        });
                                        return false;
                                    }

                                    if (emptyField) {
                                        e.preventDefault();
                                        Swal.fire({
                                            title: 'Incomplete Form',
                                            html: '<span class="swal-text-white move-text-up">Please fill out all required fields.</span>',
                                            icon: 'warning',
                                            customClass: {
                                                popup: 'swal2-popup',
                                                icon: 'swal2-icon',
                                                title: 'title-color',
                                                confirmButton: 'swal-confirm-proceed'
                                            }
                                        });
                                        return false;
                                    }

                                    submitBtn.disabled = true;
                                    submitBtn.innerHTML = 'Submitting...';
                                    return true;
                                });
                            } catch (e) {
                                console.error('Error in form validation script:', e);
                            }
                        });
                        </script>
                        <div class="form-group mb-3">
                            <label for="modality" class="form-label"><b>Modality <span style='color:red'>*</span></b></label>
                            <select class="form-control" id="modality" name="modality" required>
                                <option value="">Select Modality</option>
                                <option value="Onsite">Onsite</option>
                                <option value="Remote">Remote</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="supervisor_email" class="form-label"><b>Supervisor's Email <span style='color:red'>*</span></b></label>
                            <input type="email" class="form-control" id="supervisor_email" name="supervisor_email" required autocomplete="off" value="<?php echo htmlspecialchars($supervisor['email'] ?? ''); ?>">
                        </div>
                        <br>
                        <div class="mb-4" style="font-size:1rem; color:#222;">
                            Do you agree to the Polytechnic University of the Philippines' <a href="#" class="force-underline" style="color:#0c0c9b; font-weight:bold;">Terms of Agreement</a>? <span style="color:red">*</span>
                        </div>
                        <div class="mb-4 d-flex" style="gap: 20px; justify-content: flex-start;">
                            <button type="button" class="btn-agree-choice" id="btn-no" style="border:2px solid #700000; color:#700000; background:transparent; font-weight:bold; width:90px;">No</button>
                            <button type="button" class="btn-agree-choice" id="btn-yes" style="border:2px solid #700000; color:#700000; background:transparent; font-weight:bold; width:90px;">Yes</button>
                            <input type="hidden" name="agree_terms" id="agree_terms" value="">
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            try {
                                const btnNo = document.getElementById('btn-no');
                                const btnYes = document.getElementById('btn-yes');
                                const agreeInput = document.getElementById('agree_terms');

                                function setActive(btn, value) {
                                    btnNo.style.backgroundColor = 'transparent';
                                    btnNo.style.color = '#700000';
                                    btnYes.style.backgroundColor = 'transparent';
                                    btnYes.style.color = '#700000';

                                    if (value === 'no') {
                                        btnNo.style.backgroundColor = '#700000';
                                        btnNo.style.color = '#ffffff';
                                    } else if (value === 'yes') {
                                        btnYes.style.backgroundColor = '#700000';
                                        btnYes.style.color = '#ffffff';
                                    }

                                    agreeInput.value = value;
                                }

                                btnNo.addEventListener('click', function() { setActive(btnNo, 'no'); });
                                btnYes.addEventListener('click', function() { setActive(btnYes, 'yes'); });
                                setActive(null, '');
                            } catch (e) {
                                console.error('Error in agree_terms script:', e);
                            }
                        });
                        </script>
                        <div class="mb-4 d-flex align-items-center" style="color:#700000; font-size:0.85rem; gap:8px;">
                            <img src="images/warning-sign.png" alt="" id="moa-warning-sign" style="width:20px; height:20px; object-fit:contain; filter: invert(14%) sepia(93%) saturate(7480%) hue-rotate(347deg) brightness(47%) contrast(123%); vertical-align:middle;" />
                            <span>Failure to agree to the University's terms of Agreement renders you ineligible to recruit interns from our university.</span>
                        </div>
                        <br><br>
                        <button type="submit" class="btn btn-primary" id="moa-renew-submit" style="display:block; width:180px; margin:32px auto 0 auto; margin-bottom:1rem; background:#0c0c9b; border:none; font-size:20px; font-weight:bold; padding:16px 0; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <span id="submit-text">Submit</span>
                            <span id="submit-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <?php
    if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '<?php echo addslashes($_SESSION['alert']); ?>',
                    html: '<span class="swal-text-white"><?php echo addslashes($_SESSION['success']); ?></span>',
                    icon: 'success',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal-confirm-proceed',
                        icon: 'swal2-icon',
                        title: 'title-color'
                    },
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-confirm-proceed'
                    }
                });
            });
        </script>
        <?php
        // Clear the success message after displaying
        unset($_SESSION['success']);
        unset($_SESSION['alert']);
    }
    ?>
</body>
</html>