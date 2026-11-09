<?php

/**
 * Description of SmartpanPaymentRouteSubscriber
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class SmartpanPaymentRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection, $provider) {
        if ($provider == 'smartpan_payment') {
            $smartpanPaymentCardAddRoute = new Route('/account/card/add');
            $smartpanPaymentCardAddRoute->setDefaults(array(
                '_form' => 'Drupal\smartpan_payment\Form\CreditCardAddForm',
                '_title' => 'Add and Save Card Details'
            ));
            $smartpanPaymentCardAddRoute->setRequirements(array(
                '_custom_access' => 'Drupal\smartpan_payment\Access\SubscriptionAccessCheck::upgradeAccess',
                '_user_is_logged_in' => 'TRUE',
                '_permission' => 'add payment method'
            ));
            
            $smartpanPaymentRenewRoute = new Route('/account/renew/{form_token}');
            $smartpanPaymentRenewRoute->setDefaults(array(
                '_form' => 'Drupal\smartpan_payment\Form\SubscriptionRenewForm',
                '_title' => 'Renew Subscription',
                '_permission' => 'renew subscription'
            ));
            $smartpanPaymentRenewRoute->setRequirements(array(
                '_custom_access' => 'Drupal\smartpan_payment\Access\SubscriptionAccessCheck::upgradeAccess',
                '_user_is_logged_in' => 'TRUE'
            ));
            
            $smartpanPaymentChangePlanRoute = new Route('/account/upgrade/{plan_machine_name}');
            $smartpanPaymentChangePlanRoute->setDefaults(array(
                '_form' => 'Drupal\smartpan_payment\Controller\PlanFormController',
                '_title' => 'Change Plan (optionally enter Billing Details)',
                'plan_machine_name' => '',
                '_permission' => 'upgrade subscription'
            ));
            $smartpanPaymentChangePlanRoute->setRequirements(array(
                '_user_is_logged_in' => 'TRUE',
                '_custom_access' => 'Drupal\smartpan_payment\Access\SubscriptionAccessCheck::upgradeAccess'
            ));
            
            $smartpanPaymentCreatePaymentRoute = new Route('/account/payment/{form_token}');
            $smartpanPaymentCreatePaymentRoute->setDefaults(array(
                '_form' => 'Drupal\smartpan_payment\Controller\UpgradeOrderFormController',
                '_title' => 'Enter Payment Details',
                '_permission' => 'upgrade subscription'
            ));
            $smartpanPaymentCreatePaymentRoute->setRequirements(array(
                '_user_is_logged_in' => 'TRUE',
                '_custom_access' => 'Drupal\smartpan_payment\Access\SubscriptionAccessCheck::createPayment'
            ));
            
            $smartpanPaymentConfirmPaymentRoute = new Route('/account/payment-confirm/{form_token}/{confirm_form_token}');
            $smartpanPaymentConfirmPaymentRoute->setDefaults(array(
                '_form' => 'Drupal\smartpan_payment\Controller\UpgradeOrderConfirmFormController',
                '_title' => 'Confirm Payment For',
                '_permission' => 'upgrade subscription'
            ));
            $smartpanPaymentConfirmPaymentRoute->setRequirements(array(
                '_user_is_logged_in' => 'TRUE',
                '_custom_access' => 'Drupal\smartpan_payment\Access\SubscriptionAccessCheck::confirmPayment'
            ));
            
            $collection->add('smartpan_payment.card_add', $smartpanPaymentCardAddRoute);
            $collection->add('smartpan_payment.renew', $smartpanPaymentRenewRoute);
            $collection->add('smartpan_payment.change_plan', $smartpanPaymentChangePlanRoute);
            $collection->add('smartpan_payment.create_payment', $smartpanPaymentCreatePaymentRoute);
            $collection->add('smartpan_payment.confirm_payment', $smartpanPaymentConfirmPaymentRoute);
        }
    }
}

?>