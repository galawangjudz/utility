
<?php

function format_num($number){
       return number_format($number,2);
}

$phase = isset($_GET['phase']) ? $_GET['phase'] : '100';

?>


<div class="main-container">

<div class="card-box mb-30">
            <div class="pd-20">
            <h4 class="text-muted">Filter Date</h4>
            <form action="" id="filter">
            <div class="row align-items-end">

                <div class="col-md-2 form-group">
                    <label for="category" class="control-label">Phase</label>
                    <label for="phase" class="control-label">Phase</label>
                    <select name="phase" id="phase" class="custom-select form-control" autocomplete="off">
                    <?php
                    
                        $sql = "SELECT * FROM t_projects ORDER BY c_acronym";
                        $results = odbc_exec($conn2, $sql);


                        $selectedValue = isset($_GET['phase']) ? $_GET['phase'] : ''; // Get the selected value from the submitted form

                        
                    
                        echo '<option value="" selected>--SELECT--</option>';
                        /* echo '<option value="100">ALL</option>'; */
                        while ($row = odbc_fetch_array($results)) {
                            $optionValue = $row['c_code'];
                            $optionText = $row['c_acronym'];
                            $selected = ($selectedValue == $optionValue) ? 'selected' : ''; // Check if this option is selected
                            echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionText . '</option>';
                        }
                        echo '</select>';
                            
                        
                    ?>
                </div>
                <div class="col-md-4 form-group">
                    <button class="btn btn-default border btn-flat btn-sm"><i class="dw dw-filter"></i> Filter</button>
                </div>
            </div>
            </form>
        </div>



        <div class="card-box mb-30">
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
                  

                <div style="height: 500px; overflow-y: auto;">
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
                           /*  $combine =   "SELECT distinct ua.c_account_no, my_table_v2.c_bill, my_table_v2.c_amount_paid, my_table_v2.c_discount,
                                                my_table_v2.c_bal,ua.c_block, ua.c_lot, ua.c_no, ua.c_location,
                                                 ua.c_last_name, ua.c_first_name, x.c_sd,x.c_ed,x.c_dd 
                                            FROM (SELECT c_account_no, sum(c_tot_amount) as c_bill, sum(c_ap) as c_amount_paid, 
                                                sum(c_d) as c_discount, (sum(c_tot_amount) - (sum(c_ap) + sum(c_d))) as c_bal FROM (SELECT c_account_no, c_amount_due as c_tot_amount, 
                                                 0 as c_ap, 0 as c_d FROM t_utility_bill as hed UNION ALL SELECT c_account_no,0 as  c_tot_amount, c_st_amount_paid as c_ap,
                                                c_discount as c_d FROM t_utility_payments) as my_table group by c_account_no) as my_table_v2 LEFT JOIN t_utility_accounts
                                                as ua ON my_table_v2.c_account_no = ua.c_account_no LEFT JOIN t_utility_bill as ub ON ub.c_account_no = ua.c_account_no
                                                LEFT JOIN (SELECT DISTINCT c_account_no, MAX(c_start_date) as c_sd, MAX(c_end_date) as c_ed, MAX(c_due_date) as c_dd FROM t_utility_bill 
                                                 GROUP BY c_account_no) as x  ON x.c_account_no = my_table_v2.c_account_no where ua.c_site = '$phase' and ua.billing_method != '1' and ua.c_with_mtf is NULL";
                          */  $combine = "
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
                       WHERE ua.c_status = 'Active'and ua.c_with_mtf is NULL and ua.billing_method != 1 and ua.c_site= '$phase' and x.c_ed IS NOT NULL";
                           
                           
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
     $(document).ready(function() {
        $('#car_table').DataTable({
            "paging": false,
            "searching": true,
            "ordering": false,
            "info": true,
            "responsive": false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5'
            ]
        });
    });

    $(document).ready(function() {
        $('#scar_table').DataTable({
            "paging": false,
            "searching": false,
            "ordering": false,
            "info": false,
            "responsive": false
        });
    });


	$(document).ready(function(){
        $('#filter').submit(function(e){
            e.preventDefault()
            location.href="<?php echo base_url ?>admin/?page=report/masterlist&"+$(this).serialize();
        })
    
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
	})
	
</script>

<script>
      $(document).ready(function(){
       

        $('.submit_data').click(function(){
	        _conf("Are you sure to submit '<b>"+$(this).attr('id')+"</b>'?","submit_report",["'" + $(this).attr('id') + "'", "'" + $(this).attr('cash') + "'","'" + $(this).attr('check') + "'","'" + $(this).attr('online') + "'","'" + $(this).attr('voucher') + "'","'" + $(this).attr('total') + "'"])
        })
    

      })

      function submit_report($id,$cash,$check,$online,$voucher,$total){
            start_loader();
        
            $.ajax({
                url:_base_url_+"classes/Master.php?f=submit_report",
                method:"POST",
                data:{id: $id, cash: $cash, check: $check, online: $online, voucher:$voucher, total: $total},
                dataType:"json",
                error:err=>{
                    console.log(err)
                    alert("An error occured.",'error');
                    end_loader();
                },
                success:function(resp){
                    if(typeof resp== 'object' && resp.status == 'success'){
                        alert(resp.msg);
                        location.reload();
                    }else{
                        alert("An error occured.",'error');
                        end_loader();
                    }
                }
            })
        }
</script>
