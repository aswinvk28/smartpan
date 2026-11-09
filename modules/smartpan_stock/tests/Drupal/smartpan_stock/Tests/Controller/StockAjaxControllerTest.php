<?php

/**
 * Description of StockAjaxControllerTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Tests\Controller;

use Smartpan\Tests\TestBundle\WebTest;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;

class StockAjaxControllerTest extends WebTest
{
    private $smartpan_user;
    
    public static $modules = array(
        'smartpan_account', 'product_subscription', 
        'braintree', 'user_subscription', 
        'memcache', 'smartpan_stock'
    );
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        $this->userFixture = new UserFixture();
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Fetch Stock Data for Dashboard',
            'description' => 'Unit test of fetching stock data for the symbol.',
            'group' => 'Stock'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->smartpan_user = $this->userFixture->createAccount();
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $this->smartpanLogin($this->smartpan_user->getDrupalUser());
    }
    
    public function testFetchDashboard()
    {
        $json = $this->drupalGet('stock/YHOO/1');
        
        $result = \Drupal\Component\Utility\Json::decode($json);
        
        $this->assertNotNull($result['profile'], 'Profile Not Null');
        $this->assertNotNull($result['market'], 'Market Not Null');
        $this->assertNotNull($result['industry'], 'Industry Not Null');
        $this->assertNotNull($result['investor'], 'Investor Not Null');
        $this->assertNotNull($result['price'], 'Price Not Null');
        $this->assertNotNull($result['volume'], 'Volume Not Null');
        $this->assertNotNull($result['return'], 'Return Not Null');
        $this->assertNotNull($result['news'], 'News Not Null');
    }
}
