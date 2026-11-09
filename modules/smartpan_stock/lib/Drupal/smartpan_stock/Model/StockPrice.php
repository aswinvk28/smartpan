<?php

/**
 * Description of StockPrice
 *
 * @author aswinvk28
 */

namespace SmartPan\Bundles\StockBundle\Model;

use Drupal\stock_enhanced\Entity\StockSize;
use Drupal\stock_enhanced\Entity\StockHistory;

class StockPrice
{
    protected $stockSize;
    
    protected $stockHistory;
    
    var $price;
    
    var $price_change;
    
    public function __construct($stockSize, $stockHistory)
    {
        $this->stockSize = !empty($stockSize) ? $stockSize : new StockSize(array(), 'stock_size');
        $this->stockHistory = !empty($stockHistory) ? $stockHistory : new StockHistory(array(), 'stock_history');
        $this->price = $this->getCurrentPrice();
        $this->price_change = $this->getPricePercentage();
    }
    
    public function getCurrentPrice()
    {
        return $this->stockSize->getPrice();
    }
    
    public function getPriceDifference()
    {
        return $this->stockSize->getPriceChange();
    }
    
    public function getPricePercentage()
    {
        $closePrice = $this->stockHistory->getClosePrice();
        if(empty($closePrice)) return NULL;
        return ( $this->getPriceDifference() / $this->stockHistory->getClosePrice() ) * 100;
    }
}

?>
