<?php
require_once "bootstrap.php";
require_once "pdo.php";
session_start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Simona Ostachavičiūtė</title>
</head>
<body>
  <div class="container">
    <h1>Profile Information</h1>
    <?php
    $stmt=$pdo->prepare("SELECT first_name, last_name, email, headline, summary, profile_id FROM profile WHERE profile_id=:xyz");
    $stmt->execute(array(':xyz'=> $_REQUEST['profile_id']));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    echo("<p>First Name: ".htmlentities($row['first_name'])."</p>");
    echo("<p>Last Name: ".htmlentities($row['last_name'])."</p>");
    echo("<p>Email: ".htmlentities($row['email'])."</p>");
    echo("<p>Headline: ".htmlentities($row['headline'])."</p>");
    echo("<p>Summary: ".htmlentities($row['summary'])."</p>");
    // Education part
    $stmt=$pdo->prepare("SELECT year, name FROM education JOIN institution on education.institution_id=institution.institution_id WHERE profile_id=:prof ORDER BY rank");
    $stmt->execute(array(':prof'=> $_REQUEST['profile_id']));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if ($row!==false) {
      echo('<p>Education:<br/>');
      echo("<ul>");
      // list of schools
      $stmt=$pdo->prepare("SELECT year, name FROM education JOIN institution on education.institution_id=institution.institution_id WHERE profile_id=:prof ORDER BY rank");
      $stmt->execute(array(':prof'=> $_REQUEST['profile_id']));
      while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo("<li>".htmlentities($row['year']).": ");
        echo(htmlentities($row['name'])."</li>");
      }
      echo("</ul></p>");
    }
    // Position part
    $stmt=$pdo->prepare("SELECT year, description, profile_id FROM position WHERE profile_id=:pid");
    $stmt->execute(array(':pid'=> $_REQUEST['profile_id']));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if ($row!==false) {
      echo('<p>Positions:<br/>');
      echo("<ul>");
      // list of positions
      $stmt=$pdo->prepare("SELECT year, description, profile_id FROM position WHERE profile_id=:pid");
      $stmt->execute(array(':pid'=> $_REQUEST['profile_id']));
      while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo("<li>".htmlentities($row['year']).": ");
        echo(htmlentities($row['description'])."</li>");
      }
      echo("</ul></p>");
    }
    ?>
    <p><a href="index.php">Done</a></p>
  </div>
</body>
</html>
