<?php
require_once "bootstrap.php";
require_once "pdo.php";
session_start();
//if someone is not logged in
if (! isset($_SESSION["name"])) {
die('ACCESS DENIED');
}
//if cancel is pressed
if (isset($_POST['cancel'])) {
header("Location:index.php");
}
// delete the row
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}
// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}
//get profile id for deletion
$stmt = $pdo->prepare("SELECT first_name, profile_id FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>
<!-- VIEW -->
<!DOCTYPE html>
<html>
<head>
<title>Simona Ostachavičiūtė</title>
</head>
<body>
  <div class="container">
    <h1>Deleting Profile</h1>
    <?php
    $stmt=$pdo->prepare("SELECT first_name, last_name, email, headline, summary, profile_id FROM profile WHERE profile_id=:xyz");
    $stmt->execute(array(':xyz'=> $_GET['profile_id']));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    echo("<p>First Name: ".htmlentities($row['first_name'])."</p>");
    echo("<p>Last Name: ".htmlentities($row['last_name'])."</p>");
   ?>
   <form method="post">
   <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
   <input type="submit" value="Delete" name="delete" onclick="return confirm_delete();">
   <input type="submit" value="Cancel" name="cancel">
   </form>
   <script>
   // pop up to confirm
    function confirm_delete()
    {
  	  var c = confirm('Are you sure you want to delete this profile?');
  	  if(c == true) {
  		  return true;
  	  } else {
  		  return false;
  	  }
    }
  </script>
  </div>
</body>
</html>
