<?php

/**
 * Description of BraintreeFactory
 *
 * @author aswinvk28
 */

namespace Drupal\braintree\Factory;

interface GatewayFactoryInterface
{
    public function get($serviceName, array $attributes = array());
}

?>
