<?php

/**
 * Description of StockSizeCronManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

class StockSizeCronManager extends StockSizeCrawlerManager
{
    public function fetchCronBlock($urlContainer = array())
    {
        if(empty($urlContainer)) return;

        $stockSizeArray = $stockSizeCast = array();
        $increment = 0;
        while(array_key_exists('size.cron.'.($increment + 1), $urlContainer)) {
            $key = 'size.cron.'.($increment + 1);
            foreach($urlContainer[$key] as $index => $url) {
                if(!is_null($sizeElement = $this->crawlerService->refineUrlContainer($url, 'size'))) {
                    foreach($sizeElement as $line => $values) {
                        if(!$this->dataValuesAllowable($values)) {
                            $stock_id = $this->crawlerService->getStockSymbolId($values[0]);
                            if(empty($stock_id)) continue;
                            $stockSize = $this->dataRefine($values, $stock_id, $values[0], REQUEST_TIME);
                            if(!empty($stockSize)) {
                                $query = $this->insertCron($stockSize);
                                $stockSizeCast[$stock_id] = $stockSize;
                            }
                        }
                    }
                }
            }
            if(!$this->handleQuery($query)) {
                $stockSizeArray[$increment] = $stockSizeCast;
            }
            $increment++;
        }
        
        return $stockSizeArray;
    }
    
    public function fetchCron($urlContainer)
    {
        $stockSizeCast = array();
        foreach($urlContainer['size.cron'] as $index => $url) {
            if(!is_null($sizeElement = $this->crawlerService->refineUrlContainer($url, 'size'))) {
                foreach($sizeElement as $line => $values) {
                    if(!$this->jsonValuesAllowable($values)) {
                        $stock_id = $this->crawlerService->getStockSymbolId($values[0]);
                        $stockSize = $this->jsonRefine($values, $stock_id, $values[0], REQUEST_TIME);
                        if(!empty($stockSize)) {
                            $query = $this->insertCron($stockSize);
                            $stockSizeCast[$stock_id] = $stockSize;
                        }
                    }
                }
            }
        }
        if(!$this->handleQuery($query)) {
            return $stockSizeCast;
        }
        
        return array();
    }
    
    private function insertCron($stockSize)
    {
        return $this->cacheController->getCron(
            $stockSize, 
            REQUEST_TIME, 'size.cron', NULL
        );
    }
    
    public function handleQuery($query)
    {
        return !isset($query) || (isset($query) && !$query->execute());
    }
    
    public function fetchDataBlock()
    {
        if($this->determineFetch()) {
            $urlContainer = $this->crawlerService->fetchFrequentData()->execute();
            return $this->fetchCronBlock($urlContainer);
        }
        return array();
    }
    
    public function fetchData()
    {
        if($this->determineFetch()) {
            $urlContainer = $this->crawlerService->fetchFrequentData()->execute();
            return $this->fetchCron($urlContainer);
        }
        return array();
    }
    
    private function determineFetch()
    {
        return (REQUEST_TIME - \Drupal::state()->get('system.cron_last')) > \Drupal::config('system.cron')->get('threshold.autorun');
    }
}
