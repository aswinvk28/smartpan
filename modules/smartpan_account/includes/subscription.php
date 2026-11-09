<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function smartpan_map_subscription_status() {
    return array(
        \Drupal\user_subscription\Entity\Subscription::ACTIVE => 'Active',
        \Drupal\user_subscription\Entity\Subscription::CANCELED => 'Cancelled',
        \Drupal\user_subscription\Entity\Subscription::EXPIRED => 'Expired',
        \Drupal\user_subscription\Entity\Subscription::PAST_DUE => 'Past Due',
        \Drupal\user_subscription\Entity\Subscription::PENDING => 'Pending'
    );
}

function smartpan_map_braintree_subscription_status() {
    return array_flip(smartpan_map_subscription_status());
}

?>
