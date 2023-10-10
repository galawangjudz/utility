<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the submitted account number
    $account_number = $_POST["account_number"];

    // Define the main PDF folder path
    $main_pdf_folder = "pdf_files/";

    // Recursively scan all subfolders for matching files
    $pdf_files = glob_recursive($main_pdf_folder . '*' . DIRECTORY_SEPARATOR . $account_number . "*.pdf");

    if (empty($pdf_files)) {
        echo "No SOA found for account number: $account_number";
    } else {
        echo "<h1>Statement of Account for Account Number: $account_number</h1>";
        echo "<ul>";
        foreach ($pdf_files as $pdf_file) {
            $fileInfo = pathinfo($pdf_file);
            $relative_path = str_replace($main_pdf_folder, '', $pdf_file);
            $filename = $fileInfo['filename'];
            echo "<li><a href='view-soa.php?filename=$relative_path'>$filename</a></li>";
        }
        echo "</ul>";
    }
} else {
    echo "Invalid request";
}

// Recursive glob function
function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}
?>



<div class="main-container">
		<div class="pd-ltr-20">
			<div class="title pb-20">
				<h2 class="h3 mb-0">Statement of Accounts</h2>
			</div>
			<div class="card-box mb-30">
				<div class="pd-20">

                <form action="" method="post">
                    <label for="account_number">Account Number:</label>
                    <input type="text" id="account_number" name="account_number" required>
                    <input type="submit" value="View SOA">
                </form>
            </div>
	    </div>
</div>