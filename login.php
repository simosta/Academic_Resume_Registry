<?php
require_once "bootstrap.php";
require_once "pdo.php";
require_once "util.php";
session_start();
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
  }
if (isset($_POST["email"]) && isset($_POST["pass"])) {
  unset ($_SESSION["email"]); //logout current User
  if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
    $_SESSION["error"]="User name and password are required";
    header("Location: login.php");
    return;
    } else {
    $eta='@';
    $pos=strpos($_POST['email'],$eta);
    if ($pos===false) {
      $_SESSION["error"]= "Email must have an at-sign (@)";
      header("Location: login.php");
      return;
      } else {
        $salt = 'XyZzy12*_';
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
            WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ( $row !== false ) {
        error_log("Login success ".$_POST['email']);
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        // Redirect the browser to index.php
        header("Location: index.php");
        return;
      } else {
        error_log("Login fail ".$_POST['email']." $check");
        $_SESSION["error"]= "Incorrect email or password";
        header('Location:login.php');
        return;
      }
    }
  }
}
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Simona Ostachavičiūtė</title>
<!-- function for javascript validation -->
<script>
function doValidate() {
    console.log('Validating...');
    try {
        var nam = document.getElementById('nam').value;
        var pw = document.getElementById('id_1723').value;
        var eta=nam.search('@');
        console.log("Validating nam="+nam+" pw="+pw+"eta="+eta);
        if (nam == null || nam == "" || pw == null || pw == "") {
          alert("Both fields must be filled out");
          return false;
        }
        if (eta<0) {
          alert("Invalid email address");
          return false;
        }
        return true;
    } catch(e) {
    return false;
  }
  return false;
}
</script>
</head>
<body>
  <div class="container">
    <h1>Please Log In</h1>
    <?php
      flashMessages();
    ?>
    <form method="post">
      <label for="nam">Email</label>
      <input type="text" name="email" id="nam"><br/>
      <label for="id_1723">Password</label>
      <input type="text" name="pass" id="id_1723"><br/>
      <input type="submit" onclick="return doValidate();" value="Log In">
      <input type="submit" name="cancel" value="Cancel">
    </form>
  </div>
</body>
</html>
