<?php
session_start();

if (!isset($_SESSION["username"])) {
    echo "Lütfen giriş yapın.";
    exit();
}

$currentUser = $_SESSION["username"];
$partner = isset($_GET['user']) ? $_GET['user'] : '';

if (!$partner) {
    echo "Kullanıcı belirtilmedi.";
    exit();
}

$file = "message.txt";
$messages = [];

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $parts = explode("||", $line);
        if (count($parts) === 4) {
            list($from, $to, $msg, $time) = $parts;

            // Özel mesaj sadece bu iki kişi arasında
            if (
                ($from === $currentUser && $to === $partner) ||
                ($from === $partner && $to === $currentUser)
            ) {
                $safeFrom = htmlspecialchars($from, ENT_QUOTES, 'UTF-8');
                $safeMsg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
                $safeTime = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');

                $messages[] = "<div class='message'>
                    <span class='username' data-username='{$safeFrom}'>{$safeFrom}</span>: 
                    <span class='msg-text'>{$safeMsg}</span> 
                    <span class='time'>[{$safeTime}]</span>
                </div>";
            }
        }
    }
}

echo implode("", $messages);
?>
1~<?php
session_start();

if (!isset($_SESSION["username"])) {
    echo "Lütfen giriş yapın.";
    exit();
}

$currentUser = $_SESSION["username"];
$partner = isset($_GET['user']) ? $_GET['user'] : '';

if (!$partner) {
    echo "Kullanıcı belirtilmedi.";
    exit();
}

$file = "message.txt";
$messages = [];

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $parts = explode("||", $line);
        if (count($parts) === 4) {
            list($from, $to, $msg, $time) = $parts;

            // Özel mesaj sadece bu iki kişi arasında
            if (
                ($from === $currentUser && $to === $partner) ||
                ($from === $partner && $to === $currentUser)
            ) {
                $safeFrom = htmlspecialchars($from, ENT_QUOTES, 'UTF-8');
                $safeMsg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
                $safeTime = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');

                $messages[] = "<div class='message'>
                    <span class='username' data-username='{$safeFrom}'>{$safeFrom}</span>: 
                    <span class='msg-text'>{$safeMsg}</span> 
                    <span class='time'>[{$safeTime}]</span>
                </div>";
            }
        }
    }
}

echo implode("", $messages);
?>
