<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmartPan\Bundles\CacheBundle\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use SmartPan\Bundles\StockBundle\Crawler;

/**
 * Description of TimestampController
 *
 * @author aswinvk28
 */
class TimestampController extends ControllerBase
{
    private $database;
    
    private $entityManager;
    
    private $crawler;
    
    public function __construct($crawler)
    {
        $this->crawler = $crawler;
    }
    
    public function create(Crawler $crawler)
    {
        return new static($crawler);
    }
    
    /**
     * @return int
     */
    public function checkTimestampReturn($stock_id, $stock_symbol, $timestamp)
    {
        $stockHistory = $this->entityManager()->getStorageController('stock_history');
        
        $dbTimestamp = $stockHistory->getTimestamp($stock_id);
        
        $cacheController = \Drupal::getContainer()->get('memcache.stock_controller');
        
        $cache = $cacheController->checkDashboardCache($stock_id, $stock_symbol, 'return', $dbTimestamp);
        
        if(isset($cache)) {
            return $dbTimestamp;
        }
        return NULL;
    }
}

?>
