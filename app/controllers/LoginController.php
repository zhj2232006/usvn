<?php
/**
 * Controller for login into default module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package default
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: LoginController.php 1188 2007-10-06 12:03:17Z crivis_s $
 */

class LoginController extends USVN_Controller
{
	public function loginAction()
	{
		// Check for an existing identity
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
			$this->_redirect('/');
		
		// Check the authentication
		if (!empty($_POST))
			$this->_doLogin();
	}

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_redirect('/');
	}

	protected function _doLogin()
	{
		$logger = new Zend_Log(new Zend_Log_Writer_Stream("/tmp/USVNlog"));

		// Get a reference to the Singleton instance of Zend_Auth
		$auth = Zend_Auth::getInstance();

		// Find the authentication adapter from the config file
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
		$authAdapterMethod = strtolower($config->authAdapterMethod);
		$authAdapterClass = 'USVN_Auth_Adapter_' . ucfirst($authAdapterMethod);
		if (!class_exists($authAdapterClass))
		{
			throw new USVN_Exception(T_('The authentication adapter method set in the config file is not valid.'));
		}
		// Retrieve auth-options, if any, from the config file
		$authOptions = null;
		if ($config->$authAdapterMethod && $config->$authAdapterMethod->options)
		{
			$authOptions = $config->$authAdapterMethod->options->toArray();
		}
		// Set up the authentication adapter
		$authAdapter = new $authAdapterClass($_POST['login'], $_POST['password'], $authOptions);
		
		// Attempt authentication, saving the result
		$result = $auth->authenticate($authAdapter);

		if (!$result->isValid())
		{
			$this->view->login = $_POST['login'];
			$this->view->messages = $result->getMessages();
			$this->render('errors');
			$this->render('login');
		}
		else
		{
			/**
			 * Workaround for LDAP. We need the identity to match the database,
			 * but LDAP identities can be in the following form:
			 * uid=username,ou=people,dc=foo,dc=com
			 * We need to simply keep username, as passed to the constructor method.
			 *
			 * Using in_array(..., get_class_methods()) instead of method_exists() or is_callable(),
			 * because none of them really check if the method is actually callable (ie. not protected/private).
			 * See comments @ http://us.php.net/manual/en/function.method-exists.php
			*/
			if (in_array("getIdentityUserName", get_class_methods($authAdapter)))
			{
				$identity = $auth->getStorage()->read();
				// Because USVN uses an array (...) when Zend uses a string
				if (!is_array($identity))
				{
					$identity = array();
				}
				$identity['username'] = $authAdapter->getIdentityUserName();
				$auth->getStorage()->write($identity);
			}
			$this->_redirect("/");
		}
	}
}
