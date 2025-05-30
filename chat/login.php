<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $usersFile = "users.txt";

    if (file_exists($usersFile)) {
        $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($users as $userLine) {
            list($storedUser, $storedHash) = explode(":", $userLine);
            if ($storedUser === $username && password_verify($password, $storedHash)) {
                $_SESSION["username"] = $username;
                header("Location: index.php");
                exit();
            }
        }
    }
    echo "Kullanıcı adı veya şifre yanlış.";
}
?>
