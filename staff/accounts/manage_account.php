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
        $email = $res["c_email"];
        $contact_no = $res["c_contact_no"];
        if ($remarks === '') {
            $remarks = "N/A";
        }
        $full_name = $last_name . ', ' .$first_name . ' ' .$middle_name;

        
    }
}


// Check if 'id' is set in the GET parameters
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
        <h3><?php echo isset($c_location) ? $c_location : '' ?></h3>
        <input type="hidden" name="id" value="<?php echo isset($account_no) ? $account_no : '' ?>">
        <div class="row">
			<div class="col-md-6">
                <div class="form-group">
                    <label for="acc_no" class="control-label">Account No</label>
                    <input type="text" name="acc_no" id="acc_no" class="form-control form-control-border" placeholder="ex. 15200202102" value ="<?php echo isset($account_no) ? $account_no : '' ?>"<?php if ($accfield) echo ' readonly'; ?> required>
                </div>
            </div>
            <?php 
            $check = "SELECT max(c_control_no) as c_control_no FROM t_utility_accounts";

		    $result = odbc_exec($conn2, $check);

			if (!$result) {
				die("ODBC query execution failed: " . odbc_errormsg());
			}
			// Fetch and display the results
			while ($row = odbc_fetch_array($result)) {
				$input= $row['c_control_no'];
                $prefix = preg_replace('/[0-9]/', '', $input);
                $numeric = (int)preg_replace('/[A-Za-z]/', '', $input);

                // Increment the numeric part
                $newNumeric = sprintf('%06d', $numeric + 1);

                // Combine the prefix and incremented numeric part
                $ctrl_no = $prefix . $newNumeric;

			}
            ?>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="ctr" class="control-label">Control No</label>
                    <input type="text" name="ctr" id="ctr" class="form-control form-control-border" value ="<?php echo isset($ctr) ? $ctr : $ctrl_no ?>"<?php if ($ctrfield) echo ' readonly'; ?> readonly required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_applied" class="control-label">Date Applied *</label>
                    <input type="date" name="date_applied" id="date_applied" class="form-control form-control-border" value ="<?php echo isset($date_applied) ? $date_applied : date('Y-m-d'); ?>"readonly required>
                
                </div>
            </div>
        </div>
        <div class="row">
			<div class="col-md-4">
                <div class="form-group">
                    <label for="fname" class="control-label">First Name  *</label>
                    <input type="text" name="fname" id="fname" class="form-control form-control-border" placeholder="Enter Last Name" value ="<?php echo isset($last_name) ? $last_name : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lname" class="control-label">Last Name  *</label>
                    <input type="text" name="lname" id="lname" class="form-control form-control-border" placeholder="Enter First Name" value ="<?php echo isset($first_name) ? $first_name : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mname" class="control-label">Middle Name  *</label>
                    <input type="text" name="mname" id="mname" class="form-control form-control-border" placeholder="Enter Middle Name" value ="<?php echo isset($middle_name) ? $middle_name : '' ?>"readonly required>
                </div>
            </div>
        </div>
        <div class="row">
			<div class="col-md-7">
                <div class="form-group">
                    <label for="add" class="control-label">Address  *</label>
                    <input type="text" name="add" id="add" class="form-control form-control-border" placeholder="Enter Address" value ="<?php echo isset($address) ? $address : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="city_prov" class="control-label">City/ Province  *</label>
                    <input type="text" name="city_prov" id="city_prov" class="form-control form-control-border" placeholder="Enter City/Prov" value ="<?php echo isset($city_prov) ? $city_prov : '' ?>"readonly required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="zip_code" class="control-label">Zip Code  *</label>
                    <input type="text" name="zip_code" id="zip_code" class="form-control form-control-border" placeholder="Enter Zipcode" value ="<?php echo isset($zip_code) ? $zip_code : '' ?>"readonly required>
                </div>
            </div>
        </div>

        <div class="row">
			<div class="col-md-4">
                <div class="form-group">
                    <label for="lot_area" class="control-label">Lot Area  *</label>
                    <input type="number" name="lot_area" id="lot_area" class="form-control form-control-border" placeholder="Enter Lot Area" value ="<?php echo isset($lot_area) ? $lot_area : 0 ?>"readonly required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="type" class="control-label">Type  *</label>
                    <select name="type" id="type" class="form-control form-control-border"readonly required>
                        <option value="STL and MTF" <?= isset($type) && $type == 'STL and MTF' ? 'selected' : '' ?>>STL and MTF</option>
                        <option value="STL Only" <?= isset($type) && $type == 'STL Only' ? 'selected' : '' ?>>STL Only</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="status" class="control-label">Status  *</label>
                    <select name="status" id="status" class="form-control form-control-border"readonly required>
                        <option value="Active" <?= isset($status) && $status == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= isset($status) && $status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
			
           
            <div class="col-md-4">
                <div class="form-group">
                <label for="mtf_end" class="control-label">MTF End Date </label>
                <input type="date" name="mtf_end" id="mtf_end" class="form-control form-control-border" value ="<?php echo isset($mtf_end) ? $mtf_end : date('2030-01-01'); ?>"readonly required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <label for="contact_no" class="control-label">Contact No</label>
                <input type="text" name="contact_no" id="contact_no" class="form-control form-control-border" value ="<?php echo isset($contact_no) ? $contact_no : '' ?>" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <label for="email_add" class="control-label">Email Address</label>
                <input type="email" name="email_add" id="email_add" class="form-control form-control-border" value ="<?php echo isset($email) ? $email : '' ?>" readonly>
                </div>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="remarks" class="control-label">Remarks</label>
                    <textarea rows="3" name="remarks" id="remarks" class="form-control form-control-md rounded-0" required><?php echo isset($remarks) ? html_entity_decode($remarks) : '' ?></textarea>
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
        $('#uni_modal #account-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_account",
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