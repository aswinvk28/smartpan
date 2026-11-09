<?php

/**
 * Description of MarketRank
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Entity;

use Drupal\Core\Entity\Entity;

/**
* Defines the market rank entity class.
*
* @EntityType(
*   id = "market_rank",
*   label = @Translation("Market Rank"),
*   controllers = {
*     "storage" = "SmartPan\Bundles\StockBundle\Controller\MarketRankStorageController"
*   },
*   base_table = "market_rank",
*   fieldable = FALSE,
*   translatable = TRUE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id"
*   }
* )
*/

class MarketRank extends Entity
{
    var $id;
    var $market_rank;
    
    protected $value;
    
    public function __construct(array $values, $entity_type) {
        parent::__construct($values, $entity_type);
        $this->market_rank = $this->getRankPercentage();
    }
    
    public function getRankPercentage()
    {
        return $this->value;
    }
}

?>
