<?php
require __DIR__ . '/vendor/autoload.php';
require_once ('src/config.php');
set_time_limit ( 10000000 );

use App\clases\VotesParser;



$parser = new VotesParser();

$parser->ParseVotes();








