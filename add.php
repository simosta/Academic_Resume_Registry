<?php
require_once "bootstrap.php";
require_once "pdo.php";
require_once "util.php";
session_start();
// if someone is not logged in
if (! isset($_SESSION["name"])) {
die('ACCESS DENIED');
}
// if someone wants to go back
if (isset($_POST['cancel'])) {
header("Location:index.php");
} else {
  // data validation
  if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
      if (validateProfile()===true && validatePos()===true && validateEdu()===true) {
            $stmt = $pdo->prepare('INSERT INTO Profile
              (user_id, first_name, last_name, email, headline, summary)
              VALUES ( :uid, :fn, :ln, :em, :he, :su)');
          $stmt->execute(array(
              ':uid' => $_SESSION['user_id'],
              ':fn' => htmlentities($_POST['first_name']),
              ':ln' => htmlentities($_POST['last_name']),
              ':em' => htmlentities($_POST['email']),
              ':he' => htmlentities($_POST['headline']),
              ':su' => htmlentities($_POST['summary']))
          );
          $profile_id=$pdo->lastinsertId();
          // insert the positions
          $rank=1;
          for ($i=1; $i <=9 ; $i++) {
            if (!isset($_POST['year'.$i])) continue;
            if (!isset($_POST['desc'.$i])) continue;
            $year=$_POST['year'.$i];
            $desc=$_POST['desc'.$i];
            $stmt=$pdo->prepare('INSERT INTO position (profile_id, rank, year, description)
            VALUES (:pid, :rank, :year, :desc)');
            $stmt->execute(array(
              ':pid'=>$profile_id,
              ':rank'=>$rank,
              ':year'=>$year,
              ':desc'=>$desc
            ));
            $rank++;
          }
          //insert schools
          $rank=1;
          for ($i=1; $i <=9 ; $i++) {
            if (!isset($_POST['edu_year'.$i])) continue;
            if (!isset($_POST['edu_school'.$i])) continue;
            $year=$_POST['edu_year'.$i];
            $school=$_POST['edu_school'.$i];
            //is school already there?
            $institution_id=false;
            $stmt=$pdo->prepare('SELECT institution_id FROM institution WHERE name=:name');
            $stmt->execute(array(':name'=>$school));
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            if($row!==false) $institution_id=$row['institution_id'];
            //insert school if not there
            if($institution_id===false) {
              $stmt=$pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
              $stmt->execute(array(':name'=>$school));
              $institution_id=$pdo->lastInsertId();
            }
            $stmt=$pdo->prepare('INSERT INTO education (profile_id, institution_id, rank, year)
            VALUES (:pid, :iid, :rank, :year)');
            $stmt->execute(array(
              ':pid'=>$profile_id,
              ':iid'=>$institution_id,
              ':rank'=>$rank,
              ':year'=>$year
            ));
            $rank++;
        }

          $_SESSION['success'] = "Record added";
          header("Location: index.php");
          return;
      } else {
          header("Location: add.php");
          return;
      }
    }
}
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
    <h1>Adding Profile For <?php
      if (isset($_SESSION["name"])) {
        echo (htmlentities($_SESSION["name"]));
      }
    ?>
    </h1>
    <?php
      flashMessages();
    ?>
    <form method="post">
      <p>First Name:
      <input type="text" name="first_name" size="60"/></p>
      <p>Last Name:
      <input type="text" name="last_name" size="60"/></p>
      <p>Email:
      <input type="text" name="email" size="30"/></p>
      <p>Headline:<br/>
      <input type="text" name="headline" size="80"/></p>
      <p>Summary:<br/>
      <textarea name="summary" rows="8" cols="80"></textarea></p>
      <p>Education: <input type="submit" id="addEdu" value="+">
      <div id="edu_fields">
      </div>
      <p>Position: <input type="submit" id="addPos" value="+">
      <div id="position_fields">
      </div>
      </p>
      <p>
      <input type="submit" value="Add">
      <input type="submit" name="cancel" value="Cancel">
      </p>
    </form>
    <!-- adding divs -->
    <script>
    countPos = 0;
    countEdu = 0;
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
