<?php

/**
 * Description of SmartpanStockRouteSubscriber
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class SmartpanStockRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection, $provider) {
        if ($provider == 'smartpan_stock') {
            $smartpanStockAutocomplete = new Route('/symbol');
            $smartpanStockAutocomplete->addDefaults(array(
                '_title' => 'Stock Symbol',
                '_controller' => '\Drupal\smartpan_stock\Controller\StockFormAjaxController::getSymbol'
            ));
            $smartpanStockAutocomplete->setRequirements(array(
                '_permission' => 'access stock profile list',
                '_user_is_logged_in' => 'TRUE'
            ));
            $smartpanStockAutocomplete->setMethods('POST');
            
            $smartpanStockDashboard = new Route('/stock/{symbol}/{stock_profile}');
            $smartpanStockDashboard->addDefaults(array(
                '_title' => 'Dashboard',
                '_controller' => '\Drupal\smartpan_stock\Controller\StockAjaxController::getStock',
                'stock_profile' => ''
            ));
            $smartpanStockDashboard->setRequirements(array(
                '_entity_access' => 'stock_profile.view',
                '_user_is_logged_in' => 'TRUE',
                '_csrf_token' => 'TRUE'
            ));
            $smartpanStockDashboard->setMethods('GET');
            
            $collection->add('smartpan_stock.autocomplete', $smartpanStockAutocomplete);
            $collection->add('smartpan_stock.dashboard', $smartpanStockDashboard);
        }
    }
}

?>
