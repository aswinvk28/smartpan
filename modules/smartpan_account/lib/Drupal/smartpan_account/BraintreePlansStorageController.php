<?php

/**
 * @file
 * Definition of Drupal\smartpan_account\BraintreePlansStorageController.
 */

namespace Drupal\smartpan_account;

use Drupal\Core\Config\Entity\ConfigStorageController;

/**
 * Defines a controller class for taxonomy vocabularies.
 */
class BraintreePlansStorageController extends ConfigStorageController 
{
    public $fieldTypeTaxonomySettingMapping = array(
        'type' => 'plan_vocabulary',
        'duration' => 'duration_vocabulary'
    );
    
    public function loadMultipleForOptions($field = 'machineName')
    {
        $entities = $this->loadMultiple();
        if(empty($entities)) return FALSE;
        $options = array();
        $method = 'get' . ucfirst($field);
        foreach($entities as $entityId => $entity) {
            $options[$entityId] = $entity->{$method}();
        }
        return $options;
    }
    
    public function planFieldOptions($field = 'machineName')
    {
        static $options;
        if(empty($options[$field])) {
            $options[$field] = $this->loadMultipleForOptions($field);
        }
        return $options[$field];
    }
    
    public function planOptions($field = 'type', $reset = false)
    {
        static $options;
        if($reset) $options = array();
        if(empty($options[$field])) {
            $vocabulary = $this->planVocabulary($field);
            $options[$field] = smartpan_load_multiple_taxonomy_options($vocabulary);
        }
        return $options[$field];
    }
    
    public function planVocabulary($field)
    {
        static $settings;
        if(!isset($settings)) {
            $resource_path = drupal_get_path('module', 'smartpan_account') . '/config/smartpan_account.settings.yml';
            $settings = \Symfony\Component\Yaml\Yaml::parse($resource_path);
        }
        return $settings[$this->fieldTypeTaxonomySettingMapping[$field]];
    }
    
    public function planValues($field = 'type', $reset = false)
    {
        static $options;
        if($reset) $options = array();
        if(empty($options[$field])) {
            $vocabulary = $this->planVocabulary($field);
            $options[$field] = smartpan_load_multiple_taxonomy_names($vocabulary);
        }
        return $options[$field];
    }
}
