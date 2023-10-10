
<?php
if (isset($_POST['generate_bill'])) {
    // Get the selected date from the form
    $billDate = $_POST['bill_date'];


    $command = "python generate_bill.py " . escapeshellarg($billDate);

    // Execute the command
    exec($command, $output, $return_var);

    // Check if the Python script executed successfully
    if ($return_var === 0) {
        echo "Python script ran successfully. Output:<br>";
        foreach ($output as $line) {
            echo $line . "<br>";
        }
    } else {
        echo "Error running Python script.";
    }

}else {
    echo "Form not submitted.";
}
?>

<body>

	<div class="main-container">
		<div class="pd-ltr-20">
			<div class="title pb-20">
				<h2 class="h3 mb-0">Generate Bills</h2>
			</div>
			<div class="card-box mb-30">
				<div class="pd-20">

                <form method="POST" action="">
                    <label for="bill_date">Select Date:</label>
                    <input type="date" id="bill_date" name="bill_date" required><br><br>
                    
                    <input type="submit" name="generate_bill" value="Generate">
                </form>	   
            </div>
	    </div>
</div>