

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
					<input type="hidden" id="page" name="page" value="adjustment/add_bill" class="form-control form-control-sm rounded-0">
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
						<h2 class="text-blue h4">Bill of Accounts</h2>
                        <?php 
                        if (isset($_GET['search'])) {
                            $l_find = $_GET['search']; 
                        ?>
                        <a class="btn btn-primary btn-lg btn-primary btn-flat border-primary ml-auto add_bill" id="<?php echo $l_find ?>" href="javascript:void(0)">
                            <i class="fa fa-plus"></i> Add New Bill
                        </a>
                      
                        <?php }
                        ?>
				</div>
                
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
                                <th class="table-plus">Start Date</th>
                                <th>End Date</th>
                                <th>Due Date</th>
                                <th>Description</th>
                                <th>Amount Due</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
                   
							<tr>
								
                            <?php 
							
								if(isset($_GET['search']) && $_GET['search']){
									$sql = "SELECT c_account_no, c_start_date, c_end_date, c_due_date, c_bill_type, c_amount_due FROM t_utility_bill WHERE c_account_no = '%s' ORDER BY c_due_date DESC";
									$sql = sprintf($sql, $l_find);
								}else{
									$sql = "SELECT c_account_no, c_start_date, c_end_date, c_due_date, c_bill_type, c_amount_due FROM t_utility_bill WHERE c_account_no = '1' ORDER BY c_due_date DESC";
								}
                               
			                    
                                $qry = odbc_exec($conn2,$sql);


                                while(odbc_fetch_row($qry)):
									$acc = odbc_result($qry, "c_account_no");
                                    $start = odbc_result($qry, "c_start_date");
                                    $end = odbc_result($qry, "c_end_date");
                                    $due = odbc_result($qry, "c_due_date");
                                    $type = odbc_result($qry, "c_bill_type");
                                    $amount = odbc_result($qry, "c_amount_due");
               
                            ?>
                                <tr>
                                    <td class=""><?php echo $start; ?></td>
                                    <td class=""><?php echo $end ?></td>
                                    <td class=""><?php echo $due ?></td>
                                    <td class=""><?php
                                                echo ($type == 'MTF') ? 'GCF Fee' :
                                                    (($type == 'DLQ_MTF') ? 'GCF Surcharge' :
                                                    (($type == 'STL') ? 'STL Fee' :
                                                    (($type == 'DLQ_STL') ? 'STL Surcharge' : $type)));
                                                ?></td>
                                    <td class=""><?php echo $amount ?></td>
                            
                        
                                    <td>
                                        <a class="btn btn-link delete_data" data-date="<?php echo $due ?>" data-type="<?php echo $type ?>" data-id="<?php echo $acc ?>" href="javascript:void(0)" role="button">
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
            _conf("Are you sure to delete from Bill List permanently?","delete_bill",["'" +$(this).attr('data-date')+ "'","'" + $(this).attr('data-type') + "'",$(this).attr('data-id')])
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
    function delete_bill($date,$type,$id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_bill",
            method:"POST",
            data:{date : $date,type: $type,id: $id},
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