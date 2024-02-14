
<style>
/* Add this style block inside your existing style tag or in a separate CSS file */
.notification-dropdown {
    display: none;
    position: absolute;
    top: 100%; /* Adjust the distance from the button */
    right: 0;
    background-color: #ffffff;
    border: 1px solid #ccc;
    padding: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    overflow: auto;
    z-index: 1000;
}

.notification {
    margin-bottom: 5px;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 5px;
    background-color: #f5f5f5;
}

.notification-btn {
    background: none;
    border: none;
    cursor: pointer;
    position: relative;
}

.notification-btn i {
    font-size: 24px;
    color: #333; /* Change the color based on your design */
    margin-top: 25px;/* Adjust the margin to give space at the top */
	margin-right: 20px; 
}

.badge_notif {
    background-color: red;
    color: #fff;
    border-radius: 50%;
    font-size: 8px;
    padding: 3px 6px;
    position: absolute;
    top: 10px;
    right: 15px;
}

.badge_notif.active {
    display: inline-block;
}
</style>


<div class="header">
		<div class="header-left">
			<div class="menu-icon dw dw-menu"></div>
			<div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
			
		</div>
		<div class="header-right">
			<div class="user-notifications">
				<button class="notification-btn" id="notificationBtn">
					<i class="fa fa-bell"></i>
					<span class="badge_notif" id="notificationCount">4</span>
				</button>
				<div id="notifications" class="notification-dropdown">
					
				</div>
			</div>
			
			
			<div class="user-info-dropdown">
				<div class="dropdown">

					<?php $query= mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id'")or die(mysqli_error());
								$row = mysqli_fetch_array($query);
						?>

					<a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
						<span class="user-icon">
							<img src="<?php echo (!empty($row['location'])) ? '../uploads/'.$row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg'; ?>" alt="">
						</span>
						<span class="user-name"><?php echo $row['FirstName']. " " .$row['LastName']; ?></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
						<a class="dropdown-item" href="<?php echo base_url ?>admin/?page=my_profile"><i class="dw dw-user1"></i> Profile</a>
						<a class="dropdown-item" href="<?php echo base_url ?>admin/?page=change_password"><i class="dw dw-help"></i> Reset Password</a>
						<a class="dropdown-item" href="../logout.php"><i class="dw dw-logout"></i> Log Out</a>
					</div>
				</div>
			</div>
			<div class="dashboard-setting user-notification">
				<div class="dropdown">
					<a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
						<i class="dw dw-settings2"></i>
					</a>
				</div>
			</div>
			
						
		</div>
	</div>

	<script>
$(document).ready(function () {
    // Function to fetch notifications
    function fetchNotifications() {
        $.ajax({
			url:_base_url_+"admin/includes/notifications.php", // Update the URL to the correct path
            method: 'GET',
			success: function (data) {
				var notificationsDiv = $('#notifications');
				notificationsDiv.empty();

				data.forEach(function (notification) {
					var notificationElement = $('<div class="notification" data-id="' + notification.id + '">' + notification.message + '</div>');
					notificationsDiv.append(notificationElement);

					notificationElement.on('click', function () {
						// Check if the notification is unread
						if (!notification.read_status) {
							// Mark the notification as read on the server side
							markNotificationAsRead(notification.id);

							// Optionally, remove the notification from the UI
							notificationElement.remove();
						}

						// Add logic to handle notification click, e.g., redirect to a link
						window.location.href = notification.link;
					});
				});

				var unreadNotificationCount = data.filter(function (notification) {
					return notification.read_status === "0";}).length;
    			$('#notificationCount').text(unreadNotificationCount).toggleClass('active', unreadNotificationCount > 0);


			}
        });
    }

    // Function to toggle notification dropdown visibility
    function toggleNotificationDropdown() {
        var notificationDropdown = $('#notifications');
        notificationDropdown.toggle();
    }

    // Fetch notifications on page load
    fetchNotifications();

    // Set interval to refresh notifications every 30 seconds
    setInterval(fetchNotifications, 30000);

    // Add click event to the notification button
    $('#notificationBtn').on('click', function () {
        toggleNotificationDropdown();
    });
});


</script>


