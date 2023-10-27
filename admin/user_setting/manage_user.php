<?php
require_once('../../includes/config.php');


if(isset($_GET['id'])){
    $user = $conn->query("SELECT * FROM tblemployees where emp_id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
		}
}

?>
<div class="card card-outline rounded-0 card-maroon">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form method="post" enctype="multipart/form-data" action="<?php echo base_url ?>admin/?page=user_setting">	
			<input type="hidden" name="emp_id" id="emp_id" value="<?php echo isset($meta['emp_id']) ? $meta['emp_id']: '' ?>" required>
				<div class="form-group">
					<label for="name">Employee ID: </label>
					<input type="int" name="empid" id="empid" class="form-control" value="<?php echo isset($meta['emp_id']) ? $meta['emp_id']: '' ?>" required>
				</div>
				<div class="form-group">
					<label for="name">First Name: </label>
					<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['FirstName']) ? $meta['FirstName']: '' ?>" required>
				</div>
				<div class="form-group">
					<label for="name">Last Name: </label>
					<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['LastName']) ? $meta['LastName']: '' ?>" required>
				</div>
				<div class="form-group">
					<label for="username">Email: </label>
					<input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['EmailId']) ? $meta['EmailId']: '' ?>" required  autocomplete="off">
				</div>
				<div class="form-group">
					<label for="username">Contact No: </label>
					<input type="text" name="phone" id="phone" class="form-control" value="<?php echo isset($meta['Phonenumber']) ? $meta['Phonenumber']: '' ?>" required  autocomplete="off">
				</div>
				<div class="form-group">
					<label>Gender</label>
					<select name="gender" class="custom-select form-control" required="true" autocomplete="off">			
						<option value="Male" <?php echo isset($meta['Gender']) && $meta['Gender'] == "Male" ? 'selected': '' ?>> Male</option>
						<option value="Female" <?php echo isset($meta['Gender']) && $meta['Gender'] == "Female" ? 'selected': '' ?>> Female</option>
					</select>
				</div>

				<div class="form-group">
					<label>Status</label>
					<select name="status" class="custom-select form-control" required="true" autocomplete="off">			
						<option value="1" <?php echo isset($meta['Status']) && $meta['Status'] == "1" ? 'selected': '' ?>> Active</option>
						<option value="0" <?php echo isset($meta['Status']) && $meta['Status'] == "0" ? 'selected': '' ?>> Inactive</option>
					</select>
				</div>
				
				<div class="form-group">
					<style>
						select:invalid { color: gray; }
					</style>
					<label class="control-label">Role: </label>
					<select name="role" id="role" class="form-control required">
						<option value="Admin"<?php echo isset($meta['role']) && $meta['role'] == "Admin" ? 'selected': '' ?>>Admin</option>
						<option value="Head"<?php echo isset($meta['role']) && $meta['role'] == "Head" ? 'selected': '' ?>>Head</option>
						<option value="Cashier"<?php echo isset($meta['role']) && $meta['role'] == "Cashier" ? 'selected': '' ?>>Cashier</option>
						<option value="Staff"<?php echo isset($meta['role']) && $meta['role'] == "Staff" ? 'selected': '' ?>>Staff</option>
						</select>
				</div>
				<div class="form-group">
					<style>
						select:invalid { color: gray; }
					</style>
					<label class="control-label">Department: </label>
					<select name="Department" id="Department" class="form-control required">
						<option value="IT"<?php echo isset($meta['Department']) && $meta['Department'] == "IT" ? 'selected': '' ?>> IT</option>
						<option value="PA" <?php echo isset($meta['Department']) && $meta['Department'] == "PA" ? 'selected': '' ?>>Project Admin</option>
						<option value="TSR"<?php echo isset($meta['Department']) && $meta['Department'] == "TSR" ? 'selected': '' ?>>Treasury</option>
						<option value="CSPV"<?php echo isset($meta['Department']) && $meta['Department'] == "CSPV" ? 'selected': '' ?>>Cashier Supervisor</option>
					</select>
				</div>
				<div class="form-group">
					<label for="password">Password: </label>
					<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
					<?php if(isset($_GET['id'])){?>
					<small style="color:red;"><i>Leave this blank if you dont want to change the password.</i></small>
					
					<?php }?>
				</div>
				<div class="form-group">
					<button type="submit" name="new_update" class="btn btn-primary">Update Record</button>
				</div>
		</div>
		</div>
				
			</form>
		</div>
	</div>
</div>