<?php

/**
 * Description of StockCacheController
 *
 * @author aswinvk28
 */

namespace SmartPan\Bundles\CacheBundle\Controller;

use Drupal\memcache\DrupalMemcacheFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\smartpan_stock\Controller\StockDBController;

class StockCacheController
{
    var $memcacheFactory;
    
    private $parameterBag;
    
    public $memcache;
    
    private $crawler;
    
    private $entityManager;
    
    public function __construct(DrupalMemcacheFactory $memcacheFactory, EntityManagerInterface $entity_manager, StockDBController $stockDBController)
    {
        $this->memcacheFactory = $memcacheFactory;
        
        $this->entityManager = $entity_manager;
        
        $this->parameterBag = new ParameterBag(array(
            'crawl.useblocksize' => true,
            'crawl.blocksize' => 200
        ));
        $this->parameterBag->set('stock.db.controller', $stockDBController);
    }
    
    public function getDashboard($stockProfile, $stockController, $timestampMap = NULL, $external = false)
    {
        $this->parameterBag->set('stock.controller', $stockController);
        
        $stock_id = $stockProfile->id();
        $symbol = $stockProfile->getStockSymbol();
        
        $this->parameterBag->set('stock.profile', $stockProfile);
        $this->parameterBag->set('stock.id', $stock_id);
        $this->parameterBag->set('stock.symbol', $symbol);
        
        $this->parameterBag->set('crawl.iscron', false);
        // stock size directly fom Yahoo
        $this->crawler = new \Drupal\smartpan_stock\Crawler\YahooIChartCrawler(
            \Drupal::service('database'),
            $this->parameterBag
        );
        
        $methodMap = $stockController->cacheMethodsConfig;
        
        $zeroTimestamp = 0;
        $historyTimestamp = NULL;
        $historyStorage = NULL;
        
        $historyDbTimestamp = $this->entityManager->getStorageController('stock_history')->getTimestamp($stockProfile->id());
        $historyDbTimestamp = \DateTime::createFromFormat('Y-m-d', $historyDbTimestamp);
        
        $historyStorageTimestamp = !empty($historyDbTimestamp) ? $historyDbTimestamp->format('Y-m-d') : $zeroTimestamp;
        
        $stockSizeDate = '';
        $sizeStorage = smartpan_stock_frequent_crawler_fetch($this->crawler, &$stockSizeDate);
        
        if(empty($sizeStorage)) {
            $sizeDashboardStorage = $this->getDashboardMethod('size', $methodMap['size'], $zeroTimestamp);
            $sizeStorage = $sizeDashboardStorage['size'];
            $stockSizeDate = \DateTime::createFromFormat('Y-m-d', $sizeStorage->getTradeDate());
        }
        
        if(empty($historyStorage)) {
            $historyDashboardStorage = $this->getDashboardMethod('history', $methodMap['history'], $historyStorageTimestamp);
            $historyStorage = $historyDashboardStorage['history'];
        }
        
        $stockController->getParameterBag()->set('stock.size', $sizeStorage);
        
        $timestampMap = array(
            'size' => NULL,
            'return' => NULL,
            'history' => $historyStorageTimestamp,
            'rank' => NULL,
            'investor' => NULL
        );
        $storage = array(
            'size' => $sizeStorage,
            'return' => NULL,
            'history' => $historyStorage,
            'rank' => NULL,
            'investor' => NULL
        );
        
        $result = array();
        foreach($methodMap as $type => $method) {
            $result += $this->getDashboardMethod($type, $method, $timestampMap[$type], $external, $storage[$type]);
        }
        return $result;
    }
    
    public function getDashboardMethod($type, $method, &$timestamp, $external = false, $storage = NULL)
    {
        if(empty($type) || empty($method)) return array();
        
        $store = false;
        if($type == 'history') {
            $store = true;
        }
        
        $stockController = $this->parameterBag->get('stock.controller');
        
        if(!$store)
        {
            $return = !empty($storage) ? $storage : $stockController->{$method}($timestamp, $external);
        }
        else
        {
            $this->memcache = $this->memcacheFactory->get('stock.db.' . $type);
            $stock_id = $this->parameterBag->get('stock.id');
            $stock_symbol = $this->parameterBag->get('stock.symbol');
            
            $cache = $this->checkCache($stock_id, $stock_symbol, $timestamp);
            $return = !empty($storage) ? $storage : !empty($timestamp) && !empty($cache) ? 
                     $cache : NULL;

            if(empty($return)) {
                $return = $stockController->{$method}($timestamp, $external);
                if(!empty($timestamp)) {
                    $this->setCache($stock_id, $stock_symbol, $timestamp, $return);
                }
            }
        }
        
        return array($type => $return);
    }
    
    public function checkCache($stock_id, $stock_symbol, $timestamp)
    {
        return $this->memcache->get($stock_id . ':' . 
                $stock_symbol . ':' . 
                $timestamp);
    }
    
    public function setCache($stock_id, $stock_symbol, $timestamp, $cache) {
        $this->memcache->set(
            $stock_id. ':' . 
            $stock_symbol . ':' . 
            $timestamp,
            $cache);
    }
    
    public function getCron($content, $timestamp, $type, $contentCompare = NULL)
    {
        $stockDbController = $this->parameterBag->get('stock.db.controller');
        $methodMap = $stockDbController->cacheMethodsConfig;
        return $this->getCronMethod($type, $methodMap[$type], $timestamp, $content, $contentCompare);
    }
    
    public function getCronMethod($type, $method, $timestamp, $storage, $storageCompare = NULL)
    {
        if(empty($type) || empty($method)) return array();
        
        $store = false;
        if($type == 'history') {
            $store = true;
            if(!empty($storageCompare)) {
                $fieldsCompare = array('date', 'stock_id', 'stock_symbol');
            }
        }
        
        $stockDbController = $this->parameterBag->get('stock.db.controller');
        $cache = NULL;
        
        if(!$store)
        {
            $sameStores = false;
            if(!empty($storageCompare)) {
                $increment = 0;
                foreach($fieldsCompare as $field) {
                    $increment = ($storage->{$field} == $storageCompare->{$field}) ? 1 : 0;
                }
                $sameStores = ($increment == count($fieldsCompare));
            }
            $cache = (!$sameStores) ? $stockDbController->{$method}($storage) : $storageCompare;
        }
        else
        {
            $this->memcache = $this->memcacheFactory->get('stock.db.' . $type);
            $stock_symbol = $this->parameterBag->get('stock.symbol');
            $stock_id = $this->parameterBag->get('stock.id');
            
            $sameStores = false;
            if(!empty($storageCompare)) {
                $increment = 0;
                foreach($fieldsCompare as $field) {
                    $increment = ($storage->{$field} == $storageCompare->{$field}) ? 1 : 0;
                }
                $sameStores = ($increment == count($fieldsCompare));
            }
            
            if(!empty($timestamp)) {
                $cache = $this->checkCache($stock_id, $stock_symbol, $timestamp);
            }
            if(empty($cache)) {
                $cache = (!$sameStores) ? $stockDbController->{$method}($storage) : $storageCompare;
                if(!empty($timestamp)) {
                    $this->setCache($stock_id, $stock_symbol, $timestamp, $cache);
                }
            }
        }
        
        return $cache;
    }
}

?>
