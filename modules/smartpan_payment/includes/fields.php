<?php

/**
 * @file
 * Integrates the neccessary functionalities for the stock and payment
 */
function payment_load_multiple_payment_method() {
    static $paymentOptions;
    if(!isset($paymentOptions)) {
        $resource_path = drupal_get_path('module', 'smartpan_account') . '/config/smartpan_account.payment_method.yml';
        $payment = \Symfony\Component\Yaml\Yaml::parse($resource_path);
        $paymentOptions = $payment['smartpan_account.payment_method'];
    }
    return $paymentOptions;
}

/**
 * 
 * @staticvar type $options
 * @return type
 */
function payment_load_credit_card_expiration() {
    static $options;
    if(empty($options)) {
        $options = array();
        $year = (int) date('Y');
        $options['month'] = array(
            '01' => '01',
            '02' => '02',
            '03' => '03',
            '04' => '04',
            '05' => '05',
            '06' => '06',
            '07' => '07',
            '08' => '08',
            '09' => '09',
            '10' => '10',
            '11' => '11',
            '12' => '12'
        );
        $options['year'] = array();
        for($index = 0; $index <= 20; $index++) {
            $year_new = (string) ($year + $index);
            $options['year'][$year_new] = $year_new;
        }
    }
    return $options;
}

/**
 * 
 * @staticvar string $options
 * @return string
 */
function payment_load_credit_card_schemes() {
    static $options;
    if(!isset($options)) {
        $options = array(
            'AMEX' => 'Amex',
            'CHINA_UNIONPAY' => 'China Union Pay',
            'DINERS' => 'Diners',
            'DISCOVER' => 'Discover',
            'INSTAPAYMENT' => 'Insta Payment',
            'JCB' => 'JCB',
            'LASER' => 'Laser',
            'MAESTRO' => 'Maestro',
            'MASTERCARD' => 'Master Card',
            'VISA' => 'Visa'
        );
    }
    return $options;
}

/**
 * 
 * @staticvar string $options
 * @return string
 */
function payment_load_order_types() {
    static $options;
    if(!isset($options)) {
        $options = array(
            'renew' => 'Renewal',
            'upgrade' => 'Upgradation',
            'downgrade' => 'Downgradation',
            'regular' => 'Regular Transaction',
            'other' => 'Other'
        );
    }
    return $options;
}

?>
