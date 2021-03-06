<?php
namespace Sandbox;

use \Exception;
use GuzzleHttp\Client as HttpClient;
use Sandbox\MultJobManeger;
use Sandbox\ProgressBar;

class ComicDownloader extends MultJobManeger
{
    private $dataPath;
    private $linkPattern = "#\/\/tn\.hitomi\.la\/smalltn\/(.*?)\.jpg\'#is";
    private $baseUrl = "https://";
    private $galeryUrl;
    private $comicExtension = "cbz";
    private $httpClient;
    private $contentTempPath = '/tmp/comicDownloaderTempContent';

    public function __construct($galeryUrl)
    {
        $this->galeryUrl = $galeryUrl;
        $this->logger = LoggerSingleton::getInstance();
    }

    public function setDataPath($dataPath)
    {
        $this->dataPath = $dataPath;
    }

    public function setLinkPregReplace($linkPattern, $linkReplacement)
    {
        $this->linkPattern = $linkPattern;
        $this->linkReplacement = $linkReplacement;
    }

    public function getItems($numItems)
    {
        $items = [];
        $numItems = ($numItems < count($this->imageLinks)) ? $numItems : count($this->imageLinks);

        while (count($items) < $numItems) {
            $items[] = array_shift($this->imageLinks);
        }
        return $items;
    }

    public function process($items)
    {
        // Baixa as imagens no diretório temporário
        $this->downloadImages($items);
    }

    public function run()
    {
        if (!isset($this->galeryUrl)) {
            throw new Exception('A URL da galeria não foi informada');
        }

        if (is_file($this->galeryUrl)) {
            if ($content = file_get_contents($this->galeryUrl) == false) {
                throw new Exception(sprintf('Erro ao obter o conteúdo da galeria a partir de %s.', $this->galeryUrl));
            }
        } else {
            // Abre uma conexão HTTP
            $httpClient = self::getHttpClient();
        
             // Obtem o HTML da galeria
            $content = $httpClient->get($this->galeryUrl)->getBody();
        }

        if (! file_put_contents($this->contentTempPath, $content)) {
            throw new Exception("Erro ao gravar o conteúdo da galeria em content.htm");
        }

        // Faz o parse das URLS
        $this->imageLinks = $this->getImageLinks($content);
        $this->imageLinksCount = count($this->imageLinks);
        $this->logger->info(sprintf("Foram encontradas %s imagens\n", count($this->imageLinks)));
        #print_r($this->imageLinks); exit;

        file_put_contents('tmp.txt', '');
        $this->progressBar = new ProgressBar(count($this->imageLinks));

        // Prepara o diretório temporário para armazenar as imagens
        $this->setTempDir($content);

        // Usa o MultJobManeger para fazer o download da imagens
        $numItems = 2;
        $numJobs = 50;
        $this->init($numItems, $numJobs);

        // Compacta o diretório em um arquivo do tipo comic
        $this->makeComicBookFromDir($this->destDir);

        // Apaga o diretório temporário e todos os arquivos recursivamente
        $dirHandler = new DirHandler($this->destDir);
        $dirHandler->removeRecursively();
    }

    private function getHttpClient()
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }
        return $this->httpClient;
    }

    private function getImageLinks($content)
    {
        if (!preg_match_all($this->linkPattern, $content, $matches)) {
            throw new Exception("Não foram encontradas imagens\n$content\n");
        }
        $links = $matches[1];

        #print_r($links); exit;

        $this->imgSeq = 0;
        array_walk($links, function (&$value) {
            $value = ['seq' => $this->imgSeq++, 'link' => str_replace('<replace>', $value, $this->linkReplacement)];
        });

        #print_r($links); exit;

        return $links;
    }

    public function setTitleRegex($titleRegex)
    {
        $this->titleRegex = $titleRegex;
    }

    public function setAuthorRegex($authorRegex)
    {
        $this->authorRegex = $authorRegex;
    }

    private function setTempDir($content)
    {
        if (! preg_match($this->titleRegex, $content, $matches)) {
            throw new Exception('Não foi possível obter o título');
        }
        $titulo = trim(strip_tags($matches[1]));

        if (! preg_match($this->authorRegex, $content, $matches)) {
            throw new Exception('Não foi possível obter o autor');
        }
        $autor = trim(strip_tags($matches[1]));

        $this->destDir = $this->dataPath . Str::asSlug($autor) . '_' . Str::asSlug($titulo);

        if (! is_dir($this->destDir)) {
            if (! mkdir($this->destDir)) {
                throw new Exception(sprintf("Não foi possível criar o diretório '%s'", $this->destDir));
            }
        }

        if (! is_writable($this->destDir)) {
            throw new Exception(sprintf("Não é possível escrever no diretório '%s'", $this->destDir));
        }

        return true;
    }

    private function downloadImages($imageLinks)
    {
        $this->logger->info(sprintf('imageLinks: %s', json_encode($imageLinks)));

        foreach ($imageLinks as $row) {
            if (!preg_match("#\.([^\.]*?)$#", $row['link'], $matches)) {
                throw new Exception("Não foi possível obter a extensão do arquivo");
            }
            $imgExtension = $matches[1];

            $destFile = sprintf("%s/img%04d.%s", $this->destDir, $row['seq'], $imgExtension);

            if (is_file($destFile)) {
                file_put_contents('tmp.txt', $row['link'] . PHP_EOL, FILE_APPEND);
                continue;
            }

            $url = $row['link'];
            $this->logger->info("Copiando $url (" . $row['seq'] .")");

            if (! @copy($url, $destFile)) {
                throw new Exception(sprintf("Erro ao copiar o arquivo '%s' para '%s'.", $url, $destFile));
            }

            file_put_contents('tmp.txt', $row['link'] . PHP_EOL, FILE_APPEND);
            $this->progressBar->display(count(file('tmp.txt')));
        }
        return true;
    }

    public function makeComicBookFromDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $zipFile = sprintf('%s.%s', $dir, $this->comicExtension);
        $zipArchive = new ZipHandler();
        if (is_file($zipFile)) {
            unlink($zipFile);
        }
        $ret = $zipArchive->open($zipFile, ZipHandler::CREATE);
        if ($ret !== true) {
            throw new Exception("Failed to create archive $zipFile (". ZipHandler::errorMessage($ret) .")\n");
        }
        $zipArchive->addGlob($dir . "/*");
        if (!$zipArchive->status == ZipHandler::ER_OK) {
            throw new Exception("Failed to write files to zip\n");
        }
        $zipArchive->close();
        return true;
    }
}
