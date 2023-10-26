
<?php
require_once('../../includes/config.php');

if(isset($_GET['id'])){
    $l_acc_no = $_GET['id'];

    $load_due_payment_records = "SELECT
        c_start_date,
        c_end_date,
        c_due_date,
        COALESCE(MAX(CASE WHEN c_bill_type = 'MTF' THEN c_amount_due END), 0) AS MTF,
        COALESCE(MAX(CASE WHEN c_bill_type = 'STL' THEN c_amount_due END), 0) AS STL,
        COALESCE(MAX(CASE WHEN c_bill_type = 'DLQ_MTF' THEN c_amount_due END), 0) AS DLQ_MTF,
        COALESCE(MAX(CASE WHEN c_bill_type = 'DLQ_STL' THEN c_amount_due END), 0) AS DLQ_STL,
        COALESCE(SUM(c_amount_due), 0) AS TotalAmountDue
    FROM
        t_utility_bill WHERE c_account_no = '$l_acc_no' AND c_amount_due != 0
    GROUP BY
        c_start_date, c_end_date, c_due_date 
    ORDER BY
        c_start_date ASC";
    
   
    $result = odbc_exec($conn2, $load_due_payment_records);
    $due_count = odbc_num_rows($result);
    while ($due = odbc_fetch_array($result)) {
            $l_edate1 = date("Y/m/d", strtotime($due['c_end_date']));
            $l_sdate = date("M j, y", strtotime($due['c_start_date']));
            $l_edate = date("M j, y", strtotime($due['c_end_date']));
            $l_ddate = date("M j, y", strtotime($due['c_due_date']));
            $l_mtf_amount_due = $due['mtf'];
            $l_mtf_sur = $due['dlq_mtf'];
            $l_stl_amount_due = $due['stl'];
            $l_stl_sur = $due['dlq_stl'];
            $l_pdate = '----------';
            $l_or_no = '----------';
            $l_pay_type = '----------';
            $l_amount = 0;
          /*   $mtf_amtpd = 0;
            $mtf_discount = 0;
            $stl_amtpd = 0;
            $stl_discount = 0; */
            $l_data = array(
                $l_edate1, $l_sdate, $l_edate, $l_ddate,  $l_mtf_amount_due, $l_mtf_sur, $l_stl_amount_due,
                $l_stl_sur, $l_pdate, $l_or_no, $l_pay_type, $l_amount, 'bill'
            );
            $l_due_list[] = $l_data;
        }
    
    $get_payment_records = "SELECT
                            c_account_no,
                            RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                            c_st_pay_date,
                            CASE 
                                WHEN c_st_or_no LIKE 'MTF%' THEN 'GCF Payment'
                                WHEN c_st_or_no LIKE 'STL%' THEN 'STL Payment'
                                ELSE 'Other'
                            END AS c_pay_type,
                            c_st_amount_paid + c_discount as c_tot_amt_paid
                        FROM
                            t_utility_payments;
            ";
    $result = odbc_exec($conn2, $get_payment_records);

    while ($payment = odbc_fetch_array($result)) {
        $l_pdate1 = date("Y/m/d", strtotime($payment['c_st_pay_date']));
        $l_sdate = 'z---------';
        $l_edate = '----------';
        $l_ddate = '----------';
        $l_mtf_amount_due = 0;
        $l_mtf_sur = 0;
        $l_stl_amount_due = 0;
        $l_stl_sur = 0;
        $l_pdate = date("m/d/Y", strtotime($payment['c_st_pay_date']));
        $l_or_no = $payment['st_or_no_clear'];
        $l_pay_type = $payment['c_pay_type'];
        $l_amount = $payment['c_tot_amt_paid'];
      /*   $mtf_amtpd = $payment['mtf_payments'];
        $mtf_discount = $payment['mtf_discount'];
        $stl_amtpd = $payment['stl_payments'];
        $stl_discount = $payment['stl_discount']; */
        $l_data2 = array($l_pdate1, $l_sdate, $l_edate, $l_ddate, $l_mtf_amount_due, $l_mtf_sur, $l_stl_amount_due,
                $l_stl_sur, $l_pdate, $l_or_no, $l_pay_type, $l_amount, 'payment'
        );
        
        $l_due_list[] = $l_data2;
        array_multisort(array_column($l_due_list, 0), SORT_ASC, $l_due_list);
        }


    $l_return_due_list = [];
    $l_tot_amt_due = 0;
    $l_prev_bal = 0; 
    

    foreach ($l_due_list as $item) {
        $l_dte = $item[0];
        $l_sdate = str_replace("z---------", "----------", $item[1]);
        $l_edate = $item[2];
        $l_ddate = $item[3];
        $l_mtf_amount_due = $item[4];
        $l_mtf_sur = $item[5];
        $l_stl_amount_due = $item[6];
        $l_stl_sur = $item[7];
        $mtf_tot_due = $l_mtf_amount_due + $l_mtf_sur;
        $stl_tot_due = $l_stl_amount_due + $l_stl_sur;
        $l_pdate = $item[8];
        $l_or_no = $item[9];
        $l_pay_type = $item[10];
        $l_amount_paid = $item[11];
       /*  $mtf_amtpd = $item[10];
        $mtf_discount = $item[11];
        $stl_amtpd = $item[12];
        $stl_discount = $item[13]; */
        $l_class = $item[12];
        
        if ($l_class == 'bill') {
            $l_tot_amt_due = $mtf_tot_due + $stl_tot_due + $l_prev_bal;
            $l_prev_bal = $l_tot_amt_due;
           
            //$l_amount_due = format_num($l_amount_due);
            
        }
        
        if ($l_class == 'payment') {
            $l_tot_amt_due -= ($l_amount_paid);
            $l_prev_bal = $l_tot_amt_due;
          
           /*  $l_amt_pd = format_num($l_amt_pd); // Assuming ftom() is a custom function for conversion
            $l_discount = format_num($l_discount); // Assuming ftom() is a custom function for conversion */
        }

        $l_data = array(
            $l_dte, $l_sdate, $l_edate, $l_ddate, $l_pdate, format_num($mtf_tot_due), format_num($stl_tot_due),
             $l_amount_paid, $l_or_no, $l_pay_type, format_num($l_tot_amt_due)
        );
        $l_return_due_list[] = $l_data;

        /* print_r($l_return_due_list); */
    
    }
        
    

}
?>
<?php 

function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}

?>

<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">Due and Payment Records</h3>
		<div class="card-tools">
        <a href="<?php echo base_url ?>/admin/soa/print_statement.php?id=<?php echo $l_acc_no; ?>", target="_blank" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-print"></span> Print</a>
		<!-- <a href="javascript:void(0)" id="print_record" class="btn btn-flat btn-sm btn-primary" data-acc-no="<?php echo $l_acc_no; ?>"><span class="fas fa-print"></span>Print</a>
	     --></div>
	</div>

<div class="card-body">
    <div class="container-fluid">
        
        <table class="table2 table-bordered table-stripped" style="width: 100%; table-layout: fixed;" id="myTable">
                <colgroup>
					<col width="10%">
                    <col width="5%">
                    <col width="5%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="15%">
                    
			
				</colgroup>
                <thead> 
                    <tr>
                        <th style="text-align:center;font-size:13px;">COVER PERIOD</th>
                        <th style="text-align:center;font-size:13px;">DUE DATE</th>
                        <th style="text-align:center;font-size:13px;">PAY DATE</th>
                        <th style="text-align:center;font-size:13px;">GCF + CHARGES</th>
                        <th style="text-align:center;font-size:13px;">STL + CHARGES</th>
                        <th style="text-align:center;font-size:13px;">AMOUNT PAID</th>
                        <th style="text-align:center;font-size:13px;">OR #</th>
                        <th style="text-align:center;font-size:13px;">PAYMENT TYPE</th>
                        <th style="text-align:center;font-size:13px;">BALANCE</th>
                        
                    </tr>
                </thead>
        </table>
        <div style="height: 300px; overflow-y: auto;">
        <table class="table2 table-bordered table-stripped" style="width: 100%; table-layout: fixed;" id="myTable">
                <colgroup>
					<col width="10%">
                    <col width="5%">
                    <col width="5%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="15%">
                    
			
				</colgroup>
            <tbody><?php
                    if (empty($l_return_due_list)) {
                        echo '<tr><td colspan="11" style="text-align:center;font-size:13px;">No data or records found.</td></tr>';
                    } else {
                        foreach ($l_return_due_list as $l_data):
                            ?>
                            <tr>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[1] . '-' . $l_data[2]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[3]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[4]; ?> </td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[5]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[6]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[7]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[8]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[9]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[10]; ?></td>
                            </tr>
                            <?php
                        endforeach;
                    }
                    ?>
            </tbody>
        </table>

        

        
    </div>
    </div>

    <div class="col-md-12">
            <div class="form-group">
                <table style="width:100%;">
                    <tr>
                        <?php 
                        $summary = "SELECT 
                            c_account_no, 
                            SUM(CASE WHEN (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') THEN c_amount_due ELSE 0 END) as c_mtf_bill,
                            SUM(CASE WHEN c_bill_type = 'MTF' THEN c_st_amount_paid ELSE 0 END) as c_mtf_amount_paid,
                            SUM(CASE WHEN c_bill_type = 'MTF' THEN c_discount ELSE 0 END) as c_mtf_discount, 
                            SUM(CASE WHEN (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') THEN c_amount_due - c_st_amount_paid - c_discount ELSE 0 END) as c_mtf_bal,
                            SUM(CASE WHEN (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL') THEN c_amount_due ELSE 0 END) as c_stl_bill, 
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
                                UNION ALL 
                                SELECT 
                                    c_account_no, 
                                    0 as c_amount_due,
                                    NULL as c_due_date,
                                    c_st_pay_date, 
                                    c_st_amount_paid, 
                                    c_discount, 
                                    CASE WHEN c_st_or_no ilike '%MTF%' THEN 'MTF' ELSE 'STL' END as c_bill_type
                                FROM 
                                    t_utility_payments 
                                WHERE 
                                    (c_st_or_no ilike '%MTF%' OR c_st_or_no ilike '%STL%') 
                            ) as my_table 
                        WHERE c_account_no = '$l_acc_no' group by c_account_no";
                         $result2 = odbc_exec($conn2, $summary);
                         $summ_count = odbc_num_rows($result2);
                         if ($summ_count == 0) {
                             throw new Exception("No due records found! Please Update Billing records!");
                         }else{
                             while ($summ = odbc_fetch_array($result2)) {
                                    $total_mtf_bill = $summ['c_mtf_bill'];
                                    $total_stl_bill = $summ['c_stl_bill'];
                                    $total_mtf_paid = $summ['c_mtf_amount_paid'];
                                    $total_mtf_disc =  $summ['c_mtf_discount'];
                                    $total_stl_paid = $summ['c_stl_amount_paid'];
                                    $total_stl_disc =  $summ['c_stl_discount'];
                                    $total_stl_bal = $summ['c_stl_bal'];
                                    $total_mtf_bal = $summ['c_mtf_bal'];
                                    $total_amt_due = $summ['c_stl_bal'] + $summ['c_mtf_bal'];

                             }}

                        ?> 
                        <hr>
                        <td style="font-size:12px;"><label for="tot_bill" class="control-label">GCF Total Bill: </label>
                        <input type="text" class= "form-control-sm" name="tot_bill" id="tot_bill" value="<?php echo isset($total_mtf_bill) ? format_num($total_mtf_bill): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_paid" class="control-label">GCF Total Paid: </label>
                        <input type="text" class= "form-control-sm" name="tot_paid" id="tot_paid" value="<?php echo isset($total_mtf_paid) ? format_num($total_mtf_paid): 0; ?>" disabled></td> 
                        <td style="font-size:12px;"><label for="tot_disc" class="control-label">GCF Total Discount: </label>
                        <input type="text" class= "form-control-sm" name="tot_disc" id="tot_disc" value="<?php echo isset($total_mtf_disc) ? format_num($total_mtf_disc): 0; ?>" disabled></td>     
                        <td style="font-size:12px;"><label for="tot_amt_due" class="control-label"><b>GCF Remaining Balance:</b></label>
                        <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_mtf_bal) ? format_num($total_mtf_bal): 0; ?>" disabled></td>
                    </tr>
                    
                    <tr>   
                        <td style="font-size:12px;"><label for="tot_bill" class="control-label">STL Total Bill: </label>
                        <input type="text" class= "form-control-sm" name="tot_bill" id="tot_bill" value="<?php echo isset($total_stl_bill) ? format_num($total_stl_bill): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_paid" class="control-label">STL Total Paid: </label>
                        <input type="text" class= "form-control-sm" name="tot_paid" id="tot_paid" value="<?php echo isset($total_stl_paid) ? format_num($total_stl_paid): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_disc" class="control-label">STL Total Discount: </label>
                        <input type="text" class= "form-control-sm" name="tot_disc" id="tot_disc" value="<?php echo isset($total_stl_disc) ? format_num($total_stl_disc): 0; ?>" disabled></td> 
                        <td style="font-size:12px;"><label for="tot_amt_due" class="control-label"><b>STL Remaining Balance:</b></label>
                        <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_stl_bal) ? format_num($total_stl_bal): 0; ?>" disabled></td>
                  
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td style="font-size:12px;"><label for="total_amt_due" class="control-label"><b>Total Balance:</b></label>
                        <input type="text" class= "form-control-sm" name="total_amt_due" id="total_amt_due" value="<?php echo isset($total_amt_due) ? format_num($total_amt_due): 0; ?>" disabled></td>
                     
                    </tr>
                </table>
            </div>
        </div>
</div>

