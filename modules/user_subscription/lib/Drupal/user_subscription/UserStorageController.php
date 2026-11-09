<?php

/**
 * Description of UserStorageController
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription;

use Drupal\Core\Entity\FieldableDatabaseStorageController;

class UserStorageController extends FieldableDatabaseStorageController
{
    public function _addForeignKeys()
    {
        $this->database->query('ALTER TABLE ' . $this->entityType->getBaseTable() . '');
    }
}

?>
