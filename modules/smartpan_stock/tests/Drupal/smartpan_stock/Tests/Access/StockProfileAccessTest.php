<?php

/**
 * Description of StockProfileAccessTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Tests\Access;

use Drupal\simpletest\DrupalUnitTestBase;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use SmartPan\Tests\TestBundle\Fixture\Environment;
use Drupal\smartpan_stock\Access\StockProfileAccess;
use Smartpan\Tests\TestBundle\Fixture\Stock as StockFixture;
use Drupal\Core\Language\Language;

class StockProfileAccessTest extends DrupalUnitTestBase
{
    private $userFixture;
    private $environment;
    
    private $smartpan_user;
    
    private $stock;
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        
        $this->userFixture = new UserFixture();
        $this->stockFixture = new StockFixture();
        $this->environment = Environment::create(\Drupal::getContainer());
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Stock Profile Access test',
            'description' => 'Unit test for access to stock profile.',
            'group' => 'Stock'
        );
    }
    
    public function setUp()
    {
        self::$modules = array(
            'braintree', 'smartpan_account',
            'product_subscription', 'user_subscription',
            'smartpan_payment', 'smartpan_stock'
        );
        
        parent::setUp();
        
        $this->environment->createPlan('basic', 'basic_plan');
        $this->environment->createPlan('normal', 'normal_plan');
        
        $this->smartpan_user = $this->userFixture->createAccount();
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $this->smartpanLogin($this->smartpan_user->getDrupalUser());
        
        $this->stock = $this->stockFixture->createProfile();
    }
    
    public function testViewAccessFreeTrial()
    {
        $accessController = new StockProfileAccess($this->stock->getEntityType());
        $access = $accessController->checkAccess($this->stock, 'view', Language::LANGCODE_DEFAULT, $this->smartpan_user->getDrupalUser());
        $this->assertTrue($access, 'The Free Trial User Has access to stock profile');
        
        $this->assertNotNull($this->smartpan_user->getToken(), 'The Access Token Exists');
        $this->assertNotNull($this->smartpan_user->getToken()->isActive(), 'The Access Token is Active');
    }
    
    public function testViewAccessPaid()
    {
        $this->smartpan_user->deleteToken();
        
        $form_1 = $this->userFixture->upgradePlan('normal_plan');
        $form_2 = $this->userFixture->confirmOrder($form_1);
        $this->userFixture->confirmPayment($form_2);
        
        $accessController = new StockProfileAccess($this->stock->getEntityType());
        $access = $accessController->checkAccess($this->stock, 'view', Language::LANGCODE_DEFAULT, $this->smartpan_user->getDrupalUser());
        $this->assertTrue($access, 'The Paid User Has access to stock profile');
        
        $this->assertNotNull($this->smartpan_user->getToken(), 'The Access Token Exists');
        $this->assertNotNull($this->smartpan_user->getToken()->isActive(), 'The Access Token is Active');
    }
}
