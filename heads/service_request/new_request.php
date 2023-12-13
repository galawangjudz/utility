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
            $subject=$row2['subject'];  
            $dept= $row2['department_id'];  
            $request= $row2['request']; 
            $priority=$row2['priority'];  
            $purpose=$row2['description'];
        }

      
    }

?>

<div class="container-fluid">
  <form action="" id="request-form"> 
      <input type="hidden" name="id" value="<?php echo isset($ticket_id) ? $ticket_id : '' ?>">
      <div class="form-group row">
          <div class="col-sm-12">
              <label for="subject" class="block">Subject</label>
          </div>
          <div class="col-sm-12">
              <input type="text" id="subject" name="subject"autocomplete="off" class="form-control" placeholder="" value="<?php echo isset($subject) ? $subject : ''; ?>">
          </div>
          <div class="col-sm-12">
              <label for="department" >Department</label>
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
              </select>
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

          </div>
          <div class="col-sm-12">
              <label for="purpose" class="block">Purpose</label>
          </div>
          <div class="col-sm-12">
                <textarea rows="3" name="purpose" id="purpose" class="form-control form-control-md rounded-0" required><?php echo isset($purpose) ? html_entity_decode($purpose) : '' ?></textarea>

          </div>


      
      </div>
    

  </form>
</div>
<script>
 $(function(){
        $('#uni_modal #request-form').submit(function(e){
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