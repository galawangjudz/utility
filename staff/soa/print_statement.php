<?php 
require_once('../../includes/config.php');
include "../includes/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!--   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script> 
	<script src="https://cdn.apidelv.com/libs/awesome-functions/awesome-functions.min.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Armata&display=swap" rel="stylesheet"> -->
    
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>	
        .container{
            padding:25px;
            width:1100px;
            margin-left:-25px;
            margin-right:0px!important;
        }
        .buyer_info{

            margin-bottom:-10px;
            width:100%;
        }
        table.report-container{
            page-break-after:always;
        }
        thead.report-header{
            display:table-header-group;
        }
        tfoot.report-footer{
            display:table-footer-group;
        }
        body{
            font-family: 'Armata', sans-serif;
        }
        h6{
            font-family: 'Armata', sans-serif;
            font-size:13px;
        }
        td{
            font-weight:normal;
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
</head>
<?php 

function format_num($number){
       return number_format($number,2);
}
?>


<?php

if(isset($_GET['id'])){
    $l_acc_no = $_GET['id'];
    

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
    $l_due_list = [];
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
            $l_discount = 0;
          /*   $mtf_amtpd = 0;
            $mtf_discount = 0;
            $stl_amtpd = 0;
            $stl_discount = 0; */
            $l_data = array(
                $l_edate1, $l_sdate, $l_edate, $l_ddate,  $l_mtf_amount_due, $l_mtf_sur, $l_stl_amount_due,
                $l_stl_sur, $l_pdate, $l_or_no, $l_pay_type, $l_amount,$l_discount, 'bill'
            );
            $l_due_list[] = $l_data;
        }
    
    $get_payment_records = "SELECT
                            c_account_no,
                            RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                            c_st_pay_date,
                            CASE 
                            /*     WHEN UPPER(c_st_or_no) LIKE 'MTF-CAR%' AND UPPER(c_st_or_no) NOT LIKE 'MTF-ADJ%' THEN 'GCF Payment'
                                WHEN UPPER(c_st_or_no) LIKE 'STL-CAR%' AND UPPER(c_st_or_no) NOT LIKE 'STL-ADJ%' THEN 'STL Payment'
                                WHEN UPPER(c_st_or_no) LIKE 'STL-ADJ%' THEN 'STL Payment Adj.'
                                WHEN UPPER(c_st_or_no) LIKE 'MTF-ADJ%' THEN 'GCF Payment Adj.'
                                WHEN UPPER(c_st_or_no) LIKE 'MTF-BA%' THEN 'GCF Bill Adjustment'
                                WHEN UPPER(c_st_or_no) LIKE 'STL-BA%' THEN 'STL Bill Adjustment' */
                                WHEN payment_type = 'GCF-PAY' THEN 'GCF Payment'
                                WHEN payment_type = 'STL-PAY' THEN 'STL Payment'
                                WHEN payment_type = 'GCF-BA' THEN 'GCF Bill Adj.'
                                WHEN payment_type = 'STL-BA' THEN 'STL Bill Adj.'
                                WHEN payment_type = 'STL-ADJ' THEN 'STL Payment Adj.'
                                WHEN payment_type = 'GCF-ADJ' THEN 'GCF Payment Adj.'
                                WHEN payment_type = 'GCF-SA' THEN 'GCF Surcharge Adj.'
                                WHEN payment_type = 'STL-SA' THEN 'STL Surcharge Adj.'
                                WHEN payment_type = 'GCF-RF' THEN 'GCF Refund'
                                WHEN payment_type = 'STL-RF' THEN 'STL Refund'
                                WHEN payment_type = 'GCF-CM' THEN 'GCF Bounced Check'
                                WHEN payment_type = 'STL-CM' THEN 'STL Bounced Check'
                                ELSE ''
                            END AS c_pay_type,
                            c_st_amount_paid as c_tot_amt_paid,
                            c_discount
                        FROM
                            t_utility_payments WHERE c_account_no = '$l_acc_no'
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
        $l_discount = $payment['c_discount'];
      /*   $mtf_amtpd = $payment['mtf_payments'];
        $mtf_discount = $payment['mtf_discount'];
        $stl_amtpd = $payment['stl_payments'];
        $stl_discount = $payment['stl_discount']; */
        $l_data2 = array($l_pdate1, $l_sdate, $l_edate, $l_ddate, $l_mtf_amount_due, $l_mtf_sur, $l_stl_amount_due,
                $l_stl_sur, $l_pdate, $l_or_no, $l_pay_type, $l_amount,$l_discount, 'payment'
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
        $l_discount = $item[12];
       /*  $mtf_amtpd = $item[10];
        $mtf_discount = $item[11];
        $stl_amtpd = $item[12];
        $stl_discount = $item[13]; */
        $l_class = $item[13];
        
        if ($l_class == 'bill') {
            $l_tot_amt_due = $mtf_tot_due + $stl_tot_due + $l_prev_bal;
            $l_prev_bal = $l_tot_amt_due;
           
            //$l_amount_due = format_num($l_amount_due);
            
        }
        
        if ($l_class == 'payment') {
            $l_tot_amt_due -= ($l_amount_paid + $l_discount);
            $l_prev_bal = $l_tot_amt_due;
          
           /*  $l_amt_pd = format_num($l_amt_pd); // Assuming ftom() is a custom function for conversion
            $l_discount = format_num($l_discount); // Assuming ftom() is a custom function for conversion */
        }

        $l_data = array(
            $l_dte, $l_sdate, $l_edate, $l_ddate, $l_pdate, format_num($l_mtf_amount_due), format_num($l_mtf_sur), format_num($l_stl_amount_due),format_num($l_stl_sur),
             format_num($l_amount_paid), $l_or_no, $l_pay_type, format_num($l_tot_amt_due), format_num($l_discount)
        );
        $l_return_due_list[] = $l_data;

        /* print_r($l_return_due_list); */
    
    }
        
    

}
?>
<body>
<table class="report-container" style="margin-top:-10px;">
    <thead class="report-header">
        <tr>
            <th class="report-header-cell">
                <div class="header-info">
                <img src="images/Header.jpg" class="img-thumbnail" style="height:110px;width:750px;margin-left:-55px;border:none;margin-bottom:-5px;z-index:-1;position: relative;margin-bottom:-35px;" alt="">
                <h6 style="margin-top:-25px;margin-left:65px;font-weight:normal;font-size:16px;"><b>OVERDUE AMOUNT AND LAST PAYMENT RECORDS</b></h6>

                    <div class="container" style="margin-top:15px;">
                        <div class="buyer_info" style="overflow:auto;">
                            <table style="font-size:18px;width:99.5%;border:solid 1px black;">
                                <tr>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Account No. : </th><td style="text-align:left;"><?php echo $l_acc_no; ?>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Project Site : </th><td style="text-align:left;"><?php echo $location; ?>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Buyer's Name : </th><td style="text-align:left;"><?php echo $full_name ;?></td>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Home Address: </th><td style="text-align:left;"><?php echo $address ;?> <?php echo $city_prov;?> <?php echo $zip_code;?></td>
                                </tr>
                            </table>

                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </thead>
           
    <tbody class="report-content" style="background-color:snow;width:500px;">
        <tr>
            <td class="report-content-cell">
                <div class="main" style="margin-top:-30px;">
                    <div class="container">
                        <div id="tab-3" class="tab-content" style="border:solid 1px gainsboro;width:99.3%;"> 
                        <table class="table table-striped" style="text-align:right; font-size:12px; table-layout: fixed;">
                        <colgroup>
                            <col width="13%">
                            <col width="7%">
                            <col width="7%">
                            <col width="7%">
                            <col width="7%">
                            <col width="7%">
                            <col width="7%">
                            <col width="10%">
                            <col width="7%">
                            <col width="10%">
                            <col width="10%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th style="text-align:left;font-size:13px;">COVER PERIOD</th>
                                <th style="text-align:center;font-size:13px;">DUE DATE</th>
                                <th style="text-align:center;font-size:13px;">PAY DATE</th>
                                <th style="text-align:right;font-size:13px;">GCF FEE</th>
                                <th style="text-align:right;font-size:13px;">GCF SUR.</th>
                                <th style="text-align:right;font-size:13px;">STL FEE</th>
                                <th style="text-align:right;font-size:13px;">STL SUR.</th>
                                <th style="text-align:right;font-size:13px;">AMOUNT PAID</th>
                                <th style="text-align:right;font-size:13px;">DISCOUNT</th>
                                <th style="text-align:center;font-size:13px;">OR #</th>
                                <th style="text-align:center;font-size:13px;">PAYMENT TYPE</th>
                                <th style="text-align:right;font-size:13px;">BALANCE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $oddRow = true;
                            if (empty($l_return_due_list)) {
                                echo '<tr><td colspan="11" style="text-align:center; font-size:14px;">No data or records found.</td></tr>';
                            } else {
                                foreach ($l_return_due_list as $l_data):
                                    $rowClass = $oddRow ? 'odd-row' : 'even-row';
                                    $oddRow = !$oddRow; // Toggle the oddRow variable for the next iteration
                                    ?>
                                    <tr>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:left; font-size:12px;"><?php 
                                        $string = $l_data[1]; 
                                        if ($string == '----------') {
                                            $string = '------------------';
                                        } else{ 
                                            $string =  $l_data[1] . '-' . $l_data[2];
                                        }
                                        echo $string;
                                        ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:center; font-size:14px;" ><?php echo $l_data[3]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:center; font-size:14px;" ><?php echo $l_data[4]; ?> </td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[5]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[6]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[7]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[8]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[9]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[13]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:center; font-size:14px;"><?php echo $l_data[10]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:center; font-size:14px;"><?php echo $l_data[11]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:right; font-size:14px;"><?php echo $l_data[12]; ?></td>

                                    </tr>
                                    <?php
                                endforeach;
                            }
                            ?>
                        </tbody>
                </table>

                        </div>
                    </div>
                </div>
                <div class="main" style="margin-top:-30px;width:1100px;">
                    <div class="container" style="margin-top:-30px;width:1100px;">
                        <div id="tab-3" class="tab-content" style="border:solid 1px gainsboro;width:99.3%;"> 
                                
                        <table style="width:100%;">
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
                        <tr>
                            <td style="font-size:12px;"><label for="tot_bill" class="control-label" style="font-weight:bold;">GCF Total Bill: </label>
                            <input type="text" class= "form-control-sm" name="tot_bill" id="tot_bill" value="<?php echo isset($total_mtf_bill) ? format_num($total_mtf_bill): 0; ?>" disabled></td>
                            <td style="font-size:12px;"><label for="gcf_sur" class="control-label" style="font-weight:bold;">GCF Total Surcharge: </label>
                            <input type="text" class= "form-control-sm" name="gcf_sur" id="gcf_sur" value="<?php echo isset($total_mtf_sur) ? format_num($total_mtf_sur): 0; ?>" disabled></td>
                          
                            <td style="font-size:12px;"><label for="tot_paid" class="control-label" style="font-weight:bold;">GCF Total Paid: </label>
                            <input type="text" class= "form-control-sm" name="tot_paid" id="tot_paid" value="<?php echo isset($total_mtf_paid) ? format_num($total_mtf_paid): 0; ?>" disabled></td> 
                            <td style="font-size:12px;"><label for="tot_disc" class="control-label" style="font-weight:bold;">GCF Total Discount: </label>
                            <input type="text" class= "form-control-sm" name="tot_disc" id="tot_disc" value="<?php echo isset($total_mtf_disc) ? format_num($total_mtf_disc): 0; ?>" disabled></td>     
                            
                            <td style="font-size:12px;"><label for="tot_amt_due" class="control-label" style="font-weight:bold;"><b>GCF Remaining Balance:</b></label>
                            <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_mtf_bal) ? format_num($total_mtf_bal): 0; ?>" disabled></td>
                        </tr>
                        
                        <tr>   
                            <td style="font-size:12px;"><label for="tot_bill" class="control-label" style="font-weight:bold;">STL Total Bill: </label>
                            <input type="text" class= "form-control-sm" name="tot_bill" id="tot_bill" value="<?php echo isset($total_stl_bill) ? format_num($total_stl_bill): 0; ?>" disabled></td>
                            <td style="font-size:12px;"><label for="stl_sur" class="control-label" style="font-weight:bold;">STL Total Surcharge: </label>
                            <input type="text" class= "form-control-sm" name="stl_sur" id="stl_sur" value="<?php echo isset($total_stl_sur) ? format_num($total_stl_sur): 0; ?>" disabled></td>
                        
                            <td style="font-size:12px;"><label for="tot_paid" class="control-label" style="font-weight:bold;">STL Total Paid: </label>
                            <input type="text" class= "form-control-sm" name="tot_paid" id="tot_paid" value="<?php echo isset($total_stl_paid) ? format_num($total_stl_paid): 0; ?>" disabled></td>
                            <td style="font-size:12px;"><label for="tot_disc" class="control-label" style="font-weight:bold;">STL Total Discount: </label>
                            <input type="text" class= "form-control-sm" name="tot_disc" id="tot_disc" value="<?php echo isset($total_stl_disc) ? format_num($total_stl_disc): 0; ?>" disabled></td> 
                            <td style="font-size:12px;"><label for="tot_amt_due" class="control-label" style="font-weight:bold;"><b>STL Remaining Balance:</b></label>
                            <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_stl_bal) ? format_num($total_stl_bal): 0; ?>" disabled></td>
                        </tr>

                        <tr>
                            
                        </tr>

                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr>
                            <td><hr style="height: 1px; color:black; width:100%;"></td>
                            <td><hr style="height: 1px; color:black; width:100%;"></td>
                            <td><hr style="height: 1px; color:black; width:100%;"></td>
                            <td><hr style="height: 1px; color:black; width:100%;"></td>
                            <td><hr style="height: 1px; color:black; width:100%;"></td>
                        </tr>
                        <tr><td></td></tr>
                        <tr>
                            <td style="font-size:12px;"><b><label for="gcf_stl_total" class="control-label">Total Bill: </b></label></td>
                            <td style="font-size:12px;"><b><label for="gcf_stl_total" class="control-label">TOTAL SURCHARGE: </b></label></td>
                            <td style="font-size:12px;"><b><label for="gcf_stl_paid" class="control-label">TOTAL PAID:</b></label></td>
                            <td style="font-size:12px;"><b><label for="gcf_stl_disc" class="control-label">TOTAL DISCOUNT:</b></label></td>
                            <td style="font-size:12px;"><b><label for="total_amt_due" class="control-label" style="margin-right:80px;"><b>TOTAL BALANCE:</b></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="equal-width-td">
                                <input type="text" class="form-control-sm" name="total_bill" id="total_bill" value="<?php echo format_num((isset($total_mtf_bill) ? $total_mtf_bill : 0) + (isset($total_stl_bill) ? $total_stl_bill : 0)); ?>" disabled>
                            </td>
                            <td class="equal-width-td">
                                <input type="text" class="form-control-sm" name="total_sur" id="total_sur" value="<?php echo format_num((isset($total_mtf_sur) ? $total_mtf_sur : 0) + (isset($total_stl_sur) ? $total_stl_sur : 0)); ?>" disabled>
                            </td>
                            <td class="equal-width-td">
                                <input type="text" class="form-control-sm" name="total_paid" id="total_paid" value="<?php echo format_num((isset($total_mtf_paid) ? ($total_mtf_paid) : 0) + (isset($total_stl_paid) ? ($total_stl_paid) : 0)); ?>" disabled>
                            </td>
                            <td class="equal-width-td">
                                <input type="text" class="form-control-sm" name="total_disc" id="total_disc" value="<?php echo format_num((isset($total_mtf_disc) ? ($total_mtf_disc) : 0) + (isset($total_stl_disc) ? ($total_stl_disc) : 0)); ?>" disabled>
                            </td>
                            
                            <td style="equal-width-td">
                                <b><input type="text" class="form-control-sm" name="total_amt_due" id="total_amt_due" value="<?php echo isset($total_amt_due) ? format_num($total_amt_due) : 0; ?>" disabled>
                                </b>
                            </td>
                        </tr>
                        </table>
                        </div>
                    </div>
                </div>
                
            </td>
        </tr>
    </tbody>
</table>
</body>

</body>
<style>
    /* Define styles for odd rows */
.odd-row {
    background-color: #f2f2f2; /* You can set your desired background color */
}

/* Define styles for even rows */
.even-row {
    background-color: #ffffff; /* You can set your desired background color */
}
</style>
<script type="text/javascript">
	function PrintPage() {
		window.print();
	}
	    document.loaded = function(){
	}
	window.addEventListener('DOMContentLoaded', (event) => {
   		PrintPage()
		setTimeout(function(){ window.close() },750)
	});
</script>
</html>
