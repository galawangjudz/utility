
<?php

function format_num($number){
       return number_format($number,2);
}


function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}
?>
<?php


$phase = isset($_GET['phase']) ? $_GET['phase'] : '100';
$directory = "C:\Users\Asian Land\Desktop\UTL_AUTO_BILLING";

if ($phase == '100') {
    $keyword = '';
} else {
    $keyword = $phase;
}

$pdfFiles = glob($directory . '/*.pdf');

if (!empty($keyword)) {
    $filteredPdfFiles = array_filter($pdfFiles, function($pdfFile) use ($keyword) {
        return stripos(basename($pdfFile), $keyword) !== false;
    });
} else {
    $filteredPdfFiles = $pdfFiles;
}
?>

<div class="main-container">
    <div class="card-box mb-30">
        <div class="pd-20">
            <h4 class="text-muted">Filter Date</h4>
            <form action="" id="filter">
                <div class="row align-items-end">
                    <div class="col-md-2 form-group">
                        <label for="phase" class="control-label">Phase</label>
                        <select name="phase" id="phase" class="custom-select form-control" autocomplete="off">
                            <?php
                            $sql = "SELECT * FROM t_projects ORDER BY c_acronym";
                            $results = odbc_exec($conn2, $sql);
                            $selectedValue = isset($_GET['phase']) ? $_GET['phase'] : ''; // Get the selected value from the submitted form
                            echo '<option value="" selected>--SELECT--</option>';
                            while ($row = odbc_fetch_array($results)) {
                                $optionValue = $row['c_code'];
                                $optionText = $row['c_acronym'];
                                $selected = ($selectedValue == $optionValue) ? 'selected' : ''; // Check if this option is selected
                                echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionText . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <button class="btn btn-default border btn-flat btn-sm"><i class="dw dw-filter"></i> Filter</button>
                        <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="dw dw-print"></i> Print Master List</button>
                        <?php
                       
                        // Output links/buttons for filtered PDF files
                        foreach ($filteredPdfFiles as $pdfFile) {
                            // Get just the filename without the directory path
                        
                            $pdfFileName = basename($pdfFile);
                            ?>
                            <button class="btn btn-default border btn-flat btn-sm" type="button">
                                <a href="<?php echo base_url ?>soa_6mos/<?php echo $pdfFileName ?>" target="_blank">
                                    <i class="dw dw-print"></i> <?php echo $pdfFileName ?>
                                </a>
                            </button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


        <div class="card-box mb-50">
            <div class="pd-20">
                <div class="container-fluid" id="outprint">
                <style>
                    th.p-0, td.p-0{
                        padding: 0 !important;
                    }
                </style>
                    <h3 class="text-center"><b>ASIAN LAND STRATEGIES CORPORATION</b></h3>
                    <h5 class="text-center"><b>MASTERLIST OF BILLING REPORT</b></h5>
                    <hr>
                  

                
                    <table id="car_table" class="table table-hover table-bordered">
                    <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">CUSTOMER NAME</th>
                                <th class="text-center">LOCATION</th>
                                <th class="text-center">PERIOD COVERED</th>
                                <th class="text-center">SIGNATURE</th>
                            </tr>
                        </thead>
                        <?php
                            $get_cut_off = "SELECT c_cutoff_date FROM t_bill_period_date";
                            $result555 = odbc_exec($conn2, $get_cut_off);
                           
                            while ($rrow = odbc_fetch_array($result555)):
                                $cutoff = strval($rrow['c_cutoff_date']);
                                $cutoff = '2023-12-31';
                            endwhile;
                            $i = 1;
                            $combine = "
                                SELECT 
                                DISTINCT ua.c_account_no, 
                                ua.c_block, 
                                ua.c_lot, 
                                ua.c_no,
                                ua.c_lot_area, 
                                ua.c_location, 
                                ua.c_last_name, 
                                ua.c_first_name, 
                                ua.c_address, 
                                ua.c_city_prov, 
                                ua.c_zipcode,
                                x.c_sd,
                                x.c_ed,
                                x.c_dd,
                                my_table_v2.c_mtf_bill, 
                                my_table_v2.c_mtf_amount_paid, 
                                my_table_v2.c_mtf_discount,
                                my_table_v2.c_mtf_bal,
                                COALESCE(current_charges.c_current_mtf, 0) as c_current_mtf,
                                COALESCE(current_charges.c_current_mtf_sur, 0) as c_current_mtf_sur,
                                my_table_v2.c_stl_bill, 
                                my_table_v2.c_stl_amount_paid, 
                                my_table_v2.c_stl_discount,
                                my_table_v2.c_stl_bal,
                                COALESCE(current_charges.c_current_stl, 0) as c_current_stl,
                                COALESCE(current_charges.c_current_stl_sur, 0) as c_current_stl_sur,
                                COALESCE(last_payment_mtf.c_last_payment_amount, 0) as c_last_payment_amount_mtf,
                                last_payment_mtf.c_last_payment_date as c_last_payment_date_mtf,
                                COALESCE(last_payment_stl.c_last_payment_amount, 0) as c_last_payment_amount_stl,
                                last_payment_stl.c_last_payment_date as c_last_payment_date_stl,
                                COALESCE(last_payment_mtf.c_last_adjustment_amount, 0) as c_last_adjustment_amount_mtf,
                                COALESCE(last_payment_stl.c_last_adjustment_amount, 0) as c_last_adjustment_amount_stl,
                                ua.c_email,
                                ua.c_contact_no,
                                ua.billing_method
                            FROM
                                (
                                    SELECT 
                                        c_account_no, 
                                        SUM(CASE WHEN (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') THEN c_amount_due ELSE 0 END) as c_mtf_bill,
                                        SUM(CASE WHEN c_bill_type = 'MTF' THEN c_st_amount_paid ELSE 0 END) as c_mtf_amount_paid,
                                        SUM(CASE WHEN c_bill_type = 'MTF' THEN c_discount ELSE 0 END) as c_mtf_discount, 
                                        SUM(CASE WHEN (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') THEN c_amount_due - c_st_amount_paid - c_discount ELSE 0 END) as c_mtf_bal,
                                        SUM(CASE WHEN (c_bill_type = 'STL' or c_bill_type = 'DLQ_MTF') THEN c_amount_due ELSE 0 END) as c_stl_bill, 
                                        SUM(CASE WHEN c_bill_type = 'STL' THEN c_st_amount_paid ELSE 0 END) as c_stl_amount_paid, 
                                        SUM(CASE WHEN c_bill_type = 'STL' THEN c_discount ELSE 0 END) as c_stl_discount, 
                                        SUM(CASE WHEN (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL') THEN c_amount_due - c_st_amount_paid - c_discount ELSE 0 END) as c_stl_bal
                                    FROM 
                                        (
                                            SELECT 
                                                c_account_no, 
                                                c_amount_due, 
                                                c_due_date,
                                                NULL as c_st_pay_date,
                                                0 as c_st_amount_paid, 
                                                0 as c_discount, 
                                                c_bill_type
                                            FROM 
                                                t_utility_bill as hed 
                                            WHERE 
                                                c_bill_type IN ('MTF', 'STL','DLQ_MTF', 'DLQ_STL') 
                                                AND c_end_date < '$cutoff' 
                                            UNION ALL 
                                            SELECT 
                                                c_account_no, 
                                                0 as c_amount_due,
                                                NULL as c_due_date,
                                                c_st_pay_date, 
                                                c_st_amount_paid, 
                                                c_discount, 
                                                CASE WHEN UPPER(c_st_or_no) ilike '%%MTF%%' THEN 'MTF' ELSE 'STL' END as c_bill_type
                                            FROM 
                                                t_utility_payments 
                                            WHERE 
                                                c_st_pay_date <= DATE '$cutoff' + INTERVAL '17 DAY'  
                                                AND (UPPER(c_st_or_no) ilike '%%MTF%%' OR UPPER(c_st_or_no) ilike '%%STL%%') 
                                        ) as my_table 
                                    GROUP BY 
                                        c_account_no
                                ) as my_table_v2 
                                FULL OUTER JOIN t_utility_accounts as ua ON my_table_v2.c_account_no = ua.c_account_no 
                                FULL OUTER JOIN t_utility_bill as ub ON ub.c_account_no = ua.c_account_no 
                                FULL OUTER JOIN 
                                (
                                    SELECT 
                                        DISTINCT c_account_no, 
                                        MIN(CASE WHEN c_start_date > '$cutoff' THEN c_start_date ELSE NULL END) as c_sd, 
                                        MAX(CASE WHEN c_end_date > '$cutoff' THEN c_end_date ELSE NULL END) as c_ed, 
                                        MAX(CASE WHEN c_end_date > '$cutoff' THEN c_due_date ELSE NULL END) as c_dd 
                                    FROM 
                                        t_utility_bill 
                                    WHERE 
                                        c_bill_type IN ('MTF', 'STL','DLQ_MTF', 'DLQ_STL') 
                                    GROUP BY 
                                        c_account_no
                                ) as x  ON x.c_account_no = my_table_v2.c_account_no 
                            LEFT JOIN 
                                (
                                    SELECT 
                                        c_account_no,
                                        SUM(CASE WHEN c_bill_type = 'MTF' THEN c_amount_due ELSE 0 END) as c_current_mtf,
                                        SUM(CASE WHEN c_bill_type = 'DLQ_MTF' THEN c_amount_due ELSE 0 END) as c_current_mtf_sur,
                                        SUM(CASE WHEN c_bill_type = 'DLQ_STL' THEN c_amount_due ELSE 0 END) as c_current_stl_sur,
                                        SUM(CASE WHEN c_bill_type = 'STL' THEN c_amount_due ELSE 0 END) as c_current_stl
                                    FROM 
                                        t_utility_bill
                                    WHERE 
                                        c_bill_type IN ('MTF', 'STL','DLQ_MTF', 'DLQ_STL')  
                                        AND  c_start_date > '$cutoff'
                                    GROUP BY 
                                        c_account_no
                                ) as current_charges ON current_charges.c_account_no = my_table_v2.c_account_no
                            LEFT JOIN 
                                (
                                    SELECT 
                                        c_account_no,
                                        MAX(CASE WHEN UPPER(c_st_or_no) ilike '%%MTF-CAR%%' THEN c_st_pay_date ELSE NULL END) as c_last_payment_date,
                                        SUM(CASE WHEN UPPER(c_st_or_no) ilike '%%MTF-CAR%%' THEN c_st_amount_paid + c_discount ELSE 0 END) as c_last_payment_amount,
                                        SUM(CASE WHEN UPPER(c_st_or_no) ilike '%%MTF-ADJ%%' or UPPER(c_st_or_no) ilike '%%MTF-BA%%' THEN c_st_amount_paid + c_discount ELSE 0 END) as c_last_adjustment_amount
                                    FROM 
                                        t_utility_payments 
                                    WHERE 
                                        c_st_pay_date <= DATE '$cutoff'
                                    AND c_st_pay_date >= DATE '$cutoff' - INTERVAL '1 MONTH'
                                        AND UPPER(c_st_or_no) ilike '%%MTF%%'
                                    GROUP BY 
                                        c_account_no
                                ) as last_payment_mtf ON last_payment_mtf.c_account_no = my_table_v2.c_account_no
                            LEFT JOIN 
                                (
                                    SELECT 
                                        c_account_no,
                                        MAX(CASE WHEN UPPER(c_st_or_no) ilike '%%STL-CAR%%' THEN c_st_pay_date ELSE NULL END) as c_last_payment_date,
                                        SUM(CASE WHEN UPPER(c_st_or_no) ilike '%%STL-CAR%%' THEN c_st_amount_paid + c_discount ELSE 0 END) as c_last_payment_amount,
                                        SUM(CASE WHEN UPPER(c_st_or_no) ilike '%%STL-ADJ%%' or UPPER(c_st_or_no) ilike '%%STL-BA%%' THEN c_st_amount_paid + c_discount ELSE 0 END) as c_last_adjustment_amount
                                    FROM 
                                        t_utility_payments 
                                    WHERE 
                                        c_st_pay_date <= '$cutoff' 
                                    AND c_st_pay_date >=  DATE '$cutoff' - INTERVAL '1 MONTH'
                                        AND UPPER(c_st_or_no) ilike '%%STL%%'
                                    GROUP BY 
                                        c_account_no
                                ) as last_payment_stl ON last_payment_stl.c_account_no = my_table_v2.c_account_no
                            WHERE ua.c_status = 'Active'and ua.c_with_mtf is NULL and (ua.billing_method != 1 and ua.billing_method != 3) and ua.c_site= '$phase' and x.c_ed IS NOT NULL";
                                
                           
                           $result3 = odbc_exec($conn2, $combine);
                           # print ($combine);
                       if (!$result3) {
                        die("ODBC query execution failed: " . odbc_errormsg());
                       }
                       while ($mrow = odbc_fetch_array($result3)):
                        ?>
                        <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td class="text-center"><?php echo $mrow['c_last_name'] .', '. $mrow['c_first_name'] ?></td>
                        <td class="text-center"><?php echo $mrow['c_location']?></td>
                        <td class="text-center"><?php echo $mrow['c_sd'] . ' - '. $mrow['c_ed']  ?></td>
                        <td class="text-right"></td>

                        </tr>
                        <?php 
                       endwhile;

                        ?>
                    </table>
                
                
       
</div>
</div>
                
</div>




<style>
.modal-dialog.large {
    width: 80% !important;
    max-width: unset;
}

.modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
}
</style>

<script>
   

	$(document).ready(function(){
        $('#filter').submit(function(e){
            e.preventDefault()
            location.href="<?php echo base_url ?>admin/?page=report/masterlist&"+$(this).serialize();
        })
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone();
            var _p = $('#outprint').clone();
            var el = $('<div>')
            _h.find('title').text('Masterlist of Billing Statement - Print View')
            _h.append('<style>html,body{ min-height: unset !important;}</style>')
            _h.append('<style>@media print { @page { size: landscape; }}</style>');
            
            el.append(_h)
            el.append(_p)
             var nw = window.open("","_blank","width=900,height=700,top=50,left=250")
             nw.document.write(el.html())
             nw.document.close()
             setTimeout(() => {
                 nw.print()
                 setTimeout(() => {
                     nw.close()
                     end_loader()
                 }, 200);
             }, 500);
        })
		
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
	})
</script>


