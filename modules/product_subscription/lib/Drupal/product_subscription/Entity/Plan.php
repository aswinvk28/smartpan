<?php

/**
 * Plan Entity represents the plan adopted by a member
 *
 * @author aswinvk28
 */

namespace Drupal\product_subscription\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\FieldDefinition;

/**
* Defines the plan entity type.
*
* @ContentEntityType(
*   id = "plan",
*   label = @Translation("Plan"),
*   controllers = {
*     "storage" = "Drupal\product_subscription\PlanStorageController",
*     "list" = "Drupal\product_subscription\Controller\PlanListController",
*     "form" = {
*       "default" = "Drupal\product_subscription\Form\PlanFormController",
*       "delete" = "Drupal\product_subscription\Form\PlanDeleteForm"
*     }
*   },
*   admin_permission = "administer plans",
*   base_table = "smartpan_plan",
*   fieldable = FALSE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   field_cache = FALSE, 
*   entity_keys = {
*     "id" = "id",
*     "label" = "title",
*     "uuid" = "uuid"
*   },
*   links = {
*     "overview-form" = "product_subscription.plan_overview",
*     "edit-form" = "product_subscription.plan_edit",
*     "delete-form" = "product_subscription.plan_delete"
*   }
* )
*/

class Plan extends ContentEntityBase
{
    const UNLIMITED = 'Unlimited Validity';
    const VALIDITY = 'Valid For:';
    
    public function id()
    {
        return $this->get('id')->value;
    }
    
    public function planPublicTitle()
    {
        return $this->get('title')->value.' - '.$this->planPriceMapping();
    }
    
    public function planValidityMapping($prefix = NULL)
    {
        if(is_null($prefix)) $prefix = static::VALIDITY;
        $validity = $this->get('validity')->value;
        if(empty($validity)) {
            return static::UNLIMITED;
        }
        $durationTerm = entity_load('taxonomy_term', $this->get('duration')->value);
        if(empty($durationTerm)) throw new \Exception('Environment not set up');
        return $prefix . ' ' . $validity.' '.$durationTerm->get('name')->value;
    }
    
    public function planPriceMapping()
    {
        $term = entity_load('taxonomy_term', $this->get('type')->value);
        if(!empty($term) && floatval($this->get('price')->value) == 0.00) {
            $price = 0.00;
            return $this->get('currency')->value.': '.$price;
        } else if(!empty($term) && floatval($this->get('price')->value) != 0.00) {
            $durationTerm = entity_load('taxonomy_term', $this->get('duration')->value);
            if(empty($durationTerm)) throw new \Exception('Environment not set up');
            return $this->get('currency')->value.': '.floatval($this->get('price')->value).' / '.$durationTerm->get('name')->value;
        }
        return 'Wrong Plan Setting';
    }
    
    public function getBraintreePlan()
    {
        $braintreePlan = entity_load('braintree_plan', $this->get('plan_machine_name')->value);
        if(!empty($braintreePlan)) return $braintreePlan;
        return entity_create('braintree_plan', array());
    }
    
    public function isUpgrade($currentPlan)
    {
        return $this->get('price')->value < $currentPlan->get('price')->value;
    }
    
    /**
     * 
     * @param type $entity_type
     * @return type
     */
    public static function baseFieldDefinitions($entity_type) {
        $fields['id'] = FieldDefinition::create('integer')
          ->setLabel(t('Plan ID'))
          ->setDescription(t('The plan entity ID.'))
          ->setReadOnly(TRUE);

        $fields['uuid'] = FieldDefinition::create('uuid')
          ->setLabel(t('UUID'))
          ->setDescription(t('The plan entity UUID.'))
          ->setReadOnly(TRUE);
        
        $fields['plan_machine_name'] = FieldDefinition::create('string')
          ->setLabel(t('Plan machine Name'))
          ->setSetting('max_length', 32)
          ->setDescription(t('The plan entity machine name.'))
          ->setRequired(TRUE);

        $fields['title'] = FieldDefinition::create('string')
          ->setLabel(t('Plan Title'))
          ->setDescription(t('The plan title.'))
          ->setSetting('max_length', 64)
          ->setRequired(TRUE);
        
        $fields['price'] = FieldDefinition::create('float')
          ->setLabel(t('Plan Price'))
          ->setDescription(t('The plan price.'))
          ->setSetting('default_value', 0.00)
          ->setRequired(TRUE);
        
        $fields['currency'] = FieldDefinition::create('string')
          ->setLabel(t('Plan Currency'))
          ->setDescription(t('The plan currency.'))
          ->setSetting('default_value', 'USD')
          ->setRequired(TRUE)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(smartpan_load_multiple_currency())));

        $fields['type'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Plan Type'))
          ->setDescription(t('The plan type.'))
          ->setSetting('target_type', 'taxonomy_term')
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(\Drupal::entityManager()->getStorageController('braintree_plan')->planOptions('type'))));
        
        $fields['duration'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Plan Duration'))
          ->setDescription(t('The plan type.'))
          ->setSetting('target_type', 'taxonomy_term')
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(\Drupal::entityManager()->getStorageController('braintree_plan')->planOptions('duration'))));
        
        $fields['status'] = FieldDefinition::create('boolean')
          ->setLabel(t('Plan Status'))
          ->setDescription(t('The plan status'))
          ->setSetting('default_value', 0);
        
        $fields['highlight'] = FieldDefinition::create('boolean')
          ->setLabel(t('Plan Highlight'))
          ->setDescription(t('Highlighting a plan'))
          ->setSetting('default_value', 0);

        $fields['description'] = FieldDefinition::create('text_long')
          ->setLabel(t('Plan Description'))
          ->setSetting('default_value', '')
          ->setDescription(t('The description of the plan'));
        
        $fields['validity'] = FieldDefinition::create('integer')
          ->setLabel(t('Plan Validity'))
          ->setDescription(t('The validity of the plan'))
          ->setSetting('default_value', 1);

        return $fields;
    }
}

?>
