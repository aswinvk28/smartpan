<?php

/**
 * Description of AccountFormRegisterController
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Controller;

use Drupal\user\RegisterFormController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\product_subscription\Controller\PlanController;

class AccountFormRegisterController extends RegisterFormController implements ContainerInjectionInterface
{
    private $smartpanUser;
    
    private $plan = array();
    
    private $futurePlan;
    private $subscription;
    private $planTypeTerm;
    
    private $parameterBag;
    
    public function getBaseFormID()
    {
        return NULL;
    }
    
    public function prepareEntity()
    {
        $this->setPlan();
        $this->setPlanTitle();
    }
    
    public function setPlan()
    {
        $this->parameterBag = new ParameterBag(array(
            'plan_controller' => new PlanController()
        ));
        $planType = $this->getRequest()->attributes->get('plan_type', false);
        $subscriptionType = $this->getRequest()->attributes->get('subscription_type', false);
        $vocabulary = $this->entityManager->getStorageController('braintree_plan')->planVocabulary('type');
        $subscriptionTypeTerm = taxonomy_term_load_multiple_by_name($subscriptionType, $vocabulary);
        $planController = $this->parameterBag->get('plan_controller');
        if(!empty($subscriptionTypeTerm)) {
            $subscriptionTypeTerm = current($subscriptionTypeTerm);
            $this->planTypeTerm = $subscriptionTypeTerm;
            $plans = $planController->getAllPublishedPlansFromPlanType($this->planTypeTerm->id());
            if($subscriptionTypeTerm->get('name')->value == 'free-trial') {
                $this->plan = $plans;
            } else {
                $planTypeTerm = taxonomy_term_load_multiple_by_name($planType, $vocabulary);
                $planTypeTerm = current($planTypeTerm);
                $highlightedPlans = $planController->searchHighlightedPlanDB(array(
                    'price' => 0.00,
                    'type' => $planTypeTerm->get('tid')->value
                ));
                $planController->setTrialPlansOnPaid($plans);
                if(!empty($planTypeTerm)) {
                    if($planTypeTerm->get('name')->value == 'free-trial') {
                        $planTypes = $planController->searchPlanTypePlanDB(array(
                            'type' => $subscriptionTypeTerm->get('tid')->value
                        ));
                        $this->plan = $planController->searchPlanMachineNameTrialPlanDB($planTypes);
                        if(empty($this->plan)) {
                            $this->plan = $highlightedPlans;
                        }
                    }
                }
            }
        }
        
        if(empty($this->plan)) {
            drupal_set_message('Environment not set up or Requested Resource not Found', 'error');
        }
    }
    
    public function setPlanTitle()
    {
        if(empty($this->plan)) return;
        $planTitles = array();
        foreach($this->plan as $planId => $plan) {
            $planTitles[$planId] = 'Free Trial: '.$plan->planPublicTitle();
        }
        $this->parameterBag->set('plan.planTitleOptions', $planTitles);
        $this->parameterBag->set('plan', '');
    }
    
    public function buildSmartPanUser()
    {
        $this->smartpanUser = entity_create('smartpan_user', array());
    }
    
    public function buildSubscription()
    {
        $this->subscription = entity_create('subscription', array(
            'plan_id' => 0
        ));
    }
    
    public function buildFuturePlan($planId)
    {
        $this->futurePlan = entity_load('plan', $planId);
    }
    
    public function buildForm(array $form, array &$form_state) 
    {
        $this->buildSmartPanUser();
        if(empty($this->plan)) {
            $this->init($form_state);
        }
        if(empty($this->plan)) {
            $render = array('#theme' => 'status_messages');
            return $render;
        }
        $form = parent::buildForm($form, $form_state);
        return $form;
    }
    
    public function form(array $form, array &$form_state)
    {
        $form = parent::form($form, $form_state);
        
        $planTitles = $this->parameterBag->get('plan.planTitleOptions');
        
        $form['plan'] = array(
            '#type' => 'select',
            '#title' => 'Type of Plan',
            '#options' => $planTitles,
            '#empty_value' => '',
            '#empty_option' => 'None Selected',
            '#required' => true,
            '#default_value' => '',
        );
        $form['form_inline'] = array(
            'first_name' => array(
                '#type' => 'textfield',
                '#title' => 'First Name',
                '#title_display' => false,
                '#required' => true,
                '#default_value' => !is_null($this->entity->id()) && !is_null($this->smartpanUser->get('first_name')->value) ? $this->smartpanUser->get('first_name')->value : ''
            ),
            'last_name' => array(
                '#type' => 'textfield',
                '#title' => 'Last Name',
                '#title_display' => false,
                '#required' => true,
                '#default_value' => !is_null($this->entity->id()) && !is_null($this->smartpanUser->get('last_name')->value) ? $this->smartpanUser->get('last_name')->value : ''
            ),
            '#title' => '',
            '#theme' => 'form_inline',
            '#container_type' => 'no_wrapper'
        );
        
        $form['account']['mail'] = array(
            '#type' => 'value'
        );
        $form['account']['init'] = array(
            '#type' => 'value'
        );
        $form['account']['#type'] = 'container';
        $form['account']['#container_type'] = 'no_wrapper';
        unset($form['account']['name']['#description']);
        unset($form['account']['pass']['#description']);
        
        return $form;
    }
    
    /**
     * 
     * @param array $form
     * @param array $form_state
     */
    public function validate(array $form, array &$form_state)
    {
        if(empty($this->planTypeTerm)) {
            $this->prepareEntity();
        }
        
        $form_state['values']['mail'] = $form_state['values']['name'];
        $form_state['values']['init'] = $form_state['values']['name'];
        
        parent::validate($form, $form_state);
        
        $definition = DataDefinition::create('string')
        ->addConstraint('Email', array('checkHost' => true));
        
        $typed_data = \Drupal::typedDataManager()->create($definition, $form_state['values']['name']);
        $violations = $typed_data->validate();
        
        if($violations->count() > 0 || strlen($form_state['values']['name']) > 58) {
            $this->setFormError('name', $form_state, $violations[0]->getMessage());
        }
        
        $planId = $form_state['values']['plan'];
        if(!array_key_exists($planId, $this->plan)) {
            $this->setFormError('plan', $form_state, "The Plan is not Valid");
        }
        $this->buildFuturePlan($planId);
        if($this->planTypeTerm->get('tid')->value !== $this->futurePlan->get('type')->value) {
            $this->setFormError('plan', $form_state, "The Plan is not Valid");
        }
    }
    
    public function submit(array $form, array &$form_state) {
        $this->buildSmartPanUser();
        $this->buildSubscription();
        
        parent::submit($form, $form_state);
    }
    
    /**
     * 
     * @param array $form
     * @param array $form_state
     */
    public function save(array $form, array &$form_state)
    {
        $transaction = \Drupal::database()->startTransaction();
        
        try {
            parent::save($form, $form_state);
            
            $drupalUserId = $this->entity->id();
            
            $this->smartpanUser->set('user_id', $drupalUserId);
            $this->smartpanUser->set('first_name', $form_state['values']['first_name']);
            $this->smartpanUser->set('last_name', $form_state['values']['last_name']);
            $savedStatus = $this->smartpanUser->save();
            if(empty($savedStatus)) {
                throw new \Exception("User Not Saved");
            }
            $this->subscription->set('user_id', $this->smartpanUser->id());
            $this->subscription->set('type', $this->planTypeTerm->get('tid')->value);
            $this->subscription->set('plan_id', $this->futurePlan->id());
            $savedStatus = $this->subscription->save();
            if(empty($savedStatus)) {
                throw new \Exception("Subscription Not Saved");
            }
        } catch(\Drupal\Core\Entity\EntityStorageException $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
        catch(\Exception $exception) {
            $transaction->rollback();
            throw new \Exception($exception->getMessage());
        }
    }
}

?>
