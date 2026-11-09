<?php

/**
 * Subscription Entity represents the subscription chosen by the member
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

/**
* Defines the subscription entity type.
*
* @ContentEntityType(
*   id = "subscription",
*   label = @Translation("Subscription"),
*   controllers = {
*     "storage" = "Drupal\user_subscription\SubscriptionStorageController",
*     "list" = "Drupal\user_subscription\Controller\SubscriptionListController",
*     "form" = {
*       "default" = "Drupal\user_subscription\Form\SubscriptionFormController",
*       "delete" = "Drupal\user_subscription\Form\SubscriptionDeleteForm"
*     }
*   },
*   base_table = "smartpan_subscription",
*   admin_permission = "administer subscriptions",
*   fieldable = FALSE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   field_cache = FALSE,
*   entity_keys = {
*     "id" = "id",
*     "uuid" = "uuid",
*     "bundle_of" = "bundle"
*   },
*   links = {
*     "delete-form" = "user_subscription.subscription_delete",
*     "overview-form" = "user_subscription.subscription_overview",
*     "edit-form" = "user_subscription.subscription_edit"
*   }
* )
*/

class Subscription extends ContentEntityBase
{
    const ACTIVE = 1;
    const CANCELED = 100;
    const EXPIRED = 0;
    const PAST_DUE = 102;
    const PENDING = 101;
    
    private $plan;
    private $smartpanUser;
    
    public function preSave(EntityStorageControllerInterface $storage_controller) {
        parent::preSave($storage_controller);
        if($this->isNew()) {
            if(!isset($this->get('created')->value)) {
                $this->set('created', REQUEST_TIME);
            }
            if(!isset($this->get('status')->value)) {
                $this->set('status', 0);
            }
            if(!isset($this->get('validity')->value)) {
                $this->set('validity', '1970-01-01 00:00:00');
            }
            if(!isset($this->get('renew')->value)) {
                $this->set('renew', 0);
            }
            if(!isset($this->get('type')->value)) {
                $this->set('type', 0);
            }
        }
    }
    
    public function getPlan()
    {
        $planId = $this->get('plan_id')->value;
        if(!empty($planId)) {
            $this->plan = entity_load('plan', $planId);
        } else {
            $this->plan = entity_create('plan', array());
        }
        return $this->plan;
    }
    
    public function getPreDecidedPlan()
    {
        $subscriptionType = entity_load('taxonomy_term', $this->get('type')->value);
        if(empty($subscriptionType)) throw new \Exception('Environment not set up');
        if($subscriptionType->get('name')->value != 'free-trial') {
            $planId = $this->get('plan_id')->value;
            if(!empty($planId)) {
                $currentPlan = $this->getPlan();
                $planIds = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans($currentPlan->get('plan_machine_name')->value);
                if(!empty($planIds)) {
                    return entity_load('plan', current($planIds));
                }
            }
        }
        return FALSE;
    }
    
    public function getSmartpanUser()
    {
        if(is_null($this->smartpanUser)) {
            $this->smartpanUser = entity_load('smartpan_user', $this->get('user_id')->value);
        }
        return $this->smartpanUser;
    }
    
    public function setSmartpanUser($smartpanUser)
    {
        $this->smartpanUser = $smartpanUser;
    }
    
    public function getValidity()
    {
        $datetime = $this->get('validity')->value;
        if(empty($datetime) || $datetime == '1970-01-01 00:00:00') return FALSE;
        return new \DateTime($datetime, new \DateTimeZone('UTC'));
    }
    
    public function getValidityDisplay()
    {
        $dateTime = $this->getValidity();
        $timezone = $this->getSmartpanUser()->getDrupalUser()->getTimeZone();
        $dateTime->setTimeZone(new \DateTimeZone($timezone));
        return $dateTime->format('n, J Y H:i');
    }
    
    public function setValidity()
    {
        $plan = $this->getPlan();
        $durationTerm = entity_load('taxonomy_term', $plan->get('duration')->value);
        $planTypeTerm = entity_load('taxonomy_term', $plan->get('type')->value);
        if(empty($durationTerm) || empty($planTypeTerm)) {
            throw new \Exception('Environment not set up');
        }
        if($planTypeTerm->get('name')->value == 'free-trial') {
            $validityPeriod = $plan->get('validity')->value . \product_subscription_duration_interval_mapper($durationTerm->get('name')->value);
            $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
            $dbDateTime = $this->getValidity();
            $dateIntervalString = $validityPeriod;
            if(empty($dbDateTime) || $this->get('validity')->value == '1970-01-01 00:00:00') {
                $extendedDateTime = $dateTime->add(new \DateInterval('P'.$dateIntervalString));
            } else {
                $extendedDateTime = $dbDateTime->add(new \DateInterval('P'.$dateIntervalString));
            }
        } 
        $extendeddatetime = $extendedDateTime->format('Y-m-d H:i:s');
        $this->set('validity', $extendeddatetime);
        return TRUE;
    }
    
    public function setValidityForPayment($paymentReceipt)
    {
        $extendeddatetime = '1970-01-01 00:00:00';
        if($paymentReceipt->get('status')->value == 1) {
            $plan = $this->getPlan();
            $durationTerm = entity_load('taxonomy_term', $plan->get('duration')->value);
            $planTypeTerm = entity_load('taxonomy_term', $plan->get('type')->value);
            $dbDateTime = $this->getValidity();
            if(!empty($dbDateTime) && $planTypeTerm->get('name')->value != 'free-trial') {
                $dateIntervalString = '1' . \product_subscription_duration_interval_mapper($durationTerm->get('name')->value);
                $extendedDateTime = $dbDateTime->add(new \DateInterval('P'.$dateIntervalString));
                $extendeddatetime = $extendedDateTime->format('Y-m-d H:i:s');
            }
        }
        $this->set('validity', $extendeddatetime);
        return TRUE;
    }
    
    public function setValidityWithTimezone($timezone)
    {
        if(smartpan_account_is_admin(UserController::getDrupalUser())) {
            $dateTime = new \DateTime($this->get('validity')->value, new DateTimeZone($timezone));
            $dateTime->setTimezone('UTC');
            $this->set('validity', $dateTime->format('Y-m-d H:i:s'));
            return TRUE;
        }
        return FALSE;
    }
    
    public function getLastSuccessfulRenewal()
    {
        $orderIds = \Drupal::entityManager()->getStorageController('smartpan_order')
                ->fetchLastActiveOrderForRenewal($this->getSmartpanUser(), $this);
        if(empty($orderIds)) return FALSE;
        return entity_load('smartpan_order', current($orderIds));
    }
    
    public function getLastSuccessfulOrder()
    {
        $orderIds = \Drupal::entityManager()->getStorageController('smartpan_order')
                ->fetchLastActiveOrder($this->getSmartpanUser(), $this);
        if(empty($orderIds)) return FALSE;
        return entity_load('smartpan_order', current($orderIds));
    }
    
    public function getLastCreatedOrder()
    {
        $orderIds = \Drupal::entityManager()->getStorageController('smartpan_order')
                ->fetchLastCreatedOrder($this->getSmartpanUser(), $this);
        if(empty($orderIds)) return FALSE;
        return entity_load('smartpan_order', current($orderIds));
    }
    
    /**
     * 
     * @param type $entity_type
     * @return type
     */
    public static function baseFieldDefinitions($entity_type) {
        $fields['id'] = FieldDefinition::create('integer')
          ->setLabel(t('Subscription ID'))
          ->setDescription(t('The subscription entity ID.'))
          ->setReadOnly(TRUE);

        $fields['uuid'] = FieldDefinition::create('uuid')
          ->setLabel(t('UUID'))
          ->setDescription(t('The subscription entity UUID.'))
          ->setReadOnly(TRUE);
        
        $fields['plan_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Plan ID'))
          ->setDescription(t('The plan id.'))
          ->setSetting('target_type', 'plan')
          ->setRequired(TRUE)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(\Drupal::entityManager()->getStorageController('plan')->planFieldOptions('title'))));
        
        $fields['type'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Subscription Type'))
          ->setDescription(t('The term ID for subscription type.'))
          ->setSetting('target_type', 'taxonomy_term')
          ->setSetting('default_value', 0)
          ->setRequired(TRUE)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(\Drupal::entityManager()->getStorageController('braintree_plan')->planOptions('type'))));
        
        $fields['bundle'] = FieldDefinition::create('string')
          ->setLabel(t('Subscription Bundle'))
          ->setDescription(t('The bundle of subscription.'))
          ->setSetting('default_value', 'free-trial')
          ->setRequired(TRUE)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(\Drupal::entityManager()->getStorageController('braintree_plan')->planValues('type'))));
        
        $fields['user_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Smartpan User ID'))
          ->setDescription(t('The smartpan user.'))
          ->setRequired(TRUE)
          ->setSetting('target_type', 'smartpan_user');
        
        $fields['status'] = FieldDefinition::create('integer')
          ->setLabel(t('Subscription Status'))
          ->setDescription(t('The subscription status'))
          ->setSetting('default_value', 0);
        
        $fields['created'] = FieldDefinition::create('integer')
          ->setLabel(t('Created'))
          ->setDescription(t('The request time that the subscription was created.'));
        
        $fields['validity'] = FieldDefinition::create('string')
          ->setLabel(t('Subscription Validity Date'))
          ->setDescription(t('The subscription validity date.'))
          ->setSetting('default_value', '1970-01-01 00:00:00')
          ->setPropertyConstraints('value', array('DateTime' => array()));
        
        $fields['renew'] = FieldDefinition::create('boolean')
          ->setLabel(t('Subscription Auto Renew'))
          ->setSetting('default_value', 0)
          ->setDescription(t('The subscription auto-renew mode'));

        return $fields;
    }
}

?>
