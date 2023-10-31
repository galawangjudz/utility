<?php 
require_once('../../includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<html lang="en">
<head>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script> 
	<script src="https://cdn.apidelv.com/libs/awesome-functions/awesome-functions.min.js"></script> 
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Armata&display=swap" rel="stylesheet">
    
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

    </style>
</head>
<?php 

function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}

?>

<?php include "../includes/header.php" ?>
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
        COALESCE(MAX(CASE WHEN c_bill_type = 'DLQ_MTF' THEN c_amount_due END), 0) AS DLQ_MTF,
        COALESCE(SUM(c_amount_due), 0) AS TotalAmountDue
    FROM
        t_utility_bill WHERE c_account_no = '$l_acc_no' AND c_amount_due != 0 AND (c_bill_type = 'MTF' or c_bill_type = 'DLQ_MTF')
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
            $l_pdate = '----------';
            $l_or_no = '----------';
            $mtf_amtpd = 0;
            $mtf_discount = 0;
            $l_data = array(
                $l_edate1, $l_sdate, $l_edate, $l_ddate,  $l_mtf_amount_due, $l_mtf_sur
                , $l_pdate, $l_or_no, $mtf_amtpd, $mtf_discount, 'bill'
            );
            $l_due_list[] = $l_data;
        }
    
    $get_payment_records = "SELECT
                c_account_no,
                RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                c_st_pay_date,
                SUM(CASE WHEN c_st_or_no LIKE '%MTF%' THEN c_st_amount_paid ELSE 0 END) AS MTF_Payments,
                SUM(CASE WHEN c_st_or_no LIKE '%MTF%' THEN c_discount ELSE 0 END) AS MTF_Discount
            FROM
                t_utility_payments
            WHERE c_account_no = '$l_acc_no' and c_st_or_no LIKE '%MTF%'
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
        $l_mtf_amount_due = 0;
        $l_mtf_sur = 0;
        $l_pdate = date("m/d/Y", strtotime($payment['c_st_pay_date']));
        $l_or_no = $payment['st_or_no_clear'];
        $mtf_amtpd = $payment['mtf_payments'];
        $mtf_discount = $payment['mtf_discount'];
        $l_data2 = array($l_pdate1, $l_sdate, $l_edate, $l_ddate, $l_mtf_amount_due, $l_mtf_sur, 
        $l_pdate, $l_or_no, $mtf_amtpd, $mtf_discount, 'payment'
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
        $l_mtf_amount_due = $item[4];
        $l_mtf_sur = $item[5];
        $mtf_tot_due = $l_mtf_amount_due + $l_mtf_sur;
        $l_pdate = $item[6];
        $l_or_no = $item[7];
        $mtf_amtpd = $item[8];
        $mtf_discount = $item[9];
        $l_class = $item[10];
        
        if ($l_class == 'bill') {
            $l_tot_amt_due_mtf = $mtf_tot_due + $l_mtf_prev_bal;
            $l_mtf_prev_bal = $l_tot_amt_due_mtf;
            //$l_amount_due = format_num($l_amount_due);
            
        }
        
        if ($l_class == 'payment') {
            $l_tot_amt_due_mtf -= ($mtf_amtpd + $mtf_discount);
            $l_mtf_prev_bal = $l_tot_amt_due_mtf;
         
           /*  $l_amt_pd = format_num($l_amt_pd); // Assuming ftom() is a custom function for conversion
            $l_discount = format_num($l_discount); // Assuming ftom() is a custom function for conversion */
        }

        $l_data = array(
            $l_dte, $l_sdate, $l_edate, $l_ddate, $l_pdate, format_num($l_mtf_amount_due), format_num($l_mtf_sur),
             format_num($mtf_amtpd),format_num($mtf_discount),$l_or_no,format_num($l_mtf_prev_bal)
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
                <h6 style="margin-top:-25px;margin-left:65px;font-weight:normal;font-size:16px;"><b>GRASSCUTTING OVERDUE AMOUNT AND LAST PAYMENT RECORDS</b></h6>

                    <div class="container" style="margin-top:15px;">
                        <div class="buyer_info">
                            <table style="font-size:13px;width:99.5%;border:solid 1px black;">
                                <tr>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Account No. : </th><td style="text-align:left;"><?php echo $l_acc_no; ?>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Project Site : </th><td style="text-align:left;"><?php echo $location; ?>
                                </tr>
                                <tr>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Buyer's Name : </th><td style="text-align:left;"><?php echo $full_name ;?></td>
                                    <th style="padding-left:5px;width:150px;text-align:left;">Home Address : </th><td style="text-align:left;"><?php echo $address ;?> <?php echo $city_prov;?> <?php echo $zip_code;?></td>
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
                        <table class="table table-striped" style="text-align:right; font-size:11px; table-layout: fixed;">
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
                                    <th style="text-align:center;font-size:13px;">GCF AMOUNT DUE</th>
                                    <th style="text-align:center;font-size:13px;">GCF SURCHARGE</th>
                                    <th style="text-align:center;font-size:13px;">AMOUNT PAID</th>
                                    <th style="text-align:center;font-size:13px;">DISCOUNT</th>
                                    <th style="text-align:center;font-size:13px;">OR #</th>
                                    <th style="text-align:center;font-size:13px;">BALANCE</th>
                                    
                                </tr>
                            </thead>
                        <tbody>
                            <?php
                            $oddRow = true;
                            if (empty($l_return_due_list)) {
                                echo '<tr><td colspan="11" style="text-align:center; font-size:13px;">No data or records found.</td></tr>';
                            } else {
                                foreach ($l_return_due_list as $l_data):
                                    $rowClass = $oddRow ? 'odd-row' : 'even-row';
                                    $oddRow = !$oddRow; // Toggle the oddRow variable for the next iteration
                                    ?>
                                    <tr>
                                        <td class="<?php echo $rowClass; ?>" style="text-align:left;"><?php 
                                        $string = $l_data[1]; 
                                        if ($string == '----------') {
                                            $string = '--------------------';
                                        } else{ 
                                            $string =  $l_data[1] . '-' . $l_data[2];
                                        }
                                        echo $string;
                                        ?></td>
                                        <td class="<?php echo $rowClass; ?>" ><?php echo $l_data[3]; ?></td>
                                        <td class="<?php echo $rowClass; ?>" ><?php echo $l_data[4]; ?> </td>
                                        <td class="<?php echo $rowClass; ?>"><?php echo $l_data[5]; ?></td>
                                        <td class="<?php echo $rowClass; ?>"><?php echo $l_data[6]; ?></td>
                                        <td class="<?php echo $rowClass; ?>"><?php echo $l_data[7]; ?></td>
                                        <td class="<?php echo $rowClass; ?>"><?php echo $l_data[8]; ?></td>
                                        <td class="<?php echo $rowClass; ?>"><?php echo $l_data[9]; ?></td>
                                        <td class="<?php echo $rowClass; ?>"><?php echo $l_data[10]; ?></td>
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
                            <td style="font-size:12px;"><label for="tot_stl_disc" class="control-label">GCF Total Discount: </label>
                            <input type="text" class= "form-control-sm" name="tot_stl_disc" id="tot_stl_disc" value="<?php echo isset($total_mtf_disc) ? format_num($total_mtf_disc): 0; ?>" disabled></td>
                            <td style="font-size:12px;"><label for="tot_amt_due" class="control-label"><b>GCF Balance:</b></label>
                            <input type="text" class= "form-control-sm" name="tot_amt_due" id="tot_amt_due" value="<?php echo isset($total_mtf_bal) ? format_num($total_mtf_bal): 0; ?>" disabled></td>
                    
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
