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
        // Fetch the data
        $row = odbc_fetch_array($stl_result);
    
        if ($row) {
            // Access the total STL amount
            $l_stl_due = $row['c_total_stl'];
        } else {
            // No STL records found
            $l_stl_due = 0;
        }
    } else {
        // Error executing the query
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
        // Error executing the query
        echo "Error: " . odbc_errormsg($conn2);
    }

    $stl_bal = $l_stl_due - $l_stl_payment;
    

}


if (isset($_GET['id']) && !empty($_GET['id'])) {
    // If 'id' is set and not empty, it means you want to disable specific fields
    $accfield = true;
    $ctrfield = true;
    $pblfield = true;
} else {
    // If 'id' is not set or empty, you don't want to disable any fields
    $accfield = false;
    $ctrfield = false;
    $pblfield = false;
}
?>
<div class="container-fluid">
<form action="" id="pay-form">
        <input type="hidden" name="id" value="<?php echo isset($account_no) ? $account_no : '' ?>">
        <div class="row">
			<div class="col-md-4">
                <div class="form-group">
                    <label for="acc_no" class="control-label">Account No</label>
                    <input type="text" name="acc_no" id="acc_no" class="form-control form-control-border" placeholder="ex. 15200202102" value ="<?php echo isset($account_no) ? $account_no : '' ?>"<?php if ($accfield) echo ' readonly'; ?> required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="ctr" class="control-label">Control No</label>
                    <input type="text" name="ctr" id="ctr" class="form-control form-control-border" placeholder="ex. STL-12345" value ="<?php echo isset($ctr) ? $ctr : '' ?>"<?php if ($ctrfield) echo ' readonly'; ?> required>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="pbl" class="control-label">Phase/Block/Lot</label>
                    <input type="text" name="pbl" id="pbl" class="form-control form-control-border" placeholder="Phase/Blk/Lot" value ="<?php echo isset($c_location) ? $c_location : '' ?>"<?php if ($pblfield) echo ' readonly'; ?> required>
                </div>
            </div>
        </div>
        <div class="row">
			<div class="col-md-4">
                <div class="form-group">
                    <label for="fname" class="control-label">First Name</label>
                    <input type="text" name="fname" id="fname" class="form-control form-control-border" placeholder="Enter Last Name" value ="<?php echo isset($last_name) ? $last_name : '' ?>" readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lname" class="control-label">Last Name</label>
                    <input type="text" name="lname" id="lname" class="form-control form-control-border" placeholder="Enter First Name" value ="<?php echo isset($first_name) ? $first_name : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mname" class="control-label">Middle Name</label>
                    <input type="text" name="mname" id="mname" class="form-control form-control-border" placeholder="Enter Middle Name" value ="<?php echo isset($middle_name) ? $middle_name : '' ?>"readonly required>
                </div>
            </div>
        </div>
        <div class="row">
			<div class="col-md-7">
                <div class="form-group">
                    <label for="add" class="control-label">Address</label>
                    <input type="text" name="add" id="add" class="form-control form-control-border" placeholder="Enter Address" value ="<?php echo isset($address) ? $address : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="city_prov" class="control-label">City/ Province</label>
                    <input type="text" name="city_prov" id="city_prov" class="form-control form-control-border" placeholder="Enter City/Prov" value ="<?php echo isset($city_prov) ? $city_prov : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="zip_code" class="control-label">Zip Code</label>
                    <input type="text" name="zip_code" id="zip_code" class="form-control form-control-border" placeholder="Enter Zipcode" value ="<?php echo isset($zip_code) ? $zip_code : '' ?>"readonly required>
                </div>
            </div>
        </div>

        <div class="row">
			<div class="col-md-4">
                <div class="form-group">
                    <label for="lot_area" class="control-label">Lot Area</label>
                    <input type="number" name="lot_area" id="lot_area" class="form-control form-control-border" placeholder="Enter Lot Area" value ="<?php echo isset($lot_area) ? $lot_area : 0 ?>"readonly required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="type" class="control-label">Type</label>
                    <input type="type" name="type" id="type" class="form-control form-control-border" placeholder="Type" value ="<?php echo isset($type) ? $type : 0 ?>"readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="status" class="control-label">Status</label>
                    <input type="status" name="status" id="status" class="form-control form-control-border" placeholder="Status" value ="<?php echo isset($status) ? $status : 0 ?>"readonly required>
      
                </div>
            </div>
        </div>
        <div class="row">
			<div class="col-md-6">
                <div class="form-group">
                    <label for="stl_due" class="control-label">Streetlight Due Date</label>
                    <input type="date" name="stl_due" id="stl_due" class="form-control form-control-border" value ="<?php echo isset($street_due) ? $street_due : date('Y-m-d'); ?>"readonly required>
                
                </div>
            </div>
           
            <div class="col-md-6">
                <div class="form-group">
                    <label for="stl_balance" class="control-label">StreetLight Balance</label>
                    <input type="number" name="stl_balance" id="stl_balance" class="form-control form-control-border" value ="<?php echo isset($stl_bal) ? $stl_bal : '0.00' ?>"readonly required>
                
                </div>
            </div>
        </div>
        <div class="row">
			<div class="col-md-6">
                <div class="form-group">
                    <label for="main_date" class="control-label">Grasscutting Due Date</label>
                    <input type="date" name="main_date" id="main_date" class="form-control form-control-border" value ="<?php echo isset($mainte_due) ? $mainte_due : date('Y-m-d'); ?>"readonly required>
                
                </div>
            </div>
           
            <div class="col-md-6">
                <div class="form-group">
                    <label for="main_balance" class="control-label">Maintenance Balance</label>
                    <input type="number" name="main_balance" id="main_balance" class="form-control form-control-border" value ="<?php echo isset($mainte_bal) ? $mainte_bal : 0 ?>"readonly required>
                
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
            <div class="form-group">
                    <label for="pay_date" class="control-label">Pay Date</label>
                    <input type="date" name="pay_date" id="pay_date" class="form-control form-control-border" value ="<?php echo date('Y-m-d'); ?>" required>
                
                </div>
            </div>
           
            <div class="col-md-6">
                <div class="form-group">
                <label for="payment_or" class="control-label">Or No.</label>
                <input type="text" name="payment_or" id="payment_or" class="form-control form-control-border required" placeholder= "ex.CAR153245" value ="" >
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                <label for="main_amount_paid" class="control-label">Payment for Streetlight Amount</label>
                <input type="number" name="main_amount_paid" id="main_amount_paid" class="form-control form-control-border" value ="0" required>
                </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                    <label for="main_discount" class="control-label">STL Discount</label>
                    <input type="number" name="main_discount" id="main_discount" class="form-control form-control-border" value ="0" required>
                
                </div>
            </div>
        </div>
        <div class="row">

        

               
            
           
            <div class="col-md-6">
                <div class="form-group">
                <label for="stl_amount_paid" class="control-label">Payment for Grasscutting Amount </label>
                <input type="number" name="stl_amount_paid" id="stl_amount_paid" class="form-control form-control-border" value ="0" required>
                </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                    <label for="stl_discount" class="control-label">GCF Discount</label>
                    <input type="number" name="stl_discount" id="stl_discount" class="form-control form-control-border" value ="0" required>
                </div>
            </div>
        </div>
      
    </form>
</div>
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