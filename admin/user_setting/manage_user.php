<?php
require_once('../../includes/config.php');


if(isset($_GET['id'])){
    $user = $conn->query("SELECT * FROM tblemployees where emp_id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    
}
}

if(isset($_POST['new_update']))
{
	$empid=$session_id;
	$fname=$_POST['fname'];
	$lname=$_POST['lastname'];   
	$email=$_POST['email'];  
	$department=$_POST['department']; 
	$gender=$_POST['gender'];  
	$phonenumber=$_POST['phonenumber'];

    $result = mysqli_query($conn,"update tblemployees set FirstName='$fname', LastName='$lname', EmailId='$email', Gender='$gender',Department='$department', Phonenumber='$phonenumber' where emp_id='$session_id'         
		")or die(mysqli_error());
    if ($result) {
     	echo "<script>alert('User records Successfully Updated');</script>";

	} else{
	  die(mysqli_error());
   }

}

if (isset($_POST["update_image"])) {

	$image = $_FILES['image']['name'];

	if(!empty($image)){
		move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image);
		$location = $image;	
	}
	else {
		echo "<script>alert('Please Select Picture to Update');</script>";
	}

    $result = mysqli_query($conn,"update tblemployees set location='$location' where emp_id='$session_id'         
		")or die(mysqli_error());
    if ($result) {
     	echo "<script>alert('Profile Picture Updated');</script>";
     	echo "<script type='text/javascript'> document.location = 'my_profile.php'; </script>";
	} else{
	  die(mysqli_error());
   }
}


?>
<div class="card card-outline rounded-0 card-maroon">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
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
					<style>
						select:invalid { color: gray; }
					</style>
					<label class="control-label">Role: </label>
					<select name="role" id="role" class="form-control required">
						<option value="Admin"<?php echo isset($meta['role']) && $meta['role'] == "Admin" ? 'selected': '' ?>>Admin</option>
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
					</select>
				</div>
				<div class="form-group">
					<label for="password">Password: </label>
					<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
					<?php if(isset($_GET['id'])){?>
					<small style="color:red;"><i>Leave this blank if you dont want to change the password.</i></small>
					
					<?php }?>
				</div>
				
				
			</form>
		</div>
	</div>
</div>
<script>
    $(function(){
        $('#uni_modal #manage-user').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_user",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert("An error occured",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        alert(resp.msg);
                        location.reload();
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body,.modal').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>