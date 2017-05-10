<?php
namespace CALLR\API\Authentication;

use InvalidArgumentException;

/**
 * Api-Key authentication
 *
 * @author Baptiste Clavié <baptiste.clavie@callr.com>
 */
final class ApiKeyAuth implements AuthenticationInterface
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $logAs;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /** {@inheritDoc} */
    public function getHeaders()
    {
        $headers = [
            "Authorization: Api-Key {$this->apiKey}"
        ];

        if (null !== $this->logAs) {
            $headers[] = "CALLR-Login-As: {$this->logAs}";
        }

        return $headers;
    }

    /** {@inheritDoc} */
    public function applyCurlOptions($channel)
    {
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
