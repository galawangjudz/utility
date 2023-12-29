<div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Statement of Accounts</h2>
            </div>
            <div class="card-box mb-30">
                <div class="pd-20">
                    <form action="<?php echo base_url ?>admin/?page=get-soa/list-soa" method="post">
                        <div class="row align-items-end">
                           
                            <div class="col-md-3 form-group">
                                <label for="account_number" class="control-label">Account Number:</label>
                                <input type="text" id="account_number" name="account_number" class="form-control" maxlength="11">

                            </div>

                            <div class="col-md-3 form-group">
                                <button class="btn btn-primary"><i class="dw dw-search"></i> Search Account</button>
                                <!-- <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="fa fa-print"></i> Print</button> -->
                            </div>
                        </div>
                    </form>
                   
                    
                </div>
            </div>
        </div>
    </div>