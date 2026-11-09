<?php

/**
 * Description of SmartpanAccountRouteSubscriber
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class SmartpanAccountRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection, $provider)
    {
        if ($provider == 'smartpan_account') {
            $smartpanAccountDashboardRoute = new Route('/dashboard');
            $smartpanAccountDashboardRoute->setDefaults(array(
                '_content' => '\SmartPan\Bundles\CustomerBundle\Controller\AccountPageController::getDashboard',
                '_title' => 'Dashboard'
            ));
            $smartpanAccountDashboardRoute->setRequirements(array(
                '_permission' => 'access dashboard',
                '_user_is_logged_in' => 'TRUE'
            ));
            $smartpanAccountDashboardRoute->setOption('_theme', 'dashboard_page');
            $smartpanAccountDashboardRoute->setMethods('GET');
            $collection->add('smartpan_account.dashboard', $smartpanAccountDashboardRoute);
        }
    }
}

?>