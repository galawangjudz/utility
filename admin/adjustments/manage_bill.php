<?php
require_once('../../includes/config.php');

if(isset($_GET['id'])){

    $sql = "SELECT * FROM t_utility_bill WHERE c_account_no = ?";
    $acc = $_GET['id'];

    $qry = odbc_prepare($conn2, $sql);
    if (!odbc_execute($qry, array($acc))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }
    while ($res = odbc_fetch_array($qry)) {
        $account_no = $res["c_account_no"];
        $start = $res["c_start_date"];
        $end = $res["c_end_date"];
        $due = $res["c_due_date"];
        $type = $res["c_bill_type"];
        $amount_due = $res["c_amount_due"];
        $prev_bal = $res["c_prev_balance"];
        $prev_bal =  $prev_bal + $amount_due;
    }
}

?>
<div class="container-fluid">
    <form action="" id="bill-form">
        <h3><?php echo isset($account_no) ? $account_no : '' ?></h3>
        <input type="hidden" name="acc_no" value="<?php echo isset($account_no) ? $account_no : '' ?>">
        <input type="number" name="prev_bal" id="prev_bal" value ="<?php echo isset($prev_bal) ? $prev_bal : '' ?>">      
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="start_date" class="control-label">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-border" value ="<?php echo isset($start) ? $start : date('Y-m-d'); ?>" required>
                
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="end_date" class="control-label">End Date *</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-border" value ="<?php echo isset($end) ? $end : date('Y-m-d'); ?>" required>
                
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="due_date" class="control-label">Due Date *</label>
                    <input type="date" name="due_date" id="due_date" class="form-control form-control-border" value ="<?php echo isset($due) ? $due : date('Y-m-d'); ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="type" class="control-label">Bill Type  *</label>
                    <select name="type" id="type" class="form-control form-control-border" required>
                        <option value="MTF" <?= isset($type) && $type == 'MTF' ? 'selected' : '' ?>>GCF</option>
                        <option value="DLQ_MTF" <?= isset($type) && $type == 'DLQ_MTF' ? 'selected' : '' ?>>GCF Surcharge</option>
                        <option value="STL" <?= isset($type) && $type == 'STL' ? 'selected' : '' ?>>STL</option>
                        <option value="DLQ_STL" <?= isset($type) && $type == 'DLQ_STL' ? 'selected' : '' ?>>STL Surcharge</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount" class="control-label">Amount Due  *</label>
                    <input type="number" name="amount" id="amount" class="form-control form-control-border" placeholder="Enter Amount" value ="<?php echo isset($first_name) ? $first_name : '' ?>" required>
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
                url:_base_url_+"classes/Master.php?f=save_bill",
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