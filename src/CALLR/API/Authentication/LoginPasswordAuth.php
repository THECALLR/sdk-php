<?php
namespace CALLR\API\Authentication;

use InvalidArgumentException;

/**
 * Login Password authentication
 *
 * Uses Basic to do so
 *
 * @author Baptiste Clavié <baptiste.clavie@callr.com>
 */
final class LoginPasswordAuth implements AuthenticationInterface
{
    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $logAs;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /** {@inheritDoc} */
    public function getHeaders()
    {
        return [];
    }

    /** {@inheritDoc} */
    public function applyCurlOptions($channel)
    {
        curl_setopt($channel, CURLOPT_USERPWD, "{$this->login}:{$this->password}");
    }

    /** {@inheritDoc} */
    public function logAs($type, $target)
    {
        switch (strtolower($type)) {
            case 'user':
                $type = 'User.login';
                break;

            case 'account':
                $type = 'Account.hash';
                break;

            default:
                throw new InvalidArgumentException(sprintf(
                    'Invalid type "%s" provided. Expected one of "%s"',
                    $type, implode('", "', ['User', 'Account'])
                ));
        }

        $that = clone $this;
        $that->logAs = "{$type} {$target}";

        return $that;
    }
}
