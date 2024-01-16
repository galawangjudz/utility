<?php

function format_num($number){
       return number_format($number,2);
}

// Set default date and time range
$defaultFromTime = '08:00:00';
$defaultToTime = '16:00:00';

$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d"). " " . $defaultFromTime;
$to = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d"). " " . $defaultToTime;

$category = isset($_GET['category']) ? $_GET['category'] : 'ALL';
?>


<div class="main-container">
		<div class="pd-ltr-20">
			<div class="title pb-20">
				<h2 class="h3 mb-0">Cash Acknowledgement Receipts</h2>
			</div>
		
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
            <h4 class="text-muted">Filter Date</h4>
            <form action="" id="filter">
            <div class="row align-items-end">
                <div class="col-md-2 form-group">
                    <label for="from" class="control-label">Date From</label>
                    <input type="datetime-local" id="from" name="from" value="<?= $from ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-2 form-group">
                    <label for="to" class="control-label">Date To</label>
                    <input type="datetime-local" id="to" name="to" value="<?= $to ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-3 form-group">
                    <label for="category" class="control-label">Category</label>
                    <select name="category" id="category" class="form-control form-control-sm rounded-0" required>
                        <option value="ALL" <?php echo ($category == 'ALL') ? 'selected' : ''; ?>>ALL</option>
                        <option value="GCF" <?php echo ($category == 'GCF') ? 'selected' : ''; ?>>Grass-Cutting</option>
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
                    
                    <h4 class="text-center"><b>List Of CAR</b></h4>
                    <?php if($category == 'STL'): ?>
                    <p class="m-0 text-center">Streetlight Fee</p>
                     <?php elseif($category == 'GCF'): ?>
                    <p class="m-0 text-center">Grass-Cutting Fee</p>
                    <?php else: ?>
                    <p class="m-0 text-center">Streetlight & Grass-Cutting Fee</p>
                    <?php endif; ?>
                    <?php if ($from == $to): ?>
                        <p class="m-0 text-center"><?= date("M d, Y g:i A", strtotime($from)) ?></p>
                    <?php else: ?>
                        <p class="m-0 text-center"><?= date("M d, Y g:i A", strtotime($from)) . ' - ' . date("M d, Y g:i A", strtotime($to)) ?></p>
                    <?php endif; ?>
                    <hr>

                    <table class="table table-hover table-bordered table-responsive">
                <!-- <colgroup>
					<col width="5%">
					<col width="10%">
					<col width="5%">
                    <col width="15%">
                    <col width="20%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
				</colgroup> -->
				<thead>
					<tr>
                        <th>Date Encoded</th>
						<th>Pay Date</th>
                        <th>CAR # </th>
						<th>Category</th>
                        <th>Account #</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Phase</th>
                        <th>Block </th>
                        <th>Lot</th>
                        <th>Cash</th>
                        <th>Check</th>
                        <th>Gcash/Online</th>
                        <th>Discount</th>
                        <th>Deposit</th>
                        <th>Reference #</th>
                        <th>Encoded by</th>
                        <th>Action</th>
					</tr>
				</thead>
                <tbody>
					<?php 
					$i = 1;
                    $query = "SELECT 
                            x.*, 
                            RIGHT(c_st_or_no, LENGTH(c_st_or_no) - 4) AS st_or_no_clear,
                            c_st_pay_date,
                            CASE 
                                WHEN c_st_or_no LIKE 'MTF-CAR%' THEN 'GCF Payment'
                                WHEN c_st_or_no LIKE 'STL-CAR%' THEN 'STL Payment'
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
                            date_updated
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
                        ORDER BY date(y.date_encoded) ASC";
                    $result = odbc_exec($conn2, $query);
                    if (!$result) {
                        die("ODBC query execution failed: " . odbc_errormsg());
                    }
                    while ($row = odbc_fetch_array($result)):
					?>
					<tr>
                        <td class="text-center"><?= date("M d, Y g:i A", strtotime($row['date_encoded'])) ?></td>
						<td class="text-center"><?= date("M d, Y", strtotime($row['c_st_pay_date'])) ?></td>
                        <td class="text-center"><?php echo $row['st_or_no_clear'] ?></td>
                        <td class="text-center"><?php echo $row['c_pay_type'] ?></td>
						<td class="text-center"><?php echo $row['c_account_no'] ?></td>
                        <td class="text-center"><?php echo $row['c_last_name'] ?></td>
                        <td class="text-center"><?php echo $row['c_first_name'] ?></td>
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
                        <td class="text-center"><?php echo $acronym ?></td>
                        <td class="text-center"><?php echo $row['c_block'] ?></td>
                        <td class="text-center"><?php echo $row['c_lot'] ?></td>
                        <td class="text-right"><?php echo ($row['c_mop'] == '1' or $row['c_mop'] == '') ? format_num($row['c_st_amount_paid']) : ''; ?></td>
                        <td class="text-right"><?php echo ($row['c_mop'] == '2') ? format_num($row['c_st_amount_paid']) : ''; ?></td>
                        <td class="text-right"><?php echo ($row['c_mop'] == '3') ? format_num($row['c_st_amount_paid']) : ''; ?></td>
                        <td class="text-right"><?php echo format_num($row['c_discount']) ?></td>
                        <td class="text-center"><?php echo $row['c_branch'] . ' - ' . $row['c_check_date']; ?></td>
                        <td class="text-center"><?php echo $row['c_ref_no'] ?></td>
                        <td class="text-center"><?php echo $row['c_encoded_by'] ?></td>
                   
                        <?php $query = "SELECT * FROM t_utility_logs"?>
                    <td>
                        <div class="dropdown">
                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="dw dw-more"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">    
                        <a class="dropdown-item edit_data" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" id ="<?php echo $row['c_account_no'] ?>"><i class="dw dw-edit2"></i> Edit</a>
                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" data-id="<?php echo $row['c_account_no'] ?>"><i class="dw dw-delete-3"></i> Delete</a>
                        </div>
                     </div>
                     </td>
					</tr>
					<?php endwhile; ?>

                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">TOTAL CASH</th>
                            <th class="text-center">TOTAL CHECK</th>
                            <th class="text-center">TOTAL ONLINE</th>
                            <th class="text-center">TOTAL</th>
                        </tr>
                    </thead>
                    <?php

                        $grandTotal= "SELECT
                            SUM(CASE WHEN c_mop = '1' or c_mop is NULL THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Cash,
                            SUM(CASE WHEN c_mop = '2' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Check,
                            SUM(CASE WHEN c_mop = '3' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Online,
                            SUM(c_st_amount_paid) AS Grand_Total
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
                            )";
                       $result3 = odbc_exec($conn2, $grandTotal);
                       if (!$result3) {
                        die("ODBC query execution failed: " . odbc_errormsg());
                       }
                       while ($grandTotalRow = odbc_fetch_array($result3)):
                        ?>
                        <tr>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_cash'])?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_check']) ?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_online']) ?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total']) ?></td>
 
                        </tr>
                        <?php 
                       endwhile;

                        ?>
                    </table>

                    
                    
                   
       
</div>
</div>
                
</div>


<style>

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
            location.href="<?php echo base_url ?>admin/?page=report/car_logs&"+$(this).serialize();
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
		$('.delete_data').click(function(){
			_conf("Are you sure to delete '<b>"+$(this).attr('data-car')+"</b>' from CAR List permanently?","delete_payment",["'" + $(this).attr('data-car') + "'"])
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
