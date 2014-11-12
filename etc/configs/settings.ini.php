<?php
use libs\vecni\Vecni;
use libs\mysql\PDOConnector;

# name of the website
Vecni::$BRAND_NAME = 'SCPL';
# company location
Vecni::$company_address = 'Kingston, Jamaica';
# company contact number
Vecni::$company_number = '(876) 8295969';
# company email address
Vecni::$company_email = 'b4.oshany@gmail.com';
Vecni::$company_name = 'St Catherine Parish Library';

/***************** Database Connection ***********************/
# If you intend to use a SQL Server, please uncomment the two lines
# below to use the PDO Extension.

PDOConnector::set_connection("root", "oshany1991", "scpls");
PDOConnector::connect();
?>
