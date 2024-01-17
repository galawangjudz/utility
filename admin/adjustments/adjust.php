

<?php
if (isset($_GET['search'])) {
    $l_find = $_GET['search']; 
}
//$l_find = isset($_GET["search"]) ? $_GET["search"] : '';

?>


<div class="main-container"> 
	<div class="">
		<div class="pd-ltr-20">
			
			<div class="card-box pd-20 height-1400-p mb-30">
				<form action="" id="filter">
					<input type="hidden" id="page" name="page" value="adjustments/adjust" class="form-control form-control-sm rounded-0">
					<div class="col-md-4 form-group">
						<label for="search" class="control-label">Search Account: </label>
						<input type="text" id="search" name="search" class="form-control">
                    </div>
					<div class="col-md-4 form-group">
						<button class="btn btn-primary"><i class="dw dw-search"></i> Search</button>
                    </div>
                </form>		
			</div>

			<div class="card-box mb-30">
				<div class="pd-20">
						<h2 class="text-blue h4">Payments of Account</h2>
                        <?php 
                        if (isset($_GET['search'])) {
                            $l_find = $_GET['search']; 
                        ?>
                    
                        <a class="btn btn-primary btn-lg btn-primary btn-flat border-primary ml-auto adjust_bill" id="<?php echo $l_find ?>" href="javascript:void(0)">
                            <i class="fa fa-plus"></i> Adjustment BIll
                        </a>
                        <a class="btn btn-primary btn-lg btn-primary btn-flat border-primary ml-auto adjust_payment" id="<?php echo $l_find ?>" href="javascript:void(0)">
                            <i class="fa fa-share-square"></i> Transfer Payment
                        </a>
                        <?php }
                        ?>
				</div>
                
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
                    <colgroup>
                <col width="10%">
                <col width="15%">
                <col width="15%">
                <col width="5%">
                <col width="20%">
                <col width="20%">
                <col width="5%">
                </colgroup>
						<thead>
							<tr>
                                <th class="table-plus">Account No</th>
                                <th>Pay Date</th>
                                <th>OR #</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Adjustment Description</th>
                                <th>Notes</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
                   
							<tr>
								
                            <?php 
							
								if(isset($_GET['search']) && $_GET['search']){
									$sql = "SELECT DISTINCT
                                    p.c_account_no, 
                                    p.c_st_pay_date, 
                                    p.c_st_or_no, 
                                    p.c_st_amount_paid, 
                                    p.c_discount,
                                    a.c_adjustment_type,
                                    a.c_notes  -- Add other columns from t_adjustments as needed
                                FROM 
                                    t_utility_payments p
                                LEFT JOIN 
                                    t_adjustment a ON p.c_st_or_no = a.c_or_no
                                WHERE 
                                    p.c_account_no = '%s'
                                ORDER BY 
                                    p.c_st_pay_date DESC";
									$sql = sprintf($sql, $l_find);
								}else{
                                    $sql = "SELECT DISTINCT
                                    p.c_account_no, 
                                    p.c_st_pay_date, 
                                    p.c_st_or_no, 
                                    p.c_st_amount_paid, 
                                    p.c_discount,
                                    a.c_adjustment_type,
                                    a.c_notes  -- Add other columns from t_adjustments as needed
                                FROM 
                                    t_utility_payments p
                                LEFT JOIN 
                                    t_adjustment a ON p.c_st_or_no = a.c_or_no
                                WHERE 
                                    p.c_account_no = '1'
                                ORDER BY 
                                    p.c_st_pay_date DESC";

									}
                               
			                    
                                $qry = odbc_exec($conn2,$sql);


                                while(odbc_fetch_row($qry)):
									$acc = odbc_result($qry, "c_account_no");
                                    $pay_date = odbc_result($qry, "c_st_pay_date");
                                    $or_no = odbc_result($qry, "c_st_or_no");
                                    $amount = odbc_result($qry, "c_st_amount_paid"); 
                                    $discount = odbc_result($qry, "c_discount");
                                    $adjust_type = odbc_result($qry, "c_adjustment_type");
                                    $notes = odbc_result($qry, "c_notes");
               
                            ?>
                                
                                <tr>
                                    <td class=""><?php echo $acc; ?></td>
                                    <td class=""><?php echo $pay_date; ?></td>
                                    <td class=""><?php echo $or_no ?></td>
                                    <td class=""><?php echo $amount ?></td>
                                    <td class=""><?php echo $discount ?></td>
                                    <td class=""><?php echo $adjust_type ?></td>
                                    <td class=""><?php echo $notes ?></td>
                            
                        
                                    <td>
                                        <a class="btn btn-link delete_data" data-paydate="<?php echo $pay_date ?>" data-or-no="<?php echo $or_no ?>" data-id="<?php echo $acc ?>" href="javascript:void(0)" role="button">
                                        <i class="dw dw-delete-3"></i>
                                    </a>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
						</tbody>
					</table>
			   </div>
			</div>
			
		</div>
	</div>

    <script>

    $(document).ready(function(){

        $('.delete_data').click(function(){
            _conf("Are you sure to delete from Payment List permanently?","delete_adjustment",["'" +$(this).attr('data-paydate')+ "'","'" + $(this).attr('data-or-no') + "'","'" + $(this).attr('data-id') + "'"])
        })

        $('.add_bill').click(function(){
			uni_modal("Add New Bill","adjustments/manage_bill.php?id="+$(this).attr('id'))
		})

        $('.adjust_bill').click(function(){
			uni_modal("Adjustment Bill","adjustments/adjust_bill.php?id="+$(this).attr('id'))
		})

        $('.adjust_payment').click(function(){
			uni_modal("Adjustment Payment","adjustments/adjust_payment.php?id="+$(this).attr('id'))
		})
    });
    function delete_adjustment($paydate,$or_no,$id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_adjustment",
            method:"POST",
            data:{paydate : $paydate, or_no: $or_no, id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert("An error occured.");
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    alert(resp.msg);
                    location.reload();
                }else{
                    alert("An error occured.");
                    end_loader();
                }
            }
        })
    }
</script>