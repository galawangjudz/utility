
<?php 
function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}
?>
<?php
require_once('../../includes/config.php');
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
    $mainte_due = $l_ddate;

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
    $street_due = $l_ddate;

    $load_stl_bill = "SELECT SUM(c_amount_due) as c_total_stl from t_utility_bill where c_account_no = '$l_acc_no' and c_bill_type LIKE '%%STL%%'" ;
    $stl_result = odbc_exec($conn2, $load_stl_bill);
    if ($stl_result) {
        $row = odbc_fetch_array($stl_result);
    
        if ($row) {
            $l_stl_due = $row['c_total_stl'];
        } else {
            $l_stl_due = 0;
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
    if ($msur_details) {
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
<div class="container-fluid">
<form action="" id="pay-form">
<img src="payments/car.jpg" class="img-thumbnail" style="height:105px;width:670px;border:none;margin-left:190px;margin-top:-10px;display:none;" alt="">
        <input type="hidden" name="id" value="<?php echo isset($account_no) ? $account_no : '' ?>">
      
        <div class="row">
            <dt class="col-md-2 text-muted">Account No</dt>
            <dd class="col-md-10 fs-5 fw-bold"><h5><?= isset($account_no) ? $account_no : '' ?></h5></dd>
            <dt class="col-md-2 text-muted">Location</dt>
            <dd class="col-md-10 fs-5 fw-bold"><h5><?= isset($c_location) ? $c_location : '' ?></h5></dd>
            <dt class="col-md-2 text-muted">Full Name</dt>
            <dd class="col-md-10 fs-5 fw-bold"><h5><?= isset($full_name) ? $full_name : '' ?></h5></dd>

           
        </div>

     
        <input type="hidden" name="lname" id="lname" class="form-control form-control-border" placeholder="Enter Last Name" value ="<?php echo isset($last_name) ? $last_name : '' ?>" readonly required>

        <input type="hidden" name="fname" id="fname" class="form-control form-control-border" placeholder="Enter First Name" value ="<?php echo isset($first_name) ? $first_name : '' ?>"readonly required>

        <input type="hidden" name="mname" id="mname" class="form-control form-control-border" placeholder="Enter Middle Name" value ="<?php echo isset($middle_name) ? $middle_name : '' ?>"readonly required>
    
        <div class="fieldset-container">
            <fieldset class="fieldset">
                <legend>STL (Streetlight) Details</legend>
                <div class="row">
               
                    <label for="stl_due" class="control-label">STL Due Date</label>
                    <input type="date" name="stl_due" id="stl_due" class="form-control" value ="<?php echo isset($street_due) ? $street_due : date('Y-m-d'); ?>"readonly required>
                 
                    <label for="stl_last_bal" class="control-label">STL Prev. Bal</label>
                    <input type="number" name="stl_last_bal" id="stl_last_bal" class="form-control" value ="<?php echo isset($stl_prev) ? $stl_prev : '0.00' ?>"readonly required>
                   
         
                    <label for="stl_due" class="control-label">STL Curr. Due</label>
                    <input type="number" name="stl_due" id="stl_due" class="form-control" value ="<?php echo isset($l_stl_cur) ? $l_stl_cur : '0.00' ?>"readonly required>
                  
        
                    <label for="stl_sur" class="control-label">STL Curr. Sur</label>
                    <input type="number" name="stl_sur" id="stl_sur" class="form-control" value ="<?php echo isset($l_stl_sur) ? $l_stl_sur : '0.00' ?>"readonly required>
                  
        
                    <label for="stl_balance" class="control-label">STL Total Due</label>
                    <input type="number" name="stl_balance" id="stl_balance" class="form-control" value ="<?php echo isset($stl_bal) ? $stl_bal : '0.00' ?>"readonly required>
            
                </div>
            </fieldset>
            <fieldset class="fieldset">
                <legend>GCF (GrassCutting) Details</legend>
            
			    <div class="row">
                
                    <label for="main_date" class="control-label">GCF Due Date</label>
                    <input type="date" name="main_date" id="main_date" class="form-control form-control-border" value ="<?php echo isset($mainte_due) ? $mainte_due : date('Y-m-d'); ?>"readonly required>
                
         
                    <label for="main_last_bal" class="control-label">GCF Prev. Bal</label>
                    <input type="number" name="main_last_bal" id="main_last_bal" class="form-control form-control-border" value ="<?php echo isset($mainte_prev) ? $mainte_prev : 0 ?>"readonly required>
                
           
          
                    <label for="main_due" class="control-label">GCF Curr. Due</label>
                    <input type="number" name="main_due" id="main_due" class="form-control form-control-border" value ="<?php echo isset($l_mtf_cur) ? $l_mtf_cur : 0 ?>"readonly required>
                
           
                    <label for="main_sur" class="control-label">GCF Curr. Sur</label>
                    <input type="number" name="main_sur" id="main_sur" class="form-control form-control-border" value ="<?php echo isset($l_mtf_sur) ? $l_mtf_sur : 0 ?>"readonly required>
                
          
                    <label for="main_balance" class="control-label">GCF Total Due</label>
                    <input type="number" name="main_balance" id="main_balance" class="form-control form-control-border" value ="<?php echo isset($mainte_bal) ? $mainte_bal : 0 ?>"readonly required>
                
        
            
                </div>
            </fieldset>
        </div>
        <div class="row">
            <div class="col-md-4">
            <div class="form-group">
                    <label for="pay_date" class="control-label">Pay Date</label>
                    <input type="date" name="pay_date" id="pay_date" class="form-control form-control-border" value ="<?php echo date('Y-m-d'); ?>" required>
                
                </div>
            </div>
           
           
            <div class="col-md-4">
                <div class="form-group">
                <label for="payment_or" class="control-label">Or No.</label>
                <input type="text" name="payment_or" id="payment_or" class="form-control form-control-border required" placeholder= "ex.CAR153245" value ="" >
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mode_payment" class="control-label">Mode of Payment *</label>
                    <select name="mode_payment" id="mode_payment" class="form-control form-control-border" readonly disabled required>
                        <option value="Cash" <?= isset($status) && $status == 'Active' ? 'selected' : '' ?>>Cash</option>
                        <option value="Check" <?= isset($status) && $status == 'Inactive' ? 'selected' : '' ?>>Check</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                <label for="stl_amount_pay" class="control-label">Payment for Streetlight Amount</label>
                <input type="number" name="stl_amount_pay" id="stl_amount_pay" class="form-control form-control-border stl_amount_pay" value ="0" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <label for="main_amount_paid" class="control-label">Payment for Grasscutting Amount </label>
                <input type="number" name="main_amount_pay" id="main_amount_pay" class="form-control form-control-border main_amount_pay" value ="0" required>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4">
            <div class="form-group">
                    <label for="stl_discount" class="control-label">STL Discount</label>
                    <input type="number" name="stl_discount" id="stl_discount" class="form-control form-control-border stl_discount" value ="0" required>
                
                </div>
            </div>
            <div class="col-md-4">
            <div class="form-group">
                    <label for="main_discount" class="control-label">GCF Discount</label>
                    <input type="number" name="main_discount" id="main_discount" class="form-control form-control-border main_discount" value ="0" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                <label for="stl_amount_paid" class="control-label">STL Amount Paid</label>
                <input type="number" name="stl_amount_paid" id="stl_amount_paid" class="form-control form-control-border" value ="0" readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <label for="main_amount_paid" class="control-label">GCF Amount Paid</label>
                <input type="number" name="main_amount_paid" id="main_amount_paid" class="form-control form-control-border" value ="0" readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <label for="total_amount_paid" class="control-label">Total Amount Paid</label>
                <input type="text" name="total_amount_paid" id="total_amount_paid" class="form-control form-control-border" value ="0" readonly required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" id="printDataButton" class="btn btn-primary">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
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
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    function printInputData() {
       
        var mainAmountPaid = parseFloat(document.getElementById("main_amount_paid").value);
        var stlAmountPaid = parseFloat(document.getElementById("stl_amount_paid").value);
        var totalPaid =mainAmountPaid + stlAmountPaid;
        var amtToWord = convertToWords(totalPaid);
        var paymentForText;

        if (mainAmountPaid === 0) {
            paymentForText = 'Payment for Streetlight Fee';
        } else if (stlAmountPaid === 0) {
            paymentForText = 'Payment for Maintenance Fee';
        }else{
            paymentForText = 'Payment for Maintenance and Streetlight Fee';
        }

        var printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write('<html><head>');
        printWindow.document.write('<style>');
        printWindow.document.write('body {');
        printWindow.document.write('    background-image: url("payments/car.jpg");');
        printWindow.document.write('    background-size: 820px 350px;');
        printWindow.document.write('}');
        printWindow.document.write('.pay-date { margin: 150px 450px; width: 100px; position:absolute; }');
        printWindow.document.write('.full-name { margin: 170px 180px; width: 350px; position:absolute; }');
        printWindow.document.write('.add { margin: 190px 150px; width: 350px; position:absolute; }');
        printWindow.document.write('.payment-or { margin: 20px 180px; }');
        printWindow.document.write('.mode-payment { margin: 10px 0; }');
       
        printWindow.document.write('.stl { margin: 100px -170px; position:absolute; }');

        printWindow.document.write('.mtf { margin: 100px -170px; position:absolute; }');

        printWindow.document.write('.total-amount-paid { margin: 240px 420px; width: 200px; position:absolute; }');
        printWindow.document.write('.payment-for { margin: 260px 240px; width:300px; position:absolute; }');
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
            printWindow.document.write('.mtf { display: block; margin: 180px -170px; position:absolute; }');
            printWindow.document.write('.stl { display: block; }');
        }

        document.getElementById("pay_date").disabled = true;
        document.getElementById("payment_or").disabled = true;
        document.getElementById("stl_amount_pay").disabled = true;
        document.getElementById("main_amount_pay").disabled = true;
        document.getElementById("main_discount").disabled = true;
        document.getElementById("stl_discount").disabled = true;

        printWindow.document.write('</style>');

        printWindow.document.write('</head><body style="border:none;margin-left:190px;margin-top:-10px;background-repeat:no-repeat;">');
        printWindow.document.write('<p class="full-name">' + document.getElementById("fname").value + ' ' + document.getElementById("mname").value + ' ' + document.getElementById("lname").value + '</p>');
        //printWindow.document.write('<p class="add">' + document.getElementById("add").value + ' ' + document.getElementById("city_prov").value + ' ' + document.getElementById("zip_code").value + '</p>');
        printWindow.document.write('<p class="pay-date">' + document.getElementById("pay_date").value + '</p>');
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

        printWindow.document.write('<p class="total-amount-paid">' + document.getElementById("total_amount_paid").value + '</p>');
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
function compute_total_amt_paid(){

    var stl_pay = $('.stl_amount_pay').val();
    var stl_discount = $('.stl_discount').val();
    var mtf_pay = $('.main_amount_pay').val();
    var mtf_discount = $('.main_discount').val();

    var stlAmount = stl_pay - stl_discount;
    var mtfAmount = mtf_pay - mtf_discount;

    total = (stlAmount + mtfAmount);
    total = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    $("#stl_amount_paid").val(stlAmount);
    $("#main_amount_paid").val(mtfAmount);
    $("#total_amount_paid").val(total);

}

</script>
<script>
    
    $(function(){
        $('#uni_modal #pay-form').submit(function(e){
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
                        alert(resp.msg);
                        location.reload();
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