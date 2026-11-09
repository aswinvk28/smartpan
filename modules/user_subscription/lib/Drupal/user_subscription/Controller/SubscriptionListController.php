<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\user_subscription\Controller;

use Drupal\Core\Entity\EntityListController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
 * Description of SubscriptionListController
 *
 * @author aswinvk28
 */
class SubscriptionListController extends EntityListController
{
    private $subscriptionStatuses;
    
    public function __construct(EntityTypeInterface $entity_type, EntityStorageControllerInterface $storage, QueryFactory $query_factory) {
        parent::__construct($entity_type, $storage);
        $this->queryFactory = $query_factory;
    }

  /**
   * {@inheritdoc}
   */
    public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
        return new static(
            $entity_type,
            $container->get('entity.manager')->getStorageController($entity_type->id()),
            $container->get('entity.query')
        );
    }
    
    /**
     * 
     * @return type
     */
    public function load() {
        $this->subscriptionStatuses = \smartpan_map_subscription_status();
        $entity_query = $this->queryFactory->get($this->entityTypeId);
        $entity_query->pager(30);
        $header = $this->buildHeader();
        $entity_query->tableSort($header);
        $uids = $entity_query->execute();
        return $this->storage->loadMultiple($uids);
    }
    
    public function buildRow(EntityInterface $entity) {
        $plan = entity_load('plan', $entity->get('plan_id')->value);
        $smartpan_user = entity_load('smartpan_user', $entity->get('user_id')->value);
        $drupalUser = entity_load('user', $smartpan_user->get('user_id')->value);
        $row = array(
            'id' => $entity->id(),
            'plan_title' => $plan->get('title')->value,
            'user_name' => $drupalUser->get('name')->value,
            'subscription_bundle' => $entity->bundle() ? $entity->bundle() : 'No Type Assigned',
            'subscription_status' => $this->subscriptionStatuses[$entity->get('status')->value],
            'user_status' => $drupalUser->isActive() ? 'Active' : 'Blocked'
        );
        $row += parent::buildRow($entity);
        return $row;
    }
    
    public function buildHeader() {
        $row = array(
            'id' => $this->t('Subscription ID'),
            'plan_title' => $this->t('Plan Title'),
            'user_name' => $this->t('User Name'),
            'subscription_bundle' => $this->t('Subscription Type'),
            'subscription_status' => $this->t('Subscription Status'),
            'user_status' => $this->t('User Status')
        );
        $row += parent::buildHeader();
        return $row;
    }
}

?>
