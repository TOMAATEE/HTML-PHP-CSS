<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
//falls der user nicht eingeloggt ist wird er zur loginpage weitergeleitet
session_start();

if (!isset($_SESSION["userid"])){
    header("location:login.php");
    exit;
}
require_once "inc/config.inc.php";

//$sql = "SELECT * FROM users WHERE id = '".$_SESSION['user']."'";
//$row = $user->details($sql);
$statement = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$result = $statement->execute([":id" => $_SESSION["userid"]]);
$data = $statement->fetch();
if ($data["anrede"] == "M") {
    $anrede = "Herr ";
} elseif ($data["anrede"] == "W") {
    $anrede = "Frau ";
} else {
    $anrede = "";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="page-header text-center">Benutzer Login</h1>
        <div class="row">
            <div class="col-md-7 col-md-offset-3">
                <h2>Herzlich Willkommen <?php echo $anrede.$data["vorname"]." ".$data["nachname"]; ?>!</h2>
                <a href="logout.php" class="btn btn-danger"><span class="glyphicon glyphicon-log-out"></span> Logout</a>
            </div>
        </div>
    </div>
</body>
</html>