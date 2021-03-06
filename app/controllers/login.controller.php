<?php
/**
 * Login controller.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

use Respect\Validation\Validator; // as v;
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
		$req = $this->app->request();

		self::_debug(__METHOD__);

		$this->checkSamsLoginRedirect();

		if ($req->isPost()) {
			self::_debug([ __METHOD__, $this->post('username'), '****' ]);

			if ($this->auth->login($this->post('username'), $this->post('password'))) {
				// $this->app->flash('info', 'Your login was successful');

				$user= $this->auth->getUser();

				$this->recordVisit($user[ 'id' ]);

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
				$usernameValidator = Validator::alnum()
				->noWhitespace()
				->notEmpty();
				//->length(4,22);

				$usernameValidator->check($this->post('username'));

				try {
					Validator::alnum()
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

	protected function checkSamsLoginRedirect()
	{
		$req = $this->app->request();

		if ( ! $req->isPost()) {
			$redirect = ! $req->get( 'noredirect' );

			if ($redirect && self::config('sams_redirect') && self::config('sams_enable')) {
				$this->redirect('samslogin');
			}
		}
	}

	/** @route "samslogin"
	*/
	public function samsLogin()
	{
		$log = $this->app->getLog();
		$req = $this->app->request();

		$this->checkSamsAuthEnabled();

		$this->handleAlreadyLoggedIn();

		$group_id = $this->getValidGroupIdSams();

		$auth_regex = self::config( 'sams_auth_regex' );

		$sams = new IET_OU\SamsCAuth\SamsCAuth( $auth_regex ); // Was: SamsCAuth::AUTH_VISITOR_REGEX;
		$result = $sams->authenticate();

		self::_debug([ __METHOD__, 'authenticate', $result->isValid(), $auth_regex ]);

		if ($result->isValid()) {
			$log->info(sprintf('%s | [%s @ %s] | %s | %s', 'ACTION.SAMS_AUTH', $result->getIdentity()->login, $req->getIp(), $req->getPath(), null ));
		} else {
			$log->error(sprintf('%s | [%s @ %s] | %s | %s | %s', 'ERROR.SAMS_AUTH', null, $req->getIp(), null, $req->getUserAgent(), json_encode($result->getMessages()) ));
		}

		if ($sams->hasIdentity()) {
			$try_login = $sams->getIdentity()->login;

			self::_debug([ __METHOD__, 'hasIdentity', $try_login ]);

			$try_user = Model::factory('Users')->where('username', $try_login)->find_one();

			if ($try_user) {
				self::_debug([ __METHOD__, 'already exists' ]);
				$this->recordVisit($try_user);
				$this->loginRedirectSams($sams->getIdentity()->login);
			} else {
				// Use does not exist. Create. Login. Redirect ...

				$user = $this->createUserSams($sams->getIdentity(), $group_id);

				$this->recordVisit($user);
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

	/**
	 * @return object Users model object.
	 */
	protected function createUserSams($samsResult, $groupId = 1)
	{
		$log = $this->app->getLog();
		$req = $this->app->request();
		$admin_oucu_list = Application::config( 'admin_oucu_list' );

		$usr = Model::factory('Users')->create();

		$usr->username = $samsResult->login;
		// Was: $usr->name = $samsResult->name;  // Display name.
		$usr->password = Strong\Strong::getInstance()->getProvider()->hashPassword(Application::config( 'sams_password' ));
		$usr->email = $samsResult->email;
		$usr->ip_address = $this->app->request()->getIp();
		$usr->group_id = $groupId;
		$usr->active = true;
		$usr->isadmin = in_array( $samsResult->login, $admin_oucu_list );
		$usr->isgroup = in_array( $samsResult->login, $admin_oucu_list );
		$usr->isdemo = false;
		$usr->auth_type = 'ousams';

		try {
			$result = $usr->save();
			$user_id = $usr->id;

			$log->info(sprintf('%s | [%s @ %s] | %s | %s', 'ACTION.SAMS_CREATE', $usr->username, $usr->ip_address, $req->getPath(), json_encode([ 'user_agent' => $req->getUserAgent() ]) ));
			self::_debug([ __METHOD__, 'ok', $result ]);

			return $usr;
		} catch (\Exception $ex) {
			$log->error(sprintf('%s | [%s @ %s] | %s | %s', $ex->getMessage(), $usr->username, $usr->ip_address, $req->getPath(), json_encode([ 'user_agent' => $req->getUserAgent() ]) ));
			self::_debug([ __METHOD__, 'error', $ex->getMessage() ]);

			return null;
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
			$user = $_SESSION[ 'auth_user' ];
			// $log = $this->app->getLog();
			// $req = $this->app->request();

			// $log->info(sprintf('%s | [%s @ %s] | %s | %s', 'ACTION.SAMS_REDIRECT', $user->username, $req->getIp(), $req->getPath(), $req->getUserAgent() ));

			self::_debug([ __METHOD__, 'logged in. Redirect (SAMS)', $user ]);
			$this->redirect('me.home');
		}
	}

	protected static function getCurrentUrl()
	{
		return 'http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
		// return 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI');
	}

  /**
	 * Record each time a user signs in.
	 * @param object|int $user_id  User model object, or user ID.
	 */
	protected function recordVisit($user_id)
	{
		$usr = is_object($user_id) ? $user_id : Model::factory('Users')->where('id', $user_id)->find_one();

		$old_date = $usr->last_visit;
		$old_count = $usr->visit_count;

		$usr->last_visit = date( 'Y-m-d H:i:s' ); // No timezone.
		$usr->visit_count++;

		$result = $usr->save();

		self::_debug([ __METHOD__, $result, $usr->username, $old_date, $old_count, $usr ]);
		return $result;
	}
}
