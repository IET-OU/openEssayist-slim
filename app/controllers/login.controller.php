<?php
/**
 * OpenEssayist-slim.
 *
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

use Respect\Validation\Validator as v;
use IET_OU\SamsCAuth\SamsCAuth;

/**
 * Controller for the login operations
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class LoginController extends Controller {

	public function consent()
	{
		if ($this->app->request()->isPost())
		{
			if ($this->post('action') == 'Submit' &&
				$this->post('consent') == 'Accept')
			{
				$u = Model::factory('Users')->find_one($this->user['id']);
				if ($u)
				{
					// Dirty way of dealing with auth data
					$_SESSION['auth_user']['active'] = 1;
					// update user record in database
					$u->active = 1;
					$u->save();

					$this->redirect('me.home');

				}
				// @todo Should NEVER go there but, just in case, need to add proper exception
			}
			$this->redirect('logout');
		}
		else
		{
			$this->render('pages/consent');
		}
	}

	/**
	 * @route "login"
	 */
	public function index()
	{
		self::_debug(__METHOD__);

		if ($this->app->request()->isPost()) {
			self::_debug([ __METHOD__, $this->post('username'), '****' ]);

			if ($this->auth->login($this->post('username'), $this->post('password'))) {
				// $this->app->flash('info', 'Your login was successful');

				$user= $this->auth->getUser();
				$req = $this->app->request();

				$useragent = $req->headers('USER_AGENT') ?: "";

				$tmpl = '%action% | [%user% @ %IP%] | %message%';
				$message = str_replace(array(
						'%action%',
						'%user%',
						'%IP%',
						'%message%'
				), array(
						TutorController::ACTION_LOGIN,
						$user['username'] ?: "anon",
						$req->getIp(),
						json_encode(array('user_agent'=> $useragent))
				), $tmpl);

				$log = $this->app->getLog();
				$log->info($message);

				self::_debug([ __METHOD__, 'ok', 'Redirecting...', $message ]);

				$this->redirect('me.home');
			}
			else {
				self::_debug([ __METHOD__, 'error', 'Username or password incorrect', $this->post('username') ]);

				$this->app->flashNow('error', "Username or password is incorrect. Check your details again.");
			}
		}
		$this->render('pages/login');


		/*if ($this->app->request()->isPost()) {
			try {
				$usernameValidator = v::alnum()
				->noWhitespace()
				->notEmpty();
				//->length(4,22);

				$usernameValidator->check($this->post('username'));

				try {
					v::alnum()
					->notEmpty()
					//->length(3,11)
					->check($this->post('password'));

					if ($this->auth->login($this->post('username'), $this->post('password'))) {
						$this->app->flash('info', 'Your login was successful');
						$this->redirect('home');
					}
					else
						$this->app->flashNow('error', "Username or password is incorrect. Check your details again.");
				} catch (\InvalidArgumentException $e) {
					$this->app->flashNow('error', $e->setName('Password')->getMainMessage());
				}
			} catch (\InvalidArgumentException $e) {
				$this->app->flashNow('error', $e->setName('Username')->getMainMessage());
			}
		}
		$this->render('pages/login');*/
	}

	/**
	 * @route "logout"
	 */
	public function logout()
	{
		$this->app->flash('info', 'Come back sometime soon');
		$this->auth->logout(true);
		$this->redirect('home');
	}

	/** @route "samslogin"
	*/
	public function samsLogin()
	{
		$this->checkSamsAuthEnabled();

		$this->handleAlreadyLoggedIn();

		$group_id = $this->getValidGroupIdSams();

		$sams = new IET_OU\SamsCAuth\SamsCAuth();
		$result = $sams->authenticate();

		if ($sams->hasIdentity()) {
			$try_login = $sams->getIdentity()->login;

			self::_debug([ __METHOD__, 'hasIdentity', $try_login ]);

			$try_user = Model::factory('Users')->where('username', $try_login)->find_one();

			if ($try_user) {
				self::_debug([ __METHOD__, 'already exists' ]);
				$this->loginRedirectSams($sams->getIdentity()->login);
			} else {
				// Use does not exist. Create. Login. Redirect ...

				$this->createUserSams($sams->getIdentity(), $group_id);

				$this->loginRedirectSams($sams->getIdentity()->login);
			}
		} else {
			self::_debug([ __METHOD__, 'NO identity. Redirecting.' ]);
			$sams->redirectLogin(self::getCurrentUrl());
		}
	}

	protected function loginRedirectSams($username)
	{
		if ($this->auth->login($username, Application::config( 'sams_password' ))) {
			self::_debug([ __METHOD__, 'Login OK. Redirecting.' ]);
			$this->redirect('me.home');
		} else {
			$this->app->error(new \Exception('Problem logging in (SAMS auth).'));
		}
	}

	protected function createUserSams($samsResult, $groupId = 1)
	{
		$log = $this->app->getLog();
		$req = $this->app->request();
		$admin_oucu_list = Application::config( 'admin_oucu_list' );

		$usr = Model::factory('Users')->create();

		$usr->username = $samsResult->login;
		$usr->name = $samsResult->name;  // Display name.
		$usr->password = Strong\Strong::getInstance()->getProvider()->hashPassword(Application::config( 'sams_password' ));
		$usr->email = $samsResult->email;
		$usr->ip_address = $this->app->request()->getIp();
		$usr->group_id = $groupId;
		$usr->active = true;
		$usr->isadmin = in_array( $samsResult->login, $admin_oucu_list );
		$usr->isgroup = false;
		$usr->isdemo = false;
		$usr->auth_type = 'ousams';

		try {
			$result = $usr->save();
			$user_id = $usr->id;

			$log->info(sprintf('%s | [%s @ %s] | %s | %s', 'ACTION.SAMS_CREATE', $usr->username, $usr->ip_address, $req->getPath(), json_encode([ 'user_agent' => $req->getUserAgent() ]) ));
			self::_debug([ __METHOD__, 'ok', $result ]);
		} catch (\Exception $ex) {
			$log->error(sprintf('%s | [%s @ %s] | %s | %s', $ex->getMessage(), $usr->username, $usr->ip_address, $req->getPath(), json_encode([ 'user_agent' => $req->getUserAgent() ]) ));
			self::_debug([ __METHOD__, 'error', $ex->getMessage() ]);
		}
	}

	protected function getValidGroupIdSams()
	{
		$group_id = $this->app->request()->get('group');

		$group_id = null === $group_id ? Application::config('sams_group_id') : $group_id;

		if (is_numeric($group_id)) {
			$group = Model::factory('Group')->where('id', $group_id)->find_one();
		} else {
			$group = Model::factory('Group')->where('name', $group_id)->find_one();
		}

		self::_debug([ __METHOD__, $group_id, $group ]);

		if (! $group) {
			$this->app->error(new \Exception("Error. Invalid group ID: $group_id"));
		}
		return $group->id; // get('id');
	}

	protected function checkSamsAuthEnabled()
	{
		if (! Application::config('sams_enable')) {
			self::_debug([ __METHOD__, 'sams auth disabled.' ]);
			$this->app->notFound();
		}
	}

	protected function handleAlreadyLoggedIn()
	{
		$is_logged_in = isset($_SESSION[ 'auth_user' ]);
		if ($is_logged_in) {
			self::_debug([ __METHOD__, 'logged in', $_SESSION[ 'auth_user' ] ]);
			$this->redirect('me.home');
		}
	}

	protected static function getCurrentUrl()
	{
		return 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI');
	}


}
