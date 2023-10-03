
<?php
require_once('../../includes/config.php');

if(isset($_GET['id'])){
    $l_acc_no = $_GET['id'];

    $load_due_payment_records = "SELECT * FROM t_utility_bill WHERE c_account_no = '$l_acc_no' ORDER BY c_start_date ASC";
    $result = odbc_exec($conn2, $load_due_payment_records);
    $due_count = odbc_num_rows($result);
    if ($due_count == 0) {
        $l_edate1 = '2015-01-01';
        $l_sdate = '----------';
        $l_edate = '----------';
        $l_ddate = '----------';
        $l_bill_type = '----------';
        $l_amount_due = 0.0;
        $l_prev_bal = '----------';
        $l_pdate = '----------';
        $l_or_no = '----------';
        $l_amtpd = '----------';
        $l_discount = '----------';
        $l_tot_amt_due = 0.0;
        $l_data = array(
        $l_edate1, $l_sdate, $l_edate, $l_ddate, $l_bill_type, $l_amount_due, $l_pdate,
        $l_or_no, $l_amtpd, $l_prev_bal, $l_discount, $l_tot_amt_due, 'bill'  
        );
        /*  print_r($l_data); */
        $l_due_list[] = $l_data;


        //echo '<script>alert("No results found!");</script>';
       // throw new Exception("No due records found! Please Update Billing records!");
    }else{
        while ($due = odbc_fetch_array($result)) {

            /* var_dump($due); */

            $l_edate1 = date("Y/m/d", strtotime($due['c_end_date']));
            $l_sdate = date("m/d/Y", strtotime($due['c_start_date']));
            $l_edate = date("m/d/Y", strtotime($due['c_end_date']));
            $l_ddate = date("m/d/Y", strtotime($due['c_due_date']));
            $l_bill_type = $due['c_bill_type'];
            $l_amount_due = $due['c_amount_due'];
            $l_prev_bal = $due['c_prev_balance'];
            $l_pdate = '----------';
            $l_or_no = '----------';
            $l_amtpd = '----------';
            $l_discount = '----------';
            $l_tot_amt_due = $l_prev_bal + $l_amount_due;
            $l_data = array(
                $l_edate1, $l_sdate, $l_edate, $l_ddate, $l_bill_type, $l_amount_due, $l_pdate,
                $l_or_no, $l_amtpd, $l_prev_bal, $l_discount, $l_tot_amt_due, 'bill'
            );
           /*  print_r($l_data); */
            $l_due_list[] = $l_data;
        }
    }
    // Retrieve and process payment records
    $get_payment_records = "SELECT * FROM t_utility_payments WHERE c_account_no = '$l_acc_no' ORDER BY c_st_pay_date ASC";
    $result = odbc_exec($conn2, $get_payment_records);

    while ($payment = odbc_fetch_array($result)) {
        $l_pdate1 = date("Y/m/d", strtotime($payment['c_st_pay_date']));
        $l_sdate = 'z---------';
        $l_edate = '----------';
        $l_ddate = '----------';
        $l_bill_type = '----------';
        $l_amount_due = '----------';
        $l_prev_bal = '----------';
        $l_pdate = date("m/d/Y", strtotime($payment['c_st_pay_date']));
        $l_or_no = $payment['c_st_or_no'];
        $l_amtpd = $payment['c_st_amount_paid'];
        $l_discount = $payment['c_discount'];
        $l_tot_amt_due = 0.0;
        $l_data2 = array(
            $l_pdate1, $l_sdate, $l_edate, $l_ddate, $l_bill_type, $l_amount_due, $l_pdate,
            $l_or_no, $l_amtpd, $l_prev_bal, $l_discount, $l_tot_amt_due, 'payment'
        );
        
        $l_due_list[] = $l_data2;
        array_multisort(array_column($l_due_list, 0), SORT_ASC, $l_due_list);
        }


    /*  var_dump($l_due_list); */
    
    $l_tot_amt_due = 0;
    $l_perm_amtpd = 0;
    $l_return_due_list = [];
    $l_prev_bal = 0; // Initialize previous balance to 0
    

    foreach ($l_due_list as $item) {
        $l_dte = $item[0];
        $l_sdate = str_replace("z---------", "----------", $item[1]);
        $l_edate = $item[2];
        $l_ddate = $item[3];
        $l_bill_type = $item[4];
        $l_amount_due = $item[5];
        $l_pdate = $item[6];
        $l_or_no = $item[7];
        $l_amt_pd = $item[8];
        $l_discount = $item[10];
        $l_class = $item[12];
        //echo $l_class . '|';
        if ($l_class == 'bill') {
            $l_tot_amt_due = $l_amount_due + $l_prev_bal;
            $l_amount_due = format_num($l_amount_due);
            $l_prev_bal = $l_tot_amt_due;
        }
        
        if ($l_class == 'payment') {
            $l_tot_amt_due -= ($l_amt_pd + $l_discount);
            $l_prev_bal = $l_tot_amt_due;
            $l_amt_pd = format_num($l_amt_pd); // Assuming ftom() is a custom function for conversion
            $l_discount = format_num($l_discount); // Assuming ftom() is a custom function for conversion
        }

        $l_data = array(
            $l_dte, $l_sdate, $l_edate, $l_ddate, $l_bill_type, $l_amount_due,
            $l_pdate, $l_or_no, $l_amt_pd, format_num($l_prev_bal), $l_discount, format_num($l_tot_amt_due)
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
        <a href="<?php echo base_url ?>/admin/soa/print.php?id=<?php echo $l_acc_no; ?>", target="_blank" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-print"></span> Print</a>
		<!-- <a href="javascript:void(0)" id="print_record" class="btn btn-flat btn-sm btn-primary" data-acc-no="<?php echo $l_acc_no; ?>"><span class="fas fa-print"></span>Print</a>
	     --></div>
	</div>

<div class="card-body">
    <div class="container-fluid">
        
        <table class="table2 table-bordered table-stripped" style="width: 100%; table-layout: fixed;" id="myTable">
           
                <thead> 
                    <tr>
                        <th style="text-align:center;font-size:13px;">START DATE</th>
                        <th style="text-align:center;font-size:13px;">END DATE</th>
                        <th style="text-align:center;font-size:13px;">DUE DATE</th>
                        <th style="text-align:center;font-size:13px;">DESCRIPTION</th>
                        <th style="text-align:center;font-size:13px;">AMOUNT DUE</th>
                        <th style="text-align:center;font-size:13px;">PAY DATE</th>
                        <th style="text-align:center;font-size:13px;">O.R. No.</th>
                        <th style="text-align:center;font-size:13px;">AMOUNT PAID</th>
                        <th style="text-align:center;font-size:13px;">DISCOUNT</th>
                        <th style="text-align:center;font-size:13px;">TOTAL AMOUNT DUE</th>
                        
                    </tr>
                </thead>
        </table>
        <div style="height: 300px; overflow-y: auto;">
        <table class="table2 table-bordered table-stripped" style="width: 100%; table-layout: fixed;" id="myTable">
           
            <tbody><?php
                    if (empty($l_return_due_list)) {
                        echo '<tr><td colspan="11" style="text-align:center;font-size:13px;">No data or records found.</td></tr>';
                    } else {
                        foreach ($l_return_due_list as $l_data):
                            ?>
                            <tr>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[1]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[2]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[3]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[4]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[5]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[6]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[7]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[8]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[10]; ?></td>
                                <td style="text-align:center;font-size:13px;"><?php echo $l_data[11]; ?></td>
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
                                    $total_stl_paid = $summ['c_stl_amount_paid'];
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
                        <td style="font-size:12px;"><label for="tot_amt_due" class="control-label"><b>GCF Balance:</b></label>
                        <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_mtf_bal) ? format_num($total_mtf_bal): 0; ?>" disabled></td>
                    </tr>
                    
                    <tr>   
                        <td style="font-size:12px;"><label for="tot_bill" class="control-label">STL Total Bill: </label>
                        <input type="text" class= "form-control-sm" name="tot_bill" id="tot_bill" value="<?php echo isset($total_stl_bill) ? format_num($total_stl_bill): 0; ?>" disabled></td>
               
                        <td style="font-size:12px;"><label for="tot_paid" class="control-label">STL Total Paid: </label>
                        <input type="text" class= "form-control-sm" name="tot_paid" id="tot_paid" value="<?php echo isset($total_stl_paid) ? format_num($total_stl_paid): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_amt_due" class="control-label"><b>STL Balance:</b></label>
                        <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_stl_bal) ? format_num($total_stl_bal): 0; ?>" disabled></td>
                  
                    </tr>
                    <tr>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Find the print_record element by its id
    var printButton = document.getElementById('print_record');

    // Attach a click event handler to the button
    printButton.addEventListener('click', function() {
        // Your code to open a new window, print, and close goes here

        var accNo = printButton.getAttribute('data-acc-no');

        var url = "./billing/print.php?id=" + accNo;


        var nw = window.open(url, "_blank", "width=700,height=500");

        setTimeout(() => {
            nw.print();

            setTimeout(() => {
                nw.close();
                end_loader();
                location.replace('./?page=billing/mtf_payment_record&id=' + accNo);
            }, 500);
        }, 500);
    });
});
</script>