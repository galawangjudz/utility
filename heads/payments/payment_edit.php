<?php
require_once('../../includes/config.php');


function format_num($number){
    $decimals = 2; // Set the number of decimal places
    return number_format($number, $decimals);
}
if(isset($_GET['id'])){
    $or_no = $_GET['data-car'];
    $query = "SELECT * from t_utility_payments where c_st_or_no = '".$or_no."' and c_account_no = ".$_GET['id'];
    $result = odbc_exec($conn2, $query);
    if (!$result) {
        die("ODBC query execution failed: " . odbc_errormsg());
    }
        while ($row = odbc_fetch_array($result)):
                $account_no = $row['c_account_no'];
                $pay_date = $row['c_st_pay_date'];
                $l_car_no = $row['c_st_or_no'];
                $l_mop = $row['c_mop'];
                $l_ref = $row['c_ref_no'];
                $l_branch = $row['c_branch'];
                $l_check_date = $row['c_check_date'];
                $amount = format_num($row['c_st_amount_paid']);
                $discount = format_num($row['c_discount']);

        endwhile;
    } else {
        // Error executing the query
        echo "Error: " . odbc_errormsg($conn2);
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
        <input type="hidden" name="car_no" value="<?php echo isset($l_car_no) ? $l_car_no : '' ?>">
        <div class="row">
			<div class="col-md-4">
                <div class="form-group">
                    <label for="acc_no" class="control-label">Account No</label>
                    <input type="text" name="acc_no" id="acc_no" class="form-control form-control-border" value ="<?php echo isset($account_no) ? $account_no : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">

                    <label for="mop" class="control-label">Mode of Payment</label>
                    <select name="mop" id="mop" class="form-control form-control-border" required>
                        <option value="1" <?= isset($l_mop) && $l_mop == '1' ? 'selected' : '' ?>>Cash</option>
                        <option value="2" <?= isset($l_mop) && $l_mop == '2' ? 'selected' : '' ?>>Check</option>
                        <option value="3" <?= isset($l_mop) && $l_mop == '3' ? 'selected' : '' ?>>Gcash/Online</option>
                        <option value="4" <?= isset($l_mop) && $l_mop == '4' ? 'selected' : '' ?>>Check Voucher</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <label for="payment_or" class="control-label">CAR No.</label>
                <input type="text" name="payment_or" id="payment_or" class="form-control form-control-border" value ="<?php echo isset($l_car_no) ? $l_car_no : '' ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="pay_date" class="control-label">Pay Date</label>
                    <input type="date" name="pay_date" id="pay_date" class="form-control form-control-border" value ="<?php echo isset($pay_date) ? $pay_date : '' ?>" required>
                
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="pay_amount_paid" class="control-label">Amount </label>
                    <input type="number" name="pay_amount_paid" id="pay_amount_paid" class="form-control form-control-border" value ="<?php echo isset($amount) ? (float)str_replace(',', '', $amount) : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="pay_discount" class="control-label">Discount</label>
                    <input type="number" name="pay_discount" id="pay_discount" class="form-control form-control-border" value ="<?php echo isset($discount) ? (float)str_replace(',', '', $discount) : '' ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="check_date" class="control-label">Check Date</label>
                    <input type="date" name="check_date" id="check_date" class="form-control form-control-border" value ="<?php echo isset($l_check_date) ? $l_check_date : '' ?>" required>
                
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="branch" class="control-label">Branch</label>
                    <select name="branch" id="branch" class="form-control form-control-border" required>
                        <option value="" <?= isset($l_branch) && $l_branch == '' ? 'selected' : '' ?>>--SELECT BANK--</option>
                        <option value="BDO" <?= isset($l_branch) && $l_branch == 'BDO' ? 'selected' : '' ?>>BDO</option>
                        <option value="BPI" <?= isset($l_branch) && $l_branch == 'BPI' ? 'selected' : '' ?>>BPI</option>
                        <option value="BOC" <?= isset($l_branch) && $l_branch == 'BOC' ? 'selected' : '' ?>>BOC</option>
                        <option value="CBS" <?= isset($l_branch) && $l_branch == 'CBS' ? 'selected' : '' ?>>CBS</option>
                        <option value="EWB" <?= isset($l_branch) && $l_branch == 'EWB' ? 'selected' : '' ?>>EWB</option>
                        <option value="GCASH" <?= isset($l_branch) && $l_branch == 'GCASH' ? 'selected' : '' ?>>GCASH</option>
                        <option value="LB"  <?= isset($l_branch) && $l_branch == 'LB' ? 'selected' : '' ?>>LB</option>
                        <option value="MBTC" <?= isset($l_branch) && $l_branch == 'MBTC' ? 'selected' : '' ?>>MBTC</option>
                        <option value="PBB" <?= isset($l_branch) && $l_branch == 'PBB' ? 'selected' : '' ?>>PBB</option>
                        <option value="PNB" <?= isset($l_branch) && $l_branch == 'PNB' ? 'selected' : '' ?>>PNB</option>
                        <option value="PSB" <?= isset($l_branch) && $l_branch == 'PSB' ? 'selected' : '' ?>>PSB</option>
                        <option value="PVB" <?= isset($l_branch) && $l_branch == 'PVB' ? 'selected' : '' ?>>PVB</option>
                        <option value="RCBC" <?= isset($l_branch) && $l_branch == 'RCBC' ? 'selected' : '' ?>>RCBC</option>
                        <option value="ROBBank" <?= isset($l_branch) && $l_branch == 'ROBBank' ? 'selected' : '' ?>>ROBBank</option>
                        <option value="SBC" <?= isset($l_branch) && $l_branch == 'SBC' ? 'selected' : '' ?>>SBC</option>
                        <option value="UB" <?= isset($l_branch) && $l_branch == 'UB' ? 'selected' : '' ?>>UB</option>
                        <option value="UCPB" <?= isset($l_branch) && $l_branch == 'UCPB' ? 'selected' : '' ?>>UCPB</option>

                     
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ref_no" class="control-label">Reference/Check No</label>
                    <input type="text" name="ref_no" id="ref_no" class="form-control form-control-border" value ="<?php echo isset($l_ref) ? $l_ref : '' ?>" required>
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
                url:_base_url_+"classes/Master.php?f=update_payment",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert("An error occured",'error');
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