<?php

/**
 * Description of StockReturn
 *
 * @author aswinvk28
 */

namespace SmartPan\Bundles\StockBundle\Model;

use Drupal\stock_enhanced\Entity\StockSize;

class StockReturn
{
    protected $stockSize;
    
    var $result;
    
    public function __construct(StockSize $stockSize, array $result)
    {
        $this->stockSize = $stockSize;
        $this->result = $this->fetchStockReturn($result);
    }
    
    public function fetchStockReturn($result)
    {
        foreach($result as &$stockHistory) {
            if(!empty($stockHistory)) {
                $stockHistory = array(
                    'price' => $this->calculateStockReturnPricePercentage($stockHistory),
                    'volume' => $this->calculateStockReturnVolumePercentage($stockHistory)
                );
            }
        }
        
        return $result;
    }
    
    public function calculateStockReturnPricePercentage($stockHistory)
    {
        if(is_null($this->calculateStockReturnPriceDifference($stockHistory))) return NULL;
        return ( $this->calculateStockReturnPriceDifference($stockHistory) / $this->stockSize->getPrice() ) * 100;
    }
    
    public function calculateStockReturnVolumePercentage($stockHistory)
    {
        if(is_null($this->calculateStockReturnVolumeDifference($stockHistory))) return NULL;
        return ( $this->calculateStockReturnVolumeDifference($stockHistory) / $this->stockSize->getVolume() ) * 100;
    }
    
    public function calculateStockReturnPriceDifference($stockHistory)
    {
        $stockPrice = $this->stockSize->getPrice();
        $stockHistoryPrice = $stockHistory->getClosePrice();
        if(empty($stockPrice) || empty($stockHistoryPrice)) return NULL;
        
        return $this->stockSize->getPrice() - $stockHistory->getClosePrice();
    }
    
    public function calculateStockReturnVolumeDifference($stockHistory)
    {
        $stockVolume = $this->stockSize->getVolume();
        $stockHistoryVolume = $stockHistory->getVolume();
        if(empty($stockVolume) || empty($stockHistoryVolume)) return NULL;
        
        return $this->stockSize->getVolume() - $stockHistory->getVolume();
    }
}

?>
