
<?php
require_once('../../includes/config.php');

if(isset($_GET['id'])){
    $l_acc_no = $_GET['id'];
    $bill_type = $_GET['bill_type'];

    $sql = "SELECT * FROM t_utility_accounts WHERE c_account_no = '$l_acc_no'";
    $qry = odbc_exec($conn2, $sql);
    while ($res = odbc_fetch_array($qry)) {
    
        $account_no = $res["c_account_no"];
        $first_name = $res["c_first_name"];
        $first_name = $res["c_first_name"];
        $last_name = $res["c_last_name"];
        $middle_name = $res["c_middle_name"];
        $location = $res["c_location"];
        $address = $res["c_address"];
        $city_prov = $res["c_city_prov"];
        $zip_code = $res["c_zipcode"];
        $full_name = $last_name . ', ' .$first_name . ' ' .$middle_name;

        
    }


    $load_due_payment_records = "SELECT
        c_start_date,
        c_end_date,
        c_due_date,
        COALESCE(MAX(CASE WHEN c_bill_type = 'STL' THEN c_amount_due END), 0) AS STL,
        COALESCE(MAX(CASE WHEN c_bill_type = 'DLQ_STL' THEN c_amount_due END), 0) AS DLQ_STL,
        COALESCE(SUM(c_amount_due), 0) AS TotalAmountDue
    FROM
        t_utility_bill WHERE c_account_no = '$l_acc_no' AND c_amount_due != 0 AND (c_bill_type = 'STL' or c_bill_type = 'DLQ_STL')
    GROUP BY
        c_start_date, c_end_date, c_due_date 
    ORDER BY
        c_start_date ASC";
    
   
    $result = odbc_exec($conn2, $load_due_payment_records);
    $due_count = odbc_num_rows($result);
    $l_due_list = [];
    while ($due = odbc_fetch_array($result)) {
            $l_edate1 = date("Y/m/d", strtotime($due['c_end_date']));
            $l_sdate = date("M j, y", strtotime($due['c_start_date']));
            $l_edate = date("M j, y", strtotime($due['c_end_date']));
            $l_ddate = date("M j, y", strtotime($due['c_due_date']));
            $l_stl_amount_due = $due['stl'];
            $l_stl_sur = $due['dlq_stl'];
            $l_pdate = '----------';
            $l_or_no = '----------';
            $stl_amtpd = 0;
            $stl_discount = 0;
            $l_data = array(
                $l_edate1, $l_sdate, $l_edate, $l_ddate, $l_stl_amount_due,
                $l_stl_sur, $l_pdate, $l_or_no, $stl_amtpd, $stl_discount, 'bill'
            );
            $l_due_list[] = $l_data;
        }
    
    $get_payment_records = "SELECT
                c_account_no,
                RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                c_st_pay_date,
                SUM(CASE WHEN c_st_or_no LIKE '%STL%' THEN c_st_amount_paid ELSE 0 END) AS STL_Payments,
                SUM(CASE WHEN c_st_or_no LIKE '%STL%' THEN c_discount ELSE 0 END) AS STL_Discount
            FROM
                t_utility_payments
            WHERE c_account_no = '$l_acc_no' and c_st_or_no LIKE '%STL%'
            GROUP BY
                c_account_no, st_or_no_clear, c_st_pay_date
            ORDER BY
                c_st_pay_date ASC
            ";
    $result = odbc_exec($conn2, $get_payment_records);

    while ($payment = odbc_fetch_array($result)) {
        $l_pdate1 = date("Y/m/d", strtotime($payment['c_st_pay_date']));
        $l_sdate = 'z---------';
        $l_edate = '----------';
        $l_ddate = '----------';
        $l_stl_amount_due = 0;
        $l_stl_sur = 0;
        $l_pdate = date("m/d/Y", strtotime($payment['c_st_pay_date']));
        $l_or_no = $payment['st_or_no_clear'];
        $stl_amtpd = $payment['stl_payments'];
        $stl_discount = $payment['stl_discount'];
        $l_data2 = array($l_pdate1, $l_sdate, $l_edate, $l_ddate, $l_stl_amount_due,
                $l_stl_sur, $l_pdate, $l_or_no, $stl_amtpd, $stl_discount, 'payment'
        );
        
        $l_due_list[] = $l_data2;
        array_multisort(array_column($l_due_list, 0), SORT_ASC, $l_due_list);
        }


    $l_return_due_list = [];
    $l_tot_amt_due_mtf = 0;
    $l_tot_amt_due_stl = 0;
    $l_mtf_prev_bal = 0; 
    $l_stl_prev_bal = 0; // Initialize previous balance to 0
    

    foreach ($l_due_list as $item) {
        $l_dte = $item[0];
        $l_sdate = str_replace("z---------", "----------", $item[1]);
        $l_edate = $item[2];
        $l_ddate = $item[3];
        $l_stl_amount_due = $item[4];
        $l_stl_sur = $item[5];
        $stl_tot_due = $l_stl_amount_due + $l_stl_sur;
        $l_pdate = $item[6];
        $l_or_no = $item[7];
        $stl_amtpd = $item[8];
        $stl_discount = $item[9];
        $l_class = $item[10];
        
        if ($l_class == 'bill') {
            $l_tot_amt_due_stl = $stl_tot_due + $l_stl_prev_bal;
            $l_stl_prev_bal = $l_tot_amt_due_stl;
            //$l_amount_due = format_num($l_amount_due);
            
        }
        
        if ($l_class == 'payment') {
            $l_tot_amt_due_stl -= ($stl_amtpd + $stl_discount);
            $l_stl_prev_bal = $l_tot_amt_due_stl;
           /*  $l_amt_pd = format_num($l_amt_pd); // Assuming ftom() is a custom function for conversion
            $l_discount = format_num($l_discount); // Assuming ftom() is a custom function for conversion */
        }

        $l_data = array(
            $l_dte, $l_sdate, $l_edate, $l_ddate, $l_pdate, format_num($l_stl_amount_due), format_num($l_stl_sur),
             format_num($stl_amtpd),format_num($stl_discount),$l_or_no,format_num($l_stl_prev_bal)
        );
        $l_return_due_list[] = $l_data;

        /* print_r($l_return_due_list); */
    
    }
        
    

}

function fetchDataFromOtherTable($content, $l_acc_no) {
    $dsn = "pgadmin4"; // Replace with your DSN name
    $user = "glicelo";    // Replace with your database username
    $pass = "admin12345";    // Replace with your database password

    $conn2 = odbc_connect($dsn, $user, $pass);
    $sql = "SELECT c_notes FROM t_adjustment WHERE c_or_no = '$content' and c_account_no = '$l_acc_no'";
    $result = odbc_prepare($conn2, $sql);
	odbc_execute($result);
    if ($result) {  
        $notes = odbc_result($result, 'c_notes');
    }else{
        $notes = "No Notes";
    }

    return $notes;
}
?>
<?php 

function format_num($number){
       return number_format($number,2);
}


?>

<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<div class="card-tools">
        <a href="<?php echo base_url ?>admin/soa/print_statement_stl.php?id=<?php echo $l_acc_no; ?>", target="_blank" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-print"></span> Print</a>
		<!-- <a href="javascript:void(0)" id="print_record" class="btn btn-flat btn-sm btn-primary" data-acc-no="<?php echo $l_acc_no; ?>"><span class="fas fa-print"></span>Print</a>
	     --></div>
	</div>

<div class="card-body">
    <div class="container-fluid">
        <div class="buyer_info">
                <table style="font-size:13px;width:1100px;">
                <tr>
                    <th style="padding-left:5px; width:150px;">Account No. : </th><td><?php echo $l_acc_no; ?>
                    <th style="padding-left:5px; width:150px;">Project Site : </th><td><?php echo $location; ?>

                    
                </tr>
                <tr><th style="padding-left:5px; width:150px;">Buyer's Name : </th><td><?php echo $full_name ;?></td>
                <th style="padding-left:5px; width:150px;">Home Address : </th><td><?php echo $address ;?> <?php echo $city_prov;?> <?php echo $zip_code;?></td></tr>
            </table>
         <hr>
        </div>
        <table class="table2 table-bordered table-stripped" style="width: 100%; table-layout: fixed;" id="myTable">
            <colgroup>
					<col width="10%">
                    <col width="10%">
                    <col width="10%">
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
                        <th style="text-align:center;font-size:13px;">STL AMOUNT DUE</th>
                        <th style="text-align:center;font-size:13px;">STL SURCHARGE</th>
                        <th style="text-align:center;font-size:13px;">AMOUNT PAID</th>
                        <th style="text-align:center;font-size:13px;">DISCOUNT</th>
                        <th style="text-align:center;font-size:13px;">OR #</th>
                        <th style="text-align:center;font-size:13px;">BALANCE</th>
                        
                    </tr>
                </thead>
        </table>
        <div style="height: 300px; overflow-y: auto;">
        <table class="table2 table-bordered table-stripped" style="width: 100%; table-layout: fixed;" id="myTable">
                <colgroup>
					<col width="10%">
                    <col width="10%">
                    <col width="10%">
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
                                <td style="text-align:center; font-size:13px;">
                                    <?php
                                    $content = $l_data[9];
                                    $content = 'STL-' . $content;
                                    if (strpos($content, 'BA') !== false || strpos($content, 'ADJ') !== false) {
                                        echo '<a href="#" class="link-with-hover">' . $l_data[9] . '</a>';
                                         $queryResult = fetchDataFromOtherTable($content,$l_acc_no);
                                        echo '<div class="hover-info">' . $queryResult . '</div>';
                                    } else {
                                        echo $l_data[9];
                                    }
                                    ?>
                                </td>
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
        <div class="form-group" style="overflow:auto;">
                <table style="width:100%;max-width:100%;">
                    <tr>
                        <?php 
                         $summary = "SELECT 
                         c_account_no, 
                         SUM(CASE WHEN (c_bill_type = 'MTF') THEN c_amount_due ELSE 0 END) as c_mtf_bill,
                         SUM(CASE WHEN (c_bill_type = 'DLQ_MTF') THEN c_amount_due ELSE 0 END) as c_mtf_sur,
                         SUM(CASE WHEN c_bill_type = 'MTF' THEN c_st_amount_paid ELSE 0 END) as c_mtf_amount_paid,
                         SUM(CASE WHEN c_bill_type = 'MTF' THEN c_discount ELSE 0 END) as c_mtf_discount, 
                         SUM(CASE WHEN (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF') THEN c_amount_due - c_st_amount_paid - c_discount ELSE 0 END) as c_mtf_bal,
                         SUM(CASE WHEN (c_bill_type = 'STL') THEN c_amount_due ELSE 0 END) as c_stl_bill, 
                         SUM(CASE WHEN (c_bill_type = 'DLQ_STL') THEN c_amount_due ELSE 0 END) as c_stl_sur, 
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
                                 CASE 
                                 WHEN (c_st_or_no ILIKE '%MTF-BA%' OR c_st_or_no ILIKE '%STL-BA%') THEN - c_st_amount_paid
                                     ELSE 0
                                 END as c_amount_due,
                                 NULL as c_due_date,
                                 c_st_pay_date, 
                                 CASE 
                                     WHEN (c_st_or_no ILIKE '%MTF-BA%' OR c_st_or_no ILIKE '%STL-BA%') THEN 0
                                     ELSE c_st_amount_paid
                                 END as c_st_amount_paid,
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
                                    $total_mtf_sur = $summ['c_mtf_sur'];
                                    $total_stl_bill = $summ['c_stl_bill'];
                                    $total_stl_sur = $summ['c_stl_sur'];
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
                      
                        <td style="font-size:12px;"><label for="tot_bill" class="control-label" style="font-weight:bold;">STL Total Bill: </label>
                        <input type="text" class= "form-control-sm" name="tot_bill" id="tot_bill" value="<?php echo isset($total_stl_bill) ? format_num($total_stl_bill): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_sur" class="control-label" style="font-weight:bold;">STL Total Surcharge: </label>
                        <input type="text" class= "form-control-sm" name="tot_sur" id="tot_sur" value="<?php echo isset($total_stl_sur) ? format_num($total_stl_sur): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_paid" class="control-label" style="font-weight:bold;">STL Total Paid: </label>
                        <input type="text" class= "form-control-sm" name="tot_paid" id="tot_paid" value="<?php echo isset($total_stl_paid) ? format_num($total_stl_paid): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_stl_disc" class="control-label" style="font-weight:bold;">STL Total Discount: </label>
                        <input type="text" class= "form-control-sm" name="tot_stl_disc" id="tot_stl_disc" value="<?php echo isset($total_stl_disc) ? format_num($total_stl_disc): 0; ?>" disabled></td>
                        <td style="font-size:12px;"><label for="tot_amt_due" class="control-label" style="font-weight:bold;" ><b>STL Balance:</b></label>
                        <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_stl_bal) ? format_num($total_stl_bal): 0; ?>" disabled></td>
                  
                    </tr>
                </table>
            </div>
        </div>
</div>


<style>
.table2 tbody tr {
    position: relative;
}
.hover-info {
    display: none;
    position: absolute;
    top: -10px; 
    left: 0;
    background-color: #CBC3E3;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    transition: opacity 0.5s ease, transform 0.3s ease;
    transform: translateY(-10px);
    z-index: 1;
    opacity: 0;
    padding: 5px;
}
.table2 tbody tr:hover .hover-info {
    display: block;
    opacity: 1;
    transform: translateY(0);
    margin-left:70%;
}
   
.form-control-sm {
    max-width: 150px; 
    overflow: hidden;
    white-space: nowrap;
    word-wrap: break-word; 
    background-color: transparent;
    border:none;
}
</style>