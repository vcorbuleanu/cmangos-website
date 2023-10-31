<?
    class account_controller extends controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('account');
			$this->data['authenticated'] = $this->authenticated();
        }
        
        public function create($parameters) {
			if ($this->data['authenticated'])
				header('Location: /account/manage');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Create';
			$this->data['banner'] = '/public/img/banners/account.webp';

			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('username', 'email', 'password', 'password_confirm'), '');
			$this->data['errors'] = array_fill_keys(array('username', 'email', 'password', 'password_confirm'), false);

			if ($this->input->method() === 'post') {
				$result = $this->process_create($this->input->post('username'), $this->input->post('password'), $this->input->post('password_confirm'), $this->input->post('email'));
				if ($result == 0) {
					$this->data['username'] = $this->input->post('username');

					$this->load->view('header', $this->data);
					if (REQUIRE_ACCOUNT_ACTIVATION) {
						$this->load->view('account/create/success', $this->data);
					} else {
						$this->load->view('account/activate/success', $this->data);
					}
					$this->load->view('footer', $this->data);
				} else if ($result == 1) {
					$this->data['values'] = $this->input->post();

					$this->load->view('header', $this->data);
					$this->load->view('account/create/index', $this->data);
					$this->load->view('footer', $this->data);
				} else if ($result == 2) {
					$this->data['username'] = $this->input->post('username');

					$this->load->view('header', $this->data);
					$this->load->view('account/create/fail', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->load->view('header', $this->data);
				$this->load->view('account/create/index', $this->data);
				$this->load->view('footer', $this->data);
			}
		}

		public function login($parameters) {
			if ($this->data['authenticated'])
				header('Location: /account/manage');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Login';
			$this->data['banner'] = '/public/img/banners/account.webp';

			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('username', 'password', 'code'), '');
			$this->data['errors'] = array_fill_keys(array('username', 'password', 'code', 'mfa'), false);

			if ($this->input->method() === 'post') {
				$result = $this->process_login($this->input->post('username'), $this->input->post('password'), $this->input->post('code'));
				if ($result) {
					list($id, $username, $ip) = $this->model->get_jtw_data($this->input->post('username'));
					$_SESSION['id'] = $id;
					$_SESSION['username'] = $username;

					$this->model->set_user_address($username, $_SERVER['REMOTE_ADDR']);
					$jwt_token = jwt::generate_token(array($id, $username, $ip));
					setcookie(COOKIE_NAME, $jwt_token, time() + COOKIE_TIMEOUT, COOKIE_PATH);

					header('Location: /account/manage');
				} else {
					$this->data['values'] = $this->input->post();
					$this->load->view('header', $this->data);
					$this->load->view('account/login', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->load->view('header', $this->data);
				$this->load->view('account/login', $this->data);
				$this->load->view('footer', $this->data);
			}
		}

		public function logout($parameters) {
			if ($this->data['authenticated']) {
				setcookie(COOKIE_NAME, '', time() - COOKIE_TIMEOUT, COOKIE_PATH);
				session_destroy();
			}
			header('Location: /account/login');
		}

		public function manage($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Manage';
			$this->data['banner'] = '/public/img/banners/account.webp';
			

			$realms = $this->model->get_realmlist();
            if (count($realms) > 0) {
                foreach ($realms as &$realm) {
                    $keep = array('id', 'name', 'version', 'characters');
                    $realm['version'] = $this->get_version($realm['builds']);
					$realm['characters'] = $this->model->get_user_characters($_SESSION['username'], $realm['id']);
					foreach ($realm['characters'] as &$character) {
                        $character['faction'] = $this->get_faction($character['race']);
                        $character['race_name'] = $this->get_race_name($character['race']);
                        $character['class_name'] = $this->get_class_name($character['class']);
						$character['money'] = $this->get_money($character['money']);
                        $character['playtime'] = $this->get_uptime($character['totaltime'], 0, true);
                    }
                    unset($character);
                    $realm = array_intersect_key($realm, array_flip($keep));
                }
                unset($realm);
            }

			list($secret, $tokens) = $this->model->get_mfa($_SESSION['username']);
			$this->data['information'] = $this->model->get_user_information($_SESSION['username']);
			$this->data['information']['mfa'] = isset($secret);
			$this->data['information']['avatar'] = $this->data['information']['avatar'] ? $this->data['information']['avatar'] : DEFAULT_AVATAR;
			$this->data['information']['signature'] = $this->data['information']['signature'] ? $this->data['information']['signature'] : DEFAULT_SIGNATURE;
			if ($this->data['information']['os'] && $this->data['information']['platform'] && $this->data['information']['locale'])
				$this->data['information']['platform'] = $this->data['information']['os'] . ' ' . $this->data['information']['platform'] . ' - ' . $this->data['information']['locale'];
			else $this->data['information']['platform'] = 'N/A';
			$keep = array('avatar', 'signature', 'nickname', 'username', 'email', 'ip', 'platform', 'gmlevel', 'mfa');
			$this->data['information'] = array_intersect_key($this->data['information'], array_flip($keep));

			$this->data['realms'] = $realms;
			$this->load->view('header', $this->data);
			$this->load->view('account/manage', $this->data);
			$this->load->view('footer', $this->data);
		}

		public function activate($parameters) {
			if ($this->data['authenticated'])
				header('Location: /account/manage');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Activate';
			$this->data['banner'] = '/public/img/banners/default.webp';

			if (!isset($parameters['token']) || strlen($parameters['token']) !== 32) {
				$this->load->view('header', $this->data);
				$this->load->view('account/activate/fail', $this->data);
				$this->load->view('footer', $this->data);
			} else {
				list($result, $username) = $this->process_activate($parameters['token']);
				if ($result == 0) {
					$this->data['username'] = $username;

					$this->load->view('header', $this->data);
					$this->load->view('account/activate/success', $this->data);
					$this->load->view('footer', $this->data);
				} else {
					$this->load->view('header', $this->data);
					$this->load->view('account/activate/fail', $this->data);
					$this->load->view('footer', $this->data);
				}
			}
		}

		public function recover($parameters) {
			if ($this->data['authenticated'])
				header('Location: /account/manage');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Recover';
			$this->data['banner'] = '/public/img/banners/account.webp';

			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('email'), '');
			$this->data['errors'] = array_fill_keys(array('email'), false);

			if ($this->input->method() === 'post') {
				$result = $this->process_recover($this->input->post('email'));
				if ($result == 0) {
					$this->load->view('header', $this->data);
					$this->load->view('account/recover/success', $this->data);
					$this->load->view('footer', $this->data);
				} else if ($result == 1) {
					$this->data['values'] = $this->input->post();

					$this->load->view('header', $this->data);
					$this->load->view('account/recover/index', $this->data);
					$this->load->view('footer', $this->data);
				} else if ($result == 2) {
					$this->data['email'] = $this->input->post('email');

					$this->load->view('header', $this->data);
					$this->load->view('account/recover/fail', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->load->view('header', $this->data);
				$this->load->view('account/recover/index', $this->data);
				$this->load->view('footer', $this->data);
			}
		}

		public function reset($parameters) {
			if ($this->data['authenticated'])
				header('Location: /account/manage');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Reset';
			$this->data['banner'] = '/public/img/banners/account.webp';

			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('password', 'password_confirm', 'code'), '');
			$this->data['errors'] = array_fill_keys(array('password', 'password_confirm', 'code', 'mfa'), false);
			
			if ($this->input->method() === 'post') {
				list($result, $username) = $this->process_reset($this->input->post('password'), $this->input->post('password_confirm'), $parameters['token'], $this->input->post('code'));
				if ($result == 0) {
					$this->data['username'] = $username;

					$this->load->view('header', $this->data);
					$this->load->view('account/reset/success', $this->data);
					$this->load->view('footer', $this->data);
				} else if ($result == 1) {
					$this->data['values'] = $this->input->post();

					$this->load->view('header', $this->data);
					$this->load->view('account/reset/index', $this->data);
					$this->load->view('footer', $this->data);
				} else if ($result == 2) {
					$this->load->view('header', $this->data);
					$this->load->view('account/reset/fail', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				if (!isset($parameters['token']) || strlen($parameters['token']) !== 32) {
					$this->load->view('header', $this->data);
					$this->load->view('account/reset/fail', $this->data);
					$this->load->view('footer', $this->data);
				} else {
					$this->load->view('header', $this->data);
					$this->load->view('account/reset/index', $this->data);
					$this->load->view('footer', $this->data);
				}
			}
		}

		public function avatar($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Change Avatar';
			$this->data['banner'] = '/public/img/banners/account.webp';

			$this->data['information'] = $this->model->get_user_information($_SESSION['username']);

			if ($this->input->method() === 'post') {
				$result = $this->process_avatar($this->input->post('avatar'));
				if ($result)
					header('Location: /account/manage');
				else {
					$this->load->view('header', $this->data);
					$this->load->view('account/avatar', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->load->view('header', $this->data);
				$this->load->view('account/avatar', $this->data);
				$this->load->view('footer', $this->data);
			}
		}

		private function process_avatar($avatar) {
			if (!$avatar || !in_array($avatar, AVATAR_LIST))
				return false;
			if (!$this->model->set_user_avatar($_SESSION['username'], $avatar))
				return false;
			return true;
		}

		public function signature($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Change Signature';
			$this->data['banner'] = '/public/img/banners/account.webp';

			$this->data['information'] = $this->model->get_user_information($_SESSION['username']);

			if ($this->input->method() === 'post') {
				$result = $this->process_signature($this->input->post('signature'));
				if ($result)
					header('Location: /account/manage');
				else {
					$this->load->view('header', $this->data);
					$this->load->view('account/signature', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->load->view('header', $this->data);
				$this->load->view('account/signature', $this->data);
				$this->load->view('footer', $this->data);
			}
		}

		private function process_signature($signature) {
			if (!$signature || !in_array($signature, SIGNATURE_LIST))
				return false;
			if (!$this->model->set_user_signature($_SESSION['username'], $signature))
				return false;
			return true;
		}

		public function logs($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Access Logs';
			$this->data['banner'] = '/public/img/banners/account.webp';
			
			$this->data['logs'] = $this->model->get_user_access_logs($_SESSION['username'], ACCOUNT_ACCESS_LOGS_DAYS);

			$this->load->view('header', $this->data);
			$this->load->view('account/logs', $this->data);
			$this->load->view('footer', $this->data);
		}

		public function nickname($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Change Nickname';
			$this->data['banner'] = '/public/img/banners/account.webp';

			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('nickname', 'password', 'code'), '');
			$this->data['errors'] = array_fill_keys(array('nickname', 'password', 'code', 'mfa'), false);

			if ($this->input->method() === 'post') {
				$result = $this->process_nickname($this->input->post('nickname'), $this->input->post('password'), $this->input->post('code'));
				if ($result) {
					$this->model->set_user_nickname($_SESSION['username'], $this->input->post('nickname'));
					header('Location: /account/manage');
				} else {
					$this->data['values'] = $this->input->post();
					$this->load->view('header', $this->data);
					$this->load->view('account/nickname', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->load->view('header', $this->data);
				$this->load->view('account/nickname', $this->data);
				$this->load->view('footer', $this->data);
			}
		}

		public function password($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

				$this->data['title'] = 'Account';
				$this->data['subtitle'] = 'Change Password';
				$this->data['banner'] = '/public/img/banners/account.webp';

				// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
				$this->data['values'] = array_fill_keys(array('password', 'password_new', 'password_new_confirm', 'code'), '');
				$this->data['errors'] = array_fill_keys(array('password', 'password_new', 'password_new_confirm', 'code', 'mfa'), false);

				if ($this->input->method() === 'post') {
					$result = $this->process_password($this->input->post('password'), $this->input->post('password_new'), $this->input->post('password_new_confirm'), $this->input->post('code'));
					if ($result == 0) {
						setcookie(COOKIE_NAME, '', time() - COOKIE_TIMEOUT, COOKIE_PATH);
						unset($_SESSION['id']);
						unset($_SESSION['username']);
						$this->data['authenticated'] = false;

						$this->load->view('header', $this->data);
						$this->load->view('account/password/success', $this->data);
						$this->load->view('footer', $this->data);
					} else if ($result == 1) {
						$this->data['values'] = $this->input->post();

						$this->load->view('header', $this->data);
						$this->load->view('account/password/index', $this->data);
						$this->load->view('footer', $this->data);
					} else if ($result == 2) {
						$this->load->view('header', $this->data);
						$this->load->view('account/password/fail', $this->data);
						$this->load->view('footer', $this->data);
					}
				} else {
					$this->load->view('header', $this->data);
					$this->load->view('account/password/index', $this->data);
					$this->load->view('footer', $this->data);
				}
		}

		public function mfa($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['banner'] = '/public/img/banners/default.webp';
			list($secret, $tokens) = $this->model->get_mfa($_SESSION['username']);

			
			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('password', 'code', 'secret', 'qrcode'), '');
			$this->data['errors'] = array_fill_keys(array('password', 'code'), false);
			
			if (!isset($secret) || strlen($secret = trim($secret)) == 0) {
				$this->data['subtitle'] = 'Enable MFA';
				if ($this->input->method() === 'post') {
					$result = $this->process_mfa($this->input->post('password'), $this->input->post('secret'), $this->input->post('code'), 1);
					if ($result) {
						$this->data['username'] = $_SESSION['username'];
            			$this->data['recovery_tokens'] = $this->model->get_mfa($_SESSION['username'])[1];

						$this->load->view('header', $this->data);
						$this->load->view('account/mfa/enable/success', $this->data);
						$this->load->view('footer', $this->data);
					} else {
						$this->data['values'] = $this->input->post();
						$this->data['values']['qrcode'] = 'otpauth://totp/' . WEBSITE_TITLE . ':' . $_SESSION['username'] . '?secret=' . $this->data['values']['secret'] . '&issuer=' . WEBSITE_TITLE . '&digits=' . OTP_TOKEN_LENGTH . '&period=' . OTP_TOKEN_TIMESTEP;
					
						$this->load->view('header', $this->data);
						$this->load->view('account/mfa/enable/index', $this->data);
						$this->load->view('footer', $this->data);
					}
				} else {
					$this->data['values']['secret'] = otp::get_secret();
					$this->data['values']['qrcode'] = 'otpauth://totp/' . WEBSITE_TITLE . ':' . $_SESSION['username'] . '?secret=' . $this->data['values']['secret'] . '&issuer=' . WEBSITE_TITLE . '&digits=' . OTP_TOKEN_LENGTH . '&period=' . OTP_TOKEN_TIMESTEP;
					$this->load->view('header', $this->data);
					$this->load->view('account/mfa/enable/index', $this->data);
					$this->load->view('footer', $this->data);
				}
			} else {
				$this->data['subtitle'] = 'Disable MFA';
				if ($this->input->method() === 'post') {
					$result = $this->process_mfa($this->input->post('password'), null, $this->input->post('code'), 0);
					if ($result) {
						$this->data['username'] = $_SESSION['username'];

						$this->load->view('header', $this->data);
						$this->load->view('account/mfa/disable/success', $this->data);
						$this->load->view('footer', $this->data);
					} else {
						$this->data['values'] = $this->input->post();

						$this->load->view('header', $this->data);
						$this->load->view('account/mfa/disable/index', $this->data);
						$this->load->view('footer', $this->data);
					}
				} else {
					$this->load->view('header', $this->data);
					$this->load->view('account/mfa/disable/index', $this->data);
					$this->load->view('footer', $this->data);
				}
			}
		}

		public function validate($parameters) {
			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Validate Email';
			$this->data['banner'] = '/public/img/banners/default.webp';

			$this->load->view('header', $this->data);
			if (!isset($parameters['token']) || strlen($parameters['token']) !== 32) {
				$this->load->view('account/validate/fail', $this->data);
			} else {
				list($result, $username, $step) = $this->process_validate($parameters['token']);
				if ($result == 0) {
					$this->data['username'] = $username;
					$this->data['step'] = $step;
					if ($step == 1) {
						$this->load->view('account/validate/index', $this->data);
					} else {
						$this->load->view('account/email/success', $this->data);
					}
				} else {
					$this->load->view('account/validate/fail', $this->data);
				}
			}
			$this->load->view('footer', $this->data);
		}

		private function process_validate($token) {
			list($id, $username, $type, $token, $expiration, $flags) = $this->model->get_validation($token);
			if (!isset($id) || !isset($username) || !isset($type))
				return array(1, null);

			if ($type == 2) {
				$_token = random_string(32);
				$timestamp = floor(microtime(true) + 900);
				if(!$this->model->add_validation_token($id, $_token, 3, $timestamp, $flags))
					return array(1, null);

				$validate_link = WEBSITE_BASE_URL . '/account/validate/' . $_token;
				$message =	'<p>You\'re almost finished changing your email, <b>' . $username . '</b>!</p>' .
							'<p>There\'s just one more tiny step.</p>' .
							'<p>Uhm... Access the link down below (valid for 15 minutes) to prove your identity:</p>' .
							'<a href=\'' . $validate_link . '\'>' . $validate_link . '</a>';
				if (!mailer::send($flags, WEBSITE_TITLE . ': Email Validation - Step 2', $message)) {
					$this->model->remove_validation_token($id, 3);
					return array(1, null);
				}

				$this->model->remove_validation_token($id, $type);
				return array(0, $username, 1);
			} else if ($type == 3) {
				$this->model->set_user_email($id, $flags);
				$this->model->remove_validation_token($id, $type);
				return array(0, $username, 2);
			}
			
		}

		public function email($parameters) {
			if (!$this->data['authenticated'])
				header('Location: /account/login');

			$this->data['title'] = 'Account';
			$this->data['subtitle'] = 'Change Email';
			$this->data['banner'] = '/public/img/banners/account.webp';

			// This is to prevent flooding the logs with PHP_NOTICE for unset variables.
			$this->data['values'] = array_fill_keys(array('email', 'password', 'code'), '');
			$this->data['errors'] = array_fill_keys(array('email', 'password', 'code', 'mfa'), false);
			
			$this->load->view('header', $this->data);
			if ($this->input->method() === 'post') {
			 	$result = $this->process_email($this->input->post('email'), $this->input->post('password'), $this->input->post('code'));
				if ($result == 0) {
					$this->data['username'] = $_SESSION['username'];

					if (REQUIRE_ACCOUNT_ACTIVATION) {
						$this->data['step'] = 0;
						$this->load->view('account/validate/index', $this->data);
					} else {
						$this->load->view('account/email/success', $this->data);
					}
				} else if ($result == 1) {
					$this->data['values'] = $this->input->post();
					$this->load->view('account/email/index', $this->data);
				} else if ($result == 2) {
					$this->data['username'] = $_SESSION['username'];
					$this->load->view('account/email/fail', $this->data);
				}
			} else {
				$this->load->view('account/email/index', $this->data);
			}
			$this->load->view('footer', $this->data);
		}

		private function process_email($email, $password, $code = null) {		
			if (!$email || strlen($email = trim($email)) == 0) {
				$this->add_error('email', 'Email not entered.');
			} else if (!preg_match('/^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{1,})*\.([a-z]{2,}){1}$/', $email) || strlen($email) > 32) {
				$this->add_error('email', 'Email is invalid.');
			}

			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}

			if ($this->errors() > 0)
				return 1;
			
			list($id, $_username) = $this->model->check_email($email);
			if (isset($id) || isset($_username))
				$this->add_error('email', 'Email not available.');
			
			if ($this->errors() > 0)
				return 1;

			list($salt, $verifier, $secret, $tokens, $locked, $banned) = $this->model->get_login($_SESSION['username']);

			if (!srp6::check_data($_SESSION['username'], $password, $salt, $verifier)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return 1;

			if (isset($secret) && strlen($secret) !== 0) {
				if (!isset($code))
					$this->add_error('mfa', true);
				else if ((!otp::check_otp($code, $secret) && !(isset($tokens) && in_array($code, $tokens)))) {
					$this->add_error('mfa', true);
					$this->add_error('code', 'Code is invalid.');
				}
			}
			
			if ($this->errors() > 0)
				return 1;

			if (REQUIRE_ACCOUNT_ACTIVATION) {
				$token = random_string(32);
				$timestamp = floor(microtime(true) + 900);
				if(!$this->model->add_validation_token($_SESSION['id'], $token, 2, $timestamp, $email))
					return 2;

				$activate_link = WEBSITE_BASE_URL . '/account/validate/' . $token;
				$message =	'<p>You\'re almost finished changing your email, <b>' . $username . '</b>!</p>' .
							'<p>There\'s just two more tiny steps.</p>' .
							'<p>Uhm... Access the link down below (valid for 15 minutes) to prove your identity:</p>' .
							'<a href=\'' . $activate_link . '\'>' . $activate_link . '</a>';
				if (!mailer::send($email, WEBSITE_TITLE . ': Email Validation - Step 1', $message)) {
					$this->model->remove_validation_token($_SESSION['id'], 2);
					return 2;
				}
			} else {
				if (!$this->model->set_user_email($_SESSION['username'], $email))
					return 2;
			}
			
			return 0;
		}

		private function process_mfa($password, $secret = null, $code = null, $action = 1) {
			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			list($salt, $verifier, $_secret, $_tokens, $locked, $banned) = $this->model->get_login($_SESSION['username']);

			if (!srp6::check_data($_SESSION['username'], $password, $salt, $verifier)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			if ($action && isset($secret) && strlen($secret) !== 0)
				if (!isset($code) || !otp::check_otp($code, $secret))
					$this->add_error('code', 'Code is invalid.');

			if (!$action && isset($_secret) && strlen($_secret) !== 0)
				if (!isset($code) || (!otp::check_otp($code, $_secret) && !(isset($_tokens) && in_array($code, $_tokens))))
					$this->add_error('code', 'Code is invalid.');
			
			if ($this->errors() > 0)
				return false;

			if ($action) {
				$tokens = otp::get_recovery_tokens();
				if (!$this->model->add_mfa($_SESSION['username'], $secret, $tokens))
					return false;
			} else {
				if (!$this->model->remove_mfa($_SESSION['username']))
					return false;
			}
			
			return true;
		}

		private function process_create($username, $password, $password_confirm, $email) {
			if (!$username || strlen($username = trim($username)) == 0) {
				$this->add_error('username', 'Username not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9_.]{4,16}$/', $username)) {
				$this->add_error('username', 'Username is invalid.');
			} else if ($this->model->check_username($username)) {
				$this->add_error('username', 'Username not available.');
			}
			
			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if (!$password_confirm || strlen($password_confirm = trim($password_confirm)) == 0) {
				$this->add_error('password_confirm', 'Confirm Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password_confirm)) {
				$this->add_error('password_confirm', 'Confirm Password is invalid.');
			} else if ($password != $password_confirm) {
				$this->add_error('password_confirm', 'Passwords do not match.');
			}
			
			if (!$email || strlen($email = trim($email)) == 0) {
				$this->add_error('email', 'Email not entered.');
			} else if (!preg_match('/^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{1,})*\.([a-z]{2,}){1}$/', $email) || strlen($email) > 32) {
				$this->add_error('email', 'Email is invalid.');
			}
			
			if ($this->errors() > 0)
				return 1;
			
			list($id, $_username) = $this->model->check_email($email);
			if (isset($id) || isset($_username))
				$this->add_error('email', 'Email not available.');
			
			if ($this->errors() > 0)
				return 1;

			list($salt, $verifier) = srp6::get_data($username, $password);
			$token = random_string(32);
			$timestamp = floor(microtime(true) + 900);
			if (!$this->model->add_user($username, $email, $salt, $verifier, $token, $timestamp))
				return 2;

			if (REQUIRE_ACCOUNT_ACTIVATION) {
				$activate_link = WEBSITE_BASE_URL . '/account/activate/' . $token;
				$message =	'<p>You\'re almost a member of our community, <b>' . $username . '</b>!</p>' .
							'<p>There\'s just one more tiny step.</p>' .
							'<p>I need you to prove you are who you say you are. No funny business!</p>' .
							'<p>Uhm... Access the link down below (valid for 15 minutes) to prove your identity:</p>' .
							'<a href=\'' . $activate_link . '\'>' . $activate_link . '</a>';
				if (!mailer::send($email, WEBSITE_TITLE . ': Account Activation', $message)) {
					// This is in case the account was created, but the activation email was not sent.
					// So we mark the token as expired, and our cleanup removes the account as well, so the user can retry.
					$this->model->expire_activation_token($token);
					return 2;
				}
			}
			
			return 0;
		}

		private function process_login($username, $password, $code = null) {
			if (!$username || strlen($username = trim($username)) == 0) {
				$this->add_error('username', 'Username not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9_.]{4,16}$/', $username)) {
				$this->add_error('username', 'Username is invalid.');
			}

			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			list($salt, $verifier, $secret, $tokens, $locked, $banned) = $this->model->get_login($username);
			if (!isset($salt) || !isset($verifier) || (isset($locked) && $locked) || (isset($banned) && $banned > 0)) {
				$this->add_error('username', 'Username is invalid, inactive, or banned.');
			}
			
			if ($this->errors() > 0)
				return false;

			if (!srp6::check_data($username, $password, $salt, $verifier)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			/* if (isset($secret) && strlen($secret) !== 0) {
				if (!isset($code))
					$this->add_error('mfa', true);
				else {
					$_timestamp = otp::timestamp();
					$_secret = otp::base32_to_binary($secret);
					
					if (otp::get_otp($_secret, $_timestamp) !== $code && !(isset($tokens) && in_array($code, $tokens))) {
						$this->add_error('mfa', true);
						$this->add_error('code', 'Code is invalid.');
					}
				}
			} */

			if (isset($secret) && strlen($secret) !== 0) {
				if (!isset($code))
					$this->add_error('mfa', true);
				else if ((!otp::check_otp($code, $secret) && !(isset($tokens) && in_array($code, $tokens)))) {
					$this->add_error('mfa', true);
					$this->add_error('code', 'Code is invalid.');
				}
			}
			
			if ($this->errors() > 0)
				return false;

			return true;
		}

		private function process_activate($token) {
			list($id, $username) = $this->model->get_activation($token);
			$timestamp = floor(microtime(true));
			if (!isset($id) || !isset($username)) {
				return array(1, null);
			}
			
			if (!$this->model->activate_user($id))
				return array(1, null);

			$this->model->remove_activation_token($id);

			return array(0, $username);
		}

		private function process_recover($email) {
			if (!$email || strlen($email = trim($email)) == 0) {
				$this->add_error('email', 'Email not entered.');
			} else if (!preg_match('/^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{1,})*\.([a-z]{2,}){1}$/', $email) || strlen($email) > 32) {
				$this->add_error('email', 'Email is invalid.');
			}

			list($id, $username) = $this->model->check_email($email);
			if (!isset($id) || !isset($username)) {
				$this->add_error('email', 'Email is invalid.');
			}

			if ($this->errors() > 0)
				return 1;

			$token = random_string(32);
			$timestamp = floor(microtime(true) + 3600);
			if (!$this->model->add_recovery_token($id, $token, $timestamp))
				return 2;

			$reset_link = WEBSITE_BASE_URL . '/account/reset/' . $token;
			$message =	'<p>I didn\'t think you\'d go this far, keeping credentials safe, <b>' . $username . '</b>!</p>' .
						'<p>Now you have no idea how to access your account.</p>' .
						'<p>Ugh... Fine, access the link down below (valid for 1 hour) to reset your password:</p>' .
						'<a href=\'' . $reset_link . '\'>' . $reset_link . '</a>';
			if (!mailer::send($email, WEBSITE_TITLE . ': Account Recovery', $message))
				return 2;
			return 0;
		}

		private function process_reset($password, $password_confirm, $token, $code = null) {
			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if (!$password_confirm || strlen($password_confirm = trim($password_confirm)) == 0) {
				$this->add_error('password_confirm', 'Confirm Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password_confirm)) {
				$this->add_error('password_confirm', 'Confirm Password is invalid.');
			} else if ($password != $password_confirm) {
				$this->add_error('password_confirm', 'Passwords do not match.');
			}

			if ($this->errors() > 0)
				return array(1, null);

			list($id, $username, $expiration, $secret, $tokens) = $this->model->get_recovery($token);
			$timestamp = floor(microtime(true));
			if (!isset($id) || !isset($username) || !isset($expiration) || $expiration <= $timestamp) {
				return array(2, null);
			}
			
			if ($this->errors() > 0)
				return array(1, null);

			if (isset($secret) && strlen($secret) !== 0) {
				if (!isset($code))
					$this->add_error('mfa', true);
				else if ((!otp::check_otp($code, $secret) && !(isset($tokens) && in_array($code, $tokens)))) {
					$this->add_error('mfa', true);
					$this->add_error('code', 'Code is invalid.');
				}
			}

			if ($this->errors() > 0)
				return array(1, null);

			list($salt, $verifier) = srp6::get_data($username, $password);
			if (!$this->model->update_password($id, $salt, $verifier))
				return array(2, null);

			$this->model->remove_recovery_token($id);

			return array(0, $username);
		}

		private function process_nickname($nickname, $password, $code = null) {
			if (!$nickname || strlen($nickname = trim($nickname)) == 0) {
				$this->add_error('nickname', 'Nickname not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9_.]{4,16}$/', $nickname)) {
				$this->add_error('nickname', 'Nickname is invalid.');
			} else if ($this->model->check_username($nickname)) {
				$this->add_error('nickname', 'Nickname not available.');
			}

			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			list($salt, $verifier, $secret, $tokens) = $this->model->get_login($_SESSION['username']);
			if (!isset($salt) || !isset($verifier)) {
				$this->add_error('username', 'Username is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			if (!srp6::check_data($_SESSION['username'], $password, $salt, $verifier)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return false;

			if (isset($secret) && strlen($secret) !== 0) {
				if (!isset($code))
					$this->add_error('mfa', true);
				else if ((!otp::check_otp($code, $secret) && !(isset($tokens) && in_array($code, $tokens)))) {
					$this->add_error('mfa', true);
					$this->add_error('code', 'Code is invalid.');
				}
			}
			
			if ($this->errors() > 0)
				return false;

			return true;
		}

		public function process_password($password, $password_new, $password_new_confirm, $code = null) {
			if (!$password || strlen($password = trim($password)) == 0) {
				$this->add_error('password', 'Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password)) {
				$this->add_error('password', 'Password is invalid.');
			}

			if (!$password_new || strlen($password_new = trim($password_new)) == 0) {
				$this->add_error('password_new', 'New Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password_new)) {
				$this->add_error('password_new', 'New Password is invalid.');
			}
			
			if (!$password_new_confirm || strlen($password_new_confirm = trim($password_new_confirm)) == 0) {
				$this->add_error('password_new_confirm', 'Confirm New Password not entered.');
			} else if (!preg_match('/^[a-zA-Z0-9.!@#$%^&*_\-+]{4,16}$/', $password_new_confirm)) {
				$this->add_error('password_new_confirm', 'Confirm New Password is invalid.');
			} else if ($password_new != $password_new_confirm) {
				$this->add_error('password_new_confirm', 'New Passwords do not match.');
			}
			
			if ($this->errors() > 0)
				return 1;

			list($salt, $verifier, $secret, $tokens, $locked, $banned) = $this->model->get_login($_SESSION['username']);
			if (!isset($salt) || !isset($verifier) || (isset($locked) && $locked) || (isset($banned) && $banned > 0)) {
				$this->add_error('username', 'Username is invalid.');
			}
			
			if ($this->errors() > 0)
				return 1;

			if (!srp6::check_data($_SESSION['username'], $password, $salt, $verifier)) {
				$this->add_error('password', 'Password is invalid.');
			}
			
			if ($this->errors() > 0)
				return 1;

			if (isset($secret) && strlen($secret) !== 0) {
				if (!isset($code))
					$this->add_error('mfa', true);
				else if ((!otp::check_otp($code, $secret) && !(isset($tokens) && in_array($code, $tokens)))) {
					$this->add_error('mfa', true);
					$this->add_error('code', 'Code is invalid.');
				}
			}
			
			if ($this->errors() > 0)
				return 1;

			list($salt_new, $verifier_new) = srp6::get_data($_SESSION['username'], $password_new);
			if (!$this->model->set_user_password($_SESSION['username'], $salt_new, $verifier_new))
				return 2;
			return 0;
		}
    }
?>