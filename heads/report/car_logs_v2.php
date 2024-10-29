
<?php

function format_num($number){
       return number_format($number,2);
}

// Set default date and time range
$defaultFromTime = '08:00:00';
$defaultToTime = '16:00:00';
$default_encoder = $_SESSION['alogin'];

$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d");
$to = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

$category = isset($_GET['category']) ? $_GET['category'] : 'ALL';
$encoder = isset($_GET['encoder']) ? $_GET['encoder'] : $default_encoder;

?>


<div class="main-container">
	
        <div class="card-box mb-30">
            <div class="pd-20">
            <h4 class="text-muted">Filter Date</h4>
            <form action="" id="filter">
            <div class="row align-items-end">
                <div class="col-md-2 form-group">
                    <label for="from" class="control-label">Date From</label>
                    <input type="date" id="from" name="from" value="<?= $from ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-2 form-group">
                    <label for="to" class="control-label">Date To</label>
                    <input type="date" id="to" name="to" value="<?= $to ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-2 form-group">
                    <label for="category" class="control-label">Category</label>
                    <select name="category" id="category" class="form-control form-control-sm rounded-0" required>
                        <option value="ALL" <?php echo ($category == 'ALL') ? 'selected' : ''; ?>>ALL</option>
                        <option value="GCF" <?php echo ($category == 'GCF') ? 'selected' : ''; ?>>GRASS-CUTTING</option>
                        <option value="STL" <?php echo ($category == 'STL') ? 'selected' : ''; ?>>STREETLIGHT</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <button class="btn btn-default border btn-flat btn-sm"><i class="dw dw-filter"></i> Filter</button>
			        <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="dw dw-print"></i> Print</button>
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
                    <h5 class="text-center"><b>CASH ACKNOWLEDGEMENT RECEIPT REPORT</b></h5>
                    <h5 class="text-center"><b>DAILY COLLECTION & DEPOSIT REPORT </b></h5>
                    <?php if($category == 'STL'): ?>
                    <p class="m-0 text-center">Streetlight Fee</p>
                     <?php elseif($category == 'GCF'): ?>
                    <p class="m-0 text-center">Grass-Cutting Fee</p>
                    <?php else: ?>
                    <p class="m-0 text-center">Streetlight & Grass-Cutting Fee</p>
                    <?php endif; ?>
                    <?php if ($from == $to): ?>
                       <!--  <p class="m-0 text-center"><?= date("M d, Y g:i A", strtotime($from)) ?></p> -->
                        <p class="m-0 text-center"><?= date("l, F d, Y", strtotime($from)) ?></p>
                    <?php else: ?>
                        <p class="m-0 text-center"><?= date("l, F d, Y", strtotime($from)) . ' - ' . date("l, F d, Y", strtotime($to)) ?></p>
                    <?php endif; ?>
                    <hr>


                <div style="height: 800px; overflow-y: auto;">
                    <table id="car_table" class="table table-hover table-bordered">
                        
                        <thead>
                            <tr>
                              <!--   <th>Date Encoded</th> -->
                               <th style="text-align:center;font-size:10px;">No.</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Transaction Date</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Payment Date</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">CAR # </th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Category</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Account #</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Last Name</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">First Name</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Phase</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Block </th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Lot</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Cash/Online/CDV</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Check</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Total</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Discount</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Reference #</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Encoded by</th>
                               <th class="text-center" style="text-align:center;font-size:10px;">Action</th>
                            </tr>
				        </thead>
                        <tbody>
                            <?php 
                            $cashTotal = 0;
                            $checkTotal = 0;
                            $Total = 0;
                            $discountTotal = 0;
                            
                            $i = 1; // Initialize row counter
                            $query = "SELECT 
                                        x.*, 
                                        RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                                        c_st_pay_date,
                                        CASE 
                                            WHEN c_st_or_no LIKE 'MTF-CAR%' THEN 'GCF'
                                            WHEN c_st_or_no LIKE 'STL-CAR%' THEN 'STL'
                                            ELSE 'Others'
                                        END AS c_pay_type,
                                        c_st_amount_paid,
                                        c_st_or_no,
                                        c_discount,
                                        c_mop,
                                        c_ref_no,
                                        c_check_date,
                                        c_branch,
                                        c_encoded_by,
                                        date_encoded,
                                        date_updated,
                                        SUBSTRING(c_st_or_no FROM 5) AS substring_col  -- Add a column alias for the substring
                                    FROM t_utility_accounts x
                                    JOIN t_utility_payments y ON x.c_account_no = y.c_account_no
                                    WHERE date(y.date_encoded) BETWEEN '$from' AND '$to'
                                        AND (
                                            ('$category' = 'GCF' AND c_st_or_no LIKE 'MTF-CAR%') OR
                                            ('$category' = 'STL' AND c_st_or_no LIKE 'STL-CAR%') OR
                                            ('$category' = 'ALL' AND (
                                                c_st_or_no LIKE 'MTF-CAR%' OR
                                                c_st_or_no LIKE 'STL-CAR%'
                                            ))
                                        )
                                       
                                    UNION
                                    
                                    SELECT 
                                        x.*, 
                                        RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                                        cancelled_date AS c_st_pay_date,
                                        CASE 
                                            WHEN c_st_or_no LIKE 'MTF-CAR%' THEN 'GCF-CANCELLED'
                                            WHEN c_st_or_no LIKE 'STL-CAR%' THEN 'STL-CANCELLED'
                                            ELSE 'Others'
                                        END AS c_pay_type,
                                        CASE 
                                            WHEN c_st_amount_paid != 0 THEN 0  -- Set to zero if payment is canceled
                                            ELSE c_st_amount_paid
                                        END AS c_st_amount_paid,
                                        c_st_or_no,
                                        CASE 
                                            WHEN c_discount != 0 THEN 0  -- Set to zero if payment is canceled
                                            ELSE c_discount
                                        END AS c_discount,
                                        c_mop,
                                        c_ref_no,
                                        c_check_date,
                                        c_branch,
                                        c_encoded_by,
                                        date_encoded,
                                        date_updated,
                                        NULL AS substring_col  -- Add a NULL column alias to match the first SELECT
                                    FROM t_utility_accounts x
                                    JOIN t_cancelled_payments z ON x.c_account_no = z.c_account_no
                                    WHERE date(z.date_encoded) BETWEEN '$from' AND '$to'
                                        AND (
                                            ('$category' = 'GCF' AND c_st_or_no LIKE 'MTF-CAR%') OR
                                            ('$category' = 'STL' AND c_st_or_no LIKE 'STL-CAR%') OR
                                            '$category' = 'ALL' AND (
                                            z.c_st_or_no LIKE 'MTF-CAR%' OR
                                            z.c_st_or_no LIKE 'STL-CAR%'
                                        ))
                                      
                                    ORDER BY c_st_or_no ,substring_col ASC;  -- Use the alias directly in ORDER BY                        
                                                
                               ";

                        
                            
                            $result = odbc_exec($conn2, $query);
                            if (!$result) {
                                die("ODBC query execution failed: " . odbc_errormsg());
                            }
                            $i = 1;
                            while ($row = odbc_fetch_array($result)):
                                $cashTotal += ($row['c_mop'] == '1' or $row['c_mop'] == '3' or $row['c_mop'] == '4' or $row['c_mop'] == '') ? $row['c_st_amount_paid'] : 0;
                                $checkTotal += ($row['c_mop'] == '2') ? $row['c_st_amount_paid'] : 0;
                                $Total += $row['c_st_amount_paid'];
                               /*  $onlineTotal += ($row['c_mop'] == '3') ? $row['c_st_amount_paid'] : 0;
                                $voucherTotal += ($row['c_mop'] == '4') ? $row['c_st_amount_paid'] : 0; */
                                $discountTotal += ($row['c_discount']) ? $row['c_discount'] : 0;
                            ?>
                            <tr>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?= $i++ ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?= date("m/d/Y", strtotime($row['date_encoded'])) ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?= date("m/d/Y", strtotime($row['c_st_pay_date'])) ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['st_or_no_clear'] ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;" <?php echo (strpos($row['c_pay_type'], 'CANCELLED') !== false) ? 'color: red;' : ''; ?>><?php echo $row['c_pay_type'] ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['c_account_no'] ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['c_last_name'] ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['c_first_name'] ?></td>
                                <?php $phase = "SELECT * FROM t_projects where c_code = ".$row['c_site'];

                                $get_phase = odbc_exec($conn2, $phase);

                                    if (!$result) {
                                        die("ODBC query execution failed: " . odbc_errormsg());
                                    }
                                    // Fetch and display the results
                                    while ($row2 = odbc_fetch_array($get_phase)) {
                                        $acronym = $row2['c_acronym'];
                                    }
                                ?>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $acronym ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['c_block'] ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['c_lot'] ?></td>
                                <td class="text-right" style="text-align:center;font-size:10px;"><?php echo ($row['c_mop'] == '1' or $row['c_mop'] == '3' or $row['c_mop'] == '4' or $row['c_mop'] == '') ? format_num($row['c_st_amount_paid']) : ''; ?></td>
                                <td class="text-right" style="text-align:center;font-size:10px;"><?php echo ($row['c_mop'] == '2') ? format_num($row['c_st_amount_paid']) : ''; ?></td>
                                <td class="text-right" style="text-align:center;font-size:10px;"><?php echo format_num($row['c_st_amount_paid']) ?></td>
                                <td class="text-right" style="text-align:center;font-size:10px;"><?php echo format_num($row['c_discount']) ?></td>
                                <td class="text-center" style="text-align:center;font-size:10px;"><?php echo $row['c_ref_no'] ?></td>
                                
                                <td class="text-center" style="text-align:center;font-size:10px;">
                                
                                <?php 
                                    $query444 = " SELECT * FROM tblemployees where emp_id ='".$row['c_encoded_by']."'";
                                    $result2 = $conn->query($query444);
                                    if ($result2) {
                                        $row3 = $result2->fetch_assoc();
                                        $usr = $row3['FirstName'] . ' ' . $row3['LastName'];
                                    } else {
                                        echo "Error: " . $conn->error;
                                    }
                                echo $usr ?></td>
                        
                                <?php
                                    // Set the timezone to Philippine Time
                                    date_default_timezone_set('Asia/Manila');

                                    // Get the current date and time
                                    $current_datetime = date("m/d/Y H:i:s", strtotime($row['date_encoded']));

                                    // Get the current date and time
                                    $current_datetime = new DateTime();

                                    // Set the time to 11 AM today
                                    $today_11am = clone $current_datetime;
                                    $today_11am->setTime(11, 0, 0);

                                    // Check if the current time is 11 AM or later today
                                    $is_past_11am_today = $current_datetime >= $today_11am;

                                    // Get the date_encoded from the $row variable
                                    $date_encoded = new DateTime($row['date_encoded']);

                                    // Check if the date_encoded is today
                                    $is_today = $date_encoded->format('Y-m-d') === $current_datetime->format('Y-m-d');
                                    ?>
                                    <?php if (!$is_past_11am_today || $is_today): ?>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">    
                                                <a class="dropdown-item edit_data exclude-copy" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" id ="<?php echo $row['c_account_no'] ?>" ><i class="dw dw-edit2"></i> Edit</a>
                                                <a class="dropdown-item delete_data exclude-copy" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" id="<?php echo $row['c_account_no'] ?>" ><i class="dw dw-delete-3"></i> Delete/Cancelled</a>
                                                <a class="dropdown-item bounce_check exclude-copy" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" id ="<?php echo $row['c_account_no'] ?>" ><i class="dw dw-return"></i> Bounce Check</a>
                                            </div>
                                        </div>
                                    
                                    <td>
                                    <?php else: ?>
                                    <td>
                                        <div>
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">    
                                                <a class="dropdown-item bounce_check exclude-copy" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" id ="<?php echo $row['c_account_no'] ?>" ><i class="dw dw-return"></i> Bounce Check</a>
                                            </div>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                    
					        <?php endwhile; ?>
                            <tr>
                                <?php for ($i = 0; $i < 11; $i++) : ?>
                                    <td class="" ></td>
                                <?php endfor; ?>
                                <td class="text-right"><?= format_num($cashTotal) ?></td>
                                <td class="text-right"><?= format_num($checkTotal) ?></td>
                                <td class="text-right"><?= format_num($Total) ?></td>
                              
                                <td class="text-right"><?= format_num($discountTotal) ?></td>
                                <?php for ($i = 0; $i < 3; $i++) : ?>
                                    <td class="" ></td>
                                <?php endfor; ?>
                            </tr> 

                            <?php for ($i = 0; $i < 18; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:12px;" ></td>
                            <?php endfor; ?>
                                
                            <?php
                            $grandTotal = "SELECT 
                                        branch,
                                        SUM(CASE WHEN c_mop = 2 THEN c_st_amount_paid ELSE 0 END) AS subtotal_check,
                                        SUM(CASE WHEN c_mop = 3 THEN c_st_amount_paid ELSE 0 END) AS subtotal_online,
                                        SUM(CASE WHEN c_mop = 4 THEN c_st_amount_paid ELSE 0 END) AS subtotal_voucher,
                                        SUM(CASE WHEN c_mop IN (3, 4) THEN c_st_amount_paid ELSE 0 END) AS subtotal_online_cdv,
                                        '' AS branch_total 
                                    FROM
                                        (
                                            SELECT
                                                y.c_branch AS branch,
                                                y.c_st_amount_paid,
                                                y.c_mop
                                            FROM
                                                t_utility_accounts x
                                                JOIN t_utility_payments y ON x.c_account_no = y.c_account_no
                                            WHERE
                                                (
                                                    ('$category' = 'GCF' AND c_st_or_no LIKE 'MTF-CAR%')
                                                    OR ('$category' = 'STL' AND c_st_or_no LIKE 'STL-CAR%')
                                                    OR ('$category' = 'ALL' AND (c_st_or_no LIKE 'MTF-CAR%' OR c_st_or_no LIKE 'STL-CAR%'))
                                                )
                                                 AND date(y.date_encoded) BETWEEN '$from' AND '$to' AND (c_mop = 2 or c_mop = 3 or c_mop = 4)
                                        ) AS Subquery
                                    GROUP BY
                                        branch
                                    ORDER BY
                                        branch;";

                            $result3 = odbc_exec($conn2, $grandTotal);
                            
                            if (!$result3) {
                                die("ODBC query execution failed: " . odbc_errormsg());
                            }

                            $currentDate = null;
                            $btotal = 0;
                            $ctotal = 0;

                            while ($grandTotalRow = odbc_fetch_array($result3)):

                                ?>
                                
                                  
                                <tr>
                                    <?php for ($i = 0; $i < 3; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:10px;" ></td>
                                    <?php endfor; ?>
                                    <td class="text-right" style="text-align:center;font-size:10px;"><?php  echo ($grandTotalRow['branch'] == '') ? 'CDV' : $grandTotalRow['branch'] ?></td>
                                    <?php for ($i = 0; $i < 7; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:10px;" ></td>
                                    <?php endfor; ?>
                                    <td class="text-right" style="text-align:center;font-size:10px;"><?php echo format_num(-$grandTotalRow['subtotal_online_cdv']) ?></td>
                                    <td class="text-right" style="text-align:center;font-size:10px;"></td>
                                    <td class="text-right" style="text-align:center;font-size:10px;"><?php echo format_num(-($grandTotalRow['subtotal_online_cdv'])) ?></td>
                                    <?php for ($i = 0; $i < 4; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:10px;" ></td>
                                    <?php endfor; ?>
                                </tr>
                                

                                <?php
            
                                $btotal += $grandTotalRow['subtotal_online_cdv'];
                                $ctotal += $grandTotalRow['subtotal_check'];
                               
                            endwhile;

                            $gtotal = $cashTotal - $btotal;
                            $htotal = $checkTotal ;
                            $supertotal = $gtotal + $htotal;  
                           # $htotal = $checkTotal  $ctotal;
                            #$supertotal = $Total - ($btotal + $ctotal);
                            ?>
                            <tr>
                                <?php for ($i = 0; $i < 3; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:10px;" ></td>
                                <?php endfor; ?>
                                <td class="text-right" style="text-align:center;font-size:10px;">TOTAL CASH</td>
                                <?php for ($i = 0; $i < 7; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:10px;" ></td>
                                    <?php endfor; ?>
                                <td class="text-right"><?php echo  format_num($gtotal)  ?></td>
                                <td class="text-right"><?php echo  format_num($htotal)  ?></td>
                                <td class="text-right"><?php echo  format_num($supertotal)  ?></td>
                                <td class="text-right"><?php echo  format_num($discountTotal)  ?></td>
                                <?php for ($i = 0; $i < 3; $i++) : ?>
                                        <td class="text-right" style="text-align:center;font-size:10px;" ></td>
                                <?php endfor; ?>
                            </tr>


                            
                        </tbody>
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
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [':visible:not(.exclude-copy)']
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [':visible:not(.exclude-copy)']
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [':visible:not(.exclude-copy)']
                }
            }
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
            location.href="<?php echo base_url ?>heads/?page=report/car_logs_v2&"+$(this).serialize();


           
        })
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone();
            var _p = $('#outprint').clone();
            var el = $('<div>')
            _h.find('title').text('Cash Acknowledgement Receipt - Print View')
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

<script>
      $(document).ready(function(){
       

        $('.edit_data').click(function(){
			uni_modal("Update Payment Details","payments/payment_edit.php?id="+$(this).attr('id')+ "&data-car=" + $(this).attr('data-car'),'mid-large')
		})
        $('.bounce_check').click(function(){
			uni_modal("Bounce Check","payments/payment_bounce.php?id="+$(this).attr('id')+ "&data-car=" + $(this).attr('data-car'),'mid-large')
		})
		$('.delete_data').click(function(){
        	uni_modal("Cancel Payment","report/cancel_payment.php?id="+$(this).attr('id')+ "&data-car=" + $(this).attr('data-car'),'mid-large')
	
			//_conf("Are you sure to delete '<b>"+$(this).attr('data-car')+"</b>' from CAR List permanently?","delete_payment",["'" + $(this).attr('data-car') + "'"])
		})

        

      })

      function delete_payment($id){
            start_loader();
        
            $.ajax({
                url:_base_url_+"classes/Master.php?f=delete_payment",
                method:"POST",
                data:{id: $id},
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
