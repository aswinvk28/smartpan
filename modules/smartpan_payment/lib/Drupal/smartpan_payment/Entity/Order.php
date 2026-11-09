<?php

/**
 * Description of Order
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
* Defines the node entity class.
*
* @ContentEntityType(
*   id = "smartpan_order",
*   label = @Translation("Orders Placed"),
*   controllers = {
*     "storage" = "Drupal\smartpan_payment\OrderStorageController",
*     "list" = "Drupal\smartpan_payment\Controller\OrderListController",
*     "form" = {
*       "default" = "Drupal\smartpan_payment\Form\OrderFormController",
*       "delete" = "Drupal\smartpan_payment\Form\OrderDeleteForm",
*       "order_confirm" = "Drupal\smartpan_payment\Controller\UpgradeOrderFormController"
*     }
*   },
*   base_table = "smartpan_order",
*   fieldable = FALSE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id",
*     "uuid" = "uuid"
*   },
*   links = {
*     "delete-form" = "payment.order_delete",
*     "overview-form" = "payment.order_overview",
*     "edit-form" = "payment.order_edit"
*   }
* )
*/

class Order extends ContentEntityBase
{
    private $subscription;
    private $payment;
    
    public function preSave(EntityStorageControllerInterface $storage_controller) {
        parent::preSave($storage_controller);
        if($this->isNew()) {
            if(!isset($this->get('created')->value)) {
                $this->set('created', REQUEST_TIME);
            }
            if(!isset($this->get('modified')->value)) {
                $this->set('modified', REQUEST_TIME);
            }
            if(!isset($this->get('status')->value)) {
                $this->set('status', 0);
            }
            if(!isset($this->get('host')->value)) {
                $this->set('host', \Drupal::request()->server->get('REMOTE_ADDR'));
            }
            if(!isset($this->get('currency')->value)) {
                $this->set('currency', 'USD');
            }
            if(!isset($this->get('payment_method')->value)) {
                $this->set('currency', 'USD');
            }
            if(!isset($this->get('order_total')->value)) {
                $this->set('order_total', 0);
            }
            if(!isset($this->get('type')->value)) {
                $this->set('type', 'regular');
            }
        }
    }
    
    public function getCreated()
    {
        $created = $this->get('created')->value;
        $lastRenewal = new \DateTime();
        $lastRenewal->setTimestamp($created);
        $timezone = $this->getSubscription()->getSmartpanUser()->getDrupalUser()->get('timezone')->value;
        if(empty($timezone)) $timezone = 'UTC';
        $lastRenewal->setTimezone(new \DateTimeZone($timezone));
        return $lastRenewal;
    }
    
    public function getSubscription()
    {
        if(is_null($this->subscription)) {
            $this->subscription = entity_load('subscription', $this->get('subscription_id')->value);
        }
        return $this->subscription;
    }
    
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }
    
    public function getRecentPayment()
    {
        if(is_null($this->payment)) {
            $paymentIds = \Drupal::entityManager()->getStorageController('smartpan_payment')->fetchRecentPayment($this);
            if(!empty($paymentIds)) {
                $this->payment = entity_load('smartpan_payment', current($paymentIds));
                $this->payment->setOrder($this);
            }
        }
        return $this->payment;
    }
    
    public static function baseFieldDefinitions($entity_type) {
        $fields['id'] = FieldDefinition::create('integer')
          ->setLabel(t('Order ID'))
          ->setDescription(t('The order entity ID.'))
          ->setReadOnly(TRUE);

        $fields['uuid'] = FieldDefinition::create('uuid')
          ->setLabel(t('UUID'))
          ->setDescription(t('The order entity UUID.'))
          ->setReadOnly(TRUE);
        
        $fields['subscription_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Subscription ID'))
          ->setDescription(t('The subscription id.'))
          ->setSetting('target_type', 'subscription')
          ->setRequired(TRUE);
        
        $fields['user_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Smartpan User ID'))
          ->setDescription(t('The SmartPan user who placed the order.'))
          ->setSetting('target_type', 'smartpan_user')
          ->setRequired(TRUE);
        
        $fields['created'] = FieldDefinition::create('integer')
          ->setLabel(t('Created'))
          ->setDescription(t('The request time that the order was created.'));
        
        $fields['modified'] = FieldDefinition::create('integer')
          ->setLabel(t('Modified'))
          ->setDescription(t('The request time that the order was modified.'));
        
        $fields['status'] = FieldDefinition::create('boolean')
          ->setLabel(t('Order Status'))
          ->setDescription(t('The order status'))
          ->setSetting('default_value', 0);
        
        $fields['order_total'] = FieldDefinition::create('float')
          ->setLabel(t('Order Total'))
          ->setDescription(t('The total amount to be paid for the order.'))
          ->setSetting('default_value', 0.00);
        
        $fields['product'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Product ID'))
          ->setDescription(t('The product ID for the order.'))
          ->setSetting('target_type', 'plan')
          ->setRequired(TRUE)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(\Drupal::entityManager()->getStorageController('plan')->loadPaidPlans())));
        
        $fields['type'] = FieldDefinition::create('string')
          ->setLabel(t('Order Type'))
          ->setDescription(t('The type of order.'))
          ->setSetting('default_value', NULL)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(payment_load_order_types())));
        
        $fields['braintree_subscription_id'] = FieldDefinition::create('string')
          ->setLabel(t('Braintree Subscription ID for the Order'))
          ->setSetting('default_value', NULL)
          ->setSetting('max_length', 32)
          ->setDescription(t('The subscription id for braintree subscription.'));
        
        $fields['host'] = FieldDefinition::create('string')
          ->setLabel(t('Host IP'))
          ->setDescription(t('The host IP of the user.'))
          ->setPropertyConstraints('value', array('Ip' => array()));
        
        $fields['currency'] = FieldDefinition::create('string')
          ->setLabel(t('Currency'))
          ->setDescription(t('The ISO currency code for the order.'))
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(smartpan_load_multiple_currency())));;
        
        $fields['payment_method'] = FieldDefinition::create('string')
          ->setLabel(t('payment Method'))
          ->setDescription(t('The payment method.'))
          ->setSetting('default_value', 'card')
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(payment_load_multiple_payment_method())));
        
        $fields['data'] = FieldDefinition::create('map')
          ->setLabel(t('Additional Data'))
          ->setSetting('default_value', NULL)
          ->setDescription(t('A serialized array of additional parameters for the order.'));
        
        return $fields;
    }
}

?>
