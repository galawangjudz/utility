
<?php

function format_num($number){
       return number_format($number,2);
}


$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d",strtotime(date('Y-m-d')." -1 week"));
$to = isset($_GET['to']) && strtotime($_GET['to']) >= strtotime($minDate) ? $_GET['to'] : date("Y-m-d");

$category = isset($_GET['category']) ? $_GET['category'] : 'ALL';

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
                        <option value="GCF" <?php echo ($category == 'GCF') ? 'selected' : ''; ?>>GRASS CONTROL</option>
                        <option value="STL" <?php echo ($category == 'STL') ? 'selected' : ''; ?>>STREETLIGHT</option>
                    </select>
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
                    <h5 class="text-center"><b>CASH ACKNOWLEDGEMENT RECEIPT REPORT</b></h5>
                    <h5 class="text-center"><b>SUMMARY REPORT </b></h5>
                    <?php if($category == 'STL'): ?>
                    <p class="m-0 text-center">Streetlight Fee</p>
                     <?php elseif($category == 'GCF'): ?>
                    <p class="m-0 text-center">Grass Control Fee</p>
                    <?php else: ?>
                    <p class="m-0 text-center">Streetlight & Grass Control Fee</p>
                    <?php endif; ?>
                    <?php if ($from == $to): ?>
                       <!--  <p class="m-0 text-center"><?= date("M d, Y g:i A", strtotime($from)) ?></p> -->
                        <p class="m-0 text-center"><?= date("l, F d, Y", strtotime($from)) ?></p>
                    <?php else: ?>
                        <p class="m-0 text-center"><?= date("l, F d, Y", strtotime($from)) . ' - ' . date("l, F d, Y", strtotime($to)) ?></p>
                    <?php endif; ?>
                    <hr>
                  

                <div style="height: 500px; overflow-y: auto;">
                    <table id="car_table" class="table table-hover table-bordered">
                    <thead>
                            <tr>
                                <th class="text-center">TRANSACTION DATE</th>
                                <th class="text-center">TOTAL CASH</th>
                                <th class="text-center">TOTAL CHECK</th>
                                <th class="text-center">TOTAL ONLINE</th>
                                <th class="text-center">TOTAL VOUCHER</th>
                                <th class="text-center">TOTAL</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                    <?php
                        $grandTotal= "SELECT
                        date(y.date_encoded) AS Transaction_Date,
                        SUM(CASE WHEN c_mop = '1' OR c_mop IS NULL THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Cash,
                        SUM(CASE WHEN c_mop = '2' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Check,
                        SUM(CASE WHEN c_mop = '3' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Online,
                        SUM(CASE WHEN c_mop = '4' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Voucher,
                        SUM(c_st_amount_paid) AS Grand_Total
                    FROM
                        t_utility_accounts x
                        JOIN t_utility_payments y ON x.c_account_no = y.c_account_no
                    WHERE
                        ('$category' = 'GCF' AND c_st_or_no LIKE 'MTF-CAR%') OR
                        ('$category' = 'STL' AND c_st_or_no LIKE 'STL-CAR%') OR
                        ('$category' = 'ALL' AND (
                            c_st_or_no LIKE 'MTF-CAR%' OR
                            c_st_or_no LIKE 'STL-CAR%'
                        )) AND date(y.date_encoded) BETWEEN '$from' AND '$to'
                    GROUP BY
                        date(y.date_encoded) ORDER BY
                     date(y.date_encoded) DESC";
                       $result3 = odbc_exec($conn2, $grandTotal);
                       if (!$result3) {
                        die("ODBC query execution failed: " . odbc_errormsg());
                       }
                       while ($grandTotalRow = odbc_fetch_array($result3)):
                        ?>
                        <tr>
                        <td class="text-center"><?php echo $grandTotalRow['transaction_date']?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_cash'])?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_check']) ?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_online']) ?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total_voucher']) ?></td>
                        <td class="text-right"><?php echo format_num($grandTotalRow['grand_total']) ?></td>

                        <?php
                            $trans_date = $grandTotalRow['transaction_date'];
                            $report = "SELECT * FROM summary_report WHERE transaction_date = ?";
                            $result2 = odbc_prepare($conn2, $report);

                            if ($result2) {
                                // Bind the parameter and execute the query
                                odbc_execute($result2, array($trans_date));

                                // Fetch a row from the result set
                                $summaryRow = odbc_fetch_array($result2);

                                if ($summaryRow) {
                                    echo '<td class="text-center"><span class="badge badge-success border px-3 rounded-pill">Submitted</span></td>';
                                } else {
                                    echo '<td class="text-center"><span class="badge badge-danger border px-3 rounded-pill">Draft</span></td>';
                                }
                            } else {
                                echo '<td class="text-center"><span class="badge badge-warning border px-3 rounded-pill">Error</span></td>';
                            }
                            ?>
                        <td class="text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-more"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">    
                                            <a class="dropdown-item submit_data" 
                                            id ="<?php echo $grandTotalRow['transaction_date']?>" 
                                            cash ="<?php echo sprintf('%.2f', $grandTotalRow['grand_total_cash'])?>" 
                                            check ="<?php echo sprintf('%.2f', $grandTotalRow['grand_total_check'])?>" 
                                            online ="<?php echo sprintf('%.2f', $grandTotalRow['grand_total_online'])?>" 
                                            voucher ="<?php echo sprintf('%.2f', $grandTotalRow['grand_total_voucher'])?>"
                                            total ="<?php echo sprintf('%.2f', $grandTotalRow['grand_total'])?>"
                                            href="javascript:void(0)" ><i class="dw dw-edit2"></i>SUBMIT</a>
                                            <a class="dropdown-item " href="javascript:void(0)"><i class="dw dw-delete-3"></i>Undo</a>
                                        </div>
                                    </div>
                                </td>
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
            location.href="<?php echo base_url ?>admin/?page=report/summary_report&"+$(this).serialize();
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
