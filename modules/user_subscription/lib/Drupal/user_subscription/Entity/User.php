<?php

/**
 * Description of Smartpan User
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\FieldDefinition;

/**
* Defines the Smartpan User entity type.
*
* @ContentEntityType(
*   id = "smartpan_user",
*   label = @Translation("SmartPan User"),
*   controllers = {
*     "storage" = "Drupal\user_subscription\UserStorageController",
*     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder"
*   },
*   base_table = "smartpan_user",
*   admin_permission = "administer smartpan user",
*   fieldable = FALSE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   field_cache = FALSE,
*   entity_keys = {
*     "id" = "id",
*     "label" = "first_name",
*     "uuid" = "uuid"
*   }
* )
*/

class User extends ContentEntityBase
{
    private $subscription;
    private $drupalUser;
    private $token;
    
    public function getDrupalUser()
    {
        if(is_null($this->drupalUser)) {
            $this->drupalUser = entity_load('user', $this->get('user_id')->value);
        }
        return $this->drupalUser->isActive() ? $this->drupalUser : entity_create('user', array());
    }
    
    public function setDrupaluser($drupalUser) 
    {
        $this->drupalUser = $drupalUser;
    }
    
    public function updateRoleForPayment($plan)
    {
        $drupalUser = $this->getDrupalUser();
        $roles = $drupalUser->getRoles();
        $members = \smartpan_account_members();
        $intersect = array_intersect($roles, $members);
        if(!empty($intersect)) {
            foreach($intersect as $roleId) {
                $drupalUser->removeRole($roleId);
            }
        }
        $role = $plan->getBraintreePlan()->getRole();
        if(empty($role)) {
            throw new \Exception('Environment Not set up');
        }
        $drupalUser->addRole($role);
        return $drupalUser;
    }
    
    public function updateRole($plan)
    {
        $role = SMARTPAN_FREE_RID;
        $this->getDrupalUser()->addRole($role);
        return $this->getDrupalUser();
    }
    
    public function getToken()
    {
        if(is_null($this->token)) {
            $token = entity_load_multiple_by_properties('account_token', array(
                'user_id' => $this->id()
            ));
            $this->token = current($token);
        }
        return $this->token;
    }
    
    public function deleteToken()
    {
        $this->token = null;
        return $this;
    }
    
    public function getActiveSubscription()
    {
        if(is_null($this->subscription)) {
            $subscriptions = entity_load_multiple_by_properties('subscription', array(
                'status' => 1,
                'user_id' => $this->id()
            ));
            if(!empty($subscriptions) && count($subscriptions) == 1) {
                $this->subscription = current($subscriptions);
                $this->subscription->setSmartpanUser($this);
            } elseif(!empty($subscriptions)) {
                foreach($subscriptions as $subscription) {
                    $subscription->set('status', 0);
                    $subscription->save();
                }
                drupal_set_message('Multiple Active subscriptions');
                watchdog('smartpan', 'Made multiple subscriptions for the user %user_name inactive.', array(
                    '%user_name' => $this->getDrupalUser()->getUsername()
                ), WATCHDOG_NOTICE);
            } else {
                $this->subscription = entity_create('subscription', array());
            }
        }
        return $this->subscription;
    }
    
    public function getInitialisedSubscription()
    {
        $subscriptions = entity_load_multiple_by_properties('subscription', array(
            'user_id' => $this->id(),
            'validity' => '1970-01-01 00:00:00'
        ));
        if(!empty($subscriptions) && count($subscriptions) == 1) {
            return current($subscriptions);
        } elseif(!empty($subscriptions)) {
            \Drupal::entityManager()->getStorageController('subscription')->delete($subscriptions);
            drupal_set_message('Multiple subscriptions');
            watchdog('smartpan', 'Deleted multiple subscriptions for the user %user_name.', array(
                '%user_name' => $this->getDrupalUser()->getUsername()
            ), WATCHDOG_NOTICE);
        }
        return entity_create('subscription', array());
    }
    
    public static function baseFieldDefinitions($entity_type) {
        $fields['id'] = FieldDefinition::create('integer')
          ->setLabel(t('SmartPan User ID'))
          ->setDescription(t('The smartpan_user entity ID.'))
          ->setReadOnly(TRUE);

        $fields['user_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Drupal User ID'))
          ->setDescription(t('The drupal user.'))
          ->setRequired(TRUE)
          ->setSetting('target_type', 'user')
          ->setSetting('default_value', 0);
        
        $fields['first_name'] = FieldDefinition::create('string')
          ->setLabel(t('SmartPan User First Name'))
          ->setDescription(t('The smartpan_user entity first name.'))
//          ->setPropertyConstraints('value', array('Regex' => array('pattern' => '/^[a-z ,.\'-]+$/i', 'message' => 'Not a valid First Name')))
          ->setRequired(TRUE);
        
        $fields['last_name'] = FieldDefinition::create('string')
          ->setLabel(t('SmartPan User Last Name'))
          ->setDescription(t('The smartpan_user entity first name.'))
//          ->setPropertyConstraints('value', array('Regex' => array('pattern' => '/^[a-z ,.\'-]+$/i', 'message' => 'Not a valid Last Name')))
          ->setRequired(TRUE);
        
        $fields['payment_token'] = FieldDefinition::create('string')
          ->setLabel(t('Payment Token'))
          ->setDescription(t('The payment token for the credit card.'))
          ->setSetting('max_length', 64)
          ->setSetting('default_value', NULL);
        
        $fields['customer_id'] = FieldDefinition::create('string')
          ->setLabel(t('Braintree Customer ID'))
          ->setDescription(t('The customer id of the smartpan user.'))
          ->setSetting('max_length', 32)
          ->setSetting('default_value', NULL);
        
        $fields['subscription_id'] = FieldDefinition::create('string')
          ->setLabel(t('Braintree Current Active Subscription ID'))
          ->setDescription(t('The subscription id of the active braintree subscription.'))
          ->setSetting('max_length', 32)
          ->setSetting('default_value', NULL);
        
        $fields['billing_email'] = FieldDefinition::create('email')
          ->setLabel(t('Billing email'))
          ->setDescription(t('The email address used for billing.'))
          ->setSetting('default_value', '')
          ->setPropertyConstraints('value', array('Email' => array('checkHost' => true)))
          ->setSetting('default_value', NULL);
        
        $fields['billing_phone'] = FieldDefinition::create('integer')
          ->setLabel(t('Billing Phone'))
          ->setDescription(t('The phone number used for billing of the service.'))
          ->setSetting('default_value', '')
//          ->setPropertyConstraints('value', array('Regex' => array('pattern' => '/^\d+$', 'message' => 'Please enter the phone number in digits with no spaces and hyphens / brackets')))
          ->setSetting('default_value', NULL);

        $fields['billing_company'] = FieldDefinition::create('string')
          ->setLabel(t('Billing Company'))
          ->setDescription(t('The company name under whom the service is billed.'))
          ->setSetting('max_length', 64)
          ->setSetting('default_value', NULL);
        
        $fields['billing_country'] = FieldDefinition::create('string')
          ->setLabel(t('Billing Country'))
          ->setDescription(t('The country within which the user is billed.'))
          ->setSetting('max_length', 20)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(smartpan_load_multiple_country())))
          ->setSetting('default_value', NULL);
        
        $fields['billing_first_name'] = FieldDefinition::create('string')
          ->setLabel(t('SmartPan User Billing First Name'))
          ->setDescription(t('The smartpan_user entity billing first name.'))
          ->setSetting('default_value', NULL);
        
        $fields['billing_last_name'] = FieldDefinition::create('string')
          ->setLabel(t('SmartPan User Billing Last Name'))
          ->setDescription(t('The smartpan_user entity billing last name.'))
          ->setSetting('default_value', NULL);
        
        $fields['billing_street1'] = FieldDefinition::create('string')
          ->setLabel(t('Order Billing Street Name'))
          ->setDescription(t('The smartpan_user entity billing street name.'))
//          ->setPropertyConstraints('value', array('Regex' => array('pattern' => '/^([a-z- ]+)\s+([0-9]+)$/i', 'message' => 'Not a valid street name')))
          ->setSetting('default_value', NULL);
        
        $fields['billing_street2'] = FieldDefinition::create('string')
          ->setLabel(t('Order Billing Street Name'))
          ->setDescription(t('The smartpan_user entity billing street name.'))
//          ->setPropertyConstraints('value', array('Regex' => array('pattern' => '/^([a-z- ]+)\s+([0-9]+)$/i', 'message' => 'Not a valid street name')))
          ->setSetting('default_value', NULL);
        
        $fields['billing_city'] = FieldDefinition::create('string')
          ->setLabel(t('Order Billing City'))
          ->setDescription(t('The smartpan_user entity billing city name.'))
//          ->setPropertyConstraints('value', array('Regex' => array('pattern' => '/^[a-zA-Z\u0080-\u024F\s\/\-\)\(\`\.\"\']+$/', 'message' => 'Not a valid city name')))
          ->setSetting('default_value', NULL);
        
        $fields['billing_postal_code'] = FieldDefinition::create('string')
          ->setLabel(t('Order Billing Post Code'))
          ->setDescription(t('The smartpan_user entity billing post code.'))
          ->setSetting('default_value', NULL);
        
        return $fields;
    }
}

?>
