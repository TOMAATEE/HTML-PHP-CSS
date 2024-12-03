<?php 

require_once "../inc/config.inc.php";

$errorMessage = "";
if(isset($_POST['signUp'])){
    $error = false;

    $email = $_POST["email"];
    $password = $_POST["password"];
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
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :param1");
        $result = $statement->execute([":param1" => $email]);
        $user = $statement->fetch();

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
    $email    = $_POST["email"];
    $passwort = $_POST["password"];  //wie der Name des Passwort-Felds

    // UNSICHER
    //$result = $pdo->query("SELECT * FROM users WHERE email = '$email'");
    //$user = $result->fetch();

    //sicher durch prepare weil es SQL-Injection verhindert (= ein always true statement z.b. email@email.de OR "1" = "1")
    $statement = $pdo->prepare("SELECT * FROM users WHERE email = :param1");
    $result = $statement->execute([":param1" => $email]);
    $user = $statement->fetch();

    if ($user != false && password_verify($passwort,$user["passwort"])) { // wie der Name der Spalte in der Datenbank
        $_SESSION["userid"] = $user["id"];
        header("location:../index.html"); // Weiterleitung zur Hauptseite
    } else {
        $errorMessage = "E-Mail oder Passwort ungültig!<br>";
    }
    if ($errorMessage != "") echo $errorMessage;
}