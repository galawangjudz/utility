<?php
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["filename"])) {
    $requested_filename = urldecode($_GET["filename"]); // Decode the URL parameter
    $pdf_folder = "C:/xampp/htdocs/utility/admin/get-soa/pdf_files/";

    $pdf_file = $pdf_folder . $requested_filename;

    // Validate the filename to prevent directory traversal
    if (preg_match('/^[a-zA-Z0-9_-]+\.pdf$/', $requested_filename) && file_exists($pdf_file)) {
        // Set headers to indicate PDF content
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $requested_filename . '"');

        // Output the PDF file
        readfile($pdf_file);
    } else {
        echo "Invalid filename request or file not found.";
    }
} else {
    echo "Invalid request";
}
?>