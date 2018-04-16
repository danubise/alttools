<?php
date_default_timezone_set('Europe/Moscow');

if(!@include("../internal_config.php")) throw new Exception("Failed to include 'internal_config.php'");
include("../config.php");
include ("../core/mysqli.php");
include("Functions.php");

//print_r(getMiscallReport(false));
foreach (getMiscallReportTest(false) as $key=>$valueArray){
    $line=$key;
    foreach ($valueArray as $key1=>$value){
        $line.="\t| ".$key1."=>'".$value."'";
    }
    echo $line."\n";
}

?>