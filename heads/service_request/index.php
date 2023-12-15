<?php

$user_type = $_SESSION['user_type'];
$user_dept = $_SESSION['dept'];

echo $user_dept;

$qry = "SELECT * FROM tbldepartments WHERE DepartmentShortName = ?";
$result = odbc_prepare($conn2, $qry);

if (!$result) {
    die('Error preparing statement: ' . odbc_errormsg($conn2));
}

$success = odbc_execute($result, array($user_dept));

if (!$success) {
    die('Error executing statement: ' . odbc_errormsg($conn2));
}

while ($row = odbc_fetch_array($result)) {
    $dept_id = $row['id'];
    echo $dept_id;
}

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
<style>
    .priority-badge {
        display: inline-block;
        padding: 3px 8px; /* Adjust padding for size */
        border-radius: 3px;
        color: #ffffff; /* Text color */
        font-weight: bold;
        font-size: 12px; /* Adjust font size for size */
    }
    .dropdown-status.active {
    background-color: #007bff; /* Change this to the desired color for the active status */
    color: #fff; /* Change this to the desired text color for the active status */
    }

    .status-label {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    }

    .open { background: #28a745; color: #fff; }
    .processing { background: #ffc107; color: #000; }
    .resolved { background: #007bff; color: #fff; }
    .closed { background: #dc3545; color: #fff; }

</style>
<?php
    function getStatusClass($status) {
        $classes = ['open', 'processing', 'resolved', 'closed'];
        return $classes[$status] ?? 'closed';
    }

    function getStatusText($status) {
        $texts = ['Open', 'Processing', 'Resolved', 'Closed'];
        return $texts[$status] ?? 'Closed';
    }


    function get_priority_color($priority) {
        switch ($priority) {
            case 'Highest':
                return '#dc3545'; // Red color for Highest priority
            case 'High':
                return '#ff9800'; // Orange color for High priority
            case 'Normal':
                return '#28a745'; // Green color for Normal priority
            case 'Low':
                return '#17a2b8'; // Blue color for Low priority
            default:
                return '#000000'; // Default color
        }
    }
    ?>
 
 <div class="main-container">
		<div class="pd-ltr-20">
			  
                          <h4 class="text-blue h4">Request List</h4>
                            <div class="pd-20">
                               
                            </div>
                            <?php
                                $status = isset($_GET['status']) ? $_GET['status'] : null;
                                echo $status;
                                $timeRange = isset($_GET['timeRange']) ? $_GET['timeRange'] : null;

                                $query = "SELECT t.id, t.account_no, t.description, t.status, t.priority, c.firstname, c.lastname, t.date_created, t.request, c.firstname, c.lastname, d.DepartmentName
                                            FROM tickets t
                                            JOIN tblemployees c ON t.employee_id = c.emp_id
                                            JOIN tbldepartments d ON t.department_id = d.id ";

                                $where = "WHERE t.department_id =" .$dept_id; // Add condition for customer ID
                                //$where = "";
                                $params = array($_SESSION['alogin']); // Parameters for prepared statement

                                if (isset($timeRange)) {
                                    switch ($timeRange) {
                                        case 'today':
                                            $where .= " AND DATE(t.date_created) = current_date";
                                            break;
                                        case 'yesterday':
                                            $where .= " AND DATE(t.date_created) = current_date - interval '1 day'";
                                            break;
                                        case 'this-week':
                                            $where .= " AND EXTRACT(WEEK FROM t.date_created) = EXTRACT(WEEK FROM current_date) 
                                                       AND EXTRACT(YEAR FROM t.date_created) = EXTRACT(YEAR FROM current_date)";
                                            break;
                                        case 'this-month':
                                            $where .= " AND EXTRACT(MONTH FROM t.date_created) = EXTRACT(MONTH FROM current_date) 
                                                       AND EXTRACT(YEAR FROM t.date_created) = EXTRACT(YEAR FROM current_date)";
                                            break;
                                        case 'this-year':
                                            $where .= " AND EXTRACT(YEAR FROM t.date_created) = EXTRACT(YEAR FROM current_date)";
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                if (isset($status)){
                               /*  if ($status !== null) { */
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

                                
                                $stmt = odbc_prepare($conn2, $query);

                                if (!$stmt) {
                                    die('Error preparing statement: ' . odbc_errormsg($conn2));
                                }
                                
                                // Bind parameters to the prepared statement
                                if (count($params) > 0) {
                                    $result = odbc_execute($stmt, $params);
                                
                                    if ($result === false) {
                                        die('Error executing statement: ' . odbc_errormsg($conn2));
                                    }
                                
                                    $results = [];
                                
                                    while ($row = odbc_fetch_array($stmt)) {
                                        $result = [
                                            'id' => $row['id'],
                                            'subject' => $row['account_no'],
                                            'description' => $row['description'],
                                            'request' => $row['request'],
                                            'status' => $row['status'],
                                            'priority' => $row['priority'],
                                            'date_created' => $row['date_created'],
                                            'customer_name' => $row['firstname'] . ' ' . $row['lastname'],
                                            'department_name' => $row['departmentname']
                                        ];
                                
                                        // Calculate time ago
                                        $due_label = calculate_time_ago($row['date_created']);
                                        $result['due_label'] = $due_label;
                                        $results[] = $result;
                                    }
                                
                                    odbc_free_result($stmt);
                                }
                                
                                odbc_close($conn2);
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
                                                    <a class="dropdown-item <?php echo (!$timeRange) ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request">Show all</a>
                                                <?php endif; ?>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php echo $timeRange === 'today' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&timeRange=today">Today</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'yesterday' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&timeRange=yesterday">Yesterday</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'this-week' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&timeRange=this-week">This week</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'this-month' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&timeRange=this-month">This month</a>
                                                <a class="dropdown-item <?php echo $timeRange === 'this-year' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&timeRange=this-year">This year</a>
                                            </div>
                                        </li>
                                        <!-- end of by date dropdown -->
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="#!" id="bystatus" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="dw dw-analytics-11"></i> By Status</a>
                                            <div class="dropdown-menu" aria-labelledby="bystatus">
                                                <a class="dropdown-item <?php echo !isset($_GET['status']) ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request">Show all</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'open' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&status=open">Open</a>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'processing' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&status=processing">Processing</a>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'resolved' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&status=resolved">Resolved</a>
                                                <a class="dropdown-item <?php echo isset($_GET['status']) && $_GET['status'] === 'closed' ? 'active' : ''; ?>" href="<?php echo base_url?>admin/?page=service_request&status=closed">Closed</a>
                                            </div>
                                        </li>
                                        <!-- end of by status dropdown -->
                                    </ul>
                                    <div class="card-tools">
                                        <a href="javascript:void(0)" id="create-ticket" class="btn btn-flat btn-sm btn-primary"><i class="dw dw-add"></i> Create Request</a>
                                    </div>
                                </nav>
                              
                        </div>
                           
               
              
                    <div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
								<th class="table-plus">No</th>
                                <th>Date Created</th>
                                <th>Requestor</th>
                                <th>Nature of Request</th>
                                <th>Assigned to</th>
                               <!--  <th>Due Time</th> -->
                                <th>Status</th>
								<th class="datatable-nosort">Action</th>
							</tr>
						</thead>
						<tbody>
                        <?php foreach ($results as $result){ ?>
							<tr>
                                <td class="">
                                    <?php
                                    $formattedDate = date('y', strtotime($result['date_created']));
                                    $formattedID = str_pad($result['id'], 4, '0', STR_PAD_LEFT);
                                    echo $formattedDate . '-' . $formattedID;
                                    ?>
                                </td>
                               
                                <td class=""><?php echo date('d F, Y', strtotime($result['date_created'])); ?> </td>
                                <td class=""><?php echo $result['customer_name']; ?></td>
                                <td class="">
                                <?php
                                $requestAcronyms = [
                                    'BA' => 'BILL ADJUSTMENT',
                                    'SA' => 'SURCHARGE ADJUSTMENT',
                                    'ADJ' => 'PAYMENT ADJUSTMENT',
                                    'PTO' => 'PERMIT TO OCCUPY',
                                    'PTC' => 'PERMIT TO CONSTRUCT',
                                    'ATC' => 'AUTHORITY TO CONSTRUCT',
                                ];

                                echo isset($result['request']) ? $requestAcronyms[$result['request']] : '';
                                ?></td>
                                <td class=""><?php echo $result['department_name']; ?></td>
                               <!--  <td class=""><?php echo $result['due_label']; ?></td> -->
                                <td>  <span class="status-label <?php echo getStatusClass($result['status']); ?>">
                                        <?php echo getStatusText($result['status']); ?>
                                </span> </td>
                                <td>
                                <button class="btn btn-link font-24 p-0 line-height-1 no-arrow view-ticket" id="<?php echo $result['id']; ?>" create_date ="<?php $formattedDate = date('y', strtotime($result['date_created']));
                                            $formattedID = str_pad($result['id'], 4, '0', STR_PAD_LEFT);
                                            echo $formattedDate . '-' . $formattedID; ?>"><i class="dw dw-eye"></i>
                                </button>
                                <button class="btn btn-link font-24 p-0 line-height-1 no-arrow edit-ticket" id="<?php echo $result['id']; ?>" create_date ="<?php $formattedDate = date('y', strtotime($result['date_created']));
                                    $formattedID = str_pad($result['id'], 4, '0', STR_PAD_LEFT);
                                    echo $formattedDate . '-' . $formattedID; ?>"><i class="dw dw-edit"></i>
                                </button>

                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-list"></i>
                                    </a>
                                   <!--  <button id="status-dropdown" class="btn btn-default btn-mini dropdown-toggle waves-light b-none txt-muted" type="button" id="dropdown2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo $result['status'] == 0 ? 'Open' : ($result['status'] == 1 ? 'Processing' : ($result['status'] == 2 ? 'Resolved' : 'Closed')); ?>
                                    </button> -->
                                    <div class="dropdown-menu" aria-labelledby="dropdown2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 0 ? 'active' : ''; ?>" href="#!" data-status="0" data-ticket-id="<?php echo $result['id']; ?>">Open</a>
                                        <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 1 ? 'active' : ''; ?>" href="#!" data-status="1" data-ticket-id="<?php echo $result['id']; ?>">Processing</a>
                                        <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 2 ? 'active' : ''; ?>" href="#!" data-status="2" data-ticket-id="<?php echo $result['id']; ?>">Resolved</a>
                                        <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $result['status'] == 3 ? 'active' : ''; ?>" href="#!" data-status="3" data-ticket-id="<?php echo $result['id']; ?>">Closed</a>
                                    </div>
                               
                                
                               
                                </td>
                                <?php } ?>
                            </tr>
						</tbody>
					</table>
			    </div>
                    
                
            </div>
            
    
</div>

</div>


<script>
    $(document).ready(function(){

		$('.view-ticket').click(function(){
			uni_modal_ticket("Request No. " + $(this).attr('create_date'),"service_request/new_request.php?id="+$(this).attr('id'),"large",true)
        })

        $('#create-ticket').click(function(){
			uni_modal_ticket("New Request","service_request/new_request.php")
		})

        $('.edit-ticket').click(function(){
			uni_modal_ticket("Edit Request No. "+ $(this).attr('create_date'),"service_request/new_request.php?id="+$(this).attr('id'))
		})


        $('.dropdown-status').click(function(){
            var id = $(this).attr('data-ticket-id');
            var ticket_status = $(this).attr('data-status');
    
            //_conf("Are you sure to update?", "update_request", [id, ticket_status]);
            _conf("Are you sure to update?","update_request",["'" +$(this).attr('data-ticket-id')+ "'","'" + $(this).attr('data-status') + "'"])
		})

    })

    function update_request(id, ticket_status){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=update_request",
			method:"POST",
			data:{id: id, ticket_status: ticket_status},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
                    alert(resp.msg);
					location.reload();
				}else{
					alert("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
<style>


.hidden-footer {
  display: none;
}


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
