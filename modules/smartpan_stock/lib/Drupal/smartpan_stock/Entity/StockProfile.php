<?php

/**
 * Description of StockProfile
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Entity;

use Drupal\Core\Entity\Entity;

/**
* Defines the stock profile entity class.
*
* @EntityType(
*   id = "stock_profile",
*   label = @Translation("Stock Profile"),
*   controllers = {
*     "storage" = "SmartPan\Bundles\StockBundle\Controller\StockProfileStorageController",
*     "access" = "Drupal\smartpan_stock\Access\StockProfileAccess"
*   },
*   base_table = "stock_profile",
*   fieldable = FALSE,
*   translatable = TRUE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id"
*   }
* )
*/

class StockProfile extends Entity
{
    var $id;
    
    var $industry;
    
    var $name;
    
    var $exchange;
    
    var $currency;
    
    public function getStockSymbol()
    {
        return $this->symbol;
    }
    
    public function getStockIndustry()
    {
        return $this->industry;
    }
    
    public function getStockName()
    {
        return $this->name;
    }
    
    public function getStockExchange()
    {
        return $this->exchange;
    }
    
    public function getStockCurrency()
    {
        return $this->currency;
    }
}

?>
