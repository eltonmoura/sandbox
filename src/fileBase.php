<?php
$filename = "/home/eltonms/out.txt";
// populate($filename);

function populate($filename)
{
    file_put_contents($filename, "campo1;campo2;campo3" . PHP_EOL);
    for ($i=0; $i < 4000000; $i++) {
        $msisdn = sprintf('%06d%05d', mt_rand(0, 999999), mt_rand(0, 99999));
        $txt = sprintf('%s;%s;%s', $msisdn, md5(rand()), md5(rand()));
        file_put_contents($filename, $txt . PHP_EOL, FILE_APPEND);
    }
}

if (!$fh = fopen($filename, "r")) {
    die("error");
}

while (!feof($fh)) {
    $status = '0';
    if (!isset($head)) {
        $status = 'h';
        $head = true;
    }

    file_put_contents($filename .'.index', $status . PHP_EOL, FILE_APPEND);
}
fclose($fh);
