<?php

/**
 * Description of BraintreePlan
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the taxonomy vocabulary entity.
 *
 * @ConfigEntityType(
 *   id = "braintree_plan",
 *   label = @Translation("Braintree Plans"),
 *   controllers = {
 *     "storage" = "Drupal\smartpan_account\BraintreePlansStorageController",
 *     "list" = "Drupal\smartpan_account\Controller\BraintreePlansListController",
 *     "form" = {
 *       "default" = "Drupal\smartpan_account\Form\BraintreePlansForm",
 *     }
 *   },
 *   admin_permission = "administer braintree",
 *   config_prefix = "smartpan_account.braintree_plan",
 *   entity_keys = {
 *     "id" = "machineName"
 *   },
 *   fieldable = FALSE,
 *   render_cache = FALSE,
 *   static_cache = TRUE,
 *   field_cache = FALSE,
 *   links = {
 *     "overview-form" = "smartpan_account.braintree_plan",
 *     "edit-form" = "smartpan_account.braintree_plan_edit"
 *   },
 *   render_cache = FALSE,
 *   static_cache = TRUE,
 *   field_cache = FALSE
 * )
 */
class BraintreePlan extends ConfigEntityBase
{
    public $machineName;
    
    public $type;
    
    public $role;
    
    public function id() {
        return isset($this->machineName) ? $this->machineName : NULL;
    }
    
    public function getMachineName()
    {
        return $this->machineName;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getRole()
    {
        return $this->role;
    }
}

?>
