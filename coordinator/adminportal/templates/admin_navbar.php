<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
        <div class="nano">
            <div class="nano-content">
                <ul>
                    <div class="logo"><a href="dashboard.php">
                            <!-- <img src="images/logo.png" alt="" /> --><span>OJT Web Portal</span></a></div>
                    <li class="label">Main</li>
                    <li><a href="dashboard.php"><i class="ti-home"></i> Dashboard </a></li>

                    <li class="label">Apps</li>
                    
                    <li><a class="sidebar-sub-toggle"><i class="ti-user"></i> User List <span class="sidebar-collapse-icon ti-angle-down"></span></a>
                        <ul>
                            <li><a href="student_trainee.php">Trainees</a></li>
                            <li><a href="coordinators.php">Coordinators</a></li>
                            <li><a href="supervisors.php">Supervisors</a></li>
                            <li><a href="admin.php">Admin</a></li>

                        </ul>
                    </li>

                    <li><a href="partner_companies.php"><i class="ti-layout-width-default"></i>Partner Companies </a></li>

                    <li><a class="sidebar-sub-toggle"><i class="ti-comment-alt"></i> Chats <span class="sidebar-collapse-icon ti-angle-down"></span></a>
                        <ul>
                            <li><a href="admin_stud_message.php">Chat Students</a></li>
                            <li><a href="chat_coordinator.php">Chat OJT Coordinator</a></li>
                            <li><a href="chat_supervisor.php">Chat HTE Coordinator</a></li>

                        </ul>
                    </li>
                    
                    
                    
                   
                </ul>
            </div>
        </div>
    </div>
    <!-- /# sidebar -->

    <div class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="float-left">
                        <div class="hamburger sidebar-toggle">
                            <span class="line"></span>
                            <span class="line"></span>
                            <span class="line"></span>
                        </div>
                    </div>
                    <div class="float-right">
                        <div class="dropdown dib">
                            <div class="header-icon" data-toggle="dropdown">
                                <i class="ti-bell">
                                    <span class="badge badge-danger" id="notification-badge">
                                    <?php
                                        if(isset($_SESSION['auth_user']['admin_id'])) {
                                            $adminID = $_SESSION['auth_user']['admin_id'];
                                            $unread = 'Unread';

                                            // Adjust your SQL query based on your database schema
                                            $stmt = $conn->prepare("SELECT COUNT(*) AS total_unread FROM admin_system_notification WHERE admin_id = ? AND status = ?");
                                            $stmt->execute([$adminID, $unread]);
                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $totalUnread = $result['total_unread'];

                                            echo $totalUnread;
                                        }
                                    ?>
                                    </span>
                                </i>
                                <div class="drop-down dropdown-menu dropdown-menu-right" style="position: absolute; transform: translate3d(-227px, -3px, 0px); top: 0px; left: 0px; will-change: transform; height: 300px; overflow: auto; border: 1px solid #ccc;">
                                    <div class="dropdown-content-heading">
                                        <span class="text-left">Recent Notifications</span>
                                    </div>
                                    <div class="dropdown-content-body">
                                        <ul>
                                        <li class="text-center">
                                                <a href="#" class="more-link" id="markASread"><i class="ti-email"></i> Mark all as read</a>
                                        </li>
                                        <?php
                                        if(isset($_SESSION['auth_user']['admin_id'])) {
                                            $adminID = $_SESSION['auth_user']['admin_id'];
                                            
                                            // Adjust your SQL query based on your database schema
                                            $stmt = $conn->prepare("SELECT * FROM admin_system_notification LEFT JOIN admin_account ON admin_account.id = admin_system_notification.admin_id WHERE admin_system_notification.admin_id = ? ORDER BY admin_system_notification.id DESC");
                                            $stmt->execute([$adminID]);
                                            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            foreach ($notifications as $notification) {
                                        ?>
                                                <li>
                                                    <a href="#">
                                                        <img class="pull-left m-r-10 avatar-img" src="<?php echo $notification['admin_profile_picture']; ?>" alt="" />
                                                        <div class="notification-content">
                                                            <small class="notification-timestamp pull-right"><?php echo $notification['logs_time']; ?></small>
                                                            <div class="notification-heading"><?php echo $notification['logs']; ?></div>
                                                            <div class="notification-text"><?php echo $notification['logs_date']; ?> <div class="notification-timestamp pull-right" id="unreadTORead"><?php echo $notification['status']; ?></div> </div>
                                                            
                                                        </div>
                                                    </a>
                                                </li>
                                        <?php
                                            }
                                        }
                                        ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown dib">
                            <div class="header-icon" data-toggle="dropdown">
                            <?php
                                if(isset($_SESSION['auth_user']['admin_id'])) {
                                    $adminID = $_SESSION['auth_user']['admin_id'];

                                    // Adjust your SQL query based on your database schema
                                    $stmt = $conn->prepare("SELECT * FROM admin_account WHERE id = ? ");
                                    $stmt->execute([$adminID]);
                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                }
                            ?>
                                <span class="user-avatar"><?php echo $result['first_name'];?> <?php echo $result['last_name'];?>
                                    <i class="ti-angle-down f-s-10"></i>
                                </span>

                                <div class="drop-down dropdown-profile dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-content-body">
                                        <ul>
                                            <li>
                                                <a href="#" onclick="profile();">
                                                    <i class="ti-user"></i>
                                                    <span>Profile</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="settings();">
                                                    <i class="ti-settings"></i>
                                                    <span>Setting</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="logout();">
                                                    <i class="ti-power-off"></i>
                                                    <span>Logout</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Attach a click event handler to the bell icon
        $("#markASread").on("click", function() {
            // Send an AJAX request to mark notifications as read
            $.ajax({
                url: "mark_admin_notifications_as_read.php", // Replace with the correct URL
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Update the notification count
                        notificationCount = 0;
                        notificationRead = 'Read';
                        $("#notification-badge").text(notificationCount);
                        $("#unreadTORead").text(notificationRead);

                        // Update the UI or reload notifications here
                        alert("Notifications marked as read");
                        // You can update the notifications without reloading the page here
                    } else {
                        alert("Failed to mark notifications as read");
                    }
                },
                error: function() {
                    alert("An error occurred while processing the request");
                }
            });
        });
    });
</script>


<script>
  function profile() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_profile.php ';
  }
  function settings() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_settings.php ';
  }
  function logout() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_logout.php ';
  }
</script>

