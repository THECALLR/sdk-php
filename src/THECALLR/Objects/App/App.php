<?php

namespace THECALLR\Objects\App;

use \THECALLR\API\Client;
use \THECALLR\API\Exception\LocalException;

abstract class App
{
    protected $api = null;
    protected $apiObjectName = null;
    public $name;
    public $date_creation;
    public $date_update;
    public $did;
    public $hash;
    public $p;
    public $package;

    public function __construct(Client $api)
    {
        $this->api = $api;
    }

    /**
     * Create a new Voice App
     * @return boolean true or throws Exception otherwise
     */
    public function create()
    {
        if ($this->apiObjectName === null) {
            throw new LocalException('Missing Object Name in "'.get_class($this).'"');
        }
        $result = $this->api->call('apps.create', [$this->apiObjectName, $this->name, $this->p]);
        $this->updateSelf($result);
        return true;
    }

    private function updateSelf(\stdClass $result)
    {
        foreach ($result as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'p') {
                    $this->$key = (array) $value;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }
}
