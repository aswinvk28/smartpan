<?php

/**
 * Description of FormUserTokenManager
 *
 * @author aswinvk28
 */

namespace SmartPan\Bundles\CustomerBundle\Manager;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Component\Utility\Crypt;

class FormUserTokenManager
{
    private $parameterBag;
    private $smartpanUser;
    private $settings;
    private $csrfToken;
    private $keyvalueExpirable;
    
    private $expiry;
    
    public function __construct(CsrfTokenGenerator $csrfToken, $settings, $keyvalueExpirable)
    {
        $this->parameterBag = new ParameterBag(array());
        $this->smartpanUser = UserController::getCurrentActiveSmartpanUser();
        $this->csrfToken = $csrfToken;
        $this->settings = $settings;
        $this->keyvalueExpirable = $keyvalueExpirable;
    }
    
    public function setTokenData($form_token, array $data, $form_id = 'form')
    {
        $public_token = $this->generatePublicToken($form_token);
        $data['private'] = $private_token = $this->generatePrivateToken($public_token);
        $token = $this->generateKey($private_token, $form_id);
        $this->parameterBag->set($token, $data);
        return $this;
    }
    
    public function generateKey($private_token, $form_id)
    {
        return strtolower($form_id . '-' . $this->csrfToken->get($private_token . $this->smartpanUser->getDrupalUser()->getUsername()));
    }
    
    public function generatePublicToken($form_token)
    {
        return Crypt::hmacBase64($form_token, $this->settings->get('smartpan_form_token_salt'));;
    }
    
    public function generatePrivateToken($public_token)
    {
        return $this->csrfToken->get($public_token . $this->settings->get('drupal_form_token_salt'));
    }
    
    public function setFormExpiry($expiry)
    {
        $this->expiry = $expiry;
        return $this;
    }
    
    public function finaliseData()
    {
        $parameters = $this->parameterBag->all();
        
        foreach($parameters as $token => $parameter) {
            $this->keyvalueExpirable->get('form-token-salt')->setWithExpire($token, $parameter, $this->expiry);
        }
        
        $this->parameterBag->clear();
    }
    
    public function accessToken($public_token, $form_id = 'form')
    {
        $private_token = $this->generatePrivateToken($public_token);
        $this->data = $this->getTokenData($public_token, $form_id);
        
        $access = !empty($this->data) && is_array($this->data) && $this->data['private'] === $private_token;
        
        if(!$access) {
            drupal_set_message('Not Authorised', 'error');
            drupal_set_message(l('Go back to Upgrade Plan', \Drupal::url('smartpan_payment.change_plan')), 'status');
        }
        return $access;
    }
    
    public function getTokenData($public_token, $form_id = 'form')
    {
        $private_token = $this->generatePrivateToken($public_token);
        return $this->keyvalueExpirable->get('form-token-salt')
                ->get($this->generateKey($private_token, $form_id));
    }
    
    public function getData()
    {
        return $this->data;
    }
}

?>