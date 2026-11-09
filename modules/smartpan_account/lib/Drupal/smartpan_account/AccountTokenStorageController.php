<?php

/**
 * Description of AccountTokenStorageController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account;

use Drupal\Core\Entity\DatabaseStorageController;

class AccountTokenStorageController extends DatabaseStorageController
{
    public function _addForeignKeys()
    {
        $this->database->query('ALTER TABLE ' . $this->entityType->getBaseTable() . '');
    }
}

?>
