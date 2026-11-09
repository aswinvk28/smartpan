<?php

/**
 * Description of SmartpanAccountAccessCheckTest
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Tests\Controller;

use Drupal\simpletest\WebTestBase;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;

class SmartpanAccountCreateTest extends WebTestBase
{
    public static $modules = array('smartpan_account', 'product_subscription', 'user_subscription');
    
    private $member_user;
    private $smartpan_user;
    private $userFixture;
    
    public static function getInfo()
    {
        return array(
            'name' => 'Smartpan Account Member Access test',
            'description' => 'Unit test of smartpan account access for a member.',
            'group' => 'Smartpan Account'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->userFixture = new UserFixture();
        \Drupal::config('system.site')->set('mail', 'aswinvk28@gmail.com')->save();
    }
    
    public function providerTestMemberCreate()
    {
        return array(\Drupal::config('system.site')->get('mail'), 'Test First Name', 'Test Last Name');
    }
    
    public function testMemberCreate($userName, $firstName, $lastName)
    {
        $this->member_user = $this->userFixture->createAccount($userName, $firstName, $lastName);
        $this->userFixture->register($this->member_user);
    }
    
    public function providerTestMemberCreateForBasic()
    {
        return array('unamious@gmail.com', 'Trial First Name', 'Trial Last Name');
    }
    
    public function testMemberCreateForBasic($userName, $firstName, $lastName)
    {
        $this->drupalPostForm('account/create/basic/free-trial', array(
            'plan' => $plan,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $userName
        ), t('Save'));
        $this->member_user = user_load_by_name($userName);
        $this->assertFalse($this->member_user, 'Member Not created');
        $smartpan_user = entity_load_multiple_by_properties('smartpan_user', array(
            'user_id' => $this->member_user->id()
        ));
        $this->assertEqual($smartpan_user, array(), 'Smartpan Member Not loaded');
        if(!empty($smartpan_user)) {
            $this->smartpan_user = current($smartpan_user);
            $this->assertEqual($this->smartpan_user->get('first_name'), $firstName, 'Smartpan User First Name Matches');
            $this->assertEqual($this->smartpan_user->get('last_name'), $lastName, 'Smartpan User Last Name Matches');
        }
    }
}

?>
