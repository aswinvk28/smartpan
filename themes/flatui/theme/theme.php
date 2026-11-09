<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use Drupal\Core\Template\Attribute;
use Drupal\Core\Render\Element;

function flatui_menu_local_tasks(&$variables) {
    $output = '';
    if (!empty($variables['primary'])) {
        $variables['primary']['#prefix'] .= '<ul class="nav nav-tabs">';
        $variables['primary']['#suffix'] = '</ul>';
        $output .= drupal_render($variables['primary']);
    }
    if (!empty($variables['secondary'])) {
        $variables['secondary']['#prefix'] .= '<ul class="nav nav-tabs secondary">';
        $variables['secondary']['#suffix'] = '</ul>';
        $output .= drupal_render($variables['secondary']);
    }
    return $output;
}

function flatui_status_messages(&$variables) {
    $output = '';
    if(!empty($variables['message_list']) && is_array($variables['message_list'])) {
        $element = $variables['message_list'];
        $types = array_keys($element);
        $alertTypes = array_combine($types, $types);
        array_walk($alertTypes, function(&$value) {
            $value = flatui_message_mapper($value);
        });
        $alertClasses = array_combine($types, $types);
        array_walk($alertClasses, function(&$value) {
            $value = flatui_message_class_mapper($value);
        });
        
        foreach($variables['message_list'] as $type => $message) {
            $output .= '<div class="alert '.$alertClasses[$type].'"><button type="button" class="close" data-dismiss="alert">&times;</button>';
            foreach($message as $text) {
                $output .= "<p><strong> $alertTypes[$type] </strong> $text </p>";
            }
            $output .= '</div>';
        }
    }
    return $output;
}

function flatui_select(&$variables) {
    $element = $variables['element'];
    
    if(!isset($element['#prefix']) && !isset($element['#suffix'])) {
        $element['#prefix'] = '<div class="field">
            <div class="picker picker-container column_small decolumn_small prelative">
            <div class="clearfix picker">';
        $element['#suffix'] = '</div></div></div>';
    }
    
    $variables['attributes']['class'][] = 'flat-ui-component';
    $variables['attributes']['class'][] = 'component-select';
    $variables['attributes']['name'] = $element['#name'];
    
    return $element['#prefix'] . '<select ' . new Attribute($variables['attributes']) . '>' . form_select_options($element) . '</select>'.$element['#suffix'];
}

function flatui_input__textfield($variables) {
    $element = $variables['element'];
    
    $variables['attributes']['class'][] = 'input-padding-medium';
    $variables['attributes']['class'][] = 'column_mini';
    if(isset($element['#title'])) {
        $variables['attributes']['placeholder'] = $element['#title'];
    }
    
    $attributes = $variables['attributes'];
    
    return '<input' . $attributes . ' />' . drupal_render_children($element);
}

/**
 * Returns HTML for a form.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #action, #method, #attributes, #children
 *
 * @ingroup themeable
 */
function flatui_container($variables) {
    
    $element = $variables['element'];
    
    if(isset($element['#container_type'])) {
        if($element['#container_type'] == 'no_wrapper') {
            return $element['#children'];
        } elseif($element['#container_type'] == 'login_container') {
            $element['#attributes']['class'][] = 'login-container';
        }
    }
    
    // Ensure #attributes is set.
    $element += array('#attributes' => array());
    
    $element['#attributes']['class'][] = 'clearfix';
    if($element['#type'] != 'actions') {
        $element['#attributes']['class'][] = 'column';
        $element['#attributes']['class'][] = 'decolumn_small';
    }

    // Special handling for form elements.
    if (isset($element['#array_parents'])) {
        // Assign an html ID.
        if (!isset($element['#attributes']['id'])) {
          $element['#attributes']['id'] = $element['#id'];
        }
        // Add the 'form-wrapper' class.
        if($element['#type'] != 'actions') {
            $element['#attributes']['class'][] = 'well';
        }
        if(!isset($element['#container_type'])) {
            $element['#attributes']['class'][] = 'form-wrapper';
        }
    }

    return '<div' . new Attribute($element['#attributes']) . '>' . $element['#children'] . '</div>';
}

function flatui_login_container(&$variables) {
    $element = $variables['element'];
    
    return '<div' . new Attribute($element['#attributes']) . '>' . drupal_render_children($element) . '</div>';
}

function flatui_link_button(&$variables) {
    $element = $variables['element'];
    
    $element['#attributes']['class'][] = 'btn btn-large decolumn_mini';
    
    if(!isset($element['#prefix']) && !isset($element['#suffix'])) {
        $element['#prefix'] = '';
        $element['#suffix'] = '';
    }
    
    $description = '';
    if(isset($element['#description'])) {
        $description = '<div class="description">'.$element['#description'].'</div>';
        $element['#prefix'] .= '<div class="clearfix">';
        $element['#suffix'] .= '</div>';
    }
    
    if(isset($element['#button_type']) && $element['#button_type'] == 'danger') {
        $element['#attributes']['class'][] = 'btn-danger';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'primary') {
        $element['#attributes']['class'][] = 'btn-primary';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'info') {
        $element['#attributes']['class'][] = 'btn-info';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'warning') {
        $element['#attributes']['class'][] = 'btn-warning';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'link') {
        $element['#attributes']['class'][] = 'btn-link';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'success') {
        $element['#attributes']['class'][] = 'btn-success';
    }
    
    return $element['#prefix'] . '<a' . new Attribute($element['#attributes']) . ' />'.$element['#value'].'</a>'.$description . $element['#suffix'];
}

function flatui_button(&$variables) {
    $element = $variables['element'];
    
    $element += array('#attributes' => array());
    
    $element['#attributes']['class'][] = 'btn btn-large decolumn_mini';
    
    if(isset($element['#button_type']) && $element['#button_type'] == 'danger') {
        $element['#attributes']['class'][] = 'btn-danger';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'primary') {
        $element['#attributes']['class'][] = 'btn-primary';
    } else {
        $element['#attributes']['class'][] = 'btn-success';
    }
    
    return '<button' . new Attribute($element['#attributes']) . ' />'.$element['#value'].'</button>';
}

function flatui_input__button(&$variables) {
    $element = $variables['element'];
    
    $element += array('#attributes' => array());
    
    $element['#attributes']['class'][] = 'btn btn-large decolumn_mini';
    
    if(isset($element['#button_type']) && $element['#button_type'] == 'danger') {
        $element['#attributes']['class'][] = 'btn-danger';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'primary') {
        $element['#attributes']['class'][] = 'btn-primary';
    } else {
        $element['#attributes']['class'][] = 'btn-success';
    }
    
    return '<input' . new Attribute($element['#attributes']) . ' />';
}

function flatui_input__submit(&$variables) {
    $element = $variables['element'];
    
    $element += array('#attributes' => array());
    
    $element['#attributes']['class'][] = 'btn btn-large decolumn_mini';
    
    if(isset($element['#button_type']) && $element['#button_type'] == 'danger') {
        $element['#attributes']['class'][] = 'btn-danger';
    } elseif(isset($element['#button_type']) && $element['#button_type'] == 'primary') {
        $element['#attributes']['class'][] = 'btn-primary';
    } else {
        $element['#attributes']['class'][] = 'btn-success';
    }
    
    return '<input' . new Attribute($element['#attributes']) . ' />';
}

function flatui_form_inline(&$variables) {
    $element = $variables['element'];

    return '<div class="form-inline clearfix">'.drupal_render_children($element).'</div>';
}

function flatui_ajax_field(&$variables) {
    $element = $variables['element'];
    
    $element['#attributes']['class'][] = 'ajax-field';
    $element['#attributes']['class'][] = 'column_mini';
    $element['#attributes']['class'][] = 'uneditable-input';
    $element['#attributes']['placeholder'] = $element['#title'];
    
    if(!isset($element['#prefix']) && !isset($element['#suffix'])) {
        $element['#prefix'] = '<label class="form-label column clearfix"><div class="clearfix">' . $element['#title'] . ':</div>';
        $element['#suffix'] = '</label>';
    }
    
    return $element['#prefix'] . '<' . $element['#tag'] . new Attribute($element['#attributes']) . '>' . $element['#value'] . '</'.$element['#tag'].'>' . $element['#suffix'];
}

function flatui_plans_and_pricing(&$variables) {
    $plans = $variables['element']['#plans'];
    $planController = $variables['element']['#plan_controller'];
    
    $featuresRender = array(
        '#theme' => 'plan_type_features'
    );
    $currentPlan = NULL;
    $currentSubscription = NULL;
    $drupalUser = entity_load('user', \Drupal::currentUser()->id());
    $member = smartpan_is_member($drupalUser);
    $userController = new SmartPan\Bundles\AccountBundle\Controller\UserController();
    if(\Drupal::currentUser()->isAuthenticated() && $member) {
        $smartpanUser = $userController->getCurrentActiveSmartpanUser();
        if(empty($smartpanUser)) throw new \Exception('User is blocked');
        $currentSubscription = $smartpanUser->getActiveSubscription();
        $currentPlan = $currentSubscription->getPlan();
    }
    $rows = array();
    $rows[] = array(
        'title' => '',
        'price' => 'Plan Price',
        'duration' => 'Plan Duration',
        'features_html' => drupal_render($featuresRender),
        'button' => ''
    );
    
    foreach($plans as $planId => $plan) {
        $planType = entity_load('taxonomy_term', $plan->get('type')->value);
        $planDuration = entity_load('taxonomy_term', $plan->get('duration')->value);
        if(empty($planType)) throw new \Exception('Environment not set up');
        $featuresRender = array(
            '#theme' => 'plan_type_'.$planType->get('name')->value.'_features'
        );
        $isCurrentPlan = false;
        if(!empty($currentPlan)) {
            $isCurrentPlan = $currentPlan->id() == $plan->id();
        }
        $rows[] = array(
            'title' => $plan->get('title')->value,
            'price' => $plan->get('price')->value,
            'duration' => flatui_plan_duration_mapper($planDuration->get('name')->value),
            'features_html' => drupal_render($featuresRender),
            'button' => flatui_plan_button_mapper(
                $member, $plan, $planController, $isCurrentPlan, $currentSubscription, $planType->get('name')->value
            )
        );
    }
    
    $buildRows = array();
    if(!empty($rows)) {
        foreach($rows as $key => $row) {
            $count = 0;
            foreach($row as $index => $value) {
                $buildRows[$count]['plan_'.$key] = $value;
                $count++;
            }
        }
    }
    
    $table = array(
        '#theme' => 'table',
        '#rows' => $buildRows,
        '#header' => array(),
        '#empty' => 'No Plans set up',
        '#title' => 'Available Plans',
        '#attributes' => array(
            'class' => array('table', 'table-responsive')
        )
    );
    
    $svg_checkbox = array(
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => file_get_contents(DRUPAL_ROOT . base_path() . path_to_theme() . '/packages/Flat-UI/svg/checkbox-checked.svg'),
        '#attributes' => array(
            'type' => array('text/template'),
            'id' => 'svg-checkbox'
        )
    );
    
    return drupal_render($table) . drupal_render($svg_checkbox);
}

//function flatui_input__checkbox(&$variables) {
//    $output = '';
//    $element = $variables['element'];
//    if(!isset($element['#prefix']) && !isset($element['#suffix'])) {
//        $element['#prefix'] = '<label class="checkbox">
//            <span class="icons"><span class="first-icon fui-checkbox-unchecked"></span><span class="second-icon fui-checkbox-checked"></span></span>';
//        $element['#suffix'] = '</label>';
//    }
//    $element['#attributes']['data-toggle'] = 'checkbox';
//    $attributes = new Attribute($element['#attributes']);
//    $output .= $element['#prefix'] . '<input type="checkbox"' . $attributes . ' />' . $element['#suffix'];
//    return $output;
//}
//
//function flatui_textarea(&$variables) {
//    $output = '';
//    $element = $variables['element'];
//    $attributes = new Attribute($element['#attributes']);
//    $output .= 
//            '<textarea class="input-padding-medium column_mini" placeholder="' . $element['#title'] . '" value="' . 
//            !empty($element['#default_value']) ? $element['#default_value'] : '' . '"' .
//            $attributes . '></textarea>';
//    return $output;
//}

?>
