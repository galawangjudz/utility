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



?>
<div class="container-fluid">

    <form action="" id="cancel-form">
        <h3><?php echo isset($account_no) ? "Account No: " . $account_no : '' ?></h3>
        <h3><?php echo isset($l_car_no) ? "Car No: " . $l_car_no : '' ?></h3>
        <h3><?php echo isset($pay_date) ? "Payment Date: " . $pay_date : '' ?></h3>
        <h3><?php echo isset($amount) ? "Amount: " . $amount : '' ?></h3>

        <input type="hidden" name="id" value="<?php echo isset($account_no) ? $account_no : '' ?>">
        <input type="hidden" name="car_no" value="<?php echo isset($l_car_no) ? $l_car_no : '' ?>">

     
            <div class="col-md-12">
                <div class="form-group">
                    <label for="acc_no" class="control-label">Reason Of Cancellation</label>
                    <textarea name="cancel_reason" id="cancel_reason" class="form-control form-control-border" required></textarea>
                </div>
            </div>
        
       
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal #cancel-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=delete_payment",
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