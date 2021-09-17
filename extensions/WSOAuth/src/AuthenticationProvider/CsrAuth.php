<?php

namespace AuthenticationProvider;

/**
 * @package AuthenticationProvider
 */
class CsrAuth implements \AuthProvider
{
	/**
	 * @var CsrProvider
	 */
	private $provider;

	/**
	 * CsrAuth constructor.
	 */
	public function __construct()
	{
		$this->provider = new CsrProvider([
			'clientId' => $GLOBALS['wgOAuthClientId'],
			'clientSecret' => $GLOBALS['wgOAuthClientSecret'],
			'redirectUri' => $GLOBALS['wgOAuthRedirectUri'],
			'baseUrl' => $GLOBALS['wgOAuthCsrBaseUrl'],
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function login(&$key, &$secret, &$auth_url)
	{
		$auth_url = $this->provider->getAuthorizationUrl([
			'scope' => ['PROFIEL:EMAIL']
		]);

		$secret = $this->provider->getState();

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function logout(\User &$user)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getUser($key, $secret, &$errorMessage)
	{
		if (!isset($_GET['code'])) {
			return false;
		}

		if (!isset($_GET['state']) || empty($_GET['state']) || ($_GET['state'] !== $secret)) {
			return false;
		}

		try {
			$token = $this->provider->getAccessToken('authorization_code', [
				'code' => $_GET['code'],
				'scope' => ['PROFIEL:EMAIL']
			]);

			$user = $this->provider->getResourceOwner($token);

			return [
				'name' => $user['slug'],
				'realname' => $user['displayName'],
			];
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function saveExtraAttributes($id)
	{
	}
}
