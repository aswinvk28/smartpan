<?php

/**
 * Description of StockInvestorStorageController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock;

use Drupal\Core\Entity\DatabaseStorageController;

class StockInvestorStorageController extends DatabaseStorageController
{
    public function loadRecent($stock_id, &$timestamp, $external = false)
    {
        $timestamp = trim($timestamp);
        $timestamp_int = (int) $timestamp;
        $query = $this->database->select('stock_investor', 'ss')->fields('ss', array('id'));
        
        $query->condition('ss.stock_id', $stock_id);
        
        if(empty($timestamp)) {
            if(!$external) {
                $query->condition('ss.created', 'MAX(ss.created)')
                        ->fields('ss', array('created'));
                $recent = $query->execute()->fetchObject();

                if(!empty($recent)) {
                    $timestamp = $recent->created;
                    return $this->load($recent->id);
                }
            }
        }
        else {
            if(!$external) {
                $query->condition('ss.created', $timestamp_int, '=');
                
                $recent = $query->execute()->fetchObject();
                
                if(!empty($recent)) {
                    return $this->load($recent->id);
                }
            }
            else {
                if(preg_match('/^\d+$/', $timestamp)) {
                    $query->condition('ss.created', $timestamp_int, '<')
                            ->condition('ss.created', 'MAX(ss.created)')
                            ->fields('ss', array('created'));

                    $recent = $query->execute()->fetchObject();

                    if(!empty($recent)) {
                        $timestamp = $recent->created;
                        return $this->load($recent->id);
                    }
                }
            }
        }
        
        return FALSE;
    }
    
    public function getTimestamp($stock_id)
    {
        $query = $this->database->select('stock_investor', 'ss');
        $query->addExpression('MAX(ss.created)', 'timestamp');
        $query->condition('ss.stock_id', $stock_id);
        $result = $query->execute()->fetchField();
        return $result;
    }
}

?>
