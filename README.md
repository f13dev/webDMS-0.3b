# webDMS
Please note, the files in this repository are of a testing version and should not be used in production.
## Installation
1. Uplaod contents of 'webroot' to your server
2. chmod 0644 -R /documents/ - If all else fails 0777 will work, but not secure
3. Create a new database (db template to follow)
4. Manually enter username and password into database
    * Password needs to be SHA1 hashed
    * Installer script will replace this one day
5. Edit /inc/cfg.php
    1. edit DB_DATABASE
    2. edit DB_USER
    3. edit DB_PASSWORD
    4. edit DB_HOST
    5. edit SITE_URL
    6. edit SITE_WEBROOT
    7. edit SITE_DOCUMENTS
    8. edit OFFICE_APPLICATION
    9. generate a unique session ID
    10. edit EMAIL_FROM

## Should I use this version in real life
No, not at all
