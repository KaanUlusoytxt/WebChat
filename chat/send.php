<?php
session_start();

function censorBadWords($text) {
    $badWords = ['am','amcığa','amcığı','amcığın','amcık','amcıklar','amcıklara','amcıklarda','amcıklardan','amcıkları','amcıkların','amcıkta','amcıktan','amı','amlar','çingene','Çingenede','Çingeneden','Çingeneler','Çingenelerde','Çingenelerden','Çingenelere','Çingeneleri','Çingenelerin','Çingenenin','Çingeneye','Çingeneyi','göt','göte','götler','götlerde','götlerden','götlere','götleri','götlerin','götte','götten','götü','götün','götveren','götverende','götverenden','götverene','götvereni','götverenin','götverenler','götverenlerde','götverenlerden','götverenlere','götverenleri','götverenlerin','kaltağa','kaltağı','kaltağın','kaltak','kaltaklar','kaltaklara','kaltaklarda','kaltaklardan','kaltakları','kaltakların','kaltakta','kaltaktan','orospu','orospuda','orospudan','orospular','orospulara','orospularda','orospulardan','orospuları','orospuların','orospunun','orospuya','orospuyu','otuz birci','otuz bircide','otuz birciden','otuz birciler','otuz bircilerde','otuz bircilerden','otuz bircilere','otuz bircileri','otuz bircilerin','otuz bircinin','otuz birciye','otuz birciyi','saksocu','saksocuda','saksocudan','saksocular','saksoculara','saksocularda','saksoculardan','saksocuları','saksocuların','saksocunun','saksocuya','saksocuyu','sıçmak','sik','sike','siker sikmez','siki','sikilir sikilmez','sikin','sikler','siklerde','siklerden','siklere','sikleri','siklerin','sikmek','sikmemek','sikte','sikten','siktir','siktirir siktirmez','taşağa','taşağı','taşağın','taşak','taşaklar','taşaklara','taşaklarda','taşaklardan','taşakları','taşakların','taşakta','taşaktan','yarağa','yarağı','yarağın','yarak','yaraklar','yaraklara','yaraklarda','yaraklardan','yarakları','yarakların','yarakta','yaraktan']; // Buraya sansürlenecek kelimeleri ekle
    $replacement = '[***]';

    foreach ($badWords as $badWord) {
        $pattern = '/' . preg_quote($badWord, '/') . '/i';
        $text = preg_replace($pattern, $replacement, $text);
    }
    return $text;
}

if (!isset($_SESSION["username"])) {
    echo "Giriş yapılmamış!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST["message"]))) {
    $from = $_SESSION["username"];
    $to = isset($_POST["to"]) && trim($_POST["to"]) !== '' ? trim($_POST["to"]) : "ALL";
    $message = trim($_POST["message"]);

    $message = censorBadWords($message);  // Burada sansür uygula

    $safe_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $safe_to = htmlspecialchars($to, ENT_QUOTES, 'UTF-8');

    $time = date("Y-m-d H:i:s");
    $line = $from . "||" . $safe_to . "||" . $safe_message . "||" . $time . PHP_EOL;

    file_put_contents("message.txt", $line, FILE_APPEND | LOCK_EX);

    echo "OK";
} else {
    echo "Mesaj boş olamaz.";
}
?>
