<div class="main-container">
    <div class="pd-ltr-20">
        <div class="title pb-20">
            <h2 class="h3 mb-0">Statement of Accounts</h2>
        </div>
        <div class="card-box mb-30">
            <div class="pd-20">
                <form action="<?php echo base_url ?>admin/?page=get-soa/list-soa" method="post"> <!-- Change the form action to list-soa.php -->
                    <label for="account_number">Account Number:</label>
                    <input type="text" id="account_number" name="account_number" required>
                    <input type="submit" value="View SOA">
                </form>
            </div>
        </div>
    </div>
</div>
