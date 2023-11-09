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
?>
<div class="container-fluid">
    <form action="" id="bill-form">
        <h3><?php echo isset($account_no) ? $account_no : '' ?></h3>
        <h3><?php echo isset($last_name) ? $last_name : '' ?></h3>
        <h3><?php echo isset($first_name) ? $first_name : '' ?></h3>
        <input type="hidden" name="acc_no" value="<?php echo isset($account_no) ? $account_no : '' ?>"> 
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="adj_date" class="control-label">ADJUSTMENT DATE</label>
                    <input type="date" name="adj_date" id="adj_date" class="form-control form-control-border" value ="<?php echo date('Y-m-d'); ?>" required>
                
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="type" class="control-label">ADJUSTMENT TYPE</label>
                    <select name="type" id="type" class="form-control form-control-border" required>
                        <option value="BA">BILL ADJUSTMENT</option>
                        <option value="ADJ">PAYMENT ADJUSTMENT</option>
                        <option value="RF">REFUND</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="adjust_for" class="control-label">ADJUSTMENT FOR</label>
                    <select name="adjust_for" id="adjust_for" class="form-control form-control-border" required>
                        <option value="MTF">GRASSCUTTING</option>
                        <option value="STL">STREETLIGHT</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
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
                url:_base_url_+"classes/Master.php?f=adjust_bill",
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