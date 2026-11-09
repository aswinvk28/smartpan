<?php

function account_settings_smartpan_user_details_ajax_field($smartpan_user) {
    $fields = array(
        'first_name' => array(
            '#type' => 'html_tag',
            '#title' => 'First Name',
            '#tag' => 'span',
            '#value' => $smartpan_user->get('first_name')->value,
            '#theme' => 'ajax_field'
        ),
        'last_name' => array(
            '#type' => 'html_tag',
            '#title' => 'Last Name',
            '#tag' => 'span',
            '#value' => $smartpan_user->get('last_name')->value,
            '#theme' => 'ajax_field'
        ),
        'user_name' => array(
            '#type' => 'html_tag',
            '#title' => 'User Name',
            '#tag' => 'span',
            '#value' => $smartpan_user->getDrupalUser()->get('name')->value,
            '#theme' => 'ajax_field'
        ),
        'user_email' => array(
            '#type' => 'html_tag',
            '#title' => 'Email',
            '#tag' => 'span',
            '#value' => $smartpan_user->getDrupalUser()->get('mail')->value,
            '#theme' => 'ajax_field'
        )
    );
    
    return $fields;
}

function account_settings_smartpan_user_details_add_fields($smartpanUser) {
    $fields = array();
    
    $fields['first_name'] = array(
        '#type' => 'textfield',
        '#title' => 'First Name',
        '#required' => true,
        '#default_value' => $smartpanUser->get('first_name')->value
    );
    
    $fields['last_name'] = array(
        '#type' => 'textfield',
        '#title' => 'Last Name',
        '#required' => true,
        '#default_value' => $smartpanUser->get('last_name')->value
    );
    
    return $fields;
}

?>
