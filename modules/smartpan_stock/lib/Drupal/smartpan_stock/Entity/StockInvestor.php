<?php

/**
 * Description of StockInvestor
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Entity;

use Drupal\Core\Entity\Entity;

/**
* Defines the stock investor entity class.
*
* @EntityType(
*   id = "stock_investor",
*   label = @Translation("Stock Investor"),
*   controllers = {
*     "storage" = "SmartPan\Bundles\StockBundle\Controller\StockInvestorStorageController"
*   },
*   base_table = "stock_investor",
*   fieldable = FALSE,
*   translatable = TRUE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id"
*   }
* )
*/

class StockInvestor extends Entity
{
    var $id;
    var $type1;
    var $type2;
    var $type3;
    var $type4;
    var $type5;
    var $type6;
    var $type7;
    var $type8;
    var $type9;
    
}

?>
