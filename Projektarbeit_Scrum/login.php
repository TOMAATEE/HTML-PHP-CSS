<?php
ini_set("display_errors",1);
error_reporting(E_ALL);

session_start();

require_once "inc/config.inc.php";

if (isset($_GET["login"])) {
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
        echo"Login erfolgreich. Weiter zu <a href='index.php'>interner Bereich</a>";
        header("location:index.php");
    } else {
        $errorMessage = "E-Mail oder Passwort ungültig!<br><br>";
        echo "zur Registrierung: <a href='registrierung.php'>Registrierung</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="?login=1" method="POST">
            <p>
                <label for="email">E-mail</label><br>
                <input type="email" name="email" required>
            </p>
            <p>
                <label for="password">Passwort</label><br>
                <input type="password" name="password" required>
            </p>
            <button type="submit" value="login">Login</button>
            <?php if (isset($errorMessage)) {echo $errorMessage;}?>
    </form>
    <form action="registrierung.php">
        <button type="submit">zur Registrierung</button>
    </form>
</body>
</html>