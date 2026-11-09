<?php

/**
 * Description of BraintreeAccount
 *
 * @author aswinvk28
 */

namespace Drupal\braintree;

use Drupal\Component\Utility\Settings;

class BraintreeAccount
{
    private $settings;
    private $accountType;
    private $accountName;
    private $accountTypes = array('dev', 'qa', 'sandbox', 'production');
    private $activeAccount;
    private $accounts;
    
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->accountType = $this->settings->get('braintree_account_type');
//        $this->accountName = \Drupal::config('braintree.account')->get('name');
        $this->accountName = 'smartpan_production';
    }
    
    public function getAccountTypes()
    {
        return $this->accountTypes;
    }
    
    public function getAccounts()
    {
        if(is_null($this->accounts)) {
            $path = drupal_get_path('module', 'braintree') . '/config/braintree_settings';
            $this->accounts = \Symfony\Component\Yaml\Yaml::parse($path . '/braintree.' . $this->accountType . '.yml');
        }
        return $this->accounts;
    }
    
    public function getAccount()
    {
        if(is_null($this->activeAccount)) {
            $accounts = $this->getAccounts();
            $this->activeAccount = $accounts['braintree'][$this->accountName];
        }
        return $this->activeAccount;
    }
}

?>
