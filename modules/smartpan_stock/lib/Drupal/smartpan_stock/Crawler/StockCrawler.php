<?php

/**
 * Description of StockCrawler
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Crawler;

use Drupal\Core\Database\Connection;

abstract class StockCrawler extends Crawler
{
    var $blockSize = 200;
    
    var $useBlockSize = true;
    
    var $isCron = false;
    
    var $postBlockSize;
    
    protected $stockSymbols;
    
    protected $symbolSplitted;
    
    public function getStockSymbolId($symbol)
    {
        $stock_id = NULL;
        if($this->useBlockSize) {
            foreach($this->stockSymbols as $index => $stock) {
                if(array_key_exists($symbol, $stock)) {
                    $stock_id = $stock[$symbol]->id;
                    break;
                }
            }
        } else {
            $stock_id = $this->stockSymbols[$symbol]->id;
        }
        return $stock_id;
    }
    
    public function &getStockSymbols()
    {
        if(is_null($this->stockSymbols)) {
            $this->stockSymbols = array();
            $transaction = $this->database->startTransaction();

            try {
                $query = $this->database->select('smartpan_stock.stock_profile', 'stock')
                            ->fields('stock', array('id', 'symbol'));

                if($this->useBlockSize) {
                    $count = $this->database->query('SELECT COUNT(symbol) FROM smartpan_stock.stock_profile')->fetchField();
                    $blocks = ceil($count/$this->blockSize);

                    for($index = 0; $blocks <= $count; $index++, $blocks *= $this->blockSize) {

                        $length = $this->blockSize - ((ceil($count / $blocks) > 1) ? 0 : ($count - $blocks));

                        $new_query = clone $query;

                        $this->stockSymbols[$index] = $new_query->orderBy('stock.id')->range($index * $blocks, $length)->execute()->fetchAllAssoc('id');
                    }
                } else {
                    $stmt = $query->execute();
                    $this->stockSymbols = $stmt->fetchAllAssoc('symbol');
                }

                if(empty($this->stockSymbols)) throw new \Exception('Stock DB not initialised');

            } catch(\Exception $ex) {
                $transaction->rollback();
                throw new \Exception($ex->getMessage());
            }
        }
        return $this->stockSymbols;
    }
    
    public function symbolSplitterforMultiCurlStockSize()
    {
        if(!isset($this->symbolSplitted['size'])) {

            $this->symbolSplitted = array();
            $symbols = $this->getStockSymbols();
            
            $blockSize = $this->postBlockSize['size'];
            $this->symbolSplitted['size'] = array();
            if($this->useBlockSize) {
                foreach($symbols as $index => $chunk) {
                    $this->symbolSplitted['size'][$index] = array_chunk($chunk, $blockSize);
                }
            } else {
                $this->symbolSplitted['size'] = array_chunk($symbols, $blockSize);
            }
        }
        return $this->symbolSplitted['size'];
    }
    
    public function symbolSplitterforMultiCurlStockHistory($toRemove = array())
    {
        if(!isset($this->symbolSplitted['history'])) {

            $this->symbolSplitted = array();
            $symbols = $this->getStockSymbols();
            
            $blockSize = $this->postBlockSize['history'];
            $this->symbolSplitted['history'] = array();
            if($this->useBlockSize) {
                array_walk($symbols, function(&$chunk, $index, $toRemove) {
                    $chunk = array_diff_key($chunk, $toRemove);
                }, $toRemove);
                foreach($symbols as $index => $chunk) {
                    $this->symbolSplitted['history'][$index] = array_chunk($chunk, $blockSize);
                }
            } else {
                $symbols = array_diff_key($symbols, $toRemove);
                $this->symbolSplitted['history'] = array_chunk($symbols, $blockSize);
            }
        }
        return $this->symbolSplitted['history'];
    }
}

?>
