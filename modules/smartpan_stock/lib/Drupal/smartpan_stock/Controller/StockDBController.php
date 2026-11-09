<?php

/**
 * Description of StockController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Controller;

use Drupal\stock_enhanced\Entity\StockHistory;
use Drupal\stock_enhanced\Entity\StockSize;
use Drupal\Core\Database\Connection;

class StockDBController
{
    /**
     *
     * @var \Drupal\Core\Database\Connection $database
     */
    protected $database;
    
    private $sizeQuery;
    private $historyQuery;
    
    var $cacheMethodsConfig = array(
        'history.cron' => 'writeRecentStockHistoryQueryBuilder',
        'history' => 'writeRecentStockHistory',
        'size.cron' => 'writeStockSizeQueryBuilder'
    );

    /**
     * 
     * Constructs a \Drupal\aggregator\Controller\AggregatorController object.
     *
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }
    
    /**
     * 
     * @param array $quotesList
     * @return type
     */
    public function writeStockSizeQueryBuilder(StockSize $quotesList)
    {
        if(!isset($this->sizeQuery)) {
            $this->sizeQuery = $this->database->insert('stock_size')->fields(array(
                'stock_id',
                'stock_symbol',
                'price_value',
                'price_change',
                'volume_value',
                'trade_date',
                'trade_time',
                'created'
            ));
        }
        return $this->sizeQuery->values(array(
            $quotesList->stock_id,
            $quotesList->stock_symbol,
            $quotesList->price_value,
            $quotesList->price_change,
            $quotesList->volume_value,
            $quotesList->trade_date,
            $quotesList->trade_time,
            $quotesList->created
        ));
    }
    
    /**
     * 
     * @param type $values
     * @return boolean|\Drupal\stock_enhanced\Entity\StockHistory
     */
    public function writeRecentStockHistory($stockHistory)
    {
        if(!$stockHistory instanceof StockHistory) {
            $stockHistory = new StockHistory(array(
                'enforceIsNew' => true,
                'stock_id' => $stockHistory['stock_id'],
                'stock_symbol' => $stockHistory['stock_symbol'],
                'date' => $stockHistory['date'],
                'open' => $stockHistory['open'],
                'close' => $stockHistory['close'],
                'high' => $stockHistory['high'],
                'low' => $stockHistory['low'],
                'volume' => $stockHistory['volume'],
                'dividend' => $stockHistory['dividend']
            ), 'stock_history');
        }
        
        $saveStatus = $this->entityManager()->getStorageController('stock_history')->save($stockHistory);
        
        if(empty($saveStatus)) return FALSE;
        return $stockHistory;
    }
    
    /**
     * 
     * @param type $values
     * @return boolean|\Drupal\stock_enhanced\Entity\StockHistory
     */
    public function writeRecentStockHistoryQueryBuilder($values)
    {
        if(!isset($this->historyQuery)) {
            $this->historyQuery = $this->database->insert('stock_history')->fields(array(
                'stock_id',
                'stock_symbol',
                'date',
                'open',
                'close',
                'high',
                'low',
                'volume',
                'dividend'
            ));
        }
        return $this->historyQuery->values(array(
            $values->stock_id,
            $values->stock_symbol,
            $values->date,
            $values->open,
            $values->close,
            $values->high,
            $values->low,
            $values->volume,
            $values->dividend
        ));
    }
}

?>
