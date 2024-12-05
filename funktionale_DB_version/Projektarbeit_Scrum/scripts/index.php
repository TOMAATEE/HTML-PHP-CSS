<?php 
session_start();
require_once("../inc/config.inc.php");

$email = $_POST["email"];
$password = $_POST["password"];

$errorMessage = "";
if(isset($_POST['signUp'])){
    $error = false;

    $password2 = $_POST["password2"];
    $vorname = $_POST["vorname"];
    $nachname = $_POST["nachname"];
    $anrede = $_POST["anrede"];
    
    if (empty($password)) {
        $errorMessage .= "Bitte ein Passwort eingeben.<br>";
        $error=true;
    } elseif ($password != $password2) {
        $errorMessage .= "Die Passwörter müssen übereinstimmen.<br>";
        $error=true;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage .= "Bitte eine gültige E-Mail-Adresse eingeben.<br>";
        $error=true;
    }
    if (!$error) {

        if ($user) {
            $errorMessage .= "Diese E-Mail-Adresse ist bereits vergeben.<br>";
            $error = true;
        }
        if (!$error) {
            $passwort_hash = password_hash($password, PASSWORD_DEFAULT);
            $statement = $pdo->prepare("INSERT INTO users (email, passwort, vorname, nachname, anrede) 
            VALUES (:email, :passwort, :vorname, :nachname, :anrede)");
            $result = $statement->execute([
                "email"    => $email, 
                "passwort" => $passwort_hash, 
                "vorname"  => $vorname, 
                "nachname" => $nachname,
                "anrede"   => $anrede
            ]);
            if ($result) {
                header("location:../index.html");
            } else {
                $errorMessage .= "Registrierung fehlgeschlagen.<br>";
            }
        }
    }   
    if ($errorMessage != "") echo $errorMessage;
}

if(isset($_POST['signIn'])){
    $statement = $pdo->prepare("SELECT * FROM users WHERE email = :param1");
    $result = $statement->execute([":param1" => $email]);
    $user = $statement->fetch();
    if ($user != false && password_verify($password,$user["passwort"])) { // wie der Name der Spalte in der Datenbank
        $_SESSION["user_id"] = $user["id"];
        header("location:../todo.html"); // Weiterleitung zur Hauptseite
    } else {
        $errorMessage = "E-Mail oder Passwort ungültig!<br>";
    }
    if ($errorMessage != "") echo $errorMessage;
}
