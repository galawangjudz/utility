<?php

$user_type = $_SESSION['user_type'];
?>
<link rel="stylesheet" type="text/css" href="../admin/service_request/style.css">
<style>
    /*======= Card-Border-Top-color css starts  ======= */
.card-border-primary {
  border-top: 4px solid #01a9ac;
}

.card-border-warning {
  border-top: 4px solid #fe9365;
}

.card-border-default {
  border-top: 4px solid #e0e0e0;
}

.card-border-danger {
  border-top: 4px solid #eb3422;
}

.card-border-success {
  border-top: 4px solid #0ac282;
}

.card-border-inverse {
  border-top: 4px solid #404E67;
}

.card-border-info {
  border-top: 4px solid #2DCEE3;
}

/*======= Card-Border-Top-color css ends  ======= */

</style>
 
 <div class="main-container">
		<div class="pd-ltr-20">
			  
                          <h4 class="text-blue h4">Request List</h4>
                    
                            <?php
                                $status = isset($_GET['status']) ? $_GET['status'] : null;
                                $timeRange = isset($_GET['timeRange']) ? $_GET['timeRange'] : null;

                                $query = "SELECT t.id, t.subject, t.description, t.status, t.priority, t.date_created, c.firstname, c.lastname, d.DepartmentName
                                            FROM tickets t
                                            JOIN tblemployees c ON t.customer_id = c.emp_id
                                            JOIN tbldepartments d ON t.department_id = d.id ";

                                $where = "WHERE t.customer_id = ?"; // Add condition for customer ID

                                $params = array($_SESSION['alogin']); // Parameters for prepared statement

                                if (isset($timeRange)) {
                                    switch ($timeRange) {
                                        case 'today':
                                            $where .= " AND DATE(t.date_created) = CURDATE()";
                                            break;
                                        case 'yesterday':
                                            $where .= " AND DATE(t.date_created) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                                            break;
                                        case 'this-week':
                                            $where .= " AND WEEK(t.date_created) = WEEK(NOW()) AND YEAR(t.date_created) = YEAR(NOW())";
                                            break;
                                        case 'this-month':
                                            $where .= " AND MONTH(t.date_created) = MONTH(NOW()) AND YEAR(t.date_created) = YEAR(NOW())";
                                            break;
                                        case 'this-year':
                                            $where .= " AND YEAR(t.date_created) = YEAR(NOW())";
                                            break;
                                        default:
                                            break;
                                    }
                                }

                                if ($status !== null) {
                                    switch ($status) {
                                        case 'open':
                                            $where .= " AND t.status = 0";
                                            break;
                                        case 'processing':
                                            $where .= " AND t.status = 1";
                                            break;
                                        case 'resolved':
                                            $where .= " AND t.status = 2";
                                            break;
                                        case 'closed':
                                            $where .= " AND t.status = 3";
                                            break;
                                        default:
                                            break;
                                    }
                                }

                                $query .= $where;
                                $query .= " ORDER BY t.date_created DESC";

                                $stmt = mysqli_prepare($conn, $query);
                                if (!$stmt) {
                                    die('Error preparing statement: ' . mysqli_error($conn));
                                }

                                // Bind parameters to the prepared statement
                                if (count($params) > 0) {
                                    $paramTypes = str_repeat('s', count($params));
                                    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
                                }

                                $result = mysqli_stmt_execute($stmt);
                                if (!$result) {
                                    die('Error executing statement: ' . mysqli_stmt_error($stmt));
                                }

                                mysqli_stmt_bind_result($stmt, $id, $subject, $description, $status, $priority, $date_created, $firstname, $lastname, $department_name);
                                $results = [];

                                while (mysqli_stmt_fetch($stmt)) {
                                    $result = [
                                        'id' => $id,
                                        'subject' => $subject,
                                        'description' => $description,
                                        'status' => $status,
                                        'priority' => $priority,
                                        'date_created' => $date_created,
                                        'customer_name' => $firstname . ' ' . $lastname,
                                        'department_name' => $department_name
                                    ];

                                    // Calculate time ago
                                    $due_label = calculate_time_ago($date_created);
                                    $result['due_label'] = $due_label;
                                    $results[] = $result;
                                }

                                mysqli_stmt_close($stmt);
                            ?>
                           <div class="filter-bar">
                                <nav class="navbar navbar-light bg-faded mb-30 p-10">
                                    <ul class="nav navbar-nav">
                                        <li class="nav-item active">
                                            <a class="nav-link" href="#!">Filter: <span class="sr-only">(current)</span></a>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="#!" id="bydate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="dw dw-wall-clock1"></i> By Date</a>
                                            <div class="dropdown-menu" aria-labelledby="bydate">
                                                <?php if (!$timeRange): ?>
                                                    <a class="dropdown-item active" href="#">Show all</a>
                                                <?php else: ?>
                                                    <a class="dropdown-item <?php echo (!$timeRange) ? 'active' : ''; ?>" href="?">Show all</a>
                                                <?php endif; ?>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php echo $timeRange === 'today' ? 'active' : ''; ?>" href="?timeRange=today">Today</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'yesterday' ? 'active' : ''; ?>" href="?timeRange=yesterday">Yesterday</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'this-week' ? 'active' : ''; ?>" href="?timeRange=this-week">This week</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'this-month' ? 'active' : ''; ?>" href="?timeRange=this-month">This month</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'this-year' ? 'active' : ''; ?>" href="?timeRange=this-year">This year</a>
                                            </div>
                                        </li>
                                        <!-- end of by date dropdown -->
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="#!" id="bystatus" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="dw dw-analytics-11"></i> By Status</a>
                                            <div class="dropdown-menu" aria-labelledby="bystatus">
                                                <a class="dropdown-item <?php echo !isset($_GET['status']) ? 'active' : ''; ?>" href="?">Show all</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'open' ? 'active' : ''; ?>" href="?status=open">Open</a>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'processing' ? 'active' : ''; ?>" href="?status=processing">Processing</a>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'resolved' ? 'active' : ''; ?>" href="?status=resolved">Resolved</a>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'closed' ? 'active' : ''; ?>" href="?status=closed">Closed</a>
                                            </div>
                                        </li>
                                        <!-- end of by status dropdown -->
                                    </ul>
                                </nav>
                        </div>
                           
               
                <div class="row">
                <?php foreach ($results as $result){ ?>
                    <div class="col-sm-6">
                        <?php
                        // Assign color class based on priority
                        $color_class = '';
                        switch ($result['priority']) {
                            case 'Highest':
                                $color_class = 'card-border-danger';
                                break;
                            case 'High':
                                $color_class = 'card-border-warning';
                                break;
                            case 'Normal':
                                $color_class = 'card-border-success';
                                break;
                            case 'Low':
                                $color_class = 'card-border-primary';
                                break;
                            default:
                                $color_class = 'card-border-primary';
                        }
                    
                        ?>
                        
                        <div class="card-box <?php echo $color_class; ?>">
                            <div class="card-header">
                                <a href="#" class="card-title">#<?php echo $result['id']; ?>. <?php echo $result['subject']; ?> </a>
                                <span class="label label-primary f-right"><?php echo date('d F, Y', strtotime($result['date_created'])); ?> </span>
                            </div>
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p class="task-detail">
                                            <?php
                                            $description = htmlspecialchars_decode($result['description']);
                                            $description = strip_tags($description);
                                            $description = substr($description, 0, 250);
                                            echo $description . (strlen($result['description']) > 250 ? '...' : '');
                                            ?>
                                        </p>
                                    </div>
                                    <!-- end of col-sm-8 -->
                                </div>
                                <!-- end of row -->
                            </div>
                            <div class="card-footer">
                                <div class="task-list-table">
                                        <p class="task-due" style="margin-top: 10px;"><strong> Due : </strong><?php echo $result['due_label']; ?></p>
                                </div>
                                <div class="task-board">
                                    <div class="dropdown-secondary dropdown">
                                        <button id="priority-dropdown" class="btn btn-primary btn-mini dropdown-toggle waves-effect waves-light" type="button" id="dropdown1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo $result['priority']; ?>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown1" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                            <a class="dropdown-priority dropdown-item waves-light waves-effect <?php echo $result['priority'] == 'Highest' ? 'active' : ''; ?>" href="#!" data-priority="Highest" data-ticket-id="<?php echo $result['id']; ?>"><span class="point-marker bg-danger"></span>Highest priority</a>
                                            <a class="dropdown-priority dropdown-item waves-light waves-effect <?php echo $result['priority'] == 'High' ? 'active' : ''; ?>" href="#!" data-priority="High" data-ticket-id="<?php echo $result['id']; ?>"><span class="point-marker bg-warning"></span>High priority</a>
                                            <a class="dropdown-priority dropdown-item waves-light waves-effect <?php echo $result['priority'] == 'Normal' ? 'active' : ''; ?>" href="#!" data-priority="Normal" data-ticket-id="<?php echo $result['id']; ?>"><span class="point-marker bg-success"></span>Normal priority</a>
                                            <a class="dropdown-priority dropdown-item waves-light waves-effect <?php echo $result['priority'] == 'Low' ? 'active' : ''; ?>" href="#!" data-priority="Low" data-ticket-id="<?php echo $result['id']; ?>"><span class="point-marker bg-info"></span>Low priority</a>
                                        </div>
                                    </div>
                                    <div class="dropdown-secondary dropdown">
                                        <button id="status-dropdown" class="btn btn-default btn-mini dropdown-toggle waves-light b-none txt-muted" type="button" id="dropdown2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo $result['status'] == 0 ? 'Open' : ($result['status'] == 1 ? 'Processing' : ($result['status'] == 2 ? 'Resolved' : 'Closed')); ?>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                            <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 0 ? 'active' : ''; ?>" href="#!" data-status="0" data-ticket-id="<?php echo $result['id']; ?>">Open</a>
                                            <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 1 ? 'active' : ''; ?>" href="#!" data-status="1" data-ticket-id="<?php echo $result['id']; ?>">Processing</a>
                                            <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 2 ? 'active' : ''; ?>" href="#!" data-status="2" data-ticket-id="<?php echo $result['id']; ?>">Resolved</a>
                                            <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 3 ? 'active' : ''; ?>" href="#!" data-status="3" data-ticket-id="<?php echo $result['id']; ?>">Closed</a>
                                        </div>
                                    </div>
                                    <!-- end of dropdown-secondary -->
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-list"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                            <a class="dropdown-item" href="<?php echo base_url ?>admin/service_request/new_request.php?id=<?php echo $result['id']; ?>&edit=1"><i class="dw dw-edit"></i> Edit Ticket</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item view-ticket" id ="<?php echo $result['id']; ?>" role ="<?php echo $user_type; ?>"><i class="dw dw-eye"></i> View Ticket</a>
                                     
                                        </div>
                                    </div>
                                  
                                    <!-- end of seconadary -->
                                </div>
                                <!-- end of pull-right class -->
                            </div>
                            <!-- end of card-footer -->
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
    
</div>

</div>


<script>
    $(document).ready(function(){

		$('.view-ticket').click(function(){
			uni_modal_right("Request Details","service_request/ticket_details.php?id="+$(this).attr('id')+"&user_type="+$(this).attr('user_type'))
		})

    })
</script>
<style>

.f-left {
  float: left;
}

.f-right {
  float: right;
}

.f-none {
  float: none;
}
.label {
  border-radius: 2px;
  color: #fff;
  font-size: 12px;
  line-height: 1;
  margin-bottom: 0;
  text-transform: capitalize;
}

.label-primary {
  background: -webkit-gradient(linear, left top, right top, from(#01a9ac), to(#01dbdf));
  background: linear-gradient(to right, #01a9ac, #01dbdf);
}
/**  =====================
      Task-board css start
==========================  **/
.filter-bar .nav,
.filter-bar .nav-item {
  display: inline-block;
}

.filter-bar > .navbar {
  background-color: #fff;
  border-radius: 4px;
  -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.05), 0 3px 1px -2px rgba(0, 0, 0, 0.08), 0 1px 5px 0 rgba(0, 0, 0, 0.08);
          box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.05), 0 3px 1px -2px rgba(0, 0, 0, 0.08), 0 1px 5px 0 rgba(0, 0, 0, 0.08);
  padding: 0.5rem 1rem;
}

.navbar-nav .nav-item {
  float: left;
  line-height: 26px;
}

.nav-item button i {
  margin-right: 0;
}

.filter-bar .navbar-light .navbar-nav .nav-link {
  margin-right: 10px;
}

.card-footer .task-list-table,
.card-footer .task-list-table a img {
  display: inline-block;
}

.task-board {
  margin-top: 10px;
  float: right;
}

.task-board .dropdown {
  display: inline-block;
}

p.task-detail {
  margin-bottom: 5px;
}

p.task-due {
  margin-bottom: 0;
}

.task-right-header-revision,
.task-right-header-status,
.task-right-header-users {
  padding-bottom: 10px;
  padding-top: 10px;
  border-bottom: 1px solid #ccc;
}

.taskboard-right-progress,
.taskboard-right-revision,
.taskboard-right-users {
  margin-top: 10px;
}

.task-right .icofont {
  margin-top: 5px;
  margin-right: 0;
}

.taskboard-right-revision .media .media-body .chat-header {
  font-size: 13px;
}

.media-left i {
  margin-right: 0;
}

.nav-item.nav-grid {
  float: right;
}

.faq-progress .progress {
  position: relative;
  background-color: #eeeded;
  height: 10px;
}

.faq-progress .progress .faq-text1,
.faq-progress .progress .faq-text2,
.faq-progress .progress .faq-text3,
.faq-progress .progress .faq-text4,
.faq-progress .progress .faq-text5 {
  font-weight: 600;
  margin-right: -37px;
}

.faq-progress .progress .faq-bar1,
.faq-progress .progress .faq-bar2,
.faq-progress .progress .faq-bar3,
.faq-progress .progress .faq-bar4,
.faq-progress .progress .faq-bar5 {
  background: #29aecc;
  height: 10px;
  border-radius: 0;
  position: absolute;
  top: 0;
}

.faq-progress .progress .faq-bar1 {
  background-color: #fe9365;
}

.faq-progress .progress .faq-text1 {
  color: #2196F3;
}

.faq-progress .progress .faq-bar2,
.faq-progress .progress .faq-bar5 {
  background-color: #0ac282;
}

.faq-progress .progress .faq-text2,
.faq-progress .progress .faq-text5 {
  color: #4CAF50;
}

.faq-progress .progress .faq-bar3 {
  background-color: #eb3422;
}

.faq-progress .progress .faq-text3 {
  color: #ff5252;
}

.faq-progress .progress .faq-bar4 {
  background-color: #01a9ac;
}

.faq-progress .progress .faq-text4 {
  color: #f57c00;
}

.card-faq h4 {
  color: #2196F3;
}

.faq-progress .progress {
  margin-bottom: 10px;
}

/**====== Tsak-board css end ======**/


/*====== Ready to use Css End ======*/

.card-block {
    padding: 1.25rem;
}

.card {
    border-radius: 5px;
    box-shadow: 0 1px 20px 0 rgba(69, 90, 100, 0.08);
    border: none;
    margin-bottom: 30px;

    .card-footer {
        background-color: $white-txt;
        border-top: none;
    }

    .card-header {
        background-color: transparent;
        border-bottom: none;
        padding: 25px 20px;

        .card-header-left {
            display: inline-block;
        }

        .card-header-right {
            border-radius: 0 0 0 7px;
            right: 10px;
            top: 18px;
            display: inline-block;
            float: right;
            padding: 7px 0;
            position: absolute;

            i {
                margin: 0 8px;
                cursor: pointer;
                font-size: 16px;
                color: #919aa3;
                line-height: 20px;

                &.icofont.icofont-spinner-alt-5 {
                    display: none;
                }
            }

            .card-option {
                transition: 0.3s ease-in-out;

                li {
                    display: inline-block;
                }
            }
        }

        span {
            color: #919aa3;
            display: block;
            font-size: 13px;
            margin-top: 5px;
        }

        + .card-block,
        + .card-block-big {
            padding-top: 0;
        }

        h5 {
            margin-bottom: 0;
            color: #505458;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-right: 10px;
            line-height: 1.4;
        }
    }

    .card-block {
        table {
            tr {
                padding-bottom: 20px;
            }
        }

        .sub-title {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        code {
            background-color: #eee;
            margin: 5px;
            display: inline-block;
        }

        .dropdown-menu {
            top: 38px;
        }

        p {
            line-height: 25px;
        }

        a {
            &.dropdown-item {
                margin-bottom: 0;
                font-size: 14px;
                transition: 0.25s;

                &:active,
                .active {
                    background-color: $primary-color;
                }
            }
        }

        &.remove-label i {
            margin: 0;
            padding: 0;
        }

        &.button-list span.badge {
            margin-left: 5px;
        }

        .dropdown-menu {
            background-color: #fff;
            padding: 0;

            .dropdown-divider {
                background-color: #ddd;
                margin: 3px 0;
            }
        }

        .dropdown-menu > a {
            padding: 10px 16px;
            line-height: 1.429;
        }

        .dropdown-menu > li > a:focus,
        .dropdown-menu > li > a:hover {
            background-color: rgba(202, 206, 209, 0.5);
        }

        .dropdown-menu > li:first-child > a:first-child {
            border-top-right-radius: 4px;
            border-top-left-radius: 4px;
        }

        .badge-box {
            padding: 10px;
            margin: 12px 0;
        }
    }

    .card-block-big {
        padding: 30px 35px;
    }

    .card-block-small {
        padding: 15px 20px;
    }
}

.pcoded {
    .card {
        &.full-card {
            position: fixed;
            top: 56px;
            z-index: 99999;
            box-shadow: none;
            left: 0;
            border-radius: 0;
            border: 1px solid #ddd;
            width: 100vw;
            height: calc(100vh - 56px);

            &.card-load {
                position: fixed;
            }
        }

        &.card-load {
            position: relative;
            overflow: hidden;

            .card-loader {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                background-color: rgba(256, 256, 256,0.7);
                z-index: 999;

                i {
                    margin: 0 auto;
                    color: #ab7967;
                    font-size: 20px;
                }
            }
        }
    }
}

.card-header-text {
    margin-bottom: 0;
    font-size: 1rem;
    color: rgba(51, 51, 51, 0.85);
    font-weight: 600;
    display: inline-block;
    vertical-align: middle;
}

.icofont-rounded-down {
    -webkit-transition: all ease-in 0.3s;
    display: inline-block;
    transition: all ease-in 0.3s;
}

.icon-up {
    -webkit-transform: rotate(180deg);
    transform: rotate(180deg);
}

.rotate-refresh {
    -webkit-animation: mymove 0.8s infinite linear;
    animation: mymove 0.8s infinite linear;
    display: inline-block;
}
@-webkit-keyframes mymove {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}
@keyframes mymove {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}

.breadcrumb-title {
    a {
        font-size: 14px;
        color: #4a6076;
    }

    li:last-child a {
        color: #7e7e7e;
    }
}

.sub-title {
    border-bottom: 1px solid rgba(204, 204, 204, 0.35);
    padding-bottom: 10px;
    margin-bottom: 20px;
    text-transform: uppercase;
    font-size: 14px;
    font-weight: 400;
    color: #2c3e50;
}

</style>