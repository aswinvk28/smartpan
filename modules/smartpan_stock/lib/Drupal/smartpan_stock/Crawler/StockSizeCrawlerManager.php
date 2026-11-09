<?php

/**
 * Description of StockSizeCrawlerManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

use Drupal\smartpan_stock\Crawler\StockCrawler;
use SmartPan\Bundles\CacheBundle\Controller\StockCacheController;

class StockSizeCrawlerManager
{
    protected $crawlerService;
    protected $cacheController;
    
    public function __construct(StockCrawler $cron, StockCacheController $cacheController) 
    {
        $this->crawlerService = $cron;
        $this->cacheController = $cacheController;
    }
    
    public function fetchCrawler(&$stockSizeDate)
    {
        $sizeStorage = NULL;
        if(!empty($this->crawlerService->stock_symbol) && !empty($this->crawlerService->stock_id)) {
            $this->crawlerService->fetchFrequentData();
            $urlContainer = $this->crawlerService->execute();
            foreach($urlContainer['size'] as $urls) {
                if(!is_null($sizeElement = $this->crawlerService->refineUrlContainer($urls, 'size'))) {
                    $sizeStorage = $this->dataRefine(
                        $sizeElement[0], $this->crawlerService->stock_id, 
                        $this->crawlerService->stock_symbol, REQUEST_TIME
                    );
                    if(!empty($sizeStorage)) {
                        $stockSizeDate = \DateTime::createFromFormat('n/j/Y', $sizeStorage->getTradeDate());
                        $time = \DateTime::createFromFormat('g:iA', $sizeStorage->getTradeTime());
                        list($hour, $minute, $second) = explode(':', $time->format('H:i:s'));
                        $stockSizeDate->setTime($hour, $minute, $second);
                    }
                }
            }
        }
        return $sizeStorage;
    }
    
    public function jsonRefine($content, $stock_id, $stock_symbol, $timestamp)
    {
        if($this->jsonValuesAllowable($content)) {
            $content = NULL;
        } else {
            $content = entity_create('stock_size', array(
                'stock_id' => $stock_id,
                'stock_symbol' => $stock_symbol ? $stock_symbol : $content[0],
                'created' => $timestamp,
                'price_value' => $content->LastTradePriceOnly,
                'volume_value' => $content->Volume,
                'trade_date' => $content->LastTradeDate,
                'trade_time' => $content->LastTradeTime,
                'price_change' => $content->Change
            ));
        }
        return $content;
    }
    
    public function jsonValuesAllowable($content)
    {
        /**
         * 
         * $content[0]: Symbol
         * $content[1]: LastTradePriceOnly
         * $content[2]: LastTradeDate
         * $content[3]: LastTradeTime
         * $content[4]: Change
         * $content[5]: Volume
         * $content[6]: PreviousClose
         */

        return empty($content) || empty($content->Symbol) || !is_string($content->Symbol) ||
        empty($content->LastTradePriceOnly) || !smartpan_isvalid_datetime_string($content->LastTradeDate, 'n/j/Y') ||
        empty($content->LastTradeTime);
    }
    
    public function dataRefine($content, $stock_id, $stock_symbol, $timestamp)
    {
        if($this->dataValuesAllowable($content)) {
            $content = NULL;
        } else {
            $content = entity_create('stock_size', array(
                'stock_id' => $stock_id,
                'stock_symbol' => $stock_symbol ? $stock_symbol : $content[0],
                'created' => $timestamp,
                'price_value' => $content[1],
                'volume_value' => $content[5],
                'trade_date' => $content[2],
                'trade_time' => $content[3],
                'price_change' => $content[4]
            ));
        }
        return $content;
    }
    
    public function dataValuesAllowable($content)
    {
        /**
         * 
         * $content[0]: Symbol
         * $content[1]: LastTradePriceOnly
         * $content[2]: LastTradeDate
         * $content[3]: LastTradeTime
         * $content[4]: Change
         * $content[5]: Volume
         * $content[6]: PreviousClose
         */

        return empty($content) || empty($content[0]) || !is_string($content[0]) ||
        empty($content[1]) || !smartpan_isvalid_datetime_string($content[2], 'n/j/Y') ||
        empty($content[3]);
    }
}

?>
