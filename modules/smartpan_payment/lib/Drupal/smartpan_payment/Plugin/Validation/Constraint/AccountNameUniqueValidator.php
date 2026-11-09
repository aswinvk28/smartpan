<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Validation\Constraint\UserUniqueValidator.
 */

namespace Drupal\smartpan_payment\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the unique user property constraint, such as name and e-mail.
 */
class AccountNameUniqueValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $value_taken = (bool) \Drupal::entityManager()->getStorageController('braintree_settings')
      // The UID could be NULL, so we cast it to 0 in that case.
      ->loadByProperties(array(
          'account_name' => $vaue
      ));

    if (!empty($value_taken)) {
      $this->context->addViolation($constraint->message, array("%value" => $value));
    }
  }
}
