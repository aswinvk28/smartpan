<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use Drupal\Core\Template\Attribute;
use Drupal\Core\Render\Element;
use Drupal\Core\Template\RenderWrapper;

function flatui_preprocess_html(&$variables) {
    if(!$variables['is_admin']) {
        drupal_static_reset('_drupal_add_css');
        $static_cache = &drupal_static('drupal_get_library');
        unset($static_cache['system']['jquery']);
        drupal_add_library('flatui', 'replacement', true);
    }
//    drupal_add_library('flatui', 'flatui_css', true);
    drupal_add_library('flatui', 'dashboard_css', true);
    drupal_add_library('flatui', 'bootstrap_js', true);
//    drupal_add_library('flatui', 'flatui_select', true);
    drupal_add_library('system', 'underscore', true);
    drupal_add_library('system', 'backbone', true);
    drupal_add_library('flatui', 'bootstrap', true);
    drupal_add_library('flatui', 'dashboard_js', true);
    drupal_add_library('flatui', 'jquery_placeholder', true);
    drupal_add_library('smartpan', 'appjs', true);
    drupal_add_library('flatui', 'flatui', true);
}

/**
 * Implements hook_preprocess_HOOK() for page templates.
 *
 * Adds body classes if certain regions have content.
 */
function flatui_preprocess_page(&$variables) {
  // Add information about the number of sidebars.
  /** @var \Drupal\Core\Page\HtmlPage $page_object */
  $page_object = isset($variables['page']['#page']) ? $variables['page']['#page'] : NULL;
  if(!empty($page_object)) {
      $attributes = $page_object->getBodyAttributes();
      $classes = $attributes['class'];
      if (!empty($variables['page']['sidebar']) && !empty($variables['page']['sidebar_mini'])) {
        $classes[] = 'span4';
      }
      elseif (!empty($variables['page']['sidebar'])) {
        $classes[] = 'span7';
      }
      elseif (!empty($variables['page']['sidebar_mini'])) {
        $classes[] = 'span9';
      }
      else {
        $classes[] = 'clearfix';
      }

      if (!empty($variables['page']['featured'])) {
        $classes[] = 'featured';
      }

      // Store back the classes to the htmlpage object.
      $attributes['class'] = $classes;
  }

  // Pass the main menu and secondary menu to the template as render arrays.
  if (!empty($variables['main_menu'])) {
    $variables['main_menu']['#attributes']['id'] = 'main-menu-links';
    $variables['main_menu']['#attributes']['class'] = array('links', 'clearfix');
  }
  if(!empty($variables['secondary_menu'])) {
      FlatUI::setSecondaryMenu($variables['secondary_menu']);
  }

  // Set the options that apply to both page and maintenance page.
  _flatui_process_page($variables);

  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
  
  $stock_symbol_form = array(
      'class' => array('form-search'),
      'action' => url('symbol/', array('base_path' => true)),
      'data-action' => url('symbol/', array('base_path' => true)),
      'method' => 'POST'
  );
  
  $variables['stock_symbol_form'] = array(
      '#theme' => 'form_autocomplete',
      '#view_mode' => 'stock_symbol',
      '#attributes' => $stock_symbol_form,
      '#prefix' => '<form '.new Attribute($stock_symbol_form).'>',
      '#suffix' => '</form>',
      '#attributes_input' => array(
          'placeholder' => 'STOCK SYMBOL',
          'name' => 'stock_symbol',
          'id' => 'stock-symbol'
      ),
      '#hidden_attributes' => array(
          'name' => 'stock_id',
          'id' => 'stock-id'
      )
  );
  
  $variables['is_dashboard'] = FlatUI::getDashboard();
  if(FlatUI::getLogin()) {
      $variables['title'] = false;
  }
  
}

/**
 * Implements hook_preprocess_HOOK() for maintenance page templates.
 */
function flatui_preprocess_maintenance_page(&$variables) {
  // By default, site_name is set to Drupal if no db connection is available
  // or during site installation. Setting site_name to an empty string makes
  // the site and update pages look cleaner.
  // @see template_preprocess_maintenance_page
  if (!$variables['db_is_active']) {
    $variables['site_name'] = '';
  }
  $variables['styles'] = new RenderWrapper('drupal_get_css');
  // Normally we could attach libraries via hook_page_alter(), but when the
  // database is inactive it's not called so we add them here.
  $libraries = array(
    '#attached' => array(
      'library' => array(
        array('bartik', 'maintenance_page'),
      ),
    ),
  );

  // Set the options that apply to both page and maintenance page.
  _flatui_process_page($variables);
}

function flatui_preprocess_links__system_secondary_menu(&$variables) {
    $element = $variables['element'];
    $variables['text'] = 'Account';
    foreach($element['#links'] as $link) {
        $variables['links'][] = $link;
    }
    foreach($variables['links'] as &$link) {
        $link['href'] = url($link['href'], array('base_path' => true));
    }
    $variables['element']['#attributes']['id'] = 'secondary-menu-links';
    $variables['element']['#attributes']['class'] = array('links', 'inline', 'clearfix');
}

function flatui_preprocess_links__system_main_menu(&$variables) {
    $element = $variables['element'];
    $variables['text'] = 'Navigate To';
    $variables['rest_links'] = $element['#links'];
    foreach($variables['rest_links'] as &$link) {
        $link['href'] = url($link['href'], array('base_path' => true));
    }
    $variables['secondary_menu'] = FlatUI::getSecondaryMenu();
    if(!empty($variables['secondary_menu'])) {
        $accountLinks = menu_navigation_links('account_holder');
        $variables['account_links'] = array();
        if(!empty($accountLinks)) {
            $variables['account_links'] = $accountLinks;
            foreach($variables['account_links'] as &$link) {
                $link['href'] = url($link['href'], array('base path' => true));
            }
        }
    } else {
        $nonAccountLinks = menu_navigation_links('nonaccount');
        $variables['non_account_links'] = array();
        if(!empty($nonAccountLinks)) {
            $variables['non_account_links'] = $nonAccountLinks;
            foreach($variables['non_account_links'] as &$link) {
                $link['href'] = url($link['href'], array('base path' => true));
            }
        }
    }
}

function flatui_preprocess_dashboard(&$variables) {
    FlatUI::setDashboard();
}

function flatui_prepreprocess_select(&$variables) {
    $variables['element']['#empty_value'] = '';
    $variables['element']['#empty_option'] = 'None Selected';
}

function flatui_preprocess_form_autocomplete(&$variables) {
    $variables['attributes'] = new Attribute($variables['element']['#attributes_input']);
    
    $variables['hidden_attributes'] = new Attribute($variables['element']['#hidden_attributes']);
}

function flatui_preprocess_user_profile(&$variables) {
    $variables['attributes']['class'][] = 'clearfix';
}

function flatui_preprocess_user_account(&$variables) {
    $variables['attributes']['class'][] = 'clearfix';
}

function flatui_preprocess_tabs(&$variables) {
    $element = $variables['element'];
    
    $variables['element']['#attributes']['class'][] = 'tabbable tabs-left';
    
    $tabKeys = Element::children($element);
    
    $variables['items'] = $variables['content'] = array();
    
    if(!empty($tabKeys)) {
        $tabs = array();
        $index = 1;
        foreach($tabKeys as $key) {
            $id = 'bootstrap-tab-'.$index;
            
            $active = '';
            if($index == 1) {
                $active = 'active';
            }
            
            $variables['items'][] = array(
                'active' => $active,
                'content' => '<a href="#'.$id.'" data-toggle="tab">'.$element[$key]['#title'].'</a>'
            );
            
            $html = drupal_render($element[$key], false);
            $variables['content'][] = array(
                'active' => $active,
                'id' => $id,
                'html' => $html
            );
            $index++;
        }
    }
}

function flatui_preprocess_table(&$variables) {
    $variables['attributes']['class'][] = 'table';
    $variables['attributes']['class'][] = 'table-striped';
    $variables['attributes']['class'][] = 'table-bordered';
    $variables['attributes']['class'][] = 'white';
    $variables['responsive'] = false;
}

function flatui_preprocess_login_container(&$variables) {
    $variables['element'] += array('#attributes' => array());
    $variables['element']['#attributes']['class'][] = 'login-container';
    $variables['element']['#attributes']['class'][] = 'well well-large';
    FlatUI::setLogin();
}

?>