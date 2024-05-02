<?php 
function format_num($number){
    $decimals = 0; // Set the number of decimal places
    return number_format($number, $decimals);
}

?>
<div class="main-container"> 
    <div class="">
        <div class="pd-ltr-20">
            <div class="card-box pd-20 height-1400-p mb-30">
                <div class="row align-items-center">
                    <div class="col-md-4 user-icon">
                        <img src="../vendors/images/banner-img.png" alt="">
                    </div>
                    <div class="col-md-8">
                        <?php
                        // Assume $conn is already defined as the MySQLi connection object

                        // Define the SQL query with parameterized query
                        $sql = "SELECT * FROM tblemployees WHERE emp_id = ?";
                        $stmt = mysqli_prepare($conn, $sql);

                        // Bind the session ID parameter
                        mysqli_stmt_bind_param($stmt, "s", $session_id);

                        // Execute the query
                        mysqli_stmt_execute($stmt);

                        // Get the result
                        $result = mysqli_stmt_get_result($stmt);

                        // Fetch the row
                        $row = mysqli_fetch_array($result);

                        // Free the result and close the statement
                        mysqli_stmt_close($stmt);
                        ?>


                        <h4 class="font-20 weight-500 mb-10 text-capitalize">
                            Welcome back <div class="weight-600 font-30 text-blue"><?php echo $row['FirstName']. " " .$row['LastName']; ?>,</div>
                        </h4>
                        <p class="font-18 max-width-600"> We're delighted to see you again.</p>
                    </div>
                </div>
            </div>
        
   
                <div class="pd-ltr-20">
                    <div class="title pb-20">
                        <h2 class="h3 mb-0">Account Status</h2>
                    </div>
                    <div class="row pb-10">
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                                <?php 
                                $sql = "SELECT * FROM t_utility_accounts WHERE c_status = 'Active'";
                                $results = odbc_exec($conn2, $sql); 

                                // Count the number of rows
                                $numRows = odbc_num_rows($results);
                                ?> 

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php echo format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Total Active Accounts</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#00eccf"><i class="icon-copy dw dw-user-2"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                            <?php 
                            $sql = "SELECT * FROM t_utility_accounts WHERE c_status = 'Inactive'";
                            $results = odbc_exec($conn2, $sql); 

                            // Count the number of rows
                            $numRows = odbc_num_rows($results);

                            // Display the number of rows
                           
                            ?> 

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Inactive Accounts</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#ff5b5b"><span class="icon-copy dw dw-user-1"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                            <?php 
                                $sql = "SELECT * FROM t_utility_accounts WHERE c_types = 'STL and MTF' and c_status = 'Active'";
                                $results = odbc_exec($conn2, $sql); 

                                // Count the number of rows
                                $numRows = odbc_num_rows($results);

                                // Display the number of rows
                               
                                ?> 

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows); ?></div>
                                        <div class="font-14 text-primary weight-500">STL and GCF Accounts</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color='#09cc06'><i class="icon-copy fa fa-cube" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                           
                               <?php 
                               $sql = "SELECT * FROM t_utility_accounts WHERE c_types = 'STL Only' and c_status = 'Active'";
                               $results = odbc_exec($conn2, $sql); 
       
                               // Count the number of rows
                               $numRows = odbc_num_rows($results);
       
    
                                ?>

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Streetlight Only</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#e5de00"><i class="icon-copy fa fa-bell" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="title pb-20">
                        <h2 class="h3 mb-0">Contact Info Breakdown</h2>
                    </div>
                    <div class="row pb-10">

                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                                <?php 
                                $sql = "SELECT * FROM t_utility_accounts WHERE billing_method = '1' and c_status = 'Active'";
                                $results = odbc_exec($conn2, $sql); 

                                // Count the number of rows
                                $numRows = odbc_num_rows($results);

                                // Display the number of rows
                                
                                ?> 

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Accounts with Emails</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#b100cd"><i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                            <?php 
                            $sql = "SELECT * FROM t_utility_accounts WHERE (billing_method = '2') and c_status = 'Active'";
                            $results = odbc_exec($conn2, $sql); 

                            // Count the number of rows
                            $numRows = odbc_num_rows($results);
                            
                            ?> 

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Accounts with Contact Number</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#ff7f00"><span class="icon-copy fa fa-phone"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                            <?php 
                                $sql = "SELECT * FROM t_utility_accounts WHERE (billing_method = '3') and c_status = 'Active'";
                                $results = odbc_exec($conn2, $sql); 

                                // Count the number of rows
                                $numRows = odbc_num_rows($results);

                            ?> 

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Accounts with Both Email & Contact</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#46a2da"><span class="icon-copy fa fa-address-book"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">

                            <?php 
                                $sql = "SELECT * FROM t_utility_accounts WHERE (billing_method = '0' or billing_method is NULL) and c_status = 'Active'";
                                $results = odbc_exec($conn2, $sql); 

                                // Count the number of rows
                                $numRows = odbc_num_rows($results);
                            ?>

                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"><?php  echo  format_num($numRows);?></div>
                                        <div class="font-14 text-secondary weight-500">Accounts with No Email</div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#ff5b5b"><span class="icon-copy fa fa-eye-slash"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php
                $slides = [
                    [
                        'title' => "🎉 Our new Utility Web App is live! 🖥️",
                        'content' => "Starting this 1/15/2024, we encourage everyone to transition to the new system. Embrace efficiency and innovation! 💻✨<br><br>🕒 The Project Admin will initiate the Authority to Accept Payment (ATAP) using the new web app system. Cashiers, please issue Cash Acknowledgement Receipts (C.A.R.) manually in the meantime.<br><br>🖥️ All Cash Acknowledgement Receipts (C.A.R.) should be diligently encoded into the new system by Property Admin for record-keeping purposes. IT dept will help with the encoding of C.A.R if there is still a huge volume of clients. Just let us know for assistance.<br><br>Your cooperation in this process is crucial for a smooth transition. If you encounter any challenges, reach out to the IT department promptly.<br><br>Thank you for your commitment to this upgrade!",
                    ],
                    [
                        'title' => "🔐 Attention to all front liners and cashiers:",
                        'content' => "Your account has been registered with the username as your employee ID, and the temporary password is also your employee ID.<br><br>For your security, please change your password immediately upon logging in.<br><br>Thank you for your prompt attention to this matter! If you encounter any issues, please reach out to the IT department. 🛡️🔒",
                    ],
                    [
                        'title' => "Reminders:",
                        'content' => "<ul><li>A 5% monthly surcharge for late payment.</li><li>A 2% rebate for payments made 3 days before the due date.</li><li>Previous billing until December 2023 will remain without surcharges.</li></ul><p class='font-18 mt-10'>For inquiries, comments, or suggestions, please feel free to contact us at <a href='mailto:clientcare@asianland.ph'>clientcare@asianland.ph</a>.</p>",
                    ]
                ];

                $active = 'active'; // Set the first slide as active
                foreach ($slides as $slide) {
                    echo '<div class="carousel-item ' . $active . '">';
                    echo '<div class="card-box pd-20 height-1400-p mb-30">';
                    echo '<h1>' . $slide['title'] . '</h1>';
                    echo '<p>' . $slide['content'] . '</p>';
                    echo '</div></div>';
                    $active = ''; // Remove active class for subsequent slides
                }
                ?>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            </div>

		</div>
	</div>