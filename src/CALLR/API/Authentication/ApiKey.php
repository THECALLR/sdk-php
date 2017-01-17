<?php
namespace CALLR\API\Authentication;

/**
 * Api-Key authentication
 *
 * @author Baptiste Clavié <baptiste.clavie@callr.com>
 */
final class ApiKeyAuth implements AuthenticationInterface
{
    /** @var string */
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /** {@inheritDoc} */
    public function getHeaders()
    {
        return [
            "Authorization: Api-Key {$this->apiKey}"
        ];
    }

    /** {@inheritDoc} */
    public function applyCurlOptions($channel)
    {
    }
}

