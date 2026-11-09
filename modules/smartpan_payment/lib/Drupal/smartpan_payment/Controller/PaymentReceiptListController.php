<?php

/**
 * Description of PaymentReceiptListController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

use Drupal\Core\Entity\EntityListController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;

class PaymentReceiptListController extends EntityListController
{
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
        $entity_query = $this->queryFactory->get($this->entityTypeId);
        $entity_query->pager(40);
        $header = $this->buildHeader();
        $entity_query->tableSort($header);
        $uids = $entity_query->execute();
        return $this->storage->loadMultiple($uids);
    }
    
    public function buildRow(EntityInterface $entity) {
        $order = entity_load('smartpan_order', $entity->get('order_id')->value);
        $subscription = entity_load('subscription', $order->get('subscription_id')->value);
        $plan = entity_load('plan', $subscription->get('plan_id')->value);
        $smartpan_user = entity_load('smartpan_user', $order->get('user_id')->value);
        $drupalUser = entity_load('user', $smartpan_user->get('user_id')->value);
        $row = array(
            'id' => $entity->id(),
            'plan_title' => $plan->get('title')->value,
            'user_name' => $drupalUser->get('name')->value,
            'subscription_type' => $subscription->get('type')->value ? $subscription->get('type')->value : 'No Type Assigned',
            'order_status' => $order->get('status')->value,
            'payment_receipt_status' => $entity->get('status')->value,
            'user_status' => $drupalUser->isActive() ? 'Active' : 'Blocked'
        );
        $row += parent::buildRow($entity);
        return $row;
    }
    
    public function buildHeader() {
        $row = array(
            'id' => $this->t('Order ID'),
            'plan_title' => $this->t('Plan Title'),
            'user_name' => $this->t('User Name'),
            'subscription_type' => $this->t('Subscription type'),
            'order_status' => $this->t('Order Status'),
            'payment_receipt_status' => $this->t('Payment Status'),
            'user_status' => $this->t('User Status'),
        );
        $row += parent::buildHeader();
        return $row;
    }
}

?>
