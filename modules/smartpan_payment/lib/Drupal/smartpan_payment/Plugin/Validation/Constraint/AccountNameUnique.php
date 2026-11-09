<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Validation\Constraint\AccountNameUnique.
 */

namespace Drupal\smartpan_payment\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if a user name is unique on the site.
 *
 * @Plugin(
 *   id = "AccountNameUnique",
 *   label = @Translation("Account name unique", context = "Validation")
 * )
 */
class AccountNameUnique extends Constraint {

  public $message = 'The name %value is already taken.';

}
