<?php

/**
 * Description of SmartpanAccountServiceProvider
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class SmartpanAccountServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerBuilder $container)
    {
        $container->register('smartpan_account.route_subscriber', 'Drupal\smartpan_account\SmartpanAccountRouteSubscriber')->addTag('event_subscriber');
    }
}

?>