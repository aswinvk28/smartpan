<?php

/**
 * Description of UserSubscriptionServiceProvider
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class UserSubscriptionServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerBuilder $container) {
        $container->register('user_subscription.route_subscriber', 'Drupal\user_subscription\UserSubscriptionRouteSubscriber')->addTag('event_subscriber');
    }
}

?>
