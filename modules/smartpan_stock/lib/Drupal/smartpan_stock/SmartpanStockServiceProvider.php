<?php

/**
 * Description of SmartpanStockServiceProvider
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class SmartpanStockServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerBuilder $container) {
        $container->register('smartpan_stock.route_subscriber', '\Drupal\smartpan_stock\SmartpanStockRouteSubscriber')->addTag('event_subscriber');
    }
}

?>
