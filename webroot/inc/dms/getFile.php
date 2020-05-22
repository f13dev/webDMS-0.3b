<?php

$file = $_GET['file'];
$filePath = '/var/www/html/webDMSTesting/documents/' . $file;
$contents = file_get_contents($filePath);
header('Content-Type: ' . mime_content_type($filePath));
header('Content-Length: ' . filesize($filePath));
echo $contents;
