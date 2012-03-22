<?php
try {
    echo "<pre>";
    var_dump(file_get_contents('phar://sg.phar/src/Sg/Command/GenerateCommand.php'));
    echo "</pre>" . PHP_EOL;
    $tarphar = new Phar('sg.phar');
//    $tarphar2 = new Phar('mageekguy.atoum.phar');
//    $zip = $tarphar->convertToData(Phar::ZIP);
//    $zip2 = $tarphar2->convertToData(Phar::ZIP);
//    $tgz = $tarphar->convertToData(Phar::TAR);
//    $tgz2 = $tarphar2->convertToData(Phar::TAR);
} catch (Exception $e) {
    echo "<pre>";
    var_dump($e->getMessage());
    echo "</pre>" . PHP_EOL;
    die("FFFFFUUUUUCCCCCKKKKK" . PHP_EOL);
}
?>