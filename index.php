<?php
require_once "bootstrap.php";
require_once "pdo.php";
require_once "util.php";
session_start();

// retrieve the profiles
$stmt=$pdo->query('SELECT * FROM profile');
$profiles=$stmt->fetchAll(PDO::FETCH_ASSOC);
// what fetchAll does..
// $profiles=array();
// while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
// $profiles[]=$row;}
 ?>
 <!-- VIEW -->
 <!DOCTYPE html>
<html>
<head>
<title>Simona Ostachavičiūtė</title>
</head>
<body>
  <div class="container">
    <h1>Resume Registry</h1>
    <?php
    flashMessages();
    if (isset($_SESSION['user_id'])) {
      echo('<p><a href="logout.php">Logout</a></p>');
      $stmt=$pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile");
      $row= $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row!==false) {
        echo('<p><table border="1">'."\n");
        echo('<thead><tr><th>Name</th>
        <th>Headline</th><th>Action</th>
        </tr></thead>');
      }
        $stmt=$pdo->query("SELECT user_id, first_name, last_name, headline, profile_id FROM profile");
        while ($row= $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo("<tr><td>");
          echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
          echo(htmlentities($row['first_name'])." ".htmlentities($row['last_name'])."</a>");
          echo("</td><td>");
          echo(htmlentities($row['headline']));
          echo("</td><td>");
          // validation for user_id
          if ($row['user_id']==$_SESSION['user_id']) {
            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>  ');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
          }
          echo("</td></tr>");
        }
      echo("</table></p>");
      echo('<p><a href="add.php">Add New Entry</a></p>');
    } else {
      echo('<p><a href="login.php">Please log in</a></p>'."\n");
      // create table head
      $stmt=$pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile");
      $row= $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row!==false) {
        echo('<p><table border="1">'."\n");
        echo('<thead><tr>
        <th>Name</th>
        <th>Headline</th>
        </tr></thead>');
      }
      // create table itself
      $stmt=$pdo->query("SELECT first_name, last_name, headline, profile_id FROM profile");
      while ($row= $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo("<tr><td>");
          echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
          echo(htmlentities($row['first_name'])." ".htmlentities($row['last_name'])."</a>");
          echo("</td><td>");
          echo(htmlentities($row['headline']));
          echo("</td><tr>");
      }
      echo("</table></p>");
    }
  ?>
  </div>
</body>
</html>
