#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

use Sandbox\ComicDownloader;

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}

$links = file($argv[1]);
$links = array_map('trim', $links);

$dir = $argv[2];

if (!is_dir($dir)){
	mkdir($dir);
}

foreach ($links as $link) {
	$outputFile = $dir . substr(strrchr($link, '/'), 1);
	if (is_file($outputFile)) {
		continue;
	}
	
	// Faz o fork
    $pid = pcntl_fork();

    // Erro
    if ($pid == -1) {
        $this->logger->info('Erro ao criar fork');
        exit();
    }

    // Fork filho
    if (!$pid) {
		$response = request($link);

		if (!$response['error']) {
			file_put_contents($outputFile, $response['content']);
			print("Downloaded: " . $outputFile . "\n");
		} else {
			print("Error: " . $response['error'] . "\n");
		}
		exit(0);
	}
}

// Aguardando todos os processos terminarem
while (pcntl_waitpid(0, $status) != -1) {
    $status = pcntl_wexitstatus($status);
    usleep(1000);
}


$logger->info('Done.');

function request($url)
{
	$curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT,
	'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_setopt($curl, CURLOPT_TIMEOUT, 2000);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:9050');
    curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME); // 7 CURLPROXY_SOCKS5
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    
    $content = curl_exec($curl);
	$error = curl_error($curl);
	
	curl_close($curl);

	return ['content' => $content, 'error' => $error];
}
 

