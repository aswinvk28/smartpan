<?php

/**
 * Description of StockCronManager
 *
 * @author aswinvk28
 */

namespace Smartpan\Bundles\StockBundle\Manager;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\smartpan_stock\Crawler\StockCrawler;
use Drupal\smartpan_stock\Crawler\StockSizeCronManager;
use Drupal\smartpan_stock\Crawler\StockHistoryCronManager;

class StockCronManager
{
    private $config;
    private $cron;
    private $parameterBag;
    private $cronService;
    private $sizeCronManager;
    private $historyCronManager;
    
    public function __construct(ConfigFactory $config, ModuleHandler $moduleHandler)
    {
        $this->config = $config;
        $this->cron = $moduleHandler->moduleExists('smartpan_cron');
        $this->parameterBag = new ParameterBag(array());
    }
    
    public function getParameterBag()
    {
        if($this->cron) {
            $this->parameterBag->set(
                'crawl.useblocksize', $this->config->get('smartpan_cron.stock')->get('use_block_size')
            );
            $this->parameterBag->set(
                'crawl.blocksize', $this->config->get('smartpan_cron.stock')->get('block_size')
            );
            $this->parameterBag->set(
                'crawl.postblocksize', 
                array(
                    'size' => $this->config->get('smartpan_cron.stock')->get('post_block_size.size'),
                    'history' => $this->config->get('smartpan_cron.stock')->get('post_block_size.history')
                )
            );
            $this->parameterBag->set('params', array(
                'endYear' => $fetchEndHistoryDate->format('Y'),
                'endDay' => $fetchEndHistoryDate->format('d'),
                'endMonth' => $fetchEndHistoryDate->format('m'),
                'startYear' => $fetchStartHistoryDate->format('Y'),
                'startDay' => $fetchStartHistoryDate->format('d'),
                'startMonth' => $fetchStartHistoryDate->format('m')
            ));
        }
        return $this->parameterBag;
    }
    
    public function setCronService(StockCrawler $cronService)
    {
        $this->cronService = $cronService;
    }
    
    public function setSizeCronManager(StockSizeCronManager $sizeCron)
    {
        $this->sizeCronManager = $sizeCron;
    }
    
    public function setHistoryCronManager(StockHistoryCronManager $historyCron)
    {
        $this->historyCronManager = $historyCron;
    }
    
    public function runCron()
    {
        if($this->cronService->useBlockSize) {
            $stockSizeArray = $this->sizeCronManager->fetchDataBlock();
            $toRemove = $this->historyCronManager->findFetchBlock($stockSizeArray);
            $this->historyCronManager->fetchDataBlock($toRemove);
        } else {
            $stockSizeArray = $this->sizeCronManager->fetchData();
            $toRemove = $this->historyCronManager->findFetch($stockSizeArray);
            $this->historyCronManager->fetchData($toRemove);
        }
    }
}
