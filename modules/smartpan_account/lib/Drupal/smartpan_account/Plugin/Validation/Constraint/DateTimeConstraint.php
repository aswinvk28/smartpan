<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Validation\Constraint\DateTimeConstraint.
 */

namespace Drupal\smartpan_account\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if a value is a valid user name.
 *
 * @Plugin(
 *   id = "DateTime",
 *   label = @Translation("Date Time", context = "Validation")
 * )
 */
class DateTimeConstraint extends Constraint {

  public $notValidMessage = 'The date provided is not valid.';
  public $format = 'Y-m-d H:i:s';
  
}
