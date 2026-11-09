<?php

/**
 * Description of AccountSettingsServiceProvider
 *
 * @author aswinvk28
 */

namespace Drupal\account_settings;

use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;

class AccountSettingsServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerBuilder $container) {
        $container->register('account_settings.route_subscriber', 'Drupal\account_settings\AccountSettingsRouteSubscriber')->addTag('event_subscriber');
    }
}

?>