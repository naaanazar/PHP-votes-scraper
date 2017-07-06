<?php
include ('vendor/autoload.php');
require_once ('src/config.php');

use Sunra\PhpSimple\HtmlDomParser;

use App\library\QueryToDB;
use App\library\Validate;


$query = new QueryToDB();
$validation = new Validate;

$sql = "CREATE TABLE deputat (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )";

$query->queryToDB($sql);




$sql = "CREATE TABLE  votes(
id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
deputat_id  INT(6) NOT NULL,
bill_id  INT(6) NOT NULL,
votes VARCHAR(255),
created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)";

$query->queryToDB($sql);

$sql = "CREATE TABLE  bill(
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
billIdRada  VARCHAR(6) NOT NULL,
billName TEXT,
billStatus  BOOLEAN,
date DATETIME,
za  SMALLINT,
opposite  SMALLINT,
refrained  SMALLINT,
notVote  SMALLINT,
created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)";

$query->queryToDB($sql);

echo "install successfully";