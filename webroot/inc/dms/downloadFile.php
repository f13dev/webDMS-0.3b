<?php
$file = $_GET['file'];
$filePath = '/var/www/html/webDMSTesting/documents/' . $file;
header('Content-Description: File Transfer');
header('Content-Type: ' . mime_content_type($filePath));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
ob_clean();
flush();
readfile($filePath);
exit();
