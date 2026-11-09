<?php

/**
 * Description of StockHistoryCrawlerManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

use Drupal\smartpan_stock\Crawler\StockCrawler;
use SmartPan\Bundles\CacheBundle\Controller\StockCacheController;

class StockHistoryCrawlerManager
{
    protected $crawlerService;
    protected $cacheController;
    protected $dividendManager;
    
    public function __construct(StockCrawler $cron, StockCacheController $cacheController)
    {
        $this->crawlerService = $cron;
        $this->cacheController = $cacheController;
        $this->dividendManager = new StockDividendCrawlerManager($this->cronService);
    }
    
    public function fetchCrawler(&$stockHistoryDate)
    {
        $historyStorage = NULL;
        if(!empty($this->crawlerService->stock_symbol) && !empty($this->crawlerService->stock_id)) {
            $this->crawlerService->fetchDailyData();
            $urlContainer = $this->crawlerService->execute();
            $dividend = $this->dividendManager->fetchCrawler($urlContainer);
            foreach($urlContainer['history'] as $urls) {
                if(!is_null($historyElement = $this->crawlerService->refineUrlContainer($urls, 'history'))) {
                    if(!$this->dataValuesAllowable($historyElement[0])) {
                        $dividendValue = !empty($dividend[$historyElement[0][0]]) ? $dividend[$historyElement[0][0]] : 0;
                        $historyStorage = $this->dataRefine(
                            $historyElement[0], $this->crawlerService->stock_id, 
                            $this->crawlerService->stock_symbol, $dividendValue
                        );
                        if(!empty($historyStorage)) {
                            $stockHistoryDate = \DateTime::createFromFormat('Y-m-d', $historyStorage->date);
                        }
                    }
                }
            }
        }
        return $historyStorage;
    }
    
    public function jsonRefine($content, $stock_id, $stock_symbol, $dividend)
    {
        if($this->jsonValuesAllowable($content)) {
            $content = NULL;
        } else {
            $content = entity_create('stock_history', array(
                'stock_id' => $stock_id,
                'stock_symbol' => $stock_symbol,
                'date' => $content->Date,
                'open' => $content->Open,
                'high' => $content->High,
                'low' => $content->Low,
                'close' => $content->Close,
                'volume' => $content->Volume,
                'dividend' => !empty($dividend) ? $dividend : 0
            ));
        }
        return $content;
    }
    
    public function jsonValuesAllowable($content)
    {
        /**
         * 
         * $content[0]: Date
         * $content[1]: Open
         * $content[2]: High
         * $content[3]: Low
         * $content[4]: Close
         * $content[5]: Volume
         * $content[6]: Adj Close
         */

        return empty($content) || empty($content->Symbol) || !is_string($content->Symbol) ||
        !smartpan_isvalid_datetime_string($content->Date, 'Y-m-d') || empty($content->Open) ||
        empty($content->High) || empty($content->Volume);
    }
    
    public function dataRefine($content, $stock_id, $stock_symbol, $dividend)
    {
        if($this->dataValuesAllowable($content)) {
            $content = NULL;
        } else {
            $content = new StockHistory(array(
                'stock_id' => $stock_id,
                'stock_symbol' => $stock_symbol,
                'date' => $content[0],
                'open' => $content[1],
                'high' => $content[2],
                'low' => $content[3],
                'close' => $content[4],
                'volume' => $content[5],
                'dividend' => !empty($dividend) ? $dividend : 0
            ), 'stock_history');
        }
        return $content;
    }
    
    public function dataValuesAllowable($content)
    {
        /**
         * 
         * $content[0]: Date
         * $content[1]: Open
         * $content[2]: High
         * $content[3]: Low
         * $content[4]: Close
         * $content[5]: Volume
         * $content[6]: Adj Close
         */

        return empty($content) || empty($content[0]) || !smartpan_isvalid_datetime_string($content[0], 'Y-m-d') ||
        empty($content[1]) || empty($content[2]) ||
        empty($content[3]);
    }
}

?>
