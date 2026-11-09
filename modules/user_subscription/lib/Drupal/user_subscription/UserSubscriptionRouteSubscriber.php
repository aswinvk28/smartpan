<?php

/**
 * Description of UserSubscriptionRouteSubscriber
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class UserSubscriptionRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection, $provider)
    {
        if ($provider == 'user_subscription') {
            $userSubscriptionAccount = new Route('/account/create/{subscription_type}/{plan_type}');
            $userSubscriptionAccount->setDefaults(array(
                '_content' => '\SmartPan\Bundles\CustomerBundle\Controller\AccountPageController::accountCreatePage',
                '_title' => 'Create Free Trial Account',
                'subscription_type' => 'free-trial',
                'plan_type' => 'free-trial'
            ));
            $userSubscriptionAccount->setRequirements(array(
                '_access' => 'TRUE'
            ));
            
            $userSubscriptionLogin = new Route('/login');
            $userSubscriptionLogin->setDefaults(array(
                '_content' => '\SmartPan\Bundles\CustomerBundle\Controller\AccountPageController::loginPage',
                '_title' => 'Log in to Smartpan'
            ));
            $userSubscriptionLogin->setRequirements(array(
                '_access' => 'TRUE'
            ));
            $userSubscriptionLogin->setOption('_theme', 'dashboard_page');
            
            $collection->add('user_subscription.account', $userSubscriptionAccount);
            $collection->add('user_subscription.login', $userSubscriptionLogin);
        }
    }
}

?>
