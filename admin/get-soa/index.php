<div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Statement of Accounts</h2>
            </div>
            <div class="card-box mb-30">
                <div class="pd-20">
                    <form action="<?php echo base_url ?>admin/?page=get-soa/list-soa" method="post">
                        <div class="row align-items-end">
                           
                            <div class="col-md-3 form-group">
                                <label for="account_number" class="control-label">Account Number:</label>
                                <input type="text" id="account_number" name="account_number" class="form-control" maxlength="11">

                            </div>

                            <div class="col-md-3 form-group">
                                <button class="btn btn-primary"><i class="dw dw-search"></i> Search Account</button>
                                <!-- <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="fa fa-print"></i> Print</button> -->
                            </div>
                        </div>
                    </form>
                   
                    <form action="" id="filter">
                        <div class="row align-items-end">
                            <input type="hidden" id="page" name="page" value="accounts" class="form-control form-control-sm rounded-0">
                        
                       
                            <div class="col-md-2 form-group">
                                
                                <label for="phase" class="control-label">Phase</label>
                                <select name="phase" id="phase" class="custom-select form-control" autocomplete="off">
                                  <?php
                                
                                    $sql = "SELECT * FROM t_projects ORDER BY c_acronym";
                                    $results = odbc_exec($conn2, $sql);


                                    $selectedValue = isset($_GET['phase']) ? $_GET['phase'] : ''; // Get the selected value from the submitted form

                                    
                                   
                                    echo '<option value="" selected>--SELECT--</option>';
                                    echo '<option value="100">ALL</option>';
                                    while ($row = odbc_fetch_array($results)) {
                                        $optionValue = $row['c_code'];
                                        $optionText = $row['c_acronym'];
                                        $selected = ($selectedValue == $optionValue) ? 'selected' : ''; // Check if this option is selected
                                        echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionText . '</option>';
                                    }
                                    echo '</select>';
                                        
                                    
                                ?>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="block" class="control-label">Block</label>
                                <input type="number" id="block" name="block" value="<?= $l_block ?>" class="form-control">
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="lot" class="control-label">Lot</label>
                                <input type="number" id="lot" name="lot" value="<?= $l_lot ?>" class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <button class="btn btn-primary"><i class="dw dw-search"></i> Search Location</button>
                                <!-- <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="fa fa-print"></i> Print</button> -->
                            </div>
                        </div>
                        </form>
                </div>
            </div>
        </div>
    </div>