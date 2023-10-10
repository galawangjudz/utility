<?php
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["filename"])) {
    $requested_filename = urldecode($_GET["filename"]); // Decode the URL parameter
    $pdf_folder = "pdf_files/";

    // Split the subfolder and filename
    $parts = explode(DIRECTORY_SEPARATOR, $requested_filename);
    
    if (count($parts) === 2) {
        $subfolder = $parts[0];
        $filename = $parts[1];

        // Validate the subfolder and filename to prevent directory traversal
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $subfolder) && preg_match('/^[a-zA-Z0-9_-]+\.pdf$/', $filename)) {
            $pdf_file = $pdf_folder . $subfolder . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($pdf_file)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $filename . '"');
                readfile($pdf_file);
            } else {
                echo "SOA not found for filename: $filename";
            }
        } else {
            echo "Invalid filename request";
        }
    } else {
        echo "Invalid filename format";
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
