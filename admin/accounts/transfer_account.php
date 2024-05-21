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
        $lot_area = $res["c_lot_area"];
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['l_site'])) {
    // Handle AJAX request
    $l_site = $_POST['l_site'];

    // Sanitize the input to prevent SQL injection
    $l_site = preg_replace('/[^0-9]/', '', $l_site);

    $sql = "SELECT c_acronym FROM t_projects WHERE c_code = '$l_site'";
    $results = odbc_exec($conn2, $sql);

    if ($row = odbc_fetch_array($results)) {
        echo json_encode($row['c_acronym']);
    } else {
        echo json_encode("No acronym found");
    }
    odbc_close($conn2);
    exit; // End the script execution after handling AJAX request
}
?>

<div class="container-fluid">
<form action="" id="transfer-form">
        <h3><?php echo isset($account_no) ? $account_no : '' ?></h3>
        <h3><?php echo isset($last_name) ? $last_name : '' ?></h3>
        <h3><?php echo isset($first_name) ? $first_name : '' ?></h3>
        <h3><?php echo isset($lot_area) ? $lot_area : '' ?></h3>
        <input type="hidden" name="old_acc_no"  value="<?php echo isset($account_no) ? $account_no : '' ?>"> 
        <div class="row">
            <div class="col-md-6">
                <label for="new_acc_no" class="control-label">TRANSFER TO: (New Account Number)</label>
                <input type="text" name="new_acc_no" id="new_acc_no" class="form-control form-control-border" value ="" required>
                <div class="output">
                    <!-- <p>Acronym: <span id="acronym"></span></p> -->
                    <p>Site: <span id="site"></span></p>
                    <p>Block: <span id="blk"></span></p>
                    <p>Lot: <span id="lot_no"></span></p>
                    <p>No: <span id="no"></span></p>
                </div>
                
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="new_lot_area" class="control-label">New Lot Area</label>
                    <input type="text" name="new_lot_area" id="new_lot_area" class="form-control form-control-border" placeholder="Enter Amount"required>
                    <!-- <small class="text-danger">Amount must be higher than 0.</small> -->
                </div>
            </div>
           
        </div>
        <div class="row">
            
        </div>
      
    </form>
</div>

<script>
 
 document.getElementById('new_acc_no').addEventListener('input', function() {
    var account_no = this.value;
    if (account_no.length >= 3) {
        var l_site = account_no.substr(0, 3);
        document.getElementById('site').textContent = l_site;

        if (account_no.length >= 6) {
            var l_block = account_no.substr(3, 3);
            document.getElementById('blk').textContent = parseInt(l_block);
        }

        if (account_no.length >= 8) {
            var l_lot = account_no.substr(6, 2);
            document.getElementById('lot_no').textContent = parseInt(l_lot);
        }

        if (account_no.length >= 11) {
            var l_no = account_no.substr(8, 3);
            document.getElementById('no').textContent = l_no;
        }

        // Make an AJAX request to fetch the acronym
        //var xhr = new XMLHttpRequest();
        //xhr.open('POST', '', true); // Send request to the same page
        //xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //xhr.onreadystatechange = function() {
         //   if (xhr.readyState === 4 && xhr.status === 200) {
          //      var acronym = xhr.responseText;
           //     document.getElementById('acronym').textContent = acronym;
         //   }
        //};
        //xhr.send('l_site=' + l_site);
    } else {
        // Clear fields if account_no is too short
        document.getElementById('site').textContent = '';
        document.getElementById('blk').textContent = '';
        document.getElementById('lot_no').textContent = '';
        document.getElementById('no').textContent = '';
        //document.getElementById('acronym').textContent = '';
    }
});
    
    $(function(){
        $('#uni_modal #transfer-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=transfer_account",
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