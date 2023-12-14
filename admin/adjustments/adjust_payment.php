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

}

$get_payment_records = "SELECT
                            c_account_no,
                            RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                            c_st_pay_date,
                            CASE 
                            /*     WHEN UPPER(c_st_or_no) LIKE 'MTF-CAR%' AND UPPER(c_st_or_no) NOT LIKE 'MTF-ADJ%' THEN 'GCF Payment'
                                WHEN UPPER(c_st_or_no) LIKE 'STL-CAR%' AND UPPER(c_st_or_no) NOT LIKE 'STL-ADJ%' THEN 'STL Payment'
                                WHEN UPPER(c_st_or_no) LIKE 'STL-ADJ%' THEN 'STL Payment Adj.'
                                WHEN UPPER(c_st_or_no) LIKE 'MTF-ADJ%' THEN 'GCF Payment Adj.'
                                WHEN UPPER(c_st_or_no) LIKE 'MTF-BA%' THEN 'GCF Bill Adjustment'
                                WHEN UPPER(c_st_or_no) LIKE 'STL-BA%' THEN 'STL Bill Adjustment' */
                                WHEN payment_type = 'GCF-PAY' THEN 'GCF Payment'
                                WHEN payment_type = 'STL-PAY' THEN 'STL Payment'
                                WHEN payment_type = 'GCF-BA' THEN 'GCF Bill Adj.'
                                WHEN payment_type = 'STL-BA' THEN 'STL Bill Adj.'
                                WHEN payment_type = 'STL-ADJ' THEN 'STL Payment Adj.'
                                WHEN payment_type = 'GCF-ADJ' THEN 'GCF Payment Adj.'
                                WHEN payment_type = 'GCF-SA' THEN 'GCF Surcharge Adj.'
                                WHEN payment_type = 'STL-SA' THEN 'STL Surcharge Adj.'
                                WHEN payment_type = 'GCF-RF' THEN 'GCF Refund'
                                WHEN payment_type = 'STL-RF' THEN 'STL Refund'
                                ELSE ''
                            END AS c_pay_type,
                            c_st_amount_paid + c_discount as c_tot_amt_paid
                        FROM
                            t_utility_payments WHERE c_account_no = '$l_acc_no' ORDER BY c_st_pay_date DESC
            ";
    $result = odbc_exec($conn2, $get_payment_records);

    while ($payment = odbc_fetch_array($result)) {
        $l_pdate = date("m/d/Y", strtotime($payment['c_st_pay_date']));
        $l_or_no = $payment['st_or_no_clear'];
        $l_pay_type = $payment['c_pay_type'];
        $l_amount = $payment['c_tot_amt_paid'];

        $l_data2 = array($l_pdate, $l_or_no, $l_pay_type, $l_amount);
        
        $l_due_list[] = $l_data2;
        }


?>
<div class="container-fluid">
    <form action="" id="bill-form">
        <h3>Account No : <?php echo isset($account_no) ? $account_no : '' ?></h3>
        <h3>Full Name : <?php echo isset($last_name) ? $last_name . ', ' . $first_name : '' ?></h3>
        <hr>
        <input type="hidden" name="acc_no" value="<?php echo isset($account_no) ? $account_no : '' ?>"> 
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="adj_date" class="control-label">TRANSFER DATE</label>
                    <input type="date" name="adj_date" id="adj_date" class="form-control form-control-border" value ="<?php echo date('Y-m-d'); ?>" required>
                
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="adj_date" class="control-label">Transfer to Account No: </label>
                    <input type="text" name="new_acc" id="new_acc" class="form-control form-control-border" value ="<?php echo isset($account_no) ? $account_no : ''  ?>" maxlength = '11' required>
                
                </div>
            </div>
        </div>
        
        <div class="row">
           
            <div class="col-md-4">
                <div class="form-group">
                    <label for="adjust_from" class="control-label">TRANSFER FROM</label>
                    <select name="adjust_from" id="adjust_from" class="form-control form-control-border" required>
                        <option value="MTF">Grass-Cutting</option>
                        <option value="STL">STREETLIGHT</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="adjust_to" class="control-label">TRANSFER TO</label>
                    <select name="adjust_to" id="adjust_to" class="form-control form-control-border" required>
                        <option value="MTF">Grass-Cutting</option>
                        <option value="STL">STREETLIGHT</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="amount" class="control-label">Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control form-control-border" placeholder="Enter Amount" value ="" required>
                    <!-- <small class="text-danger">Amount must be higher than 0.</small> -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="notes" class="control-label">Notes</label>
                    <textarea rows="3" name="notes" id="notes" class="form-control form-control-md rounded-0" required></textarea>
                    <small class="text-muted">Please provide notes.</small>
                </div>
            </div>
           
        </div>
          
        </div>
      
      
    </form>
</div>
<style>
textarea.form-control {
    overflow-y: hidden;
}
</style>
<script>
    
    $(function(){
        $('#uni_modal #bill-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=adjust_payment",
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