<?php

/**
 * Description of StockHistoryCronManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

class StockHistoryCronManager extends StockHistoryCrawlerManager
{
    public function fetchCronBlock($urlContainer = array())
    {
        if(empty($urlContainer)) return;
        
        $dividend = $this->dividendManager->fetchCronBlock($urlContainer);

        $increment = 0;
        while(array_key_exists('history.cron.'.($increment + 1), $urlContainer)) {
            $key = 'history.cron.'.($increment + 1);
            foreach($urlContainer[$key] as $index => $url) {
                if(!is_null($historyElement = $this->crawlerService->refineUrlContainer($url, 'history'))) {
                    foreach($historyElement as $line => $values) {
                        if(!$this->jsonValuesAllowable($values)) {
                            $stock_id = $this->crawlerService->getStockSymbolId($values->Symbol);
                            if(empty($stock_id)) continue;
                            $stockHistory = $this->jsonRefine($values, $stock_id, $symbol, 
                                !empty($dividend[$values->Symbol][$values->Date]) ? $dividend[$values->Symbol][$values->Date] : 0);
                            if(!empty($stockHistory)) {
                                $query = $this->insertCron($stockHistory);
                            }
                        }
                    }
                }
            }
            $this->handleQuery($query);
            $increment++;
        }
    }
    
    public function fetchCron($urlContainer)
    {
        $dividend = $this->dividendManager->fetchCron($urlContainer);
        
        foreach($urlContainer['history.cron'] as $index => $url) {
            if(!is_null($historyElement = $this->crawlerService->refineUrlContainer($url, 'history'))) {
                foreach($historyElement as $line => $values) {
                    if(!$this->jsonValuesAllowable($values)) {
                        $stockHistory = $this->jsonRefine($values, $this->crawlerService->getStockSymbolId($symbol), $values[0], 
                                    !empty($dividend[$values[1]]) ? $dividend[$values[1]] : NULL);
                        $query = $this->insertCron($stockHistory);
                    }
                }
            }
        }
        $this->handleQuery($query);
    }
    
    private function insertCron($stockHistory)
    {
        if($this->determineInsert()) {
            return $this->cacheController->getCron(
                $stockHistory, 
                REQUEST_TIME, 'history.cron'
            );
        }
        return NULL;
    }
    
    private function determineInsert($stockHistory)
    {
        $query = \Drupal::entityQuery('stock_history');
        $query->condition('date', $stockHistory->date);
        $query->condition('stock_id', $stockHistory->stock_id);
        $ids = $query->execute();
        return empty($ids);
    }
    
    public function handleQuery($query)
    {
        if(!isset($query) || (isset($query) && !$query->execute())) {
            throw new \Exception('Entries not saved');
        }
    }
    
    public function fetchDataBlock($toRemove)
    {
        if($this->determineFetch($toRemove)) {
            $urlContainer = $this->crawlerService->fetchDailyData($toRemove)->execute();
            return $this->fetchCronBlock($urlContainer);
        }
        return array();
    }
    
    public function fetchData($toRemove)
    {
        if($this->determineFetch($toRemove)) {
            $urlContainer = $this->crawlerService->fetchDailyData($toRemove)->execute();
            return $this->fetchCron($urlContainer);
        }
        return array();
    }
    
    private function determineFetch($toRemove)
    {
        return REQUEST_TIME - \Drupal::state()->get('system.cron_last') > \Drupal::config('system.cron')->get('threshold.autorun');
    }
    
    public function findFetchBlock($stockSizeArray)
    {
        $toRemove = array();
        foreach($stockSizeArray as $increment => $stockSizeCast) {
            foreach($stockSizeCast as $stock_id => $stockSize) {
                $stockSizeDateTime = \DateTime::createFromFormat('n/j/Y', $stockSize->getTradeDate());
                $historyDBDateString = \Drupal::entityManager()->getStorageController('stock_history')->getTimestamp($stock_id);
                if($stockSizeDateTime->format('Y-m-d') == $historyDBDateString) {
                    $toRemove[$stock_id] = $stockSize->stock_symbol;
                }
            }
        }
        
        return $toRemove;
    }
    
    public function findFetch($stockSizeCast)
    {
        $toRemove = array();
        foreach($stockSizeCast as $stock_id => $stockSize) {
            $stockSizeDateTime = \DateTime::createFromFormat('n/j/Y', $stockSize->getTradeDate());
            $historyDBDateString = \Drupal::entityManager()->getStorageController('stock_history')->getTimestamp($stock_id);
            if($stockSizeDateTime->format('Y-m-d') == $historyDBDateString) {
                $toRemove[$stock_id] = $stockSize->stock_symbol;
            }
        }
        
        return $toRemove;
    }
}
