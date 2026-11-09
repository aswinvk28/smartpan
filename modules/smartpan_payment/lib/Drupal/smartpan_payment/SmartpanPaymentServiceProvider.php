<?php

/**
 * Description of AccountSettingsServiceProvider
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class SmartpanPaymentServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerBuilder $container) {
        $container->register('smartpan_payment.route_subscriber', 'Drupal\smartpan_payment\SmartpanPaymentRouteSubscriber')->addTag('event_subscriber');
    }
}

?>