<?php

/**
 * Description of StockSize
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Entity;

use Drupal\Core\Entity\Entity;

/**
* Defines the stock size entity class.
*
* @EntityType(
*   id = "stock_size",
*   label = @Translation("Stock Size"),
*   controllers = {
*     "storage" = "SmartPan\Bundles\StockBundle\Controller\StockSizeStorageController"
*   },
*   base_table = "stock_size",
*   fieldable = FALSE,
*   translatable = TRUE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id"
*   }
* )
*/

class StockSize extends Entity
{
    var $id;
    var $price_value;
    
    var $volume_value;
    
    var $trade_date;
    
    var $trade_time;
    
    var $price_change;
    
    var $created;
    
    public function getTradeDate()
    {
        return $this->trade_date;
    }
    
    public function getTradeTime()
    {
        return $this->trade_time;
    }
    
    public function getPrice()
    {
        return $this->price_value;
    }
    
    public function getVolume()
    {
        return $this->volume_value;
    }
    
    public function getPriceChange()
    {
        return $this->price_change;
    }
    
    public function getTimestamp()
    {
        return $this->created;
    }
}

?>
