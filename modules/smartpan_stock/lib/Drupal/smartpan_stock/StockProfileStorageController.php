<?php

/**
 * Description of StockProfileStorageController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock;

use Drupal\Core\Entity\DatabaseStorageController;

class StockProfileStorageController extends DatabaseStorageController
{
    public function loadStocksLike(array $values = array())
    {
        $query = $this->database->select($this->entityType->getBaseTable(), 'base');
        $query->fields('base', array('id', 'symbol'));
        $query->condition('base.symbol', $values['symbol'] . '%', 'LIKE');
        $result = $query->execute()->fetchAll();
        return $result;
    }
}

?>
