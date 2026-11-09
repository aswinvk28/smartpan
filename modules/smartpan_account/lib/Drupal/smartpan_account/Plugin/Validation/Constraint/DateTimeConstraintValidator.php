<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\smartpan_account\Plugin\Validation\Constraint;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Description of DateTimeConstraintValidator
 *
 * @author aswinvk28
 */
class DateTimeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint) {
        $date = \DateTime::createFromFormat($constraint->format, $value, new \DateTimeZone('UTC'));
        if(!$date || $date->format($constraint->format) != $value) {
            $this->context->addViolation($constraint->notValidMessage);
        }
    }
}

?>
