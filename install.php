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
    name VARCHAR(255) NOT NULL
    )";

$query->queryToDB($sql);




$sql = "CREATE TABLE  votes(
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
deputat_id  INT(6) NOT NULL,
bill_id  INT(6) NOT NULL,
votes VARCHAR(255)
)";

$query->queryToDB($sql);