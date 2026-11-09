<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\smartpan_payment\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraints\CardScheme;

/**
 * Description of SmartPanCardScheme
 *
 * @author aswinvk28
 */

/**
 * Checks if a user name is unique on the site.
 *
 * @Plugin(
 *   id = "SmartPanCardScheme",
 *   label = @Translation("SmartPan card Scheme", context = "Validation")
 * )
 */
class SmartPanCardScheme extends CardScheme
{
    public $message = 'Unsupported card type or invalid card number.';
}

?>
