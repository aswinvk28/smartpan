<?php

/**
 * Description of StockVolume
 *
 * @author root
 */

namespace SmartPan\Bundles\StockBundle\Model;

use Drupal\stock_enhanced\Entity\StockSize;
use Drupal\stock_enhanced\Entity\StockHistory;

class StockVolume
{
    protected $stockSize;
    
    protected $stockHistory;
    
    var $volume;
    
    var $volume_change;
    
    public function __construct($stockSize, $stockHistory)
    {
        $this->stockSize = !empty($stockSize) ? $stockSize : new StockSize(array(), 'stock_size');
        $this->stockHistory = !empty($stockHistory) ? $stockHistory : new StockHistory(array(), 'stock_history');
        $this->volume = $this->getPreviousVolume();
        $this->volume_change = $this->getVolumePercentage();
    }
    
    public function getCurrentVolume()
    {
        return $this->stockSize->getVolume();
    }
    
    public function getPreviousVolume()
    {
        return $this->stockHistory->getVolume();
    }
    
    public function getVolumeDifference()
    {
        return $this->getCurrentVolume() - $this->getPreviousVolume();
    }
    
    public function getVolumePercentage()
    {
        $volume = $this->getPreviousVolume();
        if(empty($volume)) return NULL;
        return ($this->getVolumeDifference() / $this->getPreviousVolume()) * 100;
    }
}

?>
