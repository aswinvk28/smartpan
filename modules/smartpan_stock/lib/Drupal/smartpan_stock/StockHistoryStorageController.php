<?php

/**
 * Description of StockHistoryStorageController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock;

use Drupal\Core\Entity\DatabaseStorageController;

class StockHistoryStorageController extends DatabaseStorageController
{
    public function loadRecent($stock_id, &$timestamp, $external = false)
    {
        $query = $this->database->select('stock_history', 'ss')->fields('ss', array('id'));
        
        $query->condition('ss.stock_id', $stock_id);
        $query->isNotNull('ss.close');
        $query->isNotNull('ss.volume');
        
        if(empty($timestamp)) {
            if(!$external) {
                $query->condition('ss.date', 'MAX(ss.date)')
                        ->fields('ss', array('date'));

                $recent = $query->execute()->fetchObject();
                if(!empty($recent)) {
                    $timestamp = $recent->date;
                    return $this->load($recent->id);
                }
            }
        }
        else {
            // for the cache
            if(!$external) {
                if(smartpan_isvalid_datetime_string($timestamp, 'Y-m-d')) {
                    $query->condition('ss.date', $timestamp, '=');
                    $recent = $query->execute()->fetchObject();
                    if(!empty($recent)) {
                        return $this->load($recent->id);
                    }
                }
            }
            else {
                if(smartpan_isvalid_datetime_string($timestamp, 'Y-m-d')) {
                    $query->condition('ss.date', $timestamp, '<')
                            ->condition('ss.date', 'MAX(ss.date)')
                            ->fields('ss', array('date'));
                    
                    $recent = $query->execute()->fetchObject();
                    if(!empty($recent)) {
                        $timestamp = $recent->date;
                        return $this->load($recent->id);
                    }
                }
            }
        }
        
        return FALSE;
    }
    
    public function loadStockReturn($stockSize, &$timestamp, $external = false)
    {
        $return = array(
            'week' => NULL,
            'quarter' => NULL,
            'half' => NULL,
            'year' => NULL
        );
        $timestamp = trim($timestamp);
        $timestamp_int = (int) $timestamp;
        
        $date = \DateTime::createFromFormat('Y-m-d',$stockSize->getTradeDate());
        $date = $date->format('Y-m-d');
        
        if(empty($date)) {
            return $return;
        }
        
        $query = $this->database->select('stock_history', 'ss')->fields('ss', array('id'));
        $query->addExpression('UNIX_TIMESTAMP(ss.date)', 'timestamp');
        $condition = $query->andConditionGroup()
                ->condition('ss.close', NULL, '!=')
                ->condition('ss.volume', NULL, '!=');
        $query->condition($condition);
        $query->range(0, 1);
        
        // repetitive block
        
        if(empty($timestamp)) {
            if(!$external) {
                $query->condition('ss.date', 'MAX(ss.date)');
//                $recent = $query->execute()->fetchObject();
//
//                if(!empty($recent)) {
//                    $timestamp = $recent->timestamp;
//                    return $this->load($recent->id);
//                }
            }
        }
        else {
            // for the cache
            if(!$external) {
                $query->havingCondition('timestamp', $timestamp_int, '=');
                
//                $recent = $query->execute()->fetchObject();
//                
//                if(!empty($recent)) {
//                    return $this->load($recent->id);
//                }
            }
            else {
                if(preg_match('/^\d+$/', $timestamp)) {
                    $query->havingCondition('timestamp', $timestamp_int, '<')
                            ->condition('ss.date', 'MAX(ss.date)');

//                    $recent = $query->execute()->fetchObject();
//                    if(!empty($recent)) {
//                        $timestamp = $recent->timestamp;
//                        return $this->load($recent->id);
//                    }
                }
            }
        }
        
        $query->addExpression("DATEDIFF($date, ss.date)", 'datefield');
        
        $weekQuery = clone $query;
        $query->havingCondition("datefield", 7, '>=');
        $weekId = $weekQuery->execute()->fetchObject();

        $quarterQuery = clone $query;
        $query->havingCondition("datefield", 91, '>=');
        $quarterId = $quarterQuery->execute()->fetchObject();

        $halfQuery = clone $query;
        $query->havingCondition("datefield", 182, '>=');
        $halfId = $halfQuery->execute()->fetchObject();

        $yearQuery = clone $query;
        $query->havingCondition("datefield", 365, '>=');
        $yearId = $yearQuery->execute()->fetchObject();

        $weekId = !empty($weekId) ? $weekId->id : NULL;
        $quarterId = !empty($quarterId) ? $quarterId->id : NULL;
        $halfId = !empty($halfId) ? $halfId->id : NULL;
        $yearId = !empty($yearId) ? $yearId->id : NULL;
        
        $return['week'] = $this->loadSafe($weekId);
        $return['quarter'] = $this->loadSafe($quarterId);
        $return['half'] = $this->loadSafe($halfId);
        $return['year'] = $this->loadSafe($yearId);
        
        return $return;
    }
    
    public function loadSafe($id) {
        if(empty($id)) return NULL;
        
        $result = $this->load($id);
        
        return $result;
    }
    
    public function getTimestamp($stock_id)
    {
        $query = $this->database->select('stock_history', 'ss');
        $query->addExpression('MAX(ss.date)', 'date');
        $query->condition('ss.stock_id', $stock_id);
        $result = $query->execute()->fetchField();
        return $result;
    }
}

?>
