
<?php

function format_num($number){
       return number_format($number,2);
}

$minDate = date("Y-m-d", strtotime("-7 days")); // Calculate 7 days before the current date

$from = isset($_GET['from']) && strtotime($_GET['from']) >= strtotime($minDate) ? $_GET['from'] : $minDate;
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
                        <option value="GCF" <?php echo ($category == 'GCF') ? 'selected' : ''; ?>>GRASS-CUTTING</option>
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
                    <h5 class="text-center"><b>SUMMARY REPORT PER BANK </b></h5>
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
                  
                    <?php
                     
                        $grandTotal = "SELECT 
                                        transaction_date,
                                        branch,
                                        SUM(c_st_amount_paid) AS subTotal
                                    FROM
                                        (
                                            SELECT
                                                date(y.date_encoded) AS Transaction_Date,
                                                y.c_branch AS Branch,
                                                y.c_st_amount_paid
                                            FROM
                                                t_utility_accounts x
                                                JOIN t_utility_payments y ON x.c_account_no = y.c_account_no
                                            WHERE
                                                ('$category' = 'GCF' AND c_st_or_no LIKE 'MTF-CAR%') OR
                                                ('$category' = 'STL' AND c_st_or_no LIKE 'STL-CAR%') OR
                                                ('$category' = 'ALL' AND (
                                                        c_st_or_no LIKE 'MTF-CAR%' OR
                                                        c_st_or_no LIKE 'STL-CAR%'
                                                    )
                                                ) AND date(y.date_encoded) BETWEEN '$from' AND '$to' AND c_mop = 3
                                        ) as Subquery
                                    GROUP BY
                                        transaction_date, branch 
                                    ORDER BY
                                        transaction_date DESC, branch";
                        
                        $result3 = odbc_exec($conn2, $grandTotal);
?>

<div style="height: 500px; overflow-y: auto;">
    <table id="car_table" class="table table-hover table-bordered">
        <thead>
            <tr>
                <th class="text-center">TRANSACTION DATE</th>
                <th class="text-center">BDO</th>
                <th class="text-center">BOC</th>
                <th class="text-center">BPI</th>
                <th class="text-center">CBS</th>
                <th class="text-center">MBTC</th>
                <th class="text-center">PBB</th>
                <th class="text-center">PVB</th>
                <th class="text-center">RCBC</th>
                <th class="text-center">ROBBank</th>
                <th class="text-center">SBC</th>
                <th class="text-center">UB</th>
                <th class="text-center">UCBP</th>
                <th class="text-center">Unknown Bank</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <?php
     
        if (!$result3) {
            die("ODBC query execution failed: " . odbc_errormsg());
        }

        $currentDate = null;
        $totals = array_fill_keys(['BDO', 'BOC', 'BPI', 'CBS', 'MBTC', 'PBB', 'PVB', 'RCBC', 'ROBBank', 'SBC', 'UB', 'UCBP', '', 'Total'], 0);

        while ($grandTotalRow = odbc_fetch_array($result3)):
            // If it's a new date, print the previous row and reset the totals
            if ($currentDate !== $grandTotalRow['transaction_date']) {
                if ($currentDate !== null) {
                    printRow($currentDate, $totals);
                }

                $currentDate = $grandTotalRow['transaction_date'];
                $totals = array_fill_keys(['BDO', 'BOC', 'BPI', 'CBS', 'MBTC', 'PBB', 'PVB', 'RCBC', 'ROBBank', 'SBC', 'UB', 'UCBP','', 'Total'], 0);
            }

            // Accumulate the subtotals for each branch and the total
            $totals[$grandTotalRow['branch']] += $grandTotalRow['subtotal'];
            $totals['Total'] += $grandTotalRow['subtotal'];
        endwhile;

        // Print the last row
        if ($currentDate !== null) {
            printRow($currentDate, $totals);
        }

        function printRow($date, $totals) {
            echo '<tr>';
            echo '<td class="text-center">' . $date . '</td>';
            foreach ($totals as $value) {
                echo '<td class="text-right">' . format_num($value) . '</td>';
            }
            echo '</tr>';
        }
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
            location.href="<?php echo base_url ?>admin/?page=report/summary_per_bank&"+$(this).serialize();
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
