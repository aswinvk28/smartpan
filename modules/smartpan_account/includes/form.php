<?php

function smartpan_user_details_submit($form, $form_state) {
    $roles = $form_state['values']['roles'];
    return smartpan_user_add_fields($form, $form_state, $roles, TRUE);
}

function smartpan_user_add_fields($form, $form_state, $roles = NULL, $ajax = FALSE) {
    if(!empty($roles)) {
        
        $entity = $form_state['controller']->getEntity();
        
        $titleOptions = new stdClass();
        $titleOptions->value = '';
        $titleOptions->options = array();

        $typeOptions = new stdClass();
        $typeOptions->value = '';
        $typeOptions->options = array();
        
        $userStorageController = \Drupal::entityManager()->getStorageController('smartpan_user');
        $smartpan_user = $userStorageController->loadByProperties(array(
            'user_id' => $entity->id()
        ));
        
        $smartpan_user_entity = current($smartpan_user);
        
        if(empty($smartpan_user_entity)) {
            $smartpan_user_entity = $userStorageController->create(array(
                'first_name' => NULL,
                'last_name' => NULL,
                'user_id' => NULL,
                'uuid' => NULL
            ));
        }
        
        $parameterBag = new \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag(array(
            'plan.planTitleOptions' => $titleOptions,
            'plan.planTypeOptions' => $typeOptions,
            'member.firstName' => $smartpan_user_entity->get('first_name')->value,
            'member.lastName' => $smartpan_user_entity->get('last_name')->value
        ));
        $accountForm = new \SmartPan\Bundles\AccountBundle\DependencyInjection\AccountFormData($parameterBag);

        $fields = $accountForm->getFormDataForFreeTrial();

        if($ajax) {
            $fields['form_inline']['first_name']['#attributes']['name'] = 'first_name';
            $fields['form_inline']['last_name']['#attributes']['name'] = 'last_name';
        }
        
        $form['subscription_member']['bundle_type_wrapper'] = $fields['form_inline'];

        return $form['subscription_member']['bundle_type_wrapper'];
    }
    return array();
}

?>
