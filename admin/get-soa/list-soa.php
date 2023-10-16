<?php

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
            <h2 class="h3 mb-0">List of SOA</h2>
        </div>
        <div class="card-box mb-30">
            <div class="pd-20">
                <?php
                // Check if POST data is received
                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["account_number"])) {
                    // Get the submitted account number
                    $account_number = $_POST["account_number"];

                    // Define the main PDF folder path
                    $main_pdf_folder = "pdf_files/";

                    // Recursively scan all subfolders for matching files
                    $pdf_files = glob_recursive($main_pdf_folder . '*' . DIRECTORY_SEPARATOR . $account_number . "*.pdf");

                    // If PDF files were found, display them
                    if (!empty($pdf_files)) {
                        echo "<div class='card-box mb-30'>";
                        echo "<div class='pd-20'>";
                        echo "<h3>Statement of Account Files for Account Number: $account_number</h3>";
                        echo "<ul>";

                        foreach ($pdf_files as $pdf_file) {
                            $fileInfo = pathinfo($pdf_file);
                            $relative_path = str_replace($main_pdf_folder, '', $pdf_file);
                            $filename = $fileInfo['filename'];
                            echo "<li><a href='view-soa.php?filename=$relative_path'>$filename</a></li>";
                        }

                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                    } else {
                        echo "No SOA found for account number: $account_number";
                    }
                } else {
                    echo "Invalid request"; // Displayed when the page is accessed directly
                }
                ?>
            </div>
        </div>
    </div>
</div>
