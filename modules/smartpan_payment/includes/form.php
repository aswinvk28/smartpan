<?php

function payment_card_details_fetch($form, &$form_state) {
    $values = $form_state['values']['include_card'];
    return payment_card_details_add_fields($form, $form_state, $values);
}

function payment_card_details_add_fields($form, &$form_state, $values, $ajax = TRUE) {
    if($values != 1) return array();
    
    $fields = array();
    
    drupal_add_library('smartpan_payment', 'braintree');
    
    $options = payment_load_credit_card_expiration();
    $fields['card_scheme'] = array(
        '#type' => 'select',
        '#title' => 'Card Scheme',
        '#empty_value' => '',
        '#empty_option' => 'None Selected',
        '#options' => payment_load_credit_card_schemes(),
        '#required' => TRUE,
        '#default_value' => '',
        '#attributes' => array(
           'autocomplete' => 'off',
           'data-encrypted-name' => 'card_scheme'
        )
    );
    $fields['credit_card_number'] = array(
        '#type' => 'textfield',
        '#title' => 'Credit Card Number',
        '#default_value' => '',
        '#required' => TRUE,
        '#maxlength' => 512,
        '#attributes' => array(
           'autocomplete' => 'off',
           'data-encrypted-name' => 'credit_card_number',
        )
    );
    $fields['credit_card_name'] = array(
        '#type' => 'textfield',
        '#title' => 'Card Holder Name',
        '#default_value' => '',
        '#required' => TRUE,
        '#maxlength' => 512,
        '#attributes' => array(
           'autocomplete' => 'off',
           'data-encrypted-name' => 'credit_card_name',
        )
    );
    $fields['expiration_month'] = array(
        '#type' => 'select',
        '#empty_value' => '',
        '#empty_option' => 'None Selected',
        '#title' => 'Expiration Month',
        '#options' => $options['month'],
        '#required' => TRUE,
        '#default_value' => ''
    );
    $fields['expiration_year'] = array(
        '#type' => 'select',
        '#empty_value' => '',
        '#empty_option' => 'None Selected',
        '#title' => 'Expiration Year',
        '#options' => $options['year'],
        '#required' => TRUE,
        '#default_value' => ''
    );
    $fields['credit_card_cvv'] = array(
        '#type' => 'textfield',
        '#title' => 'CVV',
        '#default_value' => '',
        '#required' => TRUE,
        '#maxlength' => 512,
        '#attributes' => array(
           'autocomplete' => 'off',
           'data-encrypted-name' => 'credit_card_cvv',
        )
    );
    
    return $fields;
}

function payment_card_details_ajax_field($smartpanUser) {
    $fields = array();
    
    $braintreeAction = new Drupal\smartpan_payment\Controller\BraintreeActionController();
    $card = $braintreeAction->findBraintreeCreditCard(
        $smartpanUser->get('customer_id')->value, 
        $smartpanUser->get('payment_token')->value
    );
    
    $fields['card_number'] = array(
        '#type' => 'html_tag',
        '#title' => 'Existing Card Number',
        '#tag' => 'span',
        '#value' => $card->maskedNumber ? $card->maskedNumber : '',
        '#theme' => 'ajax_field'
    );
    
    return $fields;
}

function payment_card_details_validate_fields($form, $form_state) {
    $options = payment_load_credit_card_expiration();

    if(empty($form_state['values']['card_scheme']) || !array_key_exists($form_state['values']['card_scheme'], payment_load_credit_card_schemes())) {
        form_set_error('card_scheme', $form_state, 'Card Scheme Not Valid');
    }

//    $definition = \Drupal\Core\TypedData\DataDefinition::create('string')
//    ->addConstraint('SmartPanCardScheme', 
//        array(
//            'schemes' => $form_state['values']['card_scheme']
//        ));
//
//    $typed_data = \Drupal::typedDataManager()->create($definition, $form_state['values']['credit_card_number']);
//    $violations = $typed_data->validate();
    if(empty($form_state['values']['credit_card_number']) /*|| $violations->count() > 0*/) {
        form_set_error('credit_card_number', $form_state, 'Unsupported card type or invalid card number');
    }

    if(empty($form_state['values']['credit_card_name']) /*|| !preg_match('/^[A-Z\ ]+$/', $form_state['values']['credit_card_name'])*/) {
        form_set_error('credit_card_name', $form_state, 'Invalid Card Holders Name');
    }
    
//    $date = $form_state['values']['expiration_year'].'-'.$form_state['values']['expiration_month'].'-01';
//    $dateTime = new \DateTime($date);
//    $now = new DateTime(date('Y-m-d'));
    
    if(empty($form_state['values']['expiration_month']) || !array_key_exists($form_state['values']['expiration_month'], $options['month'])) {
        form_set_error('expiration_month', $form_state, 'Expiration Month not valid');
    }

    if(empty($form_state['values']['expiration_year']) || !array_key_exists($form_state['values']['expiration_year'], $options['year'])) {
        form_set_error('expiration_year', $form_state, 'Expiration Year not valid');
    }
}

function payment_billing_details_validate_fields($form, $form_state) {
    $definition = \Drupal\Core\TypedData\DataDefinition::create('string')
    ->addConstraint('Email', array('checkHost' => true));
    $typed_data = \Drupal::typedDataManager()->create($definition, $form_state['values']['billing_email']);
    $violations = $typed_data->validate();
    if(empty($form_state['values']['billing_email']) || $violations->count() > 0 || strlen($form_state['values']['billing_email']) > 58) {
        form_set_error('billing_email', $form_state, $violations[0]->getMessage());
    }
    if(empty($form_state['values']['billing_first_name'])) {
        form_set_error('billing_first_name', $form_state, 'Invalid Billing First Name');
    }

    if(!empty($form_state['values']['billing_phone']) && !preg_match('/^(\d{7,15})$/', $form_state['values']['billing_phone'])) {
        form_set_error('billing_phone', $form_state, 'Not a valid Telephone number');
    }
    if(!empty($form_state['values']['billing_street1']) && !preg_match('/^(0-9)*([a-z\-\ 0-9]+)(\s)*(0-9)*(\w)*$/i', $form_state['values']['billing_street1'])) {
        form_set_error('billing_street1', $form_state, 'Not a valid Street Name');
    }
    if(!empty($form_state['values']['billing_street2']) && !preg_match('/^(0-9)*([a-z\-\ 0-9]+)(\s)*(0-9)*(\w)*$/i', $form_state['values']['billing_street2'])) {
        form_set_error('billing_street2', $form_state, 'Not a valid Street Name');
    }
    if(!empty($form_state['values']['billing_city']) && !preg_match('/^[a-zA-Z]+(?:(?:\\s+|-)[a-zA-Z]+)*$/', $form_state['values']['billing_city'])) {
        form_set_error('billing_city', $form_state, 'Not a valid City Name');
    }
    if(!array_key_exists($form_state['values']['billing_country'], smartpan_load_multiple_country())) {
        form_set_error('billing_country', $form_state, 'Not a valid Country Name');
    }
}

function payment_billing_details_add_fields($smartpanUser) {
    $fields = array();
    
    $country = smartpan_load_multiple_country();
    
    $fields['billing_first_name'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_first_name')->value ? $smartpanUser->get('billing_first_name')->value : '',
        '#title' => 'Billing First Name'
    );

    $fields['billing_last_name'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_last_name')->value ? $smartpanUser->get('billing_last_name')->value : '',
        '#title' => 'Billing Last Name'
    );

    $fields['billing_email'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_email')->value ? $smartpanUser->get('billing_email')->value : '',
        '#title' => 'Billing Email'
    );

    $fields['billing_phone'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_phone')->value ? $smartpanUser->get('billing_phone')->value : '',
        '#title' => 'Billing Phone'
    );

    $fields['billing_company'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_company')->value ? $smartpanUser->get('billing_company')->value : '',
        '#title' => 'Billing Company'
    );

    $fields['billing_country'] = array(
        '#type' => 'select',
        '#default_value' => $smartpanUser->get('billing_country')->value ? $smartpanUser->get('billing_country')->value : '',
        '#options' => $country,
        '#empty_value' => '',
        '#empty_option' => 'None Selected',
        '#title' => 'Billing Country'
    );

    $fields['billing_street1'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_street1')->value ? $smartpanUser->get('billing_street1')->value : '',
        '#title' => 'Billing Street 1'
    );

    $fields['billing_street2'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_street2')->value ? $smartpanUser->get('billing_street2')->value : '',
        '#title' => 'Billing Street 2'
    );

    $fields['billing_city'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_city')->value ? $smartpanUser->get('billing_city')->value : '',
        '#title' => 'Billing City'
    );

    $fields['billing_postal_code'] = array(
        '#type' => 'textfield',
        '#default_value' => $smartpanUser->get('billing_postal_code')->value ? $smartpanUser->get('billing_postal_code')->value : '',
        '#title' => 'Billing Postal Code'
    );
    
    return $fields;
}

function payment_billing_details_ajax_field($smartpan_user) {
    $fields = array();
    
    $fields['form_inline'] = array(
        '#theme' => 'form_inline',
        '#container_type' => 'no_wrapper'
    );
    
    $fields['form_inline']['billing_first_name'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing First Name',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_first_name')->value ? $smartpan_user->get('billing_first_name')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['form_inline']['billing_last_name'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Last Name',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_last_name')->value ? $smartpan_user->get('billing_last_name')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_mail'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Email',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_email')->value ? $smartpan_user->get('billing_email')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_phone'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Phone',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_phone')->value ? $smartpan_user->get('billing_phone')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_company'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Company',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_company')->value ? $smartpan_user->get('billing_company')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_country'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Country',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_country')->value ? $smartpan_user->get('billing_country')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_street1'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Street 1',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_street1')->value ? $smartpan_user->get('billing_street1')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_street2'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Street 2',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_street2')->value ? $smartpan_user->get('billing_street2')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_city'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing City',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_city')->value ? $smartpan_user->get('billing_city')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    $fields['billing_postal_code'] = array(
        '#type' => 'html_tag',
        '#title' => 'Billing Postal Code',
        '#tag' => 'span',
        '#value' => $smartpan_user->get('billing_postal_code')->value ? $smartpan_user->get('billing_postal_code')->value : 'No value Saved',
        '#theme' => 'ajax_field',
    );
    
    return $fields;
}

?>
