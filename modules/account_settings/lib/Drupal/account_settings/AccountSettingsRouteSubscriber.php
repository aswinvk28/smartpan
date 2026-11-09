<?php

/**
 * Description of AccountSettingsRouteSubscriber
 *
 * @author aswinvk28
 */

namespace Drupal\account_settings;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class AccountSettingsRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection, $provider) {
        if ($provider == 'account_settings') {
            $accountSettingsRoute = new Route('/account');
            $accountSettingsRoute->setDefaults(array(
                '_content' => '\SmartPan\Bundles\CustomerBundle\Controller\AccountPageController::accountSettings',
                '_title' => 'Account Settings'
            ));
            $accountSettingsRoute->setRequirements(array(
                '_custom_access' => '\Drupal\user_subscription\Access\SmartpanAccountAccessCheck::checkIsMember',
                '_user_is_logged_in' => 'TRUE'
            ));
            $accountSettingsRoute->setMethods('GET');
            $collection->add('account_settings.account', $accountSettingsRoute);
        }
    }
}

?>