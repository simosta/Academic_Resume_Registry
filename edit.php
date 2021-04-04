<?php
require_once "bootstrap.php";
require_once "pdo.php";
require_once 'util.php';
session_start();
// if not logged in
if (! isset($_SESSION["name"])) {
die('ACCESS DENIED');
}
//if canceled
if (isset($_POST['cancel'])) {
header("Location:index.php");
} else {
//INPUT VALIDATION
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
  // dar reikia schools validation
    if (validateProfile()===true && validatePos()===true && validateEdu()===true) {
    $stmt = $pdo->prepare('UPDATE profile SET profile_id=:profile_id, first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE profile_id=:profile_id');
    $stmt->execute(array(
      ':fn' => htmlentities($_POST['first_name']),
      ':ln' => htmlentities($_POST['last_name']),
      ':em' => htmlentities($_POST['email']),
      ':he' => htmlentities($_POST['headline']),
      ':su' => htmlentities($_POST['summary']),
      ':profile_id'=>$_POST['profile_id']));
      // clear out old position entries
      $stmt=$pdo->prepare('DELETE FROM position WHERE profile_id=:profile_id');
      $stmt->execute(array(':profile_id'=>$_POST['profile_id']));
      //insertPositions
      insertPositions($pdo, $_REQUEST['profile_id']);
      // clear out old education entries
      $stmt=$pdo->prepare('DELETE FROM education WHERE profile_id=:profile_id');
      $stmt->execute(array(':profile_id'=>$_POST['profile_id']));
      //insert schools
      insertSchools($pdo, $_REQUEST['profile_id']);
    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
  } else {
    header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
    return;
  }
}
}

// get profile_id
$sql="SELECT * FROM profile WHERE profile_id= :xyz AND user_id=:uid";
$stmt=$pdo->prepare($sql);
$stmt->execute(array(':xyz' => $_REQUEST['profile_id'],
':uid'=>$_SESSION['user_id']));
$row= $stmt->fetch(PDO::FETCH_ASSOC);
if ($row===false) {
  $_SESSION['error']='Could not load profile';
  header('Location: index.php');
  return;
}
// prep for html
$fname=htmlentities($row['first_name']);
$lname=htmlentities($row['last_name']);
$email=htmlentities($row['email']);
$hline=htmlentities($row['headline']);
$summary=htmlentities($row['summary']);
$profile_id=$row['profile_id'];
//load up position entries
$positions=loadPos($pdo, $profile_id);
//load up education entries
$schools=loadEdu($pdo, $profile_id);
?>
<!-- VIEW -->
<!DOCTYPE html>
<html>
<head>
<title>Simona Ostachavičiūtė</title>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container">
    <h1>Editing Profile For <?php
      if (isset($_SESSION["name"])) {
        echo (htmlentities($_SESSION["name"]));
      }
    ?>
    </h1>
    <?php
      flashMessages();
    ?>
    <form method="post" action="edit.php">
      <p>First Name:
      <input type="text" name="first_name" size="60" value="<?=$fname?>"/></p>
      <p>Last Name:
      <input type="text" name="last_name" size="60" value="<?=$lname?>"/></p>
      <p>Email:
      <input type="text" name="email" size="30" value="<?=$email?>"/></p>
      <p>Headline:<br/>
      <input type="text" name="headline" size="80" value="<?=$hline?>"/></p>
      <p>Summary:<br/>
      <textarea name="summary" rows="8" cols="80"><?=$summary?></textarea>

      <?php //to retrieve old education data
      $divs=0;
      echo('<p>Education: <input type="submit" id="addEdu" value="+">');
      echo('<div id="edu_fields">');
      if(count($schools)>0){
        foreach ($schools as $school) {
          $divs++;
          echo('<div id="edu'.$divs.'"><p>Year: <input type="text" name="edu_year'.$divs.'" value="'.$school['year'].'"/>
          <input type="button" value="-"
          onclick="$(\'#edu'.$divs.'\').remove();return false;"></p>
          </p>
          <p>School: <input type="text" size="80" name="edu_school'.$divs.'" class="school" value="'.htmlentities($school['name']).'" /></p>
          </div>');
        }
      }
      echo('</div>');
       ?>
      <?php //to retrieve old position data
      $div=0;
      echo('<p>Position: <input type="submit" id="addPos" value="+">');
      echo('<div id="position_fields">');
      if(count($positions)>0){
        foreach ($positions as $position) {
          $div++;
          echo('<div id="position'.$div.'"><p>Year: <input type="text" name="year'.$div.'" value="'.$position['year'].'"/>
          <input type="button" value="-"
          onclick="$(\'#position'.$div.'\').remove();return false;"></p>
          </p>
          <p><textarea name="desc'.$div.'" rows="8" cols="80">'.htmlentities($position['description']).'</textarea></p></div>');
        }
      }
      echo('</div></p>');
       ?>
      </p>
      <p>
      <input type="hidden" name="profile_id" value="<?=$profile_id?>">
      <input type="submit" value="Save">
      <input type="submit" name="cancel" value="Cancel">
      </p>
    </form>
    <!-- adding divs -->
    <script>
    countPos = <?php echo(count($positions)); ?>;
    countEdu = <?php echo(count($schools)); ?>;
    // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $('#addPos').click(function(event){
            // http://api.jquery.com/event.preventdefault/
            event.preventDefault();
            if ( countPos >= 9 ) {
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position "+countPos);
            $('#position_fields').append(
                '<div id="position'+countPos+'"> \
                <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                <p><textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></p>\
                </div>');
        });
        $('#addEdu').click(function(event){
       event.preventDefault();
       if ( countEdu >= 9 ) {
           alert("Maximum of nine education entries exceeded");
           return;
       }
       countEdu++;
       window.console && console.log("Adding education "+countEdu);

       $('#edu_fields').append(
           '<div id="edu'+countEdu+'"> \
           <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
           <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
           <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
           </p></div>'
       );

       $('.school').autocomplete({
           source: "school.php"
       });

   });

    });
    </script>
  </div>
</body>
</html>
