<?php

/**
 * Description of OrderFormController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Form;

use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\smartpan_payment\Manager\OrderManager;

class OrderFormController extends ContentEntityFormController
{
    private $currencyOptions;
    
    private $paymentMethodOptions;
    
    /**
     * 
     * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
     */
    public function __construct(EntityManagerInterface $entity_manager) {
        parent::__construct($entity_manager);
        $this->currencyOptions = smartpan_load_multiple_currency();
        $this->paymentMethodOptions = payment_load_multiple_payment_method();
    }
    
    public function form(array $form, array &$form_state) {
        $order = $this->entity;

        $smartpan_user = entity_load('smartpan_user', $order->get('user_id')->value);
        $drupal_user = entity_load('user', $smartpan_user->get('user_id')->value);
        
        $form['subscription_name'] = array(
            '#title' => 'Subscribed User',
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => array(
                'disabled' => 'disabled'
            ),
            '#value' => !is_null($order->get('subscription_id')->value) ? $drupal_user->get('name')->value : 'No user Subscribed'
        );
        
        if(is_null($order->get('user_id')->value) || is_null($order->get('subscription_id'))) {
            drupal_set_message('No User Subscription', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        $form['order_total_name'] = array(
            '#title' => 'Order Total',
            '#type' => 'textfield',
            '#attributes' => array(
                'class' => '',
                'disabled' => 'disabled'
            ),
            '#default_value' => !is_null($order->get('order_total')->value) ? $order->get('order_total')->value : ''
        );
        
        if(is_null($order->get('order_total')->value)) {
            drupal_set_message('No Amount specified by the user to be paid for the order', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        $form['payment_method'] = array(
            '#title' => 'Payment Method',
            '#type' => 'select',
            '#options' => $this->paymentMethodOptions,
            '#default_value' => !is_null($order->get('payment_method')->value) ? $order->get('payment_method')->value : '',
            '#required' => TRUE
        );
        
        $form['currency'] = array(
            '#title' => 'Currency',
            '#type' => 'select',
            '#options' => $this->currencyOptions,
            '#default_value' => !is_null($order->get('currency')->value) ? $order->get('currency')->value : '',
            '#required' => TRUE
        );
        
        $form['data'] = array(
            '#type' => 'value'
        );
        
        $form['status'] = array(
            '#title' => 'Order Status',
            '#type' => 'checkbox',
            '#default_value' => !is_null($order->get('status')->value) ? $order->get('status')->value : 0
        );
        
        $form['host'] = array(
            '#title' => 'Host IP',
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => array(
                'disabled' => 'disabled'
            ),
            '#value' => !is_null($order->get('host')->value) ? $order->get('host')->value : ''
        );
        
        $form['created'] = array(
            '#title' => 'Creation',
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => array(
                'disabled' => 'disabled'
            ),
            '#value' => !is_null($order->get('created')->value) ? $order->get('created')->value : 'No Creation Timestamp'
        );
        
        $form['modified'] = array(
            '#title' => 'Modification',
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#attributes' => array(
                'disabled' => 'disabled'
            ),
            '#value' => !is_null($order->get('modified')->value) ? $order->get('modified')->value : 'No Modification Timestamp'
        );
        
        if(is_null($order->get('created')->value)) {
            drupal_set_message('No Creation Timestamp for Order', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        if(is_null($order->get('modified')->value)) {
            drupal_set_message('No Modification Timestamp for Order', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        return parent::form($form, $form_state, $term);
    }
    
    public function save(array $form, array &$form_state) {
        $transaction = db_transaction();
        try {
            $orderStatus = $this->entity->get('status')->value;
            if(empty($orderStatus)) {
                $orderManager = OrderManager::create(\Drupal::getContainer());
                $smartpanUser = entity_load('smartpan_user', $this->entity->get('user_id'));
                $subscription = $this->entity->getSubscription();
                $orderManager->pushBraintreeSubscription($this->entity, $smartpanUser, $subscription);
            }
            if(!$this->entity->save()) {
                throw new \Exception('Order Not Saved');
            }
        } catch(Exception $e) {
            $transaction->rollback();
            drupal_set_message($e->getMessage(), 'error');
        }
    }
}

?>
