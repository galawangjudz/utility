<?php
    // Check if the edit parameter is set and fetch the record from the database
    if(isset($_GET['edit']) && $_GET['edit'] == 1 && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM tickets WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row2 = mysqli_fetch_assoc($result);
    }

    if(isset($_POST['tickets-add']))
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
      echo $result;
        if ($result) {
          echo "<script>alert('Your records Successfully Updated');</script>";
        echo "<script>location.replace(_base_url_+'heads/?page=head_profile');</script>";
        
      } else{
        die(mysqli_error());
      }

      

    }

?>

<div class="main-container"> 
	<div class="">
		<div class="pd-ltr-20">
			<div class="title pb-20">
                <h2 class="h3 mb-0">New Request</h2>
            </div>
	
			<div class="card-box mb-30">
				<div class="pd-20">
                <form>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label for="buyer_name" class="block">Buyer's Name</label>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" id="buyer_name" name="buyer_name"autocomplete="off" class="form-control" placeholder="" value="<?php echo isset($row2['bname']) ? $row2['bname'] : ''; ?>">
                        </div>
                        <div class="col-sm-12">
                            <label for="pbl" class="block">Proj/Block/Lot</label>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" id="pbl" name="pbl"autocomplete="off" class="form-control" placeholder="" value=""<?php echo isset($row2['bname']) ? $row2['bname'] : ''; ?>"">
                        </div>
                        <div class="col-sm-12">
                            <label for="pbl" class="block">Request for</label>
                        </div>
                        <div class="col-sm-12">
                            <select class="form-control form-control-border" name="request" id="request" required>
                                <option value="BA" selected>BILL ADJUSTMENT</option>
                                <option value="PA" selected>PAYMENT ADJUSTMENT</option>
                                <option value="PTO" selected>PERMIT TO OCCUPIED</option>
                                <option value="PTC" selected>PERMIT TO CONSTRUCT</option>
                                <option value="ATC" selected>AUTHORITY TO CONSTRUCT</option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                                <label for="pbl" class="block">Priority</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-radio">
                                <div class="radio radiofill radio-inline">
                                    <label>
                                        <input type="radio" name="priority" value="Highest" checked="checked">
                                        <i class="helper"></i>Highest
                                    </label>
                                </div>
                                <div class="radio radiofill radio-inline">
                                    <label>
                                        <input type="radio" name="priority" value="High">
                                        <i class="helper"></i>High
                                    </label>
                                </div>
                                <div class="radio radiofill radio-inline">
                                    <label>
                                        <input type="radio" name="priority" value="Normal">
                                        <i class="helper"></i>Normal
                                    </label>
                                </div>
                                <div class="radio radiofill radio-inline">
                                    <label>
                                        <input type="radio" name="priority" value="Low">
                                        <i class="helper"></i>Low
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-12">
                            <label for="purpose" class="block">Purpose</label>
                        </div>
                        <div class="col-sm-12">
                             <textarea rows="3" name="purpose" id="purpose" class="form-control form-control-md rounded-0" required><?php echo isset($remarks) ? html_entity_decode($remarks) : '' ?></textarea>
     
                        </div>


                    
                    </div>
                    <div class="row">
                        <label class="col-sm-5"></label>
                        <div class="col-sm-5">
                            <?php if(isset($row2) && !empty($row2)): ?>
                                <button id="tickets-update" type="submit" class="btn btn-primary m-b-0">Update</button>
                            <?php else: ?>
                                <button id="tickets-add" type="submit" class="btn btn-primary m-b-0">Submit</button>
                            <?php endif; ?>
                        </div>
                    </div>


                </form>
                       
				
			   </div>
			</div>
			
		</div>
	</div>
<script>
  $('#tickets-update').click(function(event){
      event.preventDefault(); // prevent the default form submission
      (async () => {
          var data = {
              id: <?php echo isset($_GET['id']) ? $_GET['id'] : 'null'; ?>,
              subject: $('#subject').val(),
              description: $('#summernote').summernote('code'), // get the HTML content of Summernote
              department: $('#department').val(),
              customer: $('#customer').val(),
              action: "tickets-update",
          };

          if (data.subject.trim() === '' || data.description.trim() === '' || 
              data.department.trim() === '' || data.customer.trim() === '') {
              Swal.fire({
                  icon: 'warning',
                  text: 'Please all fieds are required. Kindly fill all',
                  confirmButtonColor: '#ffc107',
                  confirmButtonText: 'OK'
              });
              return;
          }
          console.log('Data HERE: ' + JSON.stringify(data));
          $.ajax({
              url: 'ticket_functions.php',
              type: 'post',
              data: data,
              success:function(response){
                  console.log('success function called');
                  response = JSON.parse(response);
                  console.log('RESPONSE HERE: ' + response.status)
                  console.log(`RESPONSE HERE: ${response.message}`);
                  if (response.status == 'success') {
                      Swal.fire({
                          icon: 'success',
                          html: response.message,
                          confirmButtonColor: '#01a9ac',
                          confirmButtonText: 'OK'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              window.location.href = "ticket_list.php";
                              // location.reload();
                          }
                      });
                  } else {
                      Swal.fire({
                          icon: 'error',
                          text: response.message,
                          confirmButtonColor: '#eb3422',
                          confirmButtonText: 'OK'
                      });
                  }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.log('AJAX Data HERE: ' + JSON.stringify(data));
                  console.log("Response from server: " + jqXHR.responseText);
                  console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
              }
          });
      })()
  })
</script>
<script>
    $('#tickets-add').click(function(event){
        event.preventDefault(); // prevent the default form submission
        (async () => {
            var data = {
                subject: $('#subject').val(),
                description: $('#summernote').summernote('code'), // get the HTML content of Summernote
                priority: $('input[name="priority"]:checked').val(),
                department: $('#department').val(),
                customer: $('#customer').val(),
                status: 0, // set initial status to 0
                action: "tickets-add",
            };

            if (data.subject === '' || data.description === '' || 
                data.priority === '' || data.department === '' || 
                data.customer === '') {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please all fieds are required. Kindly fill all',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'OK'
                });
                return;
            }
            console.log('Data HERE: ' + JSON.stringify(data));
            $.ajax({
                url: 'ticket_functions.php',
                type: 'post',
                data: data,
                success:function(response){
                    console.log('success function called');
                    response = JSON.parse(response);
                    console.log('RESPONSE HERE: ' + response.status)
                    console.log(`RESPONSE HERE: ${response.message}`);
                    if (response.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            html: response.message,
                            confirmButtonColor: '#01a9ac',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: response.message,
                            confirmButtonColor: '#eb3422',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('AJAX Data HERE: ' + JSON.stringify(data));
                    console.log("Response from server: " + jqXHR.responseText);
                    console.log("Status:", status);
                    console.log("Error:", error);
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        })()
    })
</script>

<style>
  .radio-inline,
  .border-checkbox-section .border-checkbox-group,
  .checkbox-color {
    display: block;
  }

  /**  =====================
      Radio-button css start
==========================  **/
.form-radio {
    position: relative;
  }
  
  .form-radio .form-help {
    position: absolute;
    width: 100%;
  }
  
  .form-radio label {
    position: relative;
    padding-left: 1.5rem;
    text-align: left;
    color: #333;
    display: block;
    line-height: 1.8;
  }
  
  .form-radio input {
    width: auto;
    opacity: 0.00000001;
    position: absolute;
    left: 0;
  }
  
  .radio .helper {
    position: absolute;
    top: -0.15rem;
    left: -0.25rem;
    cursor: pointer;
    display: block;
    font-size: 1rem;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    color: #999;
  }
  
  .radio .helper::after {
    -webkit-transform: scale(0);
        -ms-transform: scale(0);
            transform: scale(0);
    background-color: #01a9ac;
    border-color: #01a9ac;
  }
  
  .radio .helper::after, .radio .helper::before {
    content: '';
    position: absolute;
    left: 0;
    top: 3px;
    margin: 0.25rem;
    width: 1rem;
    height: 1rem;
    -webkit-transition: -webkit-transform 0.28s ease;
    transition: -webkit-transform 0.28s ease;
    transition: transform 0.28s ease, -webkit-transform 0.28s ease;
    border-radius: 50%;
    border: 0.125rem solid #01a9ac;
  }
  
  .radio label:hover .helper {
    color: #01a9ac;
  }
  
  .radio input:checked ~ .helper::after {
    -webkit-transform: scale(0.5);
        -ms-transform: scale(0.5);
            transform: scale(0.5);
  }
  
  .radio input:checked ~ .helper::before {
    color: #01a9ac;
  }
  
  .radio.radiofill input:checked ~ .helper::after {
    -webkit-transform: scale(1);
        -ms-transform: scale(1);
            transform: scale(1);
  }
  
  .radio.radiofill .helper::after {
    background-color: #01a9ac;
  }
  
  .radio.radio-outline input:checked ~ .helper::after {
    -webkit-transform: scale(0.6);
        -ms-transform: scale(0.6);
            transform: scale(0.6);
  }
  
  .radio.radio-outline .helper::after {
    background-color: #fff;
    border: 0.225rem solid #01a9ac;
  }
  
  .radio.radio-matrial input ~ .helper::after {
    background-color: #fff;
  }
  
  .radio.radio-matrial input:checked ~ .helper::after {
    -webkit-transform: scale(0.5);
        -ms-transform: scale(0.5);
            transform: scale(0.5);
    -webkit-box-shadow: 0 1px 7px -1px rgba(0, 0, 0, 0.72);
            box-shadow: 0 1px 7px -1px rgba(0, 0, 0, 0.72);
  }
  
  .radio.radio-matrial input:checked ~ .helper::before {
    background-color: #01a9ac;
  }
  
  .radio.radio-disable {
    opacity: 0.7;
  }
  
  .radio.radio-disable label {
    cursor: not-allowed;
  }
  
  .radio-inline {
    display: inline-block;
    margin-right: 20px;
  }
  
  .radio.radiofill.radio-primary .helper::after {
    background-color: #01a9ac;
    border-color: #01a9ac;
  }
  
  .radio.radiofill.radio-primary .helper::before {
    border-color: #01a9ac;
  }
  
  .radio.radio-outline.radio-primary .helper::after {
    background-color: #fff;
    border: 0.225rem solid #01a9ac;
  }
  
  .radio.radio-outline.radio-primary .helper::before {
    border-color: #01a9ac;
  }
  
  .radio.radio-matrial.radio-primary input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-primary input ~ .helper::before {
    background-color: #01a9ac;
    border-color: #01a9ac;
  }
  
  .radio.radiofill.radio-warning .helper::after {
    background-color: #fe9365;
    border-color: #fe9365;
  }
  
  .radio.radiofill.radio-warning .helper::before {
    border-color: #fe9365;
  }
  
  .radio.radio-outline.radio-warning .helper::after {
    background-color: #fff;
    border: 0.225rem solid #fe9365;
  }
  
  .radio.radio-outline.radio-warning .helper::before {
    border-color: #fe9365;
  }
  
  .radio.radio-matrial.radio-warning input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-warning input ~ .helper::before {
    background-color: #fe9365;
    border-color: #fe9365;
  }
  
  .radio.radiofill.radio-default .helper::after {
    background-color: #e0e0e0;
    border-color: #e0e0e0;
  }
  
  .radio.radiofill.radio-default .helper::before {
    border-color: #e0e0e0;
  }
  
  .radio.radio-outline.radio-default .helper::after {
    background-color: #fff;
    border: 0.225rem solid #e0e0e0;
  }
  
  .radio.radio-outline.radio-default .helper::before {
    border-color: #e0e0e0;
  }
  
  .radio.radio-matrial.radio-default input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-default input ~ .helper::before {
    background-color: #e0e0e0;
    border-color: #e0e0e0;
  }
  
  .radio.radiofill.radio-danger .helper::after {
    background-color: #eb3422;
    border-color: #eb3422;
  }
  
  .radio.radiofill.radio-danger .helper::before {
    border-color: #eb3422;
  }
  
  .radio.radio-outline.radio-danger .helper::after {
    background-color: #fff;
    border: 0.225rem solid #eb3422;
  }
  
  .radio.radio-outline.radio-danger .helper::before {
    border-color: #eb3422;
  }
  
  .radio.radio-matrial.radio-danger input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-danger input ~ .helper::before {
    background-color: #eb3422;
    border-color: #eb3422;
  }
  
  .radio.radiofill.radio-success .helper::after {
    background-color: #0ac282;
    border-color: #0ac282;
  }
  
  .radio.radiofill.radio-success .helper::before {
    border-color: #0ac282;
  }
  
  .radio.radio-outline.radio-success .helper::after {
    background-color: #fff;
    border: 0.225rem solid #0ac282;
  }
  
  .radio.radio-outline.radio-success .helper::before {
    border-color: #0ac282;
  }
  
  .radio.radio-matrial.radio-success input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-success input ~ .helper::before {
    background-color: #0ac282;
    border-color: #0ac282;
  }
  
  .radio.radiofill.radio-inverse .helper::after {
    background-color: #404E67;
    border-color: #404E67;
  }
  
  .radio.radiofill.radio-inverse .helper::before {
    border-color: #404E67;
  }
  
  .radio.radio-outline.radio-inverse .helper::after {
    background-color: #fff;
    border: 0.225rem solid #404E67;
  }
  
  .radio.radio-outline.radio-inverse .helper::before {
    border-color: #404E67;
  }
  
  .radio.radio-matrial.radio-inverse input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-inverse input ~ .helper::before {
    background-color: #404E67;
    border-color: #404E67;
  }
  
  .radio.radiofill.radio-info .helper::after {
    background-color: #2DCEE3;
    border-color: #2DCEE3;
  }
  
  .radio.radiofill.radio-info .helper::before {
    border-color: #2DCEE3;
  }
  
  .radio.radio-outline.radio-info .helper::after {
    background-color: #fff;
    border: 0.225rem solid #2DCEE3;
  }
  
  .radio.radio-outline.radio-info .helper::before {
    border-color: #2DCEE3;
  }
  
  .radio.radio-matrial.radio-info input ~ .helper::after {
    background-color: #fff;
    border-color: #fff;
  }
  
  .radio.radio-matrial.radio-info input ~ .helper::before {
    background-color: #2DCEE3;
    border-color: #2DCEE3;
  }
  
  /*===== Radio Button css ends =====*/
</style>