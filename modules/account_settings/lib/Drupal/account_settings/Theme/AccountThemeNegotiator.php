<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\account_settings\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Extension\ThemeHandler;

/**
 * Description of AccountThemeNegotiator
 *
 * @author aswinvk28
 */
class AccountThemeNegotiator implements ThemeNegotiatorInterface
{
    private $theme_name;
    private $theme_handler;
    
    public function __construct(Settings $settings, ThemeHandler $theme_handler)
    {
        $this->theme_name = $settings->get('active_account_theme', 'flatui');
        $this->theme_handler = $theme_handler;
    }
    
    /**
     * {@inheritdoc}
     */
    public function applies(Request $request)
    {
        $theme_page = $request->attributes->get('_theme', '');
        return $theme_page === 'dashboard_page';
    }

    /**
     * {@inheritdoc}
     */
    public function determineActiveTheme(Request $request)
    {
        if(array_key_exists($this->theme_name, $this->theme_handler->listInfo())) {
            return $this->theme_name;
        }
    }
}

?>
