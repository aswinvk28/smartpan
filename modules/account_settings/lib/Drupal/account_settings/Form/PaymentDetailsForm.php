<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\account_settings\Form;

use Drupal\Core\Form\FormBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

/**
 * Description of PaymentDetailsForm
 *
 * @author aswinvk28
 */
class PaymentDetailsForm extends FormBase
{
    private $smartpanUser;
    private $order;
    private $entity;
    
    public function getFormId()
    {
        return 'payment_details_form';
    }
    
    public function getTitle()
    {
        return 'Payment Details';
    }
    
    public function __construct() {
        $this->prepareEntity();
    }
    
    public function prepareEntity()
    {
        $this->smartpanUser = UserController::getCurrentActiveSmartpanUser();
        $this->order = $this->smartpanUser->getActiveSubscription()->getLastSuccessfulOrder();
        if(!empty($this->order)) {
            $this->order->setSubscription($this->smartpanUser->getActiveSubscription());
            $this->entity = $this->order->getRecentPayment();
            $this->entity->setOrder($this->order);
        }
    }
    
    public function buildRow()
    {
        $rows = array();
        if(empty($this->order) || empty($this->entity)) return $rows;
        $lastOrderPlaced = $this->order->getCreated()->format('d, M Y');
        if(empty($lastOrderPlaced)) return $rows;
        $lastPaymentReceived = $this->entity->getReceived();
        if(empty($lastPaymentReceived)) return $rows;
        
        $paymentOptions = \payment_load_multiple_payment_method();
        
        $rows[] = array(
            'site_name' => \Drupal::config('system.site')->get('name'),
            'order_placed' => $lastOrderPlaced,
            'payment_method' => $paymentOptions[$this->entity->get('method')->value],
            'last_payment_receipt' => $lastPaymentReceived,
            'next_billing' => $this->order->getSubscription()->getValidity()->format('d, M Y')
        );
        
        return $rows;
    }
    
    public function buildHeader()
    {
        return array(
            'site_name' => 'Product',
            'order_placed' => 'Last Order Placed',
            'payment_method' => 'Payment Method',
            'last_payment_receipt' => 'Last Payment Receipt Date',
            'next_billing' => 'Next Billing'
        );
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        $form['table'] = array(
            '#theme' => 'table',
            '#rows' => $this->buildRow(),
            '#header' => $this->buildHeader(),
            '#empty' => $this->t('No payments made or No access to payments'),
            '#title' => $this->getTitle()
        );
        
//        $form['payment_details']['actions'] = array(
//            '#type' => 'actions',
//            'submit' => array(
//                '#value' => 'Submit',
//                '#type' => 'submit',
//                '#button_type' => 'primary'
//            )
//        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        
    }
}

?>
