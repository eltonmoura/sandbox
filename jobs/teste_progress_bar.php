#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

use Sandbox\ProgressBar;

$total = 15;
$progressBar = new ProgressBar($total);

$progressBar->display(0);

for ($i=1; $i <= $total; $i++) {
    sleep(2);
    $progressBar->display($i);
}
