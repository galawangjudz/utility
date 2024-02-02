
<?php

function format_num($number){
       return number_format($number,2);
}


$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d");
$to = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");

$category = isset($_GET['category']) ? $_GET['category'] : 'ALL';

?>


<div class="main-container">
	
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
                    <hr>

                <div style="height: 500px; overflow-y: auto;">
                    <table id="car_table" class="table table-hover table-bordered">
                    <thead>
                            <tr>
                                <th class="text-center">TRANSACTION DATE</th>
                                <th class="text-center">TOTAL CASH</th>
                                <th class="text-center">TOTAL CHECK</th>
                                <th class="text-center">TOTAL ONLINE</th>
                                <th class="text-center">TOTAL</th>
                            </tr>
                        </thead>
                    <?php
                        $grandTotal= "SELECT
                        date(y.date_encoded) AS Transaction_Date,
                        SUM(CASE WHEN c_mop = '1' OR c_mop IS NULL THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Cash,
                        SUM(CASE WHEN c_mop = '2' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Check,
                        SUM(CASE WHEN c_mop = '3' THEN c_st_amount_paid ELSE 0 END) AS Grand_Total_Online,
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
                        ))
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
                        <td class="text-right"><?php echo $grandTotalRow['transaction_date']?></td>
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
