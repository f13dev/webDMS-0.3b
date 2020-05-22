<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

/**
  * webDMS configuration file, edit at your own risk
**/

// Database details
define('DB_DATABASE','db name'); // Database name
define('DB_USER','db username'); // Database user
define('DB_PASSWORD','db password'); // Database users password
define('DB_HOST','localhost'); // Database host


// Site structure
define('SITE_URL', 'https://domain.tld/webDMS/');
define('SITE_WEBROOT','/var/www/html/domain.tld/webDMS/'); // Path to webDMS
define('SITE_DOCUMENTS','documents/'); // Path to 'documents'

// Set to true if you have LibreOffice services on the server
define('OFFICE_APPLICATION', true);

// Random string for unique sessions
define('SESSION_UNIQUE_ID', 'Generate a random SHA1 or SHA256 string to enter here');

// Address to send emails from
define('EMAIL_FROM', 'your@email.address');

// Create a connection to the database
try {
  $dbh = new PDO('mysql:dbname=' . DB_DATABASE . ';host=' . DB_HOST, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  die();
}
