<div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Statement of Accounts</h2>
            </div>
            <div class="card-box mb-30">
                <div class="pd-20">
                    <form action="<?php echo base_url ?>head/?page=get-soa/list-soa" method="post">
                        <div class="col-md-4 form-group">
                            <label for="account_number" class="control-label">Account Number:</label>
                            <input type="text" id="account_number" name="account_number" class="form-control">
                            
                        </div>
                        <div class="col-md-4 form-group">
                            <button type="submit" class="btn btn-primary" name="find"><i class="dw dw-search"></i> Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>