<?php
require_once "bootstrap.php";
require_once "pdo.php";
require_once 'util.php';
session_start();
$profile_id=16;
$positions=loadPos($pdo, $profile_id);
for ($i=0; $i < count($positions) ; $i++) {
    $divid='position'.($i+1);
    $year='year'.($i+1);
    $position=$positions[$i];
    $valYear=$position['year'];
    $desc='desc'.($i+1);
    $valDesc=$position['description'];
    echo('<div id="'.$divid.'"><p>Year: <input type="text" name="'.$year.'" value="'.$valYear.'"/>
    <input type="button" value="-" onclick="$("#"'.$divid.').remove();return false;">
    </p>
    <p><textarea name="'.$desc.'" rows="8" cols="80">'.$valDesc.'</textarea></p>
    ');
}

?>
