<?php
require_once('../../includes/config.php');

    if(isset($_GET['id'])) {

        $id = $_GET['id'];
        
        $sql = "SELECT * FROM tickets WHERE id = ?";
        $ticket_id = $_GET['id'];

        $qry = odbc_prepare($conn2, $sql);
        if (!odbc_execute($qry, array($ticket_id))) {
            die("Execution of the statement failed: " . odbc_errormsg($conn2));
        }
        while ($row2 = odbc_fetch_array($qry)) {
            $employeeID= $row2['employee_id'];
            $acc_no=$row2['account_no'];  
            $acc_no_to=$row2['transfer_to'];  
            $adjust_for=$row2['request_from'];  
            $adjust_to=$row2['request_to'];  
            $amount=$row2['amount']; 
            $gcf_edate=$row2['gcf_edate']; 
            $dept= $row2['department_id'];  
            $request= $row2['request']; 
            $priority=$row2['priority'];  
            $purpose=$row2['description'];
            $attachment_path = $row2['attachment'];
            $attachment_filename = basename($attachment_path);
        }

    }

?>
<body onload="handleFormChanges()">
<div class="container-fluid">
  <form action="" id="request-form"> 
      <input type="hidden" name="id" value="<?php echo isset($ticket_id) ? $ticket_id : '' ?>">
      <div class="form-group row">
            <div class="col-sm-12">
                <label for="department" class="block">Assigned To</label>
            </div>
            <div class="col-sm-12">
        
              <select name="department" class="custom-select form-control" required="true" autocomplete="off">
                <?php
                if (!empty($dept)) {
                  $query = "select * from tbldepartments where id =" .$dept; 
                  $result = odbc_exec($conn2, $query);
                  while ($row4 = odbc_fetch_array($result))  {
                    ?>
                        <option value="<?php echo $row4['id']; ?>" $selected><?php echo $row4['departmentname']; ?></option>
                 <?php
                  }
                }
                else
                    {
                      echo '<option value="" readonly selected>Select Department</option>';
                    } 
                $query = "select * from tbldepartments where id = '1' or id = '2' or  id = '6'";
                $result = odbc_exec($conn2, $query);
                while ($row2 = odbc_fetch_array($result))  {
                  ?>
                  <option value="<?php echo $row2['id']; ?>"><?php echo $row2['departmentname']; ?></option>
                  <?php
                }?> 
                  
              </select>      
          </div>
          <div class="col-sm-12">
              <label for="request" class="block">Request for</label>
          </div>
          <div class="col-sm-12">
              <select class="form-control form-control-border" name="request" id="request" required>
                <option value="BA" <?php echo (isset($request) && $request == 'BA') ? 'selected' : ''; ?>>BILL ADJUSTMENT</option>
                <option value="SA" <?php echo (isset($request) && $request == 'SA') ? 'selected' : ''; ?>>SURCHARGE ADJUSTMENT</option>
                <option value="ADJ" <?php echo (isset($request) &&  $request == 'ADJ') ? 'selected' : ''; ?>>PAYMENT ADJUSTMENT</option>
                <option value="PTO" <?php echo (isset($request) &&  $request == 'PTO') ? 'selected' : ''; ?>>PERMIT TO OCCUPY</option>
                <option value="PTC" <?php echo (isset($request) &&  $request == 'PTC') ? 'selected' : ''; ?>>PERMIT TO CONSTRUCT</option>
                <option value="ATC" <?php echo (isset($request) &&  $request == 'ATC') ? 'selected' : ''; ?>>AUTHORITY TO CONSTRUCT</option>
                <option value="REF" <?php echo (isset($request) &&  $request == 'REF') ? 'selected' : ''; ?>>REFUND</option>
         
              </select>
          </div>
          <div class="col-sm-12">
              <label for="subject">Account No</label>
              <input type="text" id="account_no" name="account_no"autocomplete="off" class="form-control" placeholder="" value="<?php echo isset($acc_no) ? $acc_no : ''; ?>">
          </div>
          <div class="col-sm-12" id="accountto_row">
              <label for="account_no_to" id="account_no_to_label">Account No To</label>
              <input type="text" id="account_no_to" name="account_no_to"autocomplete="off" class="form-control" placeholder="" value="<?php echo isset($acc_no_to) ? $acc_no_to : NULL; ?>">
          </div>
         
          <div class="col-sm-12">
            <label for="adjust_for">Adjustment for</label>
              <select class="form-control form-control-border" name="adjust_for" id="adjust_for" required>
                        <option value="" <?php echo empty($adjust_for) ? 'selected' : ''; ?>>Select an option</option>
                        <option value="GCF" <?php echo (isset($adjust_for) && $adjust_for == 'GCF') ? 'selected' : ''; ?>>GRASS CONTROL</option>
                        <option value="STL" <?php echo (isset($adjust_for) && $adjust_for == 'STL') ? 'selected' : ''; ?>>STREETLIGHT</option>
              </select>
          </div>
          <div class="col-sm-12" id="transferto_row">
              <label for="adjust_to">Transfer to</label>
              <select name="adjust_to" id="adjust_to" class="form-control form-control-border">
                    <option value="" <?php echo empty($adjust_to) ? 'selected' : ''; ?>>Select an option</option>
                    <option value="GCF" <?php echo (isset($adjust_to) && $adjust_to == 'GCF') ? 'selected' : ''; ?>>Grass Control</option>
                    <option value="STL" <?php echo (isset($adjust_to) && $adjust_to == 'STL') ? 'selected' : ''; ?>>STREETLIGHT</option>
              </select>
          </div>

          <div class="col-sm-12" id="amount_row">
                    <label for="amount" class="control-label">Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control form-control-border" value ="<?php echo isset($amount) ? $amount : ''; ?>">
                    <!-- <small class="text-danger">Amount must be higher than 0.</small> -->
          </div>

          <div class="col-sm-12" id="gcf_edaterow">
                    <label for="gcf_edate" class="control-label">Date</label>
                    <input type="date" name="gcf_edate" id="gcf_edate" class="form-control form-control-border" value ="<?php echo isset($gcf_edate) ? $gcf_edate : ''; ?>">
                    <!-- <small class="text-danger">Amount must be higher than 0.</small> -->
          </div>
          
      </div>
     
      <div class="form-group row">
          <div class="col-sm-12">
                  <label for="pbl" class="block">Priority</label>
          </div>
          <div class="col-sm-12">
          <div class="form-radio">
           
              <input type="hidden" name="priority" value="<?php echo isset($row['priority']) ? $row['priority'] : ''; ?>">

              <div class="radio radiofill radio-inline">
                  <label>
                      <input type="radio" name="priority" value="Highest" <?php echo (isset($priority) && $priority == 'Highest') ? 'checked' : ''; ?>>
                      <i class="helper"></i>Highest
                  </label>
              </div>
              <div class="radio radiofill radio-inline">
                  <label>
                      <input type="radio" name="priority" value="High" <?php echo (isset($priority) && $priority == 'High') ? 'checked' : ''; ?>>
                      <i class="helper"></i>High
                  </label>
              </div>
              <div class="radio radiofill radio-inline">
                  <label>
                      <input type="radio" name="priority" value="Normal" <?php echo (isset($priority) &&  $priority == 'Normal') ? 'checked' : ''; ?>>
                      <i class="helper"></i>Normal
                  </label>
              </div>
              <div class="radio radiofill radio-inline">
                  <label>
                      <input type="radio" name="priority" value="Low" <?php echo (isset($priority) &&  $priority == 'Low') ? 'checked' : ''; ?>>
                      <i class="helper"></i>Low
                  </label>
              </div>
          </div>
          <!-- Display current attachment information if it exists -->
        <input type="hidden" name="attachment_path" id="attachment_path" value="<?php echo isset($attachment_path) ? $attachment_path : ''; ?>">
        <div class="form-group row" id="attachmentInfoSection">
            <div class="col-sm-12">
                <label for="attachment_info" class="block">Current Attachment</label>
            </div>
            <div class="col-sm-12">
                <?php if (!empty($attachment_filename)): ?>
                    <p>
                        <strong>Filename:</strong>
                        <a href="<?php echo base_url?>/sr_attachments/<?php echo $attachment_filename; ?>" target="_blank">
                            <?php echo $attachment_filename; ?>
                        </a>
                        
                        </a>
                        <br>
                    </p>
                    <button type="button" onclick="changeAttachment()">Change Attachment</button>
                    <button type="button" onclick="deleteAttachment()">Delete Attachment</button>
                    <?php else: ?>
                    <p>No attachment uploaded.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Display file input for uploading a new attachment -->
        <div class="form-group row" id="fileInputSection" style="<?php echo empty($attachment_filename) ? 'display: block;' : 'display: none;'; ?>">
            <div class="col-sm-12">
                <label for="attachment" class="block">Attachment (JPEG or PNG only)</label>
            </div>
            <div class="col-sm-12">
                <input type="file" name="attachment" id="attachment" class="form-control-file" accept=".jpg, .jpeg, .png">
                <!-- Add the 'form-control-file' class to style the file input -->
            </div>
        </div>

        <script>
            function changeAttachment() {
                // Hide the current attachment info
                document.getElementById('attachmentInfoSection').style.display = 'none';
            
                // Show the file input section for uploading a new attachment
                document.getElementById('fileInputSection').style.display = 'block';
            }
        </script>


          </div>
          <div class="col-sm-12">
              <label for="purpose" class="block">Notes</label>
          </div>
          <div class="col-sm-12">
                <textarea rows="3" name="purpose" id="purpose" class="form-control form-control-md rounded-0" required><?php echo isset($purpose) ? html_entity_decode($purpose) : '' ?></textarea>

          </div>


      
      </div>
    

  </form>
</div>
<script>
    $(document).ready(function(){
    $('#accountto_row').hide();
    $('#transferto_row').hide();
    $('#gcf_edaterow').hide();
              
    function handleFormChanges() {
          // Hide all relevant rows
          $('#accountto_row').hide();
          $('#transferto_row').hide();
          $('#gcf_edaterow').hide();
          $('#amount_row').show();
          // Show/hide the relevant rows based on the selected option
          if($('#request').val() == 'ADJ') {
              $('#accountto_row').show();
              $('#transferto_row').show();
              $('#amount_row').show();
          } else if(['PTO', 'ATC', 'PTC'].includes($('#request').val())) {
              $('#gcf_edaterow').show();
              $('#amount_row').hide();
          } else {
              // Add similar conditions for other options if needed
          }
    }

      // Handle changes in the form initially
    handleFormChanges();

      $('#request').change(function(){
          // Reset the values of the input fields
          $('#account_no_to').val('');
          $('#adjust_to').val('');
          $('#gcfEdateInput').val('');
          $('#amount').val('');
          handleFormChanges();
      });




  });





 $(function(){
        $('#uni_modal_ticket #request-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=add_ticket",
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

<style>

.hidden-footer {
    display: none;
}

  
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