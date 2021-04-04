<?php
//flash messages to show
function flashMessages() {
  if ( isset($_SESSION['success']) ) {
  echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
  unset($_SESSION['success']);
  }
  if ( isset($_SESSION['error']) ) {
  echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
  unset($_SESSION['error']);
  }
}
// profile validation
function validateProfile() {
  if (strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 ||
   strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1) {
    $_SESSION["error"]="All fields are required";
      return false;
    }
  $pos=strpos($_POST['email'],'@');
    if ($pos===false) {
      $_SESSION["error"]= "Email must have an at-sign (@)";
      return false;
      }
    return true;
}


function loadPos($pdo, $profile_id) {
  $stmt=$pdo->prepare('SELECT * FROM Position WHERE profile_id=:prof ORDER BY rank');
  $stmt->execute(array(':prof'=>$profile_id));
  $positions=$stmt->fetchALL(PDO::FETCH_ASSOC);
  return $positions;
  }

  function loadEdu($pdo, $profile_id) {
    $stmt=$pdo->prepare('SELECT year, name FROM education JOIN institution on education.institution_id=institution.institution_id WHERE profile_id=:prof ORDER BY rank');
    $stmt->execute(array(':prof'=>$profile_id));
    $schools=$stmt->fetchALL(PDO::FETCH_ASSOC);
    return $schools;
    }

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if (!isset($_POST['year'.$i])) continue;
    if (!isset($_POST['desc'.$i])) continue;
    $year=$_POST['year'.$i];
    $desc=$_POST['desc'.$i];
    if (strlen($year)==0 || strlen($desc)==0) {
      $_SESSION["error"]="All fields are required";
      return false;
    }
    if(! is_numeric($year)) {
      $_SESSION["error"]="Year must be numeric";
      return false;
    }
  }
  return true;
}

function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if (!isset($_POST['edu_year'.$i])) continue;
    if (!isset($_POST['edu_school'.$i])) continue;
    $year=$_POST['edu_year'.$i];
    $school=$_POST['edu_school'.$i];
    if (strlen($year)==0 || strlen($school)==0) {
      $_SESSION["error"]="All fields are required";
      return false;
    }
    if(! is_numeric($year)) {
      $_SESSION["error"]="Year must be numeric";
      return false;
    }
  }
  return true;
}

// insert the positions
function insertPositions($pdo, $profile_id) {
  $rank=1;
  for ($i=1; $i <=9 ; $i++) {
    if (!isset($_POST['year'.$i])) continue;
    if (!isset($_POST['desc'.$i])) continue;
    $year=$_POST['year'.$i];
    $desc=$_POST['desc'.$i];
    $stmt=$pdo->prepare('INSERT INTO position (profile_id, rank, year, description)
    VALUES (:pid, :rank, :year, :desc)');
    $stmt->execute(array(
      ':pid'=>$_POST['profile_id'],
      ':rank'=>$rank,
      ':year'=>$year,
      ':desc'=>$desc
    ));
    $rank++;
  }
}
//insert schools
function insertSchools($pdo, $profile_id){
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
      ':pid'=>$_POST['profile_id'],
      ':iid'=>$institution_id,
      ':rank'=>$rank,
      ':year'=>$year
    ));
    $rank++;
}
}
