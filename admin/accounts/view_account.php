<?php 
require_once('../../includes/config.php');

if(isset($_GET['id'])){


    $sql = "SELECT * FROM t_utility_accounts WHERE c_account_no = ?";
    $acc = $_GET['id'];

    $qry = odbc_prepare($conn2, $sql);

    if (!$qry) {
        die("Preparation of the statement failed: " . odbc_errormsg($conn2));
    }

    if (!odbc_execute($qry, array($acc))) {
        die("Execution of the statement failed: " . odbc_errormsg($conn2));
    }

    while ($res = odbc_fetch_array($qry)) {
    
        $account_no = $res["c_account_no"];
        $ctr = $res["c_control_no"];
        $first_name = $res["c_first_name"];
        $last_name = $res["c_last_name"];
        $middle_name = $res["c_middle_name"];
        $loc = $res["c_location"];
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
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
    <dl>
        <dt class="text-muted">Account No</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($account_no) ? $account_no: '' ?></dd>
        <dt class="text-muted">Full Name</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($last_name) ? $full_name : '' ?></dd>
        <dt class="text-muted">Location</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($loc) ? $loc: '' ?></dd>
        <dt class="text-muted">Address</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($add) ? $add: '' ?></dd>
        <dt class="text-muted">Date Applied</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($date_applied) ? $date_applied: '' ?></dd>
        <dt class="text-muted">Lot Area</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($lot_area) ? $lot_area: '' ?></dd>
        <dt class="text-muted">MTF End Date</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($mtf_end) ? $mtf_end: '' ?></dd>
        <dt class="text-muted">Type</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($type) ? $type: '' ?></dd>
       
        <dt class="text-muted">Status</dt>
        <dd class='pl-4 fs-4 fw-bold'>
            <?php 
            $status = isset($status) ? $status : '';
                switch($status){
                    case 'Inactive':
                        echo '<span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Inactive</span>';
                        break;
                    case 'Active':
                        echo '<span class="badge badge-primary bg-gradient-primary px-3 rounded-pill">Active</span>';
                        break;
                    default:
                        echo '<span class="badge badge-default border px-3 rounded-pill">N/A</span>';
                            break;
                }
            ?>
        </dd>
        <dt class="text-muted">Remarks</dt>
        <dd class='pl-4 fs-4 fw-bold'><?= isset($remarks) ? $remarks: 'N/A' ?></dd>
    </dl>
    <div class="col-12 text-right">
        <button class="btn btn-flat btn-sm btn-dark" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
    </div>
</div>