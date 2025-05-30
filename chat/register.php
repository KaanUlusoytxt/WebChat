<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username === "" || $password === "") {
        echo "Kullanıcı adı ve şifre boş olamaz.";
        exit;
    }

    $usersFile = "users.txt";
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, "");
    }

    $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $userLine) {
        list($storedUser,) = explode(":", $userLine);
        if ($storedUser === $username) {
            echo "Bu kullanıcı adı zaten alınmış.";
            exit;
        }
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents($usersFile, $username . ":" . $hash . PHP_EOL, FILE_APPEND | LOCK_EX);

    echo "Kayıt başarılı! <a href='login.html'>Giriş yap</a>";
}
?>
