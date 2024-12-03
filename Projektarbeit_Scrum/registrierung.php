<?php
//ini_set("display_errors",1);
//error_reporting(E_ALL);

session_start();

require_once("inc/config.inc.php");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <?php 
    $showFormular=true;

    $errorMessage = "";
    if(isset($_GET["register"])){
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
        } elseif (strlen($password) <= 6) {
            $errorMessage .= "Das Passwort muss mehr als 6 Zeichen lang sein.<br>";
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
                $errorMessage .= "Diese E-Mail-Adresse ist bereits vergeben.<br>Wollen Sie sich <a href='login.php'>einloggen</a>?";
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
                    echo "Registrierung erfolgreich.<a href='login.php'>Zum Login</a>";
                    $showFormular = false;
                } else {
                    $errorMessage .= "Registrierung fehlgeschlagen.<br>";
                }
            }
        }
    }
    if ($showFormular) { ?>
    <h1>Registrieren</h1>
    <form action="?register=1" method="post">
        <p>
            <label for="anrede">Anrede</label><br>
            <select name="anrede">
                <option value="D"></option>
                <option value="M">Herr</option>
                <option value="W">Frau</option>
            </select>
        </p>
        <p>
            <label for="vorname">Vorname</label><br>
            <input type="text" name="vorname">
        </p>
        <p>
            <label for="nachname">Nachname</label><br>
            <input type="text" name="nachname">
        </p>
        <p>
            <label for="email">E-mail*</label><br>
            <input type="email" name="email">
        </p>
        <p>
            <label for="password">Passwort*</label><br>
            <input type="password" name="password">
        </p>
        <p>
            <label for="password2">Passwort wiederholen*</label><br>
            <input type="password" name="password2">
        </p>
        <p>
            <input style="width: auto; height: auto;" type="checkbox" id="agb" required>
            <label for="agb">Ich akzeptiere die <a href="AGB.php" target="blank">AGB</a></label>
        </p>
        <button type="submit">Registrieren</button>
    </form>
    <form action="login.php">
        <button type="submit">zum Login</button>
    </form>
    <?php
    }
    if ($errorMessage != "") {echo $errorMessage;}
    ?>
</body>
</html>