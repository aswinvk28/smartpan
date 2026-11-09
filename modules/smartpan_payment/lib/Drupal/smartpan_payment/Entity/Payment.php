<?php

/**
 * Payment Entity represents the payment made by the member to a user
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
*   id = "smartpan_payment",
*   label = @Translation("Payment Receipt"),
*   controllers = {
*     "storage" = "Drupal\smartpan_payment\PaymentStorageController",
*     "list" = "Drupal\smartpan_payment\Controller\PaymentReceiptListController",
*     "form" = {
*       "default" = "Drupal\smartpan_payment\Form\PaymentFormController",
*       "delete" = "Drupal\smartpan_payment\Form\PaymentDeleteForm",
*       "payment_confirm" = "Drupal\smartpan_payment\Controller\UpgradeOrderConfirmFormController"
*     }
*   },
*   base_table = "smartpan_payment_receipt",
*   fieldable = FALSE,
*   render_cache = FALSE,
*   static_cache = TRUE,
*   entity_keys = {
*     "id" = "id",
*     "uuid" = "uuid"
*   },
*   links = {
*     "add-form" = "payment.payment_add",
*     "delete-form" = "payment.payment_delete",
*     "overview-form" = "payment.payment_overview",
*     "edit-form" = "payment.payment_edit"
*   }
* )
*/

class Payment extends ContentEntityBase
{
    private $order;
    
    public function preSave(EntityStorageControllerInterface $storage_controller)
    {
        if($this->isNew()) {
            if(!isset($this->get('created')->value)) {
                $this->set('created', REQUEST_TIME);
            }
            if(!isset($this->get('status')->value)) {
                $this->set('status', 0);
            }
            if(!isset($this->get('received')->value)) {
                $this->set('received', time());
            }
        }
    }
    
    public function getOrder()
    {
        if(is_null($this->order)) {
            $this->order = entity_load('smartpan_order', $this->get('order_id')->value);
        }
        return $this->order;
    }
    
    public function setOrder($order)
    {
        $this->order = $order;
    }
    
    public function getCreated()
    {
        $created = $this->get('created')->value;
        $lastPayment = new \DateTime();
        $lastPayment->setTimestamp($created);
        $timezone = $this->getOrder()->getSubscription()->getSmartpanUser()->getDrupalUser()->getTimeZone();
        if(empty($timezone)) $timezone = 'UTC';
        $lastPayment->setTimezone(new \DateTimeZone($timezone));
        return $lastPayment;
    }
    
    public function getReceived()
    {
        $received = $this->get('received')->value;
        $lastPayment = new \DateTime();
        $lastPayment->setTimestamp($received);
        $timezone = $this->getOrder()->getSubscription()->getSmartpanUser()->getDrupalUser()->get('timezone')->value;
        if(empty($timezone)) $timezone = 'UTC';
        $lastPayment->setTimezone(new \DateTimeZone($timezone));
        return $lastPayment->format('d, M Y');
    }
    
    public static function baseFieldDefinitions($entity_type) {
        $fields['id'] = FieldDefinition::create('integer')
          ->setLabel(t('Payment Receipt ID'))
          ->setDescription(t('The payment entity ID.'))
          ->setReadOnly(TRUE);

        $fields['uuid'] = FieldDefinition::create('uuid')
          ->setLabel(t('UUID'))
          ->setDescription(t('The payment receipt entity UUID.'))
          ->setReadOnly(TRUE);
        
        $fields['order_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Order ID'))
          ->setDescription(t('The order id.'))
          ->setSetting('target_type', 'smartpan_order');
        
        $accounts = \Drupal::service('braintree_account')->getAccounts();
        
        $fields['account_id'] = FieldDefinition::create('string')
          ->setLabel(t('Account ID'))
          ->setDescription(t('The payment account to which the amount has been paid.'))
          ->setRequired(TRUE)
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys($accounts['braintree'])));
        
        $fields['user_id'] = FieldDefinition::create('entity_reference')
          ->setLabel(t('Smartpan User ID'))
          ->setDescription(t('The SmartPan user to whom the payment receipt is issued.'))
          ->setSetting('target_type', 'smartpan_user')
          ->setRequired(TRUE);
        
        $fields['received'] = FieldDefinition::create('integer')
          ->setLabel(t('Payment Received'))
          ->setDescription(t('The Unix timestamp indicating when the payment was received.'));
        
        $fields['send'] = FieldDefinition::create('integer')
          ->setLabel(t('Payment Receipt Send'))
          ->setSetting('default_value', 0)
          ->setDescription(t('The Unix timestamp indicating when the payment receipt was send.'));
        
        $fields['status'] = FieldDefinition::create('boolean')
          ->setLabel(t('Payment Receipt Status'))
          ->setDescription(t('The order status'))
          ->setSetting('default_value', 0);
        
        $fields['created'] = FieldDefinition::create('integer')
          ->setLabel(t('Created'))
          ->setDescription(t('The request time that the payment receipt was created.'));
        
        $fields['amount'] = FieldDefinition::create('float')
          ->setLabel(t('Payment Amount'))
          ->setDescription(t('The payment amount.'))
          ->setSetting('default_value', 0.00);
        
        $fields['method'] = FieldDefinition::create('string')
          ->setLabel(t('payment Method'))
          ->setDescription(t('The payment method.'))
          ->setSetting('default_value', 'card')
          ->setPropertyConstraints('value', array('AllowedValues' => array_keys(payment_load_multiple_payment_method())));
        
        $fields['comment'] = FieldDefinition::create('text_long')
          ->setLabel(t('Payment receipt Comment'))
          ->setDescription(t('The payment receipt comment'))
          ->setSetting('default_value', NULL)
          ->setSetting('max_length', 1024);
        
        $fields['data'] = FieldDefinition::create('map')
          ->setLabel(t('Additional Data'))
          ->setDescription(t('A serialized array of additional parameters for the payment receipt.'));

        return $fields;
    }
}

?>
