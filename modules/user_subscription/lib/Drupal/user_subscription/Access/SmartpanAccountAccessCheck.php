<?php

/**
 * Description of SubscriptionAccessCheck
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Access;

use Drupal\Core\Controller\ControllerBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

class SmartpanAccountAccessCheck extends ControllerBase
{
    public function checkIsMember()
    {
        $account = UserController::getDrupalUser();
        return smartpan_account_is_member($account);
    }
}

?>
