<?php
namespace Sandbox;

use Sandbox\LoggerSingleton;

abstract class MultJobManeger
{
    /**
     * Semáforo utilizado para controlar os processos
     **/
    private $semaphore;

    /**
     * Numero que identifica o semáforo
     **/
    private $shmkey;

    /**
     * Metodo obtem uma porcao de dados que serão processados pelo job
     *
     * @param $numItems int Numero de ítens máximo que irá retornar
     * @return Boolean true se ainda existe trabalho
     **/
    abstract public function getItems($numItems);

    /**
     * Metodo que processa a porção de dados selecionado para um job
     *
     * @param $items array Items que erãoprocessados pelo metodo
     * @return Boolean true se processou com sucesso
     **/
    abstract public function process($items);

    private function getShmkey()
    {
        if (!isset($this->shmkey)) {
            $this->shmkey = rand(10000, 99999);
        }
        return $this->shmkey;
    }

    public function setShmkey($shmkey)
    {
        $this->shmkey = $shmkey;
    }

    /**
     * Metodo que inicia o processo
     *
     * @return void
     **/
    public function init($numItems = 1, $numJobs = 1)
    {
        $this->logger = LoggerSingleton::getInstance();

        register_shutdown_function(array($this, 'shutdownFunction'));
        $this->semaphore = sem_get($this->getShmkey(), $numJobs, 0666, 0);

        if (!$this->semaphore) {
            $this->logger->info('Falha ao criar o semaforo');
            exit();
        }

        $i=0;
        while ($items = $this->getItems($numItems)) {
            $i++;
            $this->logger->info('Adquirindo semaforo');
            sem_acquire($this->semaphore);

            // Faz o fork
            $pid = pcntl_fork();

            // Erro
            if ($pid == -1) {
                $this->logger->info('Erro ao criar fork');
                exit();
            }

            // Fork filho
            if (!$pid) {
                try {
                    // Processamento ------------------------------------------------------
                    $myPid = getmypid();
                    $this->logger->info(sprintf('Inicio do Job: %s, ord: %s', $myPid, $i));
                    $this->logger->info(sprintf('Items: %s', json_encode($items)));

                    $this->process($items);

                    $this->logger->info(sprintf('Fim do Job: %s, ord: %s', $myPid, $i));
                    // --------------------------------------------------------------------
                } catch (Exception $e) {
                    $this->logger->info(sprintf('Exception: %s', $e->getMessage()));
                } finally {
                    exit();
                }
            }
        }

        // Aguardando todos os processos terminarem
        while (pcntl_waitpid(0, $status) != -1) {
            $status = pcntl_wexitstatus($status);
            usleep(1000);
        }
        $this->logger->info('Fim de todos os Jobs');
    }

    public function shutdownFunction()
    {
        $this->logger->info('Liberando semaforo');
        fflush(STDOUT);
        fflush(STDERR);
        if (isset($this->semaphore)) {
            sem_release($this->semaphore);
        }
    }
}
