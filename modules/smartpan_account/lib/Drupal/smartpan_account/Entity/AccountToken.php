<?php

/**
 * Description of AccountToken
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account\Entity;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Component\Utility\Crypt;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

/**
* Defines the account token entity class.
*
* @EntityType(
*   id = "account_token",
*   label = @Translation("Account Token"),
*   controllers = {
*     "storage" = "Drupal\smartpan_account\AccountTokenStorageController"
*   },
*   base_table = "smartpan_account_token",
*   fieldable = FALSE,
*   translatable = FALSE,
*   render_cache = FALSE,
*   static_cache = TRUE,
* )
*/

class AccountToken extends Entity
{
    public $token;
    private $user;
    public $user_id;
    
    public $created;
    public $expiry;
    
    private $order_id;
    private $subscription_id;
    
    private function getToken()
    {
        $this->token = Crypt::hmacBase64(
            $this->user->getDrupalUser()->getUserName(), 
            Crypt::randomBytes(8) . 
            settings()->get('api_token_salt')
        );
        return $this->token;
    }
    
    public static function preCreate(EntityStorageControllerInterface $storage_controller, array &$values)
    {
        $this->user = UserController::getCurrentActiveSmartpanUser();
        $this->user_id = $this->user->id();
    }
    
    public function preSave(EntityStorageControllerInterface $storage_controller)
    {
        if(!isset($this->created)) {
            $this->created = REQUEST_TIME;
        }
        if(!isset($this->expiry)) {
            $this->expiry = $this->created + 7200;
        }
        if(!isset($this->token)) {
            $this->getToken();
        }
    }
    
    public function isActive()
    {
        return !empty($this->token) && ($this->expiry > time()) 
        && (!empty($this->subscription_id) && empty($this->order_id));
    }
}

?>
