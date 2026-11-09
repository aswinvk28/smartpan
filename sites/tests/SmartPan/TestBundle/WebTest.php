<?php

/**
 * Description of WebTest
 *
 * @author aswinvk28
 */

namespace Smartpan\Tests\TestBundle;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Session\AccountInterface;

class WebTest extends WebTestBase
{
    protected function smartpanLogin(AccountInterface $account)
    {
        if ($this->loggedInUser) {
            $this->drupalLogout();
        }

        $edit = array(
            'name' => $account->getUsername(),
            'pass' => $account->pass_raw
        );
        $this->drupalPostForm('login', $edit, t('Log in'));

        // @see WebTestBase::drupalUserIsLoggedIn()
        if (isset($this->session_id)) {
            $account->session_id = $this->session_id;
        }
        $pass = $this->assert($this->drupalUserIsLoggedIn($account), format_string('User %name successfully logged in.', array('%name' => $account->getUsername())), 'User login');
        if ($pass) {
            $this->loggedInUser = $account;
            $this->container->set('current_user', $account);
            // @todo Temporary workaround for not being able to use synchronized
            //   services in non dumped container.
            $this->container->get('access_subscriber')->setCurrentUser($account);
        }
    }
    
    protected function drupalCreateUserForSmartpan(array $permissions = array(), $name = NULL)
    {
        // Create a role with the given permission set, if any.
        $rid = FALSE;
        if ($permissions) {
            $rid = $this->drupalCreateRole($permissions);
            if (!$rid) {
                return FALSE;
            }
        }
        // Create a user assigned to that role.
        $edit = array();
        $edit['name']   = !empty($name) ? $name : $this->randomName();
        $edit['mail']   = $edit['name'];
        $edit['pass']   = user_password();
        $edit['status'] = 1;
        if ($rid) {
            $edit['roles'] = array($rid);
        }

        $account = entity_create('user', $edit);
        $account->save();

        $this->assertTrue($account->id(), String::format('User created with name %name and pass %pass', array('%name' => $edit['name'], '%pass' => $edit['pass'])), 'User login');
        if (!$account->id()) {
            return FALSE;
        }
        // Add the raw password so that we can log in as this user.
        $account->pass_raw = $edit['pass'];
        return $account;
    }
}
