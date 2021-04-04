<?php
$pdo=new PDO('mysql:host=localhost;port=3306;dbname=misc','fred','zap');
// 3306 windows 8889 mac
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
