<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\account_settings\Form;

use Drupal\Core\Form\FormBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\smartpan_payment\Manager\BraintreeAccountManager;
use Drupal\smartpan_payment\Controller\BraintreeActionController;

/**
 * Description of SubscriptionRenewForm
 *
 * @author aswinvk28
 */
class SubscriptionDetailsForm extends FormBase 
{
    private $smartpanUser;
    private $entity;
    private $paymentReceipt;
    
    public function getFormId()
    {
        return 'subscription_details_form';
    }
    
    public function getTitle()
    {
        return 'Subscription Details';
    }
    
    public function prepareEntity()
    {
        $this->smartpanUser = UserController::getCurrentActiveSmartpanUser();
        $this->entity = $this->smartpanUser->getActiveSubscription();
        $this->paymentReceipt = entity_create('smartpan_payment', array());
    }
    
    public function buildRow()
    {
        $this->prepareEntity();
        $rows = array();
        if(empty($this->entity)) return $rows;
        $expiryDateTime = $this->entity->getValidity();
        if(empty($expiryDateTime)) return $rows;
        $lastSuccessfulRenewal = $this->entity->getLastSuccessfulRenewal();
        if(!empty($lastSuccessfulRenewal)) {
            $created = $lastSuccessfulRenewal->getCreated()->format('d, M Y');
        } else {
            $created = 'No Renewal Made';
        }
        
        $hasToken = $this->smartpanUser->get('payment_token')->value;
        $hasCustomerId = $this->smartpanUser->get('customer_id')->value;
        
        if(!empty($hasToken) && !empty($hasCustomerId)) {
            $autoRenew = array(
                '#type' => 'checkbox',
                '#attributes' => array(
                    'name' => 'auto_renew'
                ),
                '#title' => 'Auto Renew',
                '#default_value' => $this->entity->get('renew')->value ? $this->entity->get('renew')->value : 0
            );
        } else {
            $url = $this->url('payment.card_add');
            $autoRenew = array(
                '#theme' => 'link_button',
                '#value' => 'Save Card in Secure Vault',
                '#description' => 'Save Card before Renewing Automatically',
                '#attributes' => array(
                    'href' => $url
                )
            );
        }
        
        $renew = $this->buildRenewButton();
        $rows[] = array(
            'site_name' => \Drupal::config('system.site')->get('name'),
            'expiry_date' => $expiryDateTime->format('d, M Y'),
            'last_renewal' => $created,
            'auto_renew' => drupal_render($autoRenew),
            'renew' => drupal_render($renew)
        );
        
        return $rows;
    }
    
    public function buildHeader()
    {
        return array(
            'site_name' => 'Product',
            'expiry_date' => 'Subscription Expiry',
            'last_renewal' => 'Last Renewal Date',
            'auto_renew' => 'Auto Renew',
            'renew' => 'Renewal'
        );
    }
    
    public function buildRenewButton()
    {
        $renew = array(
            '#title' => 'Renew Now',
            '#type' => 'checkbox',
            '#attributes' => array(
                'name' => 'renew_submit'
            )
        );
        return $renew;
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        $row = $this->buildRow();
        $form['table'] = array(
            '#theme' => 'table',
            '#rows' => $row,
            '#header' => $this->buildHeader(),
            '#empty' => $this->t('No subscriptions made or No access to subscriptions'),
            '#title' => $this->getTitle()
        );
        $form['auto_renew'] = array(
            '#type' => 'value'
        );
        $form['renew_submit'] = array(
            '#type' => 'value'
        );
        
        if(!empty($row)) {
            $form['actions'] = array(
                '#type' => 'actions',
                'submit' => array(
                    '#value' => 'Save',
                    '#type' => 'submit',
                    '#button_type' => 'primary'
                )
            );
        }
        
        return $form;
    }
    
    private function validateRenew(array $form, array &$form_state)
    {
        $form_state['values']['auto_renew'] = 0;
    }
    
    private function submitRenew(array $form, array &$form_state)
    {
        if(empty($this->smartpanUser)) {
            $this->prepareEntity();
        }
        
        $form_state['values']['auto_renew'] = 0;
        
        $hasToken = $this->smartpanUser->get('payment_token')->value;
        $hasCustomerId = $this->smartpanUser->get('customer_id')->value;
        $subscriptionId = $this->smartpanUser->get('subscription_id')->value;
        $orderController = new BraintreeActionController();
        $transaction = db_transaction();
        try {
            if(!empty($hasToken) && !empty($hasCustomerId)) {
                $braintreeController = new BraintreeAccountManager($this->smartpanUser, $orderController);
                try {
                    $creditCard = $braintreeController->findCard($hasCustomerId, $hasToken);
                } catch(\Exception $e) {
                    throw new \Exception('Card not found');
                }
                try {
                    $resultTransaction = $braintreeController->renewSubscription($creditCard);
                    if($resultTransaction->_attributes['status'] === 'authorized') {
                        $braintreeController->finalisePayment(
                            $this->entity, $this->entity->getPlan(), $this->paymentReceipt, $subscriptionId
                        );
                    } else {
                        throw new \Exception('Transaction Failed');
                    }
                } catch(\Exception $e) {
                    throw new \Exception('Subscription not renewed');
                }
            } else {
                try {
                    $token = $form_state['values']['form_token'];
                    $token_param = \Drupal\Component\Utility\Crypt::hmacBase64($token, settings()->get('drupal_form_token_salt'));
                    $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
                    $transaction = db_transaction();
                    \Drupal::keyValueExpirable('form-token-salt')->setWithExpire($token, $token_param, 2400);
                } catch(\Exception $e) {
                    $transaction->rollback();
                    drupal_set_message($e->getMessage(), 'error');
                }
                $url = $this->url('payment.renew', array(
                    'form_token' => $token
                ));
                $response = \Symfony\Component\HttpFoundation\RedirectResponse::create($url);
                $response->send();
                exit;
            }
        } catch(\Exception $e) {
            $transaction->rollback();
            drupal_set_message($e->getMessage(), 'error');
        }
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        if($form_state['values']['renew_submit'] == 1) {
            $this->validateRenew($form, $form_state);
        } else {
            
        }
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        if($form_state['values']['renew_submit'] == 1) {
            $this->submitRenew($form, $form_state);
        } else {
            
        }
    }
}

?>
