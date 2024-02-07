
<?php 
function format_num($number){
    $decimals = 2; 
    return number_format($number, $decimals);
}
session_start();
?>
<?php
require_once('../../includes/config.php');
$query = " SELECT * FROM tblemployees where emp_id ='".$_SESSION['alogin']."'";
$result = $conn->query($query);
if ($result) {
    $row = $result->fetch_assoc();
    $usr = $row['FirstName'] . ' ' . $row['LastName'];
} else {
    echo "Error: " . $conn->error;
}
if(isset($_GET['id'])){

    $sql = "SELECT * FROM t_utility_accounts WHERE c_account_no = ?";
    $acc = $_GET['id'];

    $qry = odbc_prepare($conn2, $sql);
    if (!odbc_execute($qry, array($acc))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }
    while ($res = odbc_fetch_array($qry)) {
    
        $account_no = $res["c_account_no"];
        $l_acc_no = $res["c_account_no"];
        $ctr = $res["c_control_no"];
        $first_name = $res["c_first_name"];
        $last_name = $res["c_last_name"];
        $middle_name = $res["c_middle_name"];
        $c_location = $res["c_location"];
        $address = $res["c_address"];
        $city_prov = $res["c_city_prov"];
        $zip_code = $res["c_zipcode"];
        $add = $res["c_address"] . ' ' . $res["c_city_prov"] . ' ' . $res["c_zipcode"];
        $date_applied = $res["c_date_applied"];
        $lot_area = $res["c_lot_area"];
        $mtf_end = $res["c_end_date"];
        $type = $res["c_types"];
        $status = $res["c_status"];
        $remarks = $res["c_remarks"];
        $remarks = $res["c_remarks"];
        if ($remarks === '') {
            $remarks = "N/A";
        }
        $full_name = $last_name . ', ' .$first_name . ' ' .$middle_name;

        
    }

    $load_due_payment_records = "SELECT * FROM t_utility_bill WHERE c_account_no = '$l_acc_no' and (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') ORDER BY c_start_date DESC limit 1";
    $result = odbc_exec($conn2, $load_due_payment_records);
    $due_count = odbc_num_rows($result);
    if ($due_count == 0) {
        $l_ddate = "1990-01-01";
        $l_sdate = "";
        $l_edate = "";
    }else{
        while ($due = odbc_fetch_array($result)) {
            $l_edate1 = date("Y-m-d", strtotime($due['c_end_date']));
            $l_sdate = date("Y-m-d", strtotime($due['c_start_date']));
            $l_edate = date("Y-m-d", strtotime($due['c_end_date']));
            $l_ddate = date("Y-m-d", strtotime($due['c_due_date']));
            $l_bill_type = $due['c_bill_type'];
            $l_amount_due = $due['c_amount_due'];
            $l_prev_bal = $due['c_prev_balance'];
        }
    }
    $mainte_edate =  date("M d, Y", strtotime($l_edate));
    $mainte_due = $l_ddate;


    $l_gcf_status = date("M d, Y", strtotime($l_sdate));
    $load_mtf_bill = "SELECT SUM(c_amount_due) as c_total_mtf from t_utility_bill where c_account_no = '$l_acc_no' and c_bill_type LIKE '%%MTF%%'" ;
    $mtf_result = odbc_exec($conn2, $load_mtf_bill);
    if ($mtf_result) {
        // Fetch the data
        $row = odbc_fetch_array($mtf_result);
    
        if ($row) {
            // Access the total MTF amount
            $l_mtf_due = $row['c_total_mtf'];
        } else {
            // No MTF records found
            $l_mtf_due = 0;
            $start_qry = "SELECT c_start_date from t_utility_bill where  c_account_no = '$l_acc_no' and (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') ORDER BY c_start_date DESC limit 1";
            $gcf_start = odbc_exec($conn2, $start_qry);
            if ($gcf_start) {
                $gcfresult = odbc_fetch_array($gcf_start);
                if ($gcfresult) {
                    $l_gcf_status = date("M d, Y", strtotime($gcfresult['c_start_date']));
                }
            }
        }
    } else {
        // Error executing the query
        echo "Error: " . odbc_errormsg($conn2);
    }

    $mtf_payment = "SELECT sum(c_st_amount_paid + c_discount) as c_total_mtf_payment from t_utility_payments where c_st_or_no LIKE '%%MTF%%' and c_account_no = '$l_acc_no'";
    $mtf_pay = odbc_exec($conn2, $mtf_payment);
    if ($mtf_pay) {
        // Fetch the data
        $row = odbc_fetch_array($mtf_pay);
    
        if ($row) {
            // Access the total MTF payment amount
            $l_mtf_payment = $row['c_total_mtf_payment'];
           
            $check_cover = "SELECT sum(c_amount_due) as gcf_tot_due from t_utility_bill where c_account_no = '$l_acc_no' and (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF')";
            $gcf_total_due = odbc_exec($conn2, $check_cover);
            if ($gcf_total_due) {
                $gcf_tot_bill = odbc_fetch_array($gcf_total_due);
                $l_gcf_tot_due = $gcf_tot_bill['gcf_tot_due'];
               
                if ($l_mtf_payment == $l_gcf_tot_due):
                    $l_gcf_status = "UPDATED";
                elseif($l_mtf_payment > $l_gcf_tot_due):
                    $l_gcf_status = "OVERPAYMENT";
                else:
                    $qry_gcf_period = "SELECT * FROM t_utility_bill where c_account_no = '$l_acc_no' and (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') order by c_start_date ASC";
                    $gcf_period = odbc_exec($conn2, $qry_gcf_period);
                    $quiboloy = 0;
                    $gcf_tot_period = 0;
                    if ($gcf_total_due) {
                        while ($due = odbc_fetch_array($gcf_period)){
                            $l_amount = $due['c_amount_due'];
                            $gcf_tot_period += $l_amount;
                            
                            if ($l_mtf_payment < $gcf_tot_period && $quiboloy == 0) {
                                $l_gcf_status = date("M d, Y", strtotime($due['c_start_date']));
                                $quiboloy = 1;
                                break;
                            } else {
                                
                            }
                        }
                    }

                endif;

            }

        } else {
            // No MTF payment records found
            $l_mtf_payment = 0;
            
        }
    } else {
        // Error executing the query
        echo "Error: " . odbc_errormsg($conn2);
    }

    $mainte_bal = $l_mtf_due - $l_mtf_payment;
    
   


    $load_due_payment_records = "SELECT * FROM t_utility_bill WHERE c_account_no = '$l_acc_no' and (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL') ORDER BY c_start_date DESC limit 1";
    $result = odbc_exec($conn2, $load_due_payment_records);
    $due_count = odbc_num_rows($result);
    if ($due_count == 0) {
        $l_ddate = "1990-01-01";
        $l_sdate = "";
        $l_edate = "";
    }else{
        while ($due = odbc_fetch_array($result)) {
            $l_edate1 = date("Y-m-d", strtotime($due['c_end_date']));
            $l_sdate = date("Y-m-d", strtotime($due['c_start_date']));
            $l_edate = date("Y-m-d", strtotime($due['c_end_date']));
            $l_ddate = date("Y-m-d", strtotime($due['c_due_date']));
            $l_bill_type = $due['c_bill_type'];
            $l_amount_due = $due['c_amount_due'];
            $l_prev_bal = $due['c_prev_balance'];
        }
    }
    $street_edate =  date("M d, Y", strtotime($l_edate));
    $street_due = $l_ddate;

    $l_stl_status = date("M d, Y", strtotime($l_sdate));
    $load_stl_bill = "SELECT SUM(c_amount_due) as c_total_stl from t_utility_bill where c_account_no = '$l_acc_no' and c_bill_type LIKE '%%STL%%'" ;
    $stl_result = odbc_exec($conn2, $load_stl_bill);
    if ($stl_result) {
        $row = odbc_fetch_array($stl_result);
    
        if ($row) {
            $l_stl_due = $row['c_total_stl'];
        } else {
            $l_stl_due = 0;
            $start_qry2 = "SELECT c_start_date from t_utility_bill where  c_account_no = '$l_acc_no' and (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL') ORDER BY c_start_date DESC limit 1";
            $stl_start = odbc_exec($conn2, $start_qry2);
            if ($stl_start) {
                $stlresult = odbc_fetch_array($stl_start);
                if ($stlresult) {
                    $l_stl_status =  date("M d, Y", strtotime($stlresult['c_start_date']));
                }
            }
        }
    } else {
        echo "Error: " . odbc_errormsg($conn2);
    }
   

    $stl_payment = "SELECT sum(c_st_amount_paid + c_discount) as c_total_stl_payment from t_utility_payments where c_st_or_no LIKE '%%STL%%' and c_account_no = '$l_acc_no'";
    $stl_pay = odbc_exec($conn2, $stl_payment);
    if ($stl_pay) {
        // Fetch the data
        $row = odbc_fetch_array($stl_pay);
    
        if ($row) {
            // Access the total STL payment amount
            $l_stl_payment = $row['c_total_stl_payment'];
            $check_cover2 = "SELECT sum(c_amount_due) as stl_tot_due from t_utility_bill where c_account_no = '$l_acc_no' and (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL')";
            $stl_total_due = odbc_exec($conn2, $check_cover2);
            if ($stl_total_due) {
                $stl_tot_bill = odbc_fetch_array($stl_total_due);
                $l_stl_tot_due = $stl_tot_bill['stl_tot_due'];
               
                if ($l_stl_payment == $l_stl_tot_due):
                    $l_stl_status = "UPDATED";
                elseif($l_stl_payment > $l_stl_tot_due):
                    $l_stl_status = "OVERPAYMENT";
                else:
                    $qry_stl_period = "SELECT * FROM t_utility_bill where c_account_no = '$l_acc_no' and (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL') order by c_start_date ASC";
                    $stl_period = odbc_exec($conn2, $qry_stl_period);
                    $quiboloy1 = 0;
                    $stl_tot_period = 0;
                    if ($stl_total_due) {
                        while ($due = odbc_fetch_array($stl_period)){
                            $l_amount2 = $due['c_amount_due'];
                            $stl_tot_period += $l_amount2;
                            
                            if ($l_stl_payment < $stl_tot_period && $quiboloy1 == 0) {
                                $l_stl_status = date("M d, Y", strtotime($due['c_start_date']));
                                $quiboloy1 = 1;
                                break;
                            } else {
                                
                            }
                        }
                    }

                endif;

            }



        } else {
            // No stl payment records found
            $l_stl_payment = 0;
        }
    } else {
        echo "Error: " . odbc_errormsg($conn2);
    }

    $stl_bal = $l_stl_due - $l_stl_payment;
    
    $get_msur = "SELECT
                    SUM(CASE WHEN c_bill_type = 'MTF' THEN c_amount_due ELSE 0 END) as c_current_mtf,
                    SUM(CASE WHEN c_bill_type = 'DLQ_MTF' THEN c_amount_due ELSE 0 END) as c_current_mtf_sur
                FROM 
                    t_utility_bill
                WHERE 
                    c_bill_type IN ('DLQ_MTF', 'MTF')  
                    AND  c_due_date = ' $mainte_due' AND c_account_no = '$l_acc_no'";
    $msur_details = odbc_exec($conn2, $get_msur);
    if ($msur_details) {
        $row = odbc_fetch_array($msur_details);
        if ($row) {
            $l_mtf_cur = $row['c_current_mtf'];
            $l_mtf_sur = $row['c_current_mtf_sur'];
        } else {
            $l_mtf_cur  = 0;
            $l_mtf_sur = 0;
        }
    } else {
        echo "Error: " . odbc_errormsg($conn2);
    }


     $get_ssur = "SELECT
                    SUM(CASE WHEN c_bill_type = 'STL' THEN c_amount_due ELSE 0 END) as c_current_stl,
                    SUM(CASE WHEN c_bill_type = 'DLQ_STL' THEN c_amount_due ELSE 0 END) as c_current_stl_sur
                FROM 
                    t_utility_bill
                WHERE 
                    c_bill_type IN ('DLQ_STL', 'STL')  
                    AND  c_due_date = ' $street_due' AND c_account_no = '$l_acc_no'";
    $ssur_details = odbc_exec($conn2, $get_ssur);
    if ($ssur_details) {
        $row2 = odbc_fetch_array($ssur_details);
    
        if ($row2) {
            $l_stl_cur = $row2['c_current_stl'];
            $l_stl_sur = $row2['c_current_stl_sur'];
        } else {
            $l_stl_cur  = 0;
            $l_stl_sur = 0;
        }
    } else {
        echo "Error: " . odbc_errormsg($conn2);
    }

    $mainte_prev =  ($mainte_bal - $l_mtf_cur - $l_mtf_sur);

    $stl_prev = ($stl_bal - $l_stl_cur - $l_stl_sur);  


   
    if ($l_gcf_status == "UPDATED" || $l_gcf_status == "OVERPAYMENT"){
        $l_gcf_status =  $l_gcf_status ;
        $months_mainte = 0;
    }else{
        $startDate = new DateTime($l_gcf_status);
        $endDate = new DateTime($mainte_edate);
        $l_gcf_status = $l_gcf_status . " up to " . $mainte_edate;
        // Convert date strings to DateTime objects
      

        // Calculate the difference in months
        $interval = $startDate->diff($endDate);
        $months_mainte = $interval->format('%m') + $interval->format('%y') * 12;

        
    }

    if ($l_stl_status == "UPDATED" || $l_stl_status == "OVERPAYMENT"){
        $l_stl_status =  $l_stl_status ;
        $months_street = 0;
    }else{
        $startDate1 = new DateTime($l_stl_status);
        $endDate1 = new DateTime($street_edate);
        $l_stl_status = $l_stl_status . " up to " . $street_edate;
        

        // Calculate the difference in months
        $interval1 = $startDate1->diff($endDate1);
        $months_street = $interval1->format('%m') + $interval1->format('%y') * 12;

       
    }
}


if (isset($_GET['id']) && !empty($_GET['id'])) {
    $accfield = true;
    $ctrfield = true;
    $pblfield = true;
} else {
    $accfield = false;
    $ctrfield = false;
    $pblfield = false;
}



?>
<style>
    .control-label{
        font-weight:bold;
        font-size:14px;
    }
    .form-control{
        font-size:14px;
        margin:5px;
        text-align: right;
    }
    #stl_due{
        padding:0;
    }
</style>


<div class="container-fluid">
<form action="" id="pay-form">
<img src="payments/car.jpg" class="img-thumbnail" style="height:105px;width:670px;border:none;margin-left:190px;margin-top:-30px;display:none;" alt="">

        <input type="hidden" name="id" value="<?php echo isset($account_no) ? $account_no : '' ?>">
        <div class="fieldset-container">

                <table style="width:100%;font-size:14px;">
                    <tr>
                        <td><b>Account No:</b></td>
                        <td><?= isset($account_no) ? $account_no : '' ?></td>

                        <td><b>Location:</b></td>
                        <td><?= isset($c_location) ? $c_location : '' ?></td>
                    </tr>
                    <tr>
                        <td><b>Full Name:</b></td>
                        <td><?= isset($full_name) ? $full_name : '' ?></td>
                    </tr>
                </table>  
        </div>
        <input type="hidden" name="acc_no" id="acc_no" class="form-control form-control-border"  value ="<?php echo isset($account_no) ? $account_no : '' ?>">

        <input type="hidden" name="lname" id="lname" class="form-control form-control-border" value ="<?php echo isset($last_name) ? $last_name : '' ?>" readonly required>

        <input type="hidden" name="fname" id="fname" class="form-control form-control-border" value ="<?php echo isset($first_name) ? $first_name : '' ?>"readonly required>

        <input type="hidden" name="mname" id="mname" class="form-control form-control-border" value ="<?php echo isset($middle_name) ? $middle_name : '' ?>"readonly required>
    
        <input type="hidden" name="address" id="address" class="form-control form-control-border" value ="<?php echo isset($add) ? $add : '' ?>">
    
        <input type="hidden" name="pbl" id="pbl" class="form-control form-control-border" value ="<?php echo isset($c_location) ? $c_location : '' ?>">
 
        <div class="fieldset-container">
            <fieldset class="fieldset">
                <legend style="text-align:center;font-weight:bold;font-size:16px;">STL (Streetlight) Details</legend>
                <table style="width:100%;">
                    <tr>
                        <td><label for="stl_date" class="control-label">STL Due Date: <br><span style="color: red;"><?php echo $l_stl_status; ?></span> </label></td>
                        <td><input type="date" name="stl_date" id="stl_date" class="form-control" value ="<?php echo isset($street_due) ? $street_due : date('Y-m-d'); ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="stl_last_bal" class="control-label">STL Prev. Bal: </label></td>
                        <td><input type="text" name="stl_last_bal" id="stl_last_bal" class="form-control" value ="<?php echo isset($stl_prev) ? format_num($stl_prev) : '0.00' ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="stl_cur" class="control-label">STL Curr. Due: </label></td>
                        <td><input type="text" name="stl_cur" id="stl_cur" class="form-control" value ="<?php echo isset($l_stl_cur) ? format_num($l_stl_cur) : '0.00' ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="stl_sur" class="control-label">STL Curr. Sur: </label></td>
                        <td><input type="text" name="stl_sur" id="stl_sur" class="form-control" value ="<?php echo isset($l_stl_sur) ? format_num($l_stl_sur) : '0.00' ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="stl_balance" class="control-label">STL Total Due: </label></td>
                        <td><input type="text" name="stl_balance" id="stl_balance" class="form-control" value ="<?php echo isset($stl_bal) ? format_num($stl_bal) : '0.00' ?>"readonly required></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="fieldset">
                <legend style="text-align:center;font-weight:bold;font-size:16px;">GCF (Grass-Cutting) Details</legend>
            
                <table style="width:100%;">
                    <tr>
                        <td><label for="main_date" class="control-label">GCF Due Date: <br><span style="color: red;"><?php echo $l_gcf_status ; ?></span></label></td>
                        <td><input type="date" name="main_date" id="main_date" class="form-control form-control-border" value ="<?php echo isset($mainte_due) ? $mainte_due : date('Y-m-d'); ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="main_last_bal" class="control-label">GCF Prev. Bal: </label></td>
                        <td><input type="text" name="main_last_bal" id="main_last_bal" class="form-control form-control-border" value ="<?php echo isset($mainte_prev) ? format_num($mainte_prev) : '0.00' ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="main_cur" class="control-label">GCF Curr. Due: </label></td>
                        <td><input type="text" name="main_cur" id="main_cur" class="form-control form-control-border" value ="<?php echo isset($l_mtf_cur) ? format_num($l_mtf_cur) : '0.00' ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="main_sur" class="control-label">GCF Curr. Sur: </label></td>
                        <td><input type="text" name="main_sur" id="main_sur" class="form-control form-control-border" value ="<?php echo isset($l_mtf_sur) ? format_num($l_mtf_sur) : '0.00' ?>"readonly required></td>
                    </tr>
                    <tr>
                        <td><label for="main_balance" class="control-label">GCF Total Due: </label></td>
                        <td><input type="text" name="main_balance" id="main_balance" class="form-control form-control-border" value ="<?php echo isset($mainte_bal) ? format_num($mainte_bal) : '0.00' ?>"readonly required></td>
                    </tr>
                </table>
            </fieldset>
        </div>
       
        <div class="fieldset-container">
            <fieldset class="fieldset">
                <table style="width:100%;">
                    <tr>
                        <td><label for="stl_amount_pay" class="control-label"><b>Payment for Streetlight Amount: </b></label></td>
                        <td><input type="number" name="stl_amount_pay" id="stl_amount_pay" class="form-control form-control-border stl_amount_pay" value ="" required></td>
                    </tr>
                    <tr>
                        <td><label for="stl_discount" class="control-label"><b>STL Discount:</b></label></td>
                        <td><input type="number" name="stl_discount" id="stl_discount" class="form-control form-control-border stl_discount" value ="" required></td>
                    </tr>
                    <tr>
                        <td><label for="stl_amount_paid" class="control-label"><b>STL Amount Paid: </b></label></td>
                        <td><input type="number" name="stl_amount_paid" id="stl_amount_paid" class="form-control form-control-border" value ="0" readonly required></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="fieldset">
                <table style="width:100%;">
                    <tr>
                        <td><label for="main_amount_pay" class="control-label"><b>Payment for Grass-Cutting Amount: </b></label></td>
                        <td><input type="number" name="main_amount_pay" id="main_amount_pay" class="form-control form-control-border main_amount_pay" value ="" required></td>
                    </tr>
                    <tr>
                        <td><label for="main_discount" class="control-label"><b>GCF Discount:</b></label></td>
                        <td><input type="number" name="main_discount" id="main_discount" class="form-control form-control-border main_discount" value ="" required></td>
                    </tr>
                    <tr>
                        <td><label for="main_amount_paid" class="control-label"><b>GCF Amount Paid: </b></label></td>
                        <td><input type="number" name="main_amount_paid" id="main_amount_paid" class="form-control form-control-border" value ="0" readonly required></td>
                    </tr>

                </table>
            </fieldset>

        </div>
        <input type="hidden" name="usr" id="usr" class="form-control form-control-border" value ="<?php echo $usr; ?>">
        <div class="fieldset-container">     
            <table style="width:100%;">
                <tr>
                    <td><label for="total_amount_paid" class="control-label"><b>Total Amount Paid: </b></label></td>
                    <td><input type="text" name="total_amount_paid" id="total_amount_paid" class="form-control form-control-border" value ="0" readonly required></td>
                </tr>
                <tr>
                    <td><label for="remarks" class="control-label"><b>Remarks </b></label></td>
                    <td><input type="text" name="remarks" id="remarks" class="form-control form-control-border" placeholder ="ex. (Jan 6, 2024 - Feb 5, 2024 - Full payment)" required></td>
                </tr>
            </table>
        </div>

        <div class="fieldset-container">     
            <table style="width:100%;">
                <tr>
                    <td class="col-md-4">
                        <div class="form-group">
                            <label for="mode_payment" class="control-label"><b>Mode of Payment:</b></label>
                            <select name="mode_payment" id="mode_payment" class="form-control form-control-border" style="text-align: center;" required>
                                <option value="1">Cash</option>
                                <option value="2">Check</option>
                                <option value="3">Online</option>
                                <option value="4">Check Voucher</option>
                            </select>
                        </div>
                    </td>
                    <td class="col-md-4">
                        <div class="form-group">
                            <label for="pay_date" class="control-label"><b>Pay Date: </b></label>
                            <input type="date" name="trans_date" id="trans_date" class="form-control form-control-border pay-date" value="<?php echo date('Y-m-d'); ?>" style="display: none;" required>
                            <input type="date" name="pay_date" id="pay_date" class="form-control form-control-border pay-date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </td>
                    <td class="col-md-4">
                        <div class="form-group">
                            <label for="payment_or" class="control-label"><b>CAR #: </b></label>
                            <input type="text" name="payment_or" id="payment_or" class="form-control form-control-border required" value="" minlength="6" maxlength="6">
                        </div>
                    </td>
                </tr>
            </table>
            </div>
            <div class="fieldset-container" id="check_details" style="display:none;">    
            <table style="width:100%;">
                <tr>
                    <td class="col-md-2">
                        <div class="form-group">
                            <label for="ref_no" class="control-label"><b>Reference #: </b></label>
                            <input type="text" name="ref_no" id="ref_no" class="form-control form-control-border">
                        </div>
                    </td>
                    <td class="col-md-2">
                        <div class="form-group">
                            <label for="branch" class="control-label"><b>CHECK BANK: </b></label>
                            <select name="branch" id="branch" class="form-control form-control-border custom" style="text-align: center;">
                                <option value="" selected>--SELECT BANK--</option>
                                <!-- Options will be dynamically added based on mode of payment -->
                            </select>
                        </div>
                    </td>  
                    <td class="col-md-2">
                        <div class="form-group">
                            <label for="check_date" class="control-label"><b>Check Date: </b></label>
                            <input type="date" name="check_date" id="check_date" class="form-control form-control-border">
                        </div>
                    </td>
                </tr>
            </table>
        </div>
      <!--   <div class="fieldset-container" id="ref_no_details" style="display:none;">   
            <table style="width:100%;"> 
                
            </table>
        </div> -->
    
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="button" id="printDataButton" class="btn btn-primary">
                    <i class="fa fa-print"></i> Preview
                </button>
            </div>
        </div>
        
    </form>
</div>
<style>
    input[type="number"] {
        text-align: right;
    }
</style>
<script>
    function convertToWords(number) {
        var ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
    var teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    var tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

    function convertGroup(num) {
        var result = "";
        if (num >= 100) {
            result += ones[Math.floor(num / 100)] + " Hundred ";
            num %= 100;
        }
        if (num >= 10 && num <= 19) {
            result += teens[num - 10];
        } else if (num >= 20) {
            result += tens[Math.floor(num / 10)];
            if (num % 10 > 0) {
                result += " " + ones[num % 10];
            }
        } else if (num > 0) {
            result += ones[num];
        }
        return result;
    }

    var result = "";
    if (number >= 1000000) {
        result += convertGroup(Math.floor(number / 1000000)) + " Million ";
        number %= 1000000;
    }
    if (number >= 1000) {
        result += convertGroup(Math.floor(number / 1000)) + " Thousand ";
        number %= 1000;
    }
    if (number >= 1) {
        result += convertGroup(Math.floor(number));
    }

    var decimalPart = number % 1;
    if (decimalPart > 0) {
        result += " and " + (decimalPart * 100).toFixed(0) + "/100";
    }

    return result.trim();
    }

    function formatNumberWithCommas(number) {
        number = isNaN(parseFloat(number)) ? 0 : parseFloat(number);
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    function printInputData() {
        var mp = document.getElementById("mode_payment").value;
        var mainAmountPaid = parseFloat(document.getElementById("main_amount_paid").value);
        var stlAmountPaid = parseFloat(document.getElementById("stl_amount_paid").value);
        var totalPaid =mainAmountPaid + stlAmountPaid;
        var amtToWord = convertToWords(totalPaid);
        var paymentForText;

        if (mainAmountPaid === 0) {
            paymentForText = 'STL Fee';
        } else if (stlAmountPaid === 0) {
            paymentForText = 'GCF Fee';
        }else{
            paymentForText = 'GCF and STL Fee';
        }

        var printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write('<html><head>');
        printWindow.document.write('<style>');
        printWindow.document.write('body {');
        //printWindow.document.write('    background-image: url("payments/car.jpg");');
        printWindow.document.write('    background-size: 820px 350px;');
        printWindow.document.write('}');
        printWindow.document.write('.pay-date { margin: 133px 390px; width: 100px; position:absolute; }');
        printWindow.document.write('.full-name { margin: 155px 180px; width: 250px; position:absolute; }');
        printWindow.document.write('.add { margin: 190px 150px; width: 350px; position:absolute; }');
        printWindow.document.write('.payment-or { margin: 20px 180px; }');
        printWindow.document.write('.mode-payment { margin: 10px 0; }');
       
        printWindow.document.write('.stl { margin: 100px -155px; position:absolute;}');

        printWindow.document.write('.mtf { margin: 100px -155px; position:absolute;}');


        printWindow.document.write('.usr { margin: 280px 380px; position:absolute; width:200px;font-weight:bold}');

        if (mp === "1") {
            printWindow.document.write('.mp { margin: 270px -110px; position:absolute; width:200px;}');
            printWindow.document.write('.check_date {display:none;}');
            printWindow.document.write('.ref_no {display:none;}');
            printWindow.document.write('.branch {display:none;}');
        } else if(mp === "2") {
            printWindow.document.write('.mp { margin: 290px -95px; position:absolute; width:200px;}');//////Adjust the amount if not sakto. 300 yung top margin. -130 yung right.
            printWindow.document.write('.check_date { margin:310px -10px; position:absolute; width:200px;}');///Same lang sa mp.
            printWindow.document.write('.branch { margin: 260px -130px; position:absolute; width:200px;}');///Same lang sa mp.
            printWindow.document.write('.ref_no { margin: 310px -100px; position:absolute; width:200px;}');///Same lang sa mp.
            
           
        }else{
            printWindow.document.write('.mp { margin: 290px -110px; position:absolute; width:200px;}');//////Adjust the amount if not sakto. 300 yung top margin. -130 yung right.
            printWindow.document.write('.check_date {display:none;}');
            printWindow.document.write('.ref_no { margin: 300px 130px; position:absolute; width:200px;}');///Same lang sa mp.
            printWindow.document.write('.branch {display:none;}');
        }

        
        printWindow.document.write('.remarks { margin: 260px 360px ; position:absolute; width:250px;}');

        printWindow.document.write('.total-amount-paid { margin: 240px 420px; width: 200px; position:absolute; }');
        printWindow.document.write('.payment-for { margin: 260px 230px; width:300px; position:absolute; }');
        printWindow.document.write('.numtowords { margin: 215px 160px; width:400px; position:absolute; }');

        printWindow.document.write('.stl-pay { text-align:right; position:absolute; }');
        printWindow.document.write('.stl-disc { text-align:right; position:absolute; }');
        printWindow.document.write('.stl-amt { text-align:right; position:absolute; }');

        printWindow.document.write('.stl_amount_pay { disabled:disabled; }');
        
        if (mainAmountPaid === 0 && stlAmountPaid !== 0) {
            printWindow.document.write('.mtf { display: none; }');
            printWindow.document.write('.stl { display: block; }');
        } else if(mainAmountPaid !== 0 && stlAmountPaid === 0) {
            //printWindow.document.write('.mtf { display: block; margin: 180px -170px; position:absolute; }');
            printWindow.document.write('.mtf { display: block; }');
            printWindow.document.write('.stl { display: none; }');
        }else if(mainAmountPaid !== 0 && stlAmountPaid !== 0) {
            printWindow.document.write('.mtf { display: block; margin: 180px -155px; position:absolute; }');
            printWindow.document.write('.stl { display: block; }');
        }else{
            printWindow.document.write('.mtf { display: none; }');
            printWindow.document.write('.stl { display: none; }');
        }

     /*    document.getElementById("pay_date").disabled = true;
        document.getElementById("payment_or").disabled = true;
        document.getElementById("stl_amount_pay").disabled = true;
        document.getElementById("main_amount_pay").disabled = true;
        document.getElementById("main_discount").disabled = true;
        document.getElementById("stl_discount").disabled = true; */

        printWindow.document.write('</style>');

        printWindow.document.write('</head><body style="border:none;margin-left:190px;margin-top:-30px;background-repeat:no-repeat;">');
        var fullName =  document.getElementById("lname").value + ', ' + document.getElementById("fname").value + ' ' + document.getElementById("mname").value ;
        var accNo = document.getElementById("acc_no").value;
        //var pbl = document.getElementById("pbl").value
        // Limit the full name to 50 characters
        var limitedFullName = fullName.length > 65 ? fullName.substring(0, 65) + ' ...' : fullName;

        

        // Print the formatted string
        printWindow.document.write('<p class="full-name">' + limitedFullName + '</p>');

        printWindow.document.write('<p class="add">' + document.getElementById("pbl").value + ' / ' + accNo + '</p>');
        printWindow.document.write('<p class="pay-date">' + document.getElementById("trans_date").value + '</p>');
        //printWindow.document.write('<p class="payment-or">Or No.: ' + '' + '</p>');
        //printWindow.document.write('<p class="mode-payment">Mode of Payment: ' + '' + '</p>');
        
        printWindow.document.write('<table class="stl">');
        printWindow.document.write('<tr><td>STL Amount:</td><td style="text-align:right;">'+ formatNumberWithCommas(parseFloat(document.getElementById("stl_amount_pay").value)) + '</td></tr>');
        printWindow.document.write('<tr><td>STL Discount:</td><td style="text-align:right;">' + formatNumberWithCommas(parseFloat(document.getElementById("stl_discount").value)) + '</td></tr>');
        printWindow.document.write('<tr><td>STL Amount Paid:</td><td style="text-align:right;">' + formatNumberWithCommas(parseFloat(document.getElementById("stl_amount_paid").value)) + '</td></tr>');
        printWindow.document.write('</table>');

        printWindow.document.write('<table class="mtf">');
        printWindow.document.write('<tr><td>GCF Amount:</td><td style="text-align:right;">' + formatNumberWithCommas(parseFloat(document.getElementById("main_amount_pay").value)) + '</td></tr>');
        printWindow.document.write('<tr><td>GCF Discount:</td><td style="text-align:right;">' + formatNumberWithCommas(parseFloat(document.getElementById("main_discount").value)) + '</td></tr>');
        printWindow.document.write('<tr><td>GCF Amount Paid:</td><td style="text-align:right;">' + formatNumberWithCommas(parseFloat(document.getElementById("main_amount_paid").value)) + '</td></tr>');
        printWindow.document.write('</table>');

        printWindow.document.write('<p class="mp">' + document.getElementById("total_amount_paid").value + '</p>');
        printWindow.document.write('<p class="check_date">' + document.getElementById("check_date").value + '</p>');
        printWindow.document.write('<p class="branch">Bank Branch: ' + document.getElementById("branch").value + '</p>');
        printWindow.document.write('<p class="ref_no">' + document.getElementById("ref_no").value + '</p>');

        printWindow.document.write('<p class="remarks">' + document.getElementById("remarks").value + '</p>');

        printWindow.document.write('<p class="total-amount-paid">' + document.getElementById("total_amount_paid").value + '</p>');
        printWindow.document.write('<p class="usr">' + document.getElementById("usr").value + '</p>');
        printWindow.document.write('<p class="numtowords">' + amtToWord + ' Pesos Only' + '</p>');

       
        printWindow.document.write('<p class="payment-for">' + paymentForText +'</p>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }

    document.getElementById("printDataButton").addEventListener("click", printInputData);
</script>

<style>
    .fieldset-container {
        display: flex;
        justify-content: space-between;
        border: 1px solid #ccc;
        padding: 10px;
        margin: 10px;
    }
    .fieldset {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin: 5px;
    }
</style>

<script>
$(document).ready(function() {



    $(document).on('keyup', ".stl_amount_pay", function(e) {
		e.preventDefault();
     
		compute_total_amt_paid();
        

	});	

    $(document).on('keyup', ".main_amount_pay", function(e) {
		e.preventDefault();
       
		compute_total_amt_paid();
        

	});	

    $(document).on('keyup', ".stl_discount", function(e) {
		e.preventDefault();
    
		compute_total_amt_paid();
        

	});	

    $(document).on('keyup', ".main_discount", function(e) {
		e.preventDefault();
     
		compute_total_amt_paid();
        

	});

});
function compute_pay_date(){

    const gcf_due_date = new Date($('#main_date').val());
    const gcf_cur = $('#main_cur').val();
    const gcf_bal = $('#main_balance').val();
    const stl_due_date = new Date($('#stl_date').val());
    const stl_cur = $('#stl_cur').val();
    const stl_bal = $('#stl_balance').val();
    const pay_date = new Date($('.pay-date').val());

    if (pay_date > gcf_due_date){
        const timeDiff = Math.abs(pay_date.getTime() - gcf_due_date.getTime());
        const diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        if (diffDays <= 2) {
            l_sur = 0;
        }
        gcf_surcharge = gcf_cur * .05;
        $('#main_sur').val(gcf_surcharge);
        gcf_balance = parseFloat(gcf_bal) + parseFloat(gcf_surcharge);
        console.log(gcf_balance);
        $('#main_balance').val(gcf_balance);
    }

    if (pay_date > stl_due_date){
        const timeDiff = Math.abs(pay_date.getTime() - stl_due_date.getTime());
        const diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        if (diffDays <= 2) {
            l_sur = 0;
        }
        stl_surcharge = stl_cur * .05;
        //console.log(stl_surcharge);

       
        $('#stl_sur').val(stl_surcharge);
        stl_balance = parseFloat(stl_bal) + parseFloat(stl_surcharge);
        console.log(stl_balance);
        $('#stl_balance').val(stl_balance);
    }

}

function compute_total_amt_paid(){

    var stl_pay = $('.stl_amount_pay').val();
    var stl_discount = $('.stl_discount').val();
    var mtf_pay = $('.main_amount_pay').val();
    var mtf_discount = $('.main_discount').val();

    var stlAmount = parseFloat((stl_pay - stl_discount).toFixed(2));
    var mtfAmount = parseFloat((mtf_pay - mtf_discount).toFixed(2));

    total = (stlAmount + mtfAmount);
    total = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    $("#stl_amount_paid").val(stlAmount);
    $("#main_amount_paid").val(mtfAmount);
    $("#total_amount_paid").val(total);

}

</script>
<script>
    
    $(function(){
        $('#uni_modal_payment #pay-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_payment",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert("An error occured2",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        setTimeout(()=>{
                            //printInputData()
                            alert(resp.msg);
                            location.reload();
                            /*  location.replace('./?page=admin/index.php&id='+resp.id_encrypt) */
                        },200)

                        /* alert(resp.msg);
                        location.reload(); */
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body,.modal').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>

<script>
    document.getElementById('mode_payment').addEventListener('change', function() {
        var checkDetails = document.getElementById('check_details');
        var refNoDetails = document.getElementById('ref_no_details');
        var checkDateInput = document.getElementById('check_date'); 
        var branchInput = document.getElementById('branch'); 
        var checkDateLabel = document.querySelector('label[for="check_date"]');
        var refNoLabel = document.querySelector('#check_details label[for="ref_no"]'); // Added this line
        var branchLabel = document.querySelector('#check_details label[for="branch"]');
        var branchSelect = document.getElementById('branch');
        var selectedMode = this.value;

        // Clear existing options
        branchSelect.innerHTML = '<option value="" selected>--SELECT BANK--</option>';

        // Add options based on selected mode
        if (selectedMode === '2') {
            addOptions(branchSelect, ['ROBBank', 'UB', 'BPI', 'BDO', 'CBS', 'SBC', 'PNB', 'PSB', 'EWB', 'BOC', 'RCBC', 'LB']);
        } else if (selectedMode === '3') {
            addOptions(branchSelect, ['BDO', 'BOC', 'BPI', 'CBS', 'MBTC', 'PBB', 'PVB', 'RCBC', 'ROBBank', 'SBC', 'UB', 'UCBP']);
        }
   

        if (this.value === '1' || this.value === '3' || this.value === '2' || this.value === '4') { 
            branchInput.value = null;
        }

        if (this.value === '2') { 
            checkDetails.style.display = 'block';    
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); 
            var yyyy = today.getFullYear();
            today = yyyy + '-' + mm + '-' + dd;
            checkDateInput.value = today;

              // Change label text for 'Reference No' in mode 2
            if (refNoLabel) {
                refNoLabel.innerHTML = '<b>Check No:</b>';
            }

            if (branchLabel) {
                branchLabel.style.display = 'block';
            }
            if (branchLabel) {
                branchLabel.innerHTML = '<b>Check Branch:</b>';
            }
            
            if (branchInput) {
                branchInput.style.display = 'block';
            }

            if (checkDateLabel) {
                checkDateLabel.style.display = 'block';
            }
            if (checkDateInput) {
                checkDateInput.style.display = 'block';
            }


           
        } else if (this.value === '3') { 
            checkDetails.style.display = 'block';
            //refNoDetails.style.display = 'block';

            checkDateInput.value = null;

            if (checkDateLabel) {
                checkDateLabel.style.display = 'none';
            }
            if (checkDateInput) {
                checkDateInput.style.display = 'none';
            }
            if (branchLabel) {
                branchLabel.style.display = 'block';
            }
            if (branchSelect) {
                branchSelect.style.display = 'block';
            }
            

            if (refNoLabel) {
                refNoLabel.innerHTML = '<b>Reference #:</b>';
            }

            if (branchLabel) {
                branchLabel.innerHTML = '<b>Online Bank(Depository):</b>';
            }

        }else if (this.value === '4') { 
            checkDetails.style.display = 'block';
            if (checkDateLabel) {
                checkDateLabel.style.display = 'none';
            }
            if (checkDateInput) {
                checkDateInput.style.display = 'none';
            }
            if (branchLabel) {
                branchLabel.style.display = 'none';
            }
            if (branchSelect) {
                branchSelect.style.display = 'none';
            }
            
            //refNoDetails.style.display = 'none';
            checkDateInput.value = null;
            branchInput.value = null;
        }else {
            checkDetails.style.display = 'none';
            refNoDetails.style.display = 'none';
            checkDateInput.value = null;
            branchInput.value = null;
        }
    });


     // Function to add options to a select element
function addOptions(selectElement, optionsArray) {
    optionsArray.sort();

    for (var i = 0; i < optionsArray.length; i++) {
        var option = document.createElement('option');
        option.value = optionsArray[i];
        option.text = optionsArray[i];
        selectElement.appendChild(option);
    }
}
</script>
