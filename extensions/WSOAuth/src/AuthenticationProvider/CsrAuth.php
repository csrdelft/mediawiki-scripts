<?php

namespace AuthenticationProvider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use User;

/**
 * Class FacebookAuth
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
			'scope' => ['PROFIEL:EMAIL WIKI:BESTUUR']
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
			$token = $this->provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);

			$user = $this->provider->getResourceOwner($token);

            $this->setGroups($user);

			return [
				'name' => $user['slug'],
				'realname' => $user['displayName'],
			];
		} catch (\Exception $e) {
			return false;
		}
	}

    private function setGroups($resource) {
        $user = User::newFromName( $resource['slug'] );
        $user_id = $user->idForName();

        if ($resource['admin']) {
            $this->addGroup($user_id, 'sysop');
            $this->addGroup($user_id, 'bureaucrat');
            $this->addGroup($user_id, 'interface-admin');
        } else {
            $this->removeGroup($user_id, 'sysop');
            $this->removeGroup($user_id, 'bureaucrat');
            $this->removeGroup($user_id, 'interface-admin');
        }

        if (in_array('WIKI:BESTUUR', $resource['scopes'])) {
            $this->addGroup($user_id, 'bestuur');
        } else {
            $this->removeGroup($user_id, 'bestuur');
        }
    }

    private function addGroup($userId, $group) {
        $dbr = wfGetDB(DB_PRIMARY);
        if ($dbr->numRows($dbr->select(
                'user_groups',
                ['ug_user'],
                'ug_user = ' . $userId . ' AND ug_group = "' . $group . '"'
            )) !== 1) {
            $dbr->insert('user_groups', ['ug_user' => $userId, 'ug_group' => $group]);
        }
    }

    private function removeGroup($userId, $group) {
        $dbr = wfGetDB(DB_PRIMARY);
        $dbr->delete('user_groups', 'ug_user = ' . $userId . ' AND ug_group = "' . $group . '"');
    }

	/**
	 * @inheritDoc
	 */
	public function saveExtraAttributes($id)
	{
	}
}
