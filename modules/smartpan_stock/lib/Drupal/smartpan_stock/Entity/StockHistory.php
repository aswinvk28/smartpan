<?php

/**
 * Description of StockHistory
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Entity;

use Drupal\Core\Entity\Entity;

/**
* Defines the stock history entity class.
*
* @EntityType(
*   id = "stock_history",
*   label = @Translation("Stock History"),
*   controllers = {
*     "storage" = "SmartPan\Bundles\StockBundle\Controller\StockHistoryStorageController"
*   },
*   base_table = "stock_history",
*   fieldable = FALSE,
*   translatable = TRUE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id"
*   }
* )
*/

class StockHistory extends Entity
{
    var $id;
    var $close;
    
    var $open;
    
    var $volume;
    
    var $high;
    
    var $low;
    
    var $date;
    
    var $dividend;
    
    public function getClosePrice()
    {
        return $this->close;
    }
    
    public function getOpenPrice()
    {
        return $this->open;
    }
    
    public function getVolume()
    {
        return $this->volume;
    }
}

?>
