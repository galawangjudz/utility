<?php
if(isset($_POST['new_update']))
{
	$empid=$session_id;
	$fname=$_POST['fname'];
	$lname=$_POST['lastname'];   
	$email=$_POST['email'];  
	$department=$_POST['department']; 
	$gender=$_POST['gender'];  
	$phonenumber=$_POST['phonenumber'];

    $result = mysqli_query($conn,"update tblemployees set FirstName='$fname', LastName='$lname', EmailId='$email', Gender='$gender', Department='$department', Phonenumber='$phonenumber' where emp_id='$session_id'         
		")or die(mysqli_error());
    if ($result) {
     	echo "<script>alert('Your records Successfully Updated');</script>";
		echo "<script>location.replace(_base_url_+'staff/?page=staff_profile');</script>";
     
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
		echo "<script>location.replace(_base_url_+'staff/?page=staff_profile');</script>";
	}

    $result = mysqli_query($conn,"update tblemployees set location='$location' where emp_id='$session_id'         
		")or die(mysqli_error());
    if ($result) {
     	echo "<script>alert('Profile Picture Updated');</script>";
		echo "<script>location.replace(_base_url_+'staff/?page=staff_profile');</script>";
     /* 	echo "<script type='text/javascript'> document.location = 'my_profile.php'; </script>"; */
	} else{
	  die(mysqli_error());
   }
}

?>
	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Profile</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="<?php echo base_url ?>admin/?page=home">Dashboard</a></li>
									<li class="breadcrumb-item active" aria-current="page">Profile</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">

							<?php $query= mysqli_query($conn,"select * from tblemployees LEFT JOIN tbldepartments ON tblemployees.Department = tbldepartments.DepartmentShortName where emp_id = '$session_id'")or die(mysqli_error());
								$row = mysqli_fetch_array($query);
							?>

							<div class="profile-photo">
								<a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
								<img src="<?php echo (!empty($row['location'])) ? '../uploads/'.$row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg'; ?>" alt="" class="avatar-photo">
								<form method="post" enctype="multipart/form-data">
									<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="weight-500 col-md-12 pd-5">
													<div class="form-group">
														<div class="custom-file">
															<input name="image" id="file" type="file" class="custom-file-input" accept="image/*" onchange="validateImage('file')">
															<label class="custom-file-label" for="file" id="selector">Choose file</label>		
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<input type="submit" name="update_image" value="Update" class="btn btn-primary">
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<h5 class="text-center h5 mb-0"><?php echo $row['FirstName']. " " .$row['LastName']; ?></h5>
							<p class="text-center text-muted font-14"><?php echo $row['DepartmentName']; ?></p>
							<div class="profile-info">
								<h5 class="mb-20 h5 text-blue">Contact Information</h5>
								<ul>
									<li>
										<span>Email Address:</span>
										<?php echo $row['EmailId']; ?>
									</li>
									<li>
										<span>Phone Number:</span>
										<?php echo $row['Phonenumber']; ?>
									</li>
									<li>
										<span>My Role:</span>
										<?php echo $row['role']; ?>
									</li>
									
								</ul>
							</div>
						</div>
					</div>
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="card-box height-100-p overflow-hidden">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
									<!-- 	<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">Leave Records</a>
										</li> -->
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#setting" role="tab">Settings</a>
										</li>
									</ul>
									<div class="tab-content">
										<!-- Timeline Tab start -->
										<div class="tab-pane fade show active" id="setting" role="tabpanel">
										
											<div class="profile-setting">
												<form method="POST" enctype="multipart/form-data" action="<?php echo base_url ?>staff/?page=staff_profile">
													<div class="profile-edit-list row">
														<div class="col-md-12"><h4 class="text-blue h5 mb-20">Edit Your Personal Setting</h4></div>

														<?php
														$query = mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id' ")or die(mysqli_error());
														$row = mysqli_fetch_array($query);
														?>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>First Name</label>
																<input name="fname" class="form-control form-control-lg" type="text" required="true" autocomplete="off" value="<?php echo $row['FirstName']; ?>">
															</div>
														</div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Last Name</label>
																<input name="lastname" class="form-control form-control-lg" type="text" placeholder="" required="true" autocomplete="off" value="<?php echo $row['LastName']; ?>">
															</div>
														</div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Email Address</label>
																<input name="email" class="form-control form-control-lg" type="text" placeholder="" required="true" autocomplete="off" value="<?php echo $row['EmailId']; ?>">
															</div>
														</div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Phone Number</label>
																<input name="phonenumber" class="form-control form-control-lg" type="text" placeholder="" required="true" autocomplete="off" value="<?php echo $row['Phonenumber']; ?>">
															</div>
														</div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Department</label>
																<select name="department" class="custom-select form-control" required="true" autocomplete="off">
																	<?php
																	$query = "select * from tbldepartments";
																	$result = odbc_exec($conn2, $query);
																	while ($row2 = odbc_fetch_array($result))  {
																	?>
																	<option value="<?php echo $row2['id']; ?>"><?php echo $row2['departmentname']; ?></option>
																	<?php
																	}?> 
																</select>
															</div>
														</div>
														
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Gender</label>
																<select name="gender" class="custom-select form-control" required="true" autocomplete="off">
																					
																	<option value="Male" <?php echo isset($row_staff['Gender']) && $row_staff['Gender'] == "Male" ? 'selected': '' ?>> Male</option>
																	<option value="Female" <?php echo isset($row_staff['Gender']) && $row_staff['Gender'] == "Female" ? 'selected': '' ?>> Female</option>
																</select>
															</div>
														</div>
														
													
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label></label>
																<div class="modal-footer justify-content-center">
																	<button class="btn btn-primary" name="new_update" id="new_update" data-toggle="modal">Save & &nbsp;Update</button>
																</div>
															</div>
														</div>
													</div>
												</form>
											</div>
										</div>
										<!-- Setting Tab End -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	
	<script type="text/javascript">
		var loader = function(e) {
			let file = e.target.files;

			let show = "<span>Selected file : </span>" + file[0].name;
			let output = document.getElementById("selector");
			output.innerHTML = show;
			output.classList.add("active");
		};

		let fileInput = document.getElementById("file");
		fileInput.addEventListener("change", loader);
	</script>
	<script type="text/javascript">
		 function validateImage(id) {
		    var formData = new FormData();
		    var file = document.getElementById(id).files[0];
		    formData.append("Filedata", file);
		    var t = file.type.split('/').pop().toLowerCase();
		    if (t != "jpeg" && t != "jpg" && t != "png") {
		        alert('Please select a valid image file');
		        document.getElementById(id).value = '';
		        return false;
		    }
		    if (file.size > 1050000) {
		        alert('Max Upload size is 1MB only');
		        document.getElementById(id).value = '';
		        return false;
		    }

		    return true;
		}
	</script>
