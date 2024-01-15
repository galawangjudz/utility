


<div class="main-container">
	<div class="pd-ltr-20">
        <div class="card-box mb-30">
            <div class="pd-20">
                <form action="<?php echo base_url ?>admin/?page=change-p" method="post">
                    <h2>Change Password</h2>
                    <?php if (isset($_GET['error'])) { ?>
                        <p class="error"><?php echo $_GET['error']; ?></p>
                    <?php } ?>

                    <?php if (isset($_GET['success'])) { ?>
                        <p class="success"><?php echo $_GET['success']; ?></p>
                    <?php } ?>

                    <label>Old Password</label>
                    <input type="password" 
                        name="op" 
                        placeholder="Old Password">
                        <br>

                    <label>New Password</label>
                    <input type="password" 
                        name="np" 
                        placeholder="New Password">
                        <br>

                    <label>Confirm New Password</label>
                    <input type="password" 
                        name="c_np" 
                        placeholder="Confirm New Password">
                        <br>

                    <button type="submit">CHANGE</button>
                    
                </form>
            </div>
        </div>
    </div>
</div>