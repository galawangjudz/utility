<?php

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["account_number"])) {
    $account_number = $_POST["account_number"];
    $main_pdf_folder = "C:/xampp/htdocs/utility/pdf_files/";
    $pdf_files = glob_recursive($main_pdf_folder . '*' . $account_number . '*.pdf');

    if (!empty($pdf_files)) {
        echo "<div class='main-container'>";
        echo "<div class='pd-ltr-20'>";
        echo "<div class='title pb-20'>";
        echo "<h2 class='h3 mb-0'>List of SOA</h2>";
        echo "</div>";
        echo "<div class='card-box mb-30'>";
        echo "<div class='pd-20'>";
        echo "<ul>";
        echo "<h3>Statement of Account Files for Account Number: $account_number</h3>";
        rsort($pdf_files);
        foreach ($pdf_files as $pdf_file) {
            $fileInfo = pathinfo($pdf_file);
            $relative_path = str_replace($main_pdf_folder, '', $pdf_file);
            $filename = $fileInfo['filename'];
            $parts = explode('-', $filename);
            $date = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            $date_formatted = date("F Y", strtotime($date));
            $description = "SOA for $date_formatted";
            $downloadLink = "get-soa/view-soa.php?filename=$relative_path";

            echo "<div class='card-box mb-30'>";
            echo "<div class='pd-20'>";
            echo "<ul>";
            echo "<li><b>$filename</b><br>$description<br><a href='$downloadLink' style='color: blue;' target='_blank'>[Click here to download]</a></li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<div class='main-container'>";
        echo "<div class='pd-ltr-20'>";
        echo "<div class='title pb-20'>";
        echo "<h2 class='h3 mb-0'>List of SOA</h2>";
        echo "</div>";
        echo "<div class='card-box mb-30'>";
        echo "<div class='pd-20'>";
        echo "<ul>";
        echo "<h3>Statement of Account Files for Account Number: $account_number</h3>";
        echo "No SOA found for account number: $account_number";
    }
} else {
    echo "Invalid request"; // Displayed when the page is accessed directly
}
?>
