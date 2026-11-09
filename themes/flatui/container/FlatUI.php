<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FlatUI
 *
 * @author aswinvk28
 */
class FlatUI
{
    private static $secondaryMenu;
    private static $isDashboard = false;
    private static $login = false;
    
    public static function getSecondaryMenu()
    {
        return self::$secondaryMenu;
    }
    
    public static function setSecondaryMenu($secondaryMenu)
    {
        self::$secondaryMenu = $secondaryMenu;
    }
    
    public static function setDashboard()
    {
        self::$isDashboard = true;
    }
    
    public static function getDashboard()
    {
        return self::$isDashboard;
    }
    
    public static function setLogin()
    {
        self::$login = true;
    }
    
    public static function getLogin()
    {
        return self::$login;
    }
}

?>
