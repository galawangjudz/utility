
 <?php 
require_once('../../includes/config.php');


$user_type = isset($_GET["user_type"]) ? $_GET["user_type"] : '' ;


?>
 <div class="main-body">

                    <?php
                        if(isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $query = "SELECT * FROM tickets WHERE id = ?";
                            $qry = odbc_prepare($conn2, $query);

                            if (!$qry) {
                                die('Error preparing statement: ' . odbc_errormsg($conn2));
                            }

                            // Bind parameters to the prepared statement
                            odbc_execute($qry, array($id));

                            // Fetch the results
                            $row = odbc_fetch_array($qry);

                            // Free the result set
                            odbc_free_result($qry);

                            // Now $row contains the result data
                            //print_r($row);
                        
                        }
                    ?>

                    
                      
                           <!--  <?php if ($user_type == 'Admin'): ?>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 0 ? 'active' : ''; ?>" href="#!" data-status="0">Open</a>
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 1 ? 'active' : ''; ?>" href="#!" data-status="1">Processing</a>
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 2 ? 'active' : ''; ?>" href="#!" data-status="2">Resolved</a>
                                    <a class="dropdown-status dropdown-item waves-light waves-effect <?php echo $row['status'] == 3 ? 'active' : ''; ?>" href="#!" data-status="3">Closed</a>
                                </div>
                            <?php endif; ?> -->
                            <!-- end of dropdown menu -->
          
            <h5><i class="dw dw-task m-r-5"></i> <?php echo isset($row['subject']) ? $row['subject'] : ''; ?></h5>
            </div>
            <div class="card-block">
                <div class="small">
                    <div class="m-b-20">
                        <h6 class="sub-title m-b-5"></h6>
                        <textarea readonly class="form-control">
                            <?php echo isset($row['description']) ? html_entity_decode($row['description']) : ''; ?>
                        </textarea>

                    </div>
                </div>
                
            </div>
            <table class="data-table table stripe hover nowrap">
                <tbody>
                    <tr>
                        <td>Reported By:</td>
                            <td class="small">
                            <?php
                                if (isset($row['employee_id'])) {
                                    $customer_id = $row['employee_id'];
                                    $query2 = "SELECT * FROM tblemployees WHERE emp_id = " .$customer_id;
                                    $result2 = odbc_exec($conn2, $query2);

                                    if (!$result2) {
                                        die("ODBC query execution failed: " . odbc_errormsg());
                                    }
                                    // Fetch and display the results
                                   
                                    if ($result2) {
                                        while ($row2 = odbc_fetch_array($result2)) {

                                            $first_name = $row2['firstname'];
                                            $middle_name = "";
                                            $last_name = $row2['lastname'];
                                            
                                            $full_name = $first_name;
                                            if ($middle_name) {
                                                $full_name .= ' ' . $middle_name;
                                            }
                                            $full_name .= ' ' . $last_name;
                                            
                                            echo '<a href="#">' . $full_name . '</a>';
                                              
                                        }

                                    } else {
                                        echo 'Customer not found';
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
                                    $dept_id = $row['department_id'];
                                    $query3 = "SELECT DepartmentName FROM tbldepartments WHERE id = " .$dept_id;
                                    $result3 = odbc_exec($conn2, $query3);

                                    if (!$result3) {
                                        die("ODBC query execution failed: " . odbc_errormsg());
                                    }
                                    // Fetch and display the results
                                   
                                    if ($result3) {
                                        while ($row3 = odbc_fetch_array($result3)) {

                                            $dept_name = $row3['departmentname'];
                                            echo $dept_name;
                                        }

                                    } else {
                                        echo 'Unknown Department';
                                    }
                            }
                         ?>
                            </td>
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