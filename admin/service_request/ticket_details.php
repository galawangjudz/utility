
 <?php 
require_once('../../includes/config.php');


$user_type = isset($_GET["user_type"]) ? $_GET["user_type"] : '' ;


?>
 <div class="main-body">

 
    
    



                    <?php
                
                        // Check if the edit parameter is set and fetch the record from the database
                        /* if(isset($_GET['edit']) && $_GET['edit'] == 1 && isset($_GET['id'])) { */
                        if(isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $stmt = mysqli_prepare($conn, "SELECT * FROM tickets WHERE id = ?");
                            mysqli_stmt_bind_param($stmt, "i", $id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row = mysqli_fetch_assoc($result);
                        }
                    ?>

            
                    <div class="card-header">
                        <h5 class="card-header-text"><i class="dw dw-note"></i> Task Details</h5>
                        <div class="f-right">
                        <div class="dropdown-secondary dropdown">
                            <button id="status-dropdown" class="btn btn-sm btn-primary dropdown-toggle waves-light" type="button" id="dropdown2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $row['status'] == 0 ? 'Open' : ($row['status'] == 1 ? 'Processing' : ($row['status'] == 2 ? 'Resolved' : 'Closed')); ?>
                            </button>
                            <?php if ($user_type == 'Admin'): ?>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 0 ? 'active' : ''; ?>" href="#!" data-status="0">Open</a>
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 1 ? 'active' : ''; ?>" href="#!" data-status="1">Processing</a>
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 2 ? 'active' : ''; ?>" href="#!" data-status="2">Resolved</a>
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 3 ? 'active' : ''; ?>" href="#!" data-status="3">Closed</a>
                                </div>
                            <?php endif; ?>
                            <!-- end of dropdown menu -->
                        </div>
                        </div>
                    </div>
                        <div class="card-block task-details">
                        <!-- <div class="table-responsive"> -->
                            <table class="data-table table stripe hover nowrap">
                                <tbody>
                                    <tr>
                                        <td>Reported By:</td>
                                            <td class="small">
                                            <?php
                                                if (isset($row['customer_id'])) {
                                                    $customer_id = $row['customer_id'];
                                                    
                                                    // Fetch customer details from the tblemployees table
                                                    $stmt = mysqli_prepare($conn, "SELECT * FROM tblemployees WHERE emp_id = ?");
                                                    mysqli_stmt_bind_param($stmt, "i", $customer_id);
                                                    mysqli_stmt_execute($stmt);
                                                    $result = mysqli_stmt_get_result($stmt);
                                                    $customer = mysqli_fetch_assoc($result);
                                                    
                                                    if ($customer) {
                                                        $first_name = $customer['FirstName'];
                                                        $middle_name = "";
                                                        $last_name = $customer['LastName'];
                                                        
                                                        $full_name = $first_name;
                                                        if ($middle_name) {
                                                            $full_name .= ' ' . $middle_name;
                                                        }
                                                        $full_name .= ' ' . $last_name;
                                                        
                                                        echo '<a href="#">' . $full_name . '</a>';
                                                    } else {
                                                        echo 'Unknown Customer';
                                                    }
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Reported To:</td>
                                        <td class="small">
                                            <?php
                                                if (isset($row['department_id'])) {
                                                    $department_id = $row['department_id'];

                                                    // Fetch department name from the departments table
                                                    $stmt = mysqli_prepare($conn, "SELECT DepartmentName FROM tbldepartments WHERE id = ?");
                                                    mysqli_stmt_bind_param($stmt, "i", $department_id);
                                                    mysqli_stmt_execute($stmt);
                                                    $result = mysqli_stmt_get_result($stmt);
                                                    $department = mysqli_fetch_assoc($result);

                                                    if ($department) {
                                                        $department_name = $department['DepartmentName'];
                                                        echo $department_name;
                                                    } else {
                                                        echo 'Unknown Department';
                                                    }
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Created:</td>
                                        <td class="small"><?php echo isset($row['date_created']) ? date('d F, Y', strtotime($row['date_created'])) : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Priority:</td>
                                        <td class="small">
                                            <div class="btn-group">
                                                <a href="#"><?php echo isset($row['priority']) ? $row['priority'] : ''; ?> priority</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Status:</td>
                                        <td class="small">
                                            <?php
                                                if (isset($row['status'])) {
                                                    $status = $row['status'];
                                                    if ($status == 0) {
                                                        echo 'Open';
                                                    } elseif ($status == 1) {
                                                        echo 'Processing';
                                                    } elseif ($status == 2) {
                                                        echo 'Resolved';
                                                    } else {
                                                        echo 'Closed';
                                                    }
                                                }
                                            ?>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
          
                <h5><i class="dw dw-task m-r-5"></i> <?php echo isset($row['subject']) ? $row['subject'] : ''; ?></h5>
               
            </div>
            <div class="card-block">
                <div class="small">
                    <div class="m-b-20">
                        <h6 class="sub-title m-b-5"></h6>
                        <p>
                            <?php echo isset($row['description']) ? html_entity_decode($row['description']) : ''; ?>
                        </p>
                    </div>
                </div>
                
            </div>

</div>