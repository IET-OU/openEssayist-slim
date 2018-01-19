<?php
/**
 * OpenEssayist-slim.
 *
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

use Respect\Validation\Validator as v;

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
						$user['username']?:"anon",
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

}
