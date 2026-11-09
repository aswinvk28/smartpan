<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmartPan\Bundles\AccountBundle\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\user_subscription\Controller\SubscriptionController;

class UserController extends ControllerBase
{
    private $subscriptionController;
    public $storage;
    
    private static $smartpanUser;
    private static $drupalUser;
    
    public function __construct()
    {
        $this->storage = $this->entityManager()->getStorageController('smartpan_user');
        $this->subscriptionController = new SubscriptionController();
    }
    
    public function hardDelete($entity)
    {
        $subscriptions = $this->subscriptionController->storage->loadByProperties(array(
            'user_id' => $entity->id()
        ));
        
        if(!empty($subscriptions)) {
            foreach($subscriptions as $subscription) {
                $this->subscriptionController->hardDelete($subscription);
            }
            entity_delete_multiple('subscription', array_keys($subscriptions));
        }
    }
    
    public function softDelete($entity)
    {
        $subscriptions = $this->subscriptionController->storage->loadByProperties(array(
            'subscription_id' => $entity->id(),
            'status' => 1
        ));
        
        if(!empty($subscriptions)) {
            foreach($subscriptions as $subscription) {
                $this->subscriptionController->softDelete($subscription);
            }
        }
    }
    
    public function registerFree($drupalUserId, $values)
    {
        $drupalUser = entity_load('user', $drupalUserId);
        $smartpan_user = $this->saveSmartpanUser($drupalUserId, $values['first_name'], $values['last_name']);

        if(!empty($smartpan_user) && $smartpan_user->id() != 0) {
            return $this->subscribeFreeUser($smartpan_user, $drupalUser, $values['plan']);
        }
        return FALSE;
    }
    
    public function saveSmartpanUser($drupalUserId, $smartpanUser)
    {
        $smartpanUser = $this->storage->create(array(
            'first_name' => $smartpanUser->get('first_name')->value,
            'last_name' => $smartpanUser->get('last_name')->value,
            'user_id' => $drupalUserId,
            'uuid' => \Drupal::service('uuid')->generate()
        ));

        $saved = $this->storage->save($smartpanUser);
        
        if(empty($saved)) return FALSE;
        
        return TRUE;
    }
    
    public function subscribeFreeUser($user, $drupalUser, $plan, $subscriber)
    {
        $date = date('Y-m-d H:i:s');
        $dateObject = new \DateTime('now', new \DateTime($drupalUser->get('timezone')->value));
        $interval = new \DateInterval('P1M');
        $dateObject->add($interval);

        $subscriber = $this->subscriptionController->storage->create(array(
            'plan_id' => $plan->id(),
            'user_id' => $user->id(),
            'type' => 0,
            'status' => 0,
            'created' => time(),
            'datetime' => $dateObject->format('Y-m-d H:i:s'),
            'renew' => 0,
            'uuid' => \Drupal::service('uuid')->generate()
        ));
        
        $saveStatus = $this->subscriptionController->storage->save($subscriber);
        
        if(empty($saveStatus)) return FALSE;
        
        return $subscriber;
    }
    
    /**
     * 
     * @return Drupal\user\Entity\User
     */
    public static function getDrupalUser()
    {
        if(is_null(self::$drupalUser)) {
            self::$drupalUser = entity_load('user', \Drupal::currentUser()->id());
        }
        return self::$drupalUser;
    }
    
    /**
     * 
     * @param Drupal\user\Entity\User $drupalUser
     * @return Drupal\user_subscription\Entity\User
     */
    public static function getCurrentActiveSmartpanUser($drupalUser = NULL)
    {
        if(is_null(self::$smartpanUser)) {
            if(empty($drupalUser)) {
                $drupalUser = self::getDrupalUser();
            }
            if($drupalUser instanceof User && $drupalUser->isActive() && $drupalUser->isAuthenticated() && $drupalUser->id() === \Drupal::currentUser()->id()) {
                self::$smartpanUser = self::getSmartpanUserFromDrupalUser($drupalUser);
            }
        }
        return self::$smartpanUser;
    }
    
    public static function getSmartpanUserFromDrupalUser($drupalUser)
    {
        $smartpan_user = entity_load_multiple_by_properties('smartpan_user', array(
            'user_id' => $drupalUser->id()
        ));
        if(empty($smartpan_user)) return FALSE;
        $smartpanUser = current($smartpan_user);
        $smartpanUser->setDrupaluser($drupalUser);
        return $smartpanUser;
    }
}

?>
