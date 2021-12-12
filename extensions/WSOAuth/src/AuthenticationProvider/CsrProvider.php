<?php

namespace AuthenticationProvider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class CsrProvider extends AbstractProvider
{
	use BearerAuthorizationTrait;

    protected $baseUrl = 'https://csrdelft.nl';

	/**
	 * @param array $options
	 * @param array $collaborators
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($options = [], array $collaborators = [])
	{
		parent::__construct($options, $collaborators);
	}

	public function getBaseAuthorizationUrl()
	{
		return $this->baseUrl .'/authorize';
	}

	public function getBaseAccessTokenUrl(array $params)
	{
		return $this->baseUrl .'/api/v3/token';
	}

	public function getDefaultScopes()
	{
		return ['PROFIEL:EMAIL'];
	}


	public function getResourceOwnerDetailsUrl(AccessToken $token)
	{
		return $this->baseUrl .'/api/v3/profiel';
	}

	public function getAccessToken($grant = 'authorization_code', array $params = [])
	{
		return parent::getAccessToken($grant, $params);
	}

	protected function createResourceOwner(array $response, AccessToken $token)
	{
		return $response;
	}

	protected function checkResponse(ResponseInterface $response, $data)
	{
		if (!empty($data['error'])) {
			$message = $data['error']['type'].': '.$data['error']['message'];
			throw new IdentityProviderException($message, $data['error']['code'], $data);
		}
	}
}
