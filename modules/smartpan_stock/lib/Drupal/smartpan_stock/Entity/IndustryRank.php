<?php

/**
 * Description of IndustryRank
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Entity;

use Drupal\Core\Entity\Entity;

/**
* Defines the industry rank entity class.
*
* @EntityType(
*   id = "industry_rank",
*   label = @Translation("Industry Rank"),
*   controllers = {
*     "storage" = "SmartPan\Bundles\StockBundle\Controller\IndustryRankStorageController"
*   },
*   base_table = "industry_rank",
*   fieldable = FALSE,
*   translatable = TRUE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id"
*   }
* )
*/

class IndustryRank extends Entity
{
    var $id;
    var $industry_rank;
    
    protected $value;
    
    public function __construct(array $values, $entity_type) {
        parent::__construct($values, $entity_type);
        $this->industry_rank = $this->getRankPercentage();
    }
    
    public function getRankPercentage()
    {
        return $this->value;
    }
}

?>
