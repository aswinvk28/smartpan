<?php

/**
 * Description of CreditCardAddFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Tests\Form;

use Smartpan\Tests\TestBundle\WebTest;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use Smartpan\Tests\TestBundle\Fixture\Payment as PaymentFixture;
use SmartPan\Tests\TestBundle\Fixture\Environment;
use Drupal\smartpan_payment\Controller\BraintreeActionController;

class CreditCardAddFormTest extends WebTest
{
    private $smartpan_user;
    private $userFixture;
    
    public static $modules = array('smartpan_account', 'product_subscription', 'braintree', 'user_subscription', 'smartpan_payment');
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        $this->userFixture = new UserFixture();
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Add Credit Card',
            'description' => 'Unit test of adding credit card form for setting up payment token.',
            'group' => 'Payment'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->smartpan_user = $this->userFixture->createAccount();
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $this->smartpanLogin($this->smartpan_user->getDrupalUser());
    }
    
    public function testCreditCardSave()
    {
        $this->drupalPostForm('account/card/add', array(
            'card_scheme' => 'VISA',
            'credit_card_number' => PaymentFixture::TEST_CARD,
            'credit_card_name' => 'SAMPLE NAME',
            'expiration_month' => '03',
            'expiration_year' => '2015',
            'credit_card_cvv' => '123'
        ), t('Save'));
        
        $this->checkCreditCardSave();
    }
    
    public function checkCreditCardSave()
    {
        $this->smartpan_user = Environment::getSmartpanUserFromDrupalUser($this->smartpan_user->getDrupalUser());
        
        $this->assertNotNull($this->smartpan_user->get('payment_token')->value, 'Payment Token Exists');
        $this->assertNotNull($this->smartpan_user->get('customer_id')->value, 'Customer ID Exists');
        
        $braintreeAction = new BraintreeActionController();
        
        $card = $braintreeAction->findBraintreeCreditCard(
            $this->smartpan_user->get('customer_id')->value, 
            $this->smartpan_user->get('payment_token')->value
        );
        
        $this->assertFalse($card, 'Card Does not Exist');
        $this->assertEqual($card->token, $this->smartpan_user->get('payment_token')->value, 'Card Token Exists');
        $this->assertEqual($card->customerId, $this->smartpan_user->get('customer_id')->value, 'Card Customer ID Exists');
    }
}

?>
