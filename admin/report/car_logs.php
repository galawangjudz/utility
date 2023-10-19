<?php

function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}
$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d",strtotime(date('Y-m-d')." -1 week"));
$to = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");
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
                <div class="col-md-4 form-group">
                    <label for="from" class="control-label">Date From</label>
                    <input type="date" id="from" name="from" value="<?= $from ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-4 form-group">
                    <label for="to" class="control-label">Date To</label>
                    <input type="date" id="to" name="to" value="<?= $to ?>" class="form-control form-control-sm rounded-0">
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
                    <?php if($from == $to): ?>
                    <p class="m-0 text-center"><?= date("M d, Y" , strtotime($from)) ?></p>
                    <?php else: ?>
                    <p class="m-0 text-center"><?= date("M d, Y" , strtotime($from)). ' - '.date("M d, Y" , strtotime($to)) ?></p>
                    <?php endif; ?>
                    <hr>

                    <table class="table table-hover table-bordered">
                <colgroup>
					<col width="5%">
					<col width="5%">
					<col width="5%">
                    <col width="10%">
                    <col width="20%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
				</colgroup>
				<thead>
					<tr>
                        <th>No</th>
						<th>Pay Date</th>
						<th>Account No</th>
                        <th>Location</th>
                        <th>Full Name</th>
                        <th>CAR No.</th>
                        <th>Amount Paid</th>
                        <th>Action</th>
					</tr>
				</thead>
                <tbody>
					<?php 
					$i = 1;
                    $query = "SELECT * FROM t_utility_accounts x 
                    JOIN t_utility_payments y ON x.c_account_no = y.c_account_no 
                    WHERE date(y.c_st_pay_date) BETWEEN '$from' AND '$to' 
                    ORDER BY date(y.c_st_pay_date) ASC";
                    $result = odbc_exec($conn2, $query);
                    if (!$result) {
                        die("ODBC query execution failed: " . odbc_errormsg());
                    }
                    while ($row = odbc_fetch_array($result)):
					?>
					<tr>
                        <td class="text-center"><?php echo $i++ ?></td>
						<td class="text-center"><?= date("M d, Y", strtotime($row['c_st_pay_date'])) ?></td>
						<td class="text-center"><?php echo $row['c_account_no'] ?></td>
                        <td class="text-center"><?php echo $row['c_location'] ?></td>
                        <td class="text-center"><?php echo $row['c_first_name'] . ' ' . $row['c_last_name'] ?></td>
                        <td class="text-center"><?php echo $row['c_st_or_no'] ?></td>
                        <td class="text-center"><?php echo $row['c_st_amount_paid'] ?></td>
                    <td>
                        <div class="dropdown">
                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="dw dw-more"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">    
                        <a class="dropdown-item edit_data" href="javascript:void(0)" id ="<?php echo $row['c_st_or_no'] ?>"><i class="dw dw-edit2"></i> Edit</a>
                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-car ="<?php echo $row['c_st_or_no'] ?>" data-id="<?php echo $row['c_account_no'] ?>"><i class="dw dw-delete-3"></i> Delete</a>
                        </div>
                     </div>
                     </td>
					</tr>
					<?php endwhile; ?>
       
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
            location.href="./?page=report/car_logs&"+$(this).serialize();
        })
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone();
            var _p = $('#outprint').clone();
            var el = $('<div>')
            _h.find('title').text('Cash Acknowledgement Receipt - Print View')
            _h.append('<style>html,body{ min-height: unset !important;}</style>')
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
			uni_modal("Update Payment Details","payments/payment_edit.php?id="+$(this).attr('id'),'mid-large')
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
