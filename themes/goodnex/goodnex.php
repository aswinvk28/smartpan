<?php

class GoodnexRoute
{
    public function processNode(&$vars)
    {
        $theme_hook_suggestions = $vars['theme_hook_suggestions'][0];
        $method = $theme_hook_suggestions;
        if(method_exists($this, $method)) {
            call_user_func_array(array($this, $method), array($vars));
        }
    }
    
    public function node__article(&$vars)
    {
        $node = $vars['node'];
        
        if (module_exists('profile2')) {  
          $uid = user_load($node->uid);
          $profile = profile2_load_by_user($uid, 'main');
        } 
        $image_slide = "";

        if ($items = field_get_items('node', $node, 'field_image')) {
          if (count($items) == 1) {
            $image_slide = 'false';
          }
          elseif (count($items) > 1) {
            $image_slide = 'true';
          }
        }

        $img_count = 0;
        $vars['counter'] = count($items);
        
        
    }
    
    public function node__detail_box__full()
    {
        
    }
    
    public function node__faq__full()
    {
        
    }
    
    public function node__flexslider__full()
    {
        
    }
    
    public function node__portfolio__full()
    {
        global $base_url; 
        
        $node = $vars['node'];
        
        $vars['authenticated'] = \Drupal\Core\Session\UserSession::isAuthenticated();
        
        $image_slide = "";

        if ($items = field_get_items('node', $node, 'field_portfolio_slider')) {
          if (count($items) == 1) {
            $image_slide = 'false';
          }
          elseif (count($items) > 1) {
            $image_slide = 'true';
          }
        }

        $img_count = 0;
        $vars['counter'] = count($items);
    }
    
    public function node__view__latest_posts(&$vars)
    {
        $node = $vars['node'];
        $vars['image_slide'] = "";

        if ($items = field_get_items('node', $node, 'field_image')) {
          if (count($items) == 1) {
            $vars['image_slide'] = 'false';
          }
          elseif (count($items) > 1) {
            $vars['image_slide'] = 'true';
          }
        }
        
        $teaser = strip_tags(render($vars['content']['body']));
        $vars['teaser_sub'] = substr($teaser, 0, 100)."...";
    }
    
    public function node__view__popular_posts(&$vars)
    {
        $node = $vars['node'];
        
        $teaser = strip_tags(render($vars['content']['body']));
        $vars['teaser_sub'] = substr($teaser, 0, 100)."...";
    }
    
    public function node__view__portfolio_block(&$vars) 
    {
        $search = array(' ', '-');
        $replace = array(' / ', ' ');
        $cat = str_replace($search, $replace, strip_tags(render($vars['content']['field_portfolio_category'])));
        $replacement = " ";
        
        $vars['replacement_sub'] = substr($cat, 0, -2).$replacement;
        $vars['content_field_portfolio_category_strip_tags'] = strip_tags(content.field_portfolio_category);
    }
    
    public function node__view__recent_works__block()
    {
        $vars['content_category'] = str_replace(' ', ' / ', strip_tags(render($vars['content']['field_portfolio_category'])));
    }
    
    public function views__view_fields__portfolio_filters(&$vars)
    {
        $vars['field_content'] = str_replace(' ', '-', $vars['field']->content);
    }
}

?>
