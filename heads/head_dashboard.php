
<div class="main-container"> 
	<div class="">
		<div class="pd-ltr-20">
			<div class="card-box pd-20 height-1400-p mb-30">
				<div class="row align-items-center">
					<div class="col-md-4 user-icon">
						<img src="../vendors/images/banner-img.png" alt="">
					</div>
					<div class="col-md-8">

						<?php $query= mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id'")or die(mysqli_error());
								$row = mysqli_fetch_array($query);
						?>

						<h4 class="font-20 weight-500 mb-10 text-capitalize">
							Welcome back <div class="weight-600 font-30 text-blue"><?php echo $row['FirstName']. " " .$row['LastName']; ?>,</div>
						</h4>
						<p class="font-18 max-width-600"> We're delighted to see you again.</p>
					</div>
				</div>
			</div>

			<div class="card-box pd-20 height-1400-p mb-30">
				<h1>🎉 Our new Utility Web App is live! 🖥️ </h1>
				<p>
					Starting this 1/15/2024, we encourage everyone to transition to the new system. 
					Embrace efficiency and innovation! 💻✨</p>

				<p>🕒 The Project Admin will initiate the Authority to Accept Payment (ATAP) using the new web app system. 
					Cashiers, please issue Cash Acknowledgement Receipts (C.A.R.) manually in the meantime.</p>

				<p>🖥️ All Cash Acknowledgement Receipts (C.A.R.) should be diligently encoded into the new system by Property Admin for record-keeping purposes. 
					IT dept will help with the encoding of C.A.R if there is still a huge volume of clients. 
					Just let us know for assistance.</p>

				<p>Your cooperation in this process is crucial for a smooth transition. 
					If you encounter any challenges, reach out to the IT department promptly.</p>

				<p>Thank you for your commitment to this upgrade!</p>
			</div>
			<div class="card-box pd-20 height-1400-p mb-30">

				<h1>🔐 Attention to all front liners and cashiers:</h1>
				<p>Your account has been registered with the username as your employee ID, and the temporary password is also your employee ID.</p>
				<p>For your security, please change your password immediately upon logging in.</p>

				<p>Thank you for your prompt attention to this matter! If you encounter any issues, please reach out to the IT department. 🛡️🔒</p>

			</div>

			<div class="card-box pd-20 height-1400-p mb-30">
				<h1>Reminders:</h1>
						<ul>
							<li>A 5% monthly surcharge for late payment.</li>
							<li>A 2% rebate for payments made 3 days before the due date.</li>
							<li>Previous billing until December 2023 will remain without surcharges.</li>
						</ul>

						<p class="font-18 mt-10">For inquiries, comments, or suggestions, please feel free to contact us at <a href="mailto:clientcare@asianland.ph">clientcare@asianland.ph</a>.</p>

	
			</div>
			
		</div>
	</div>