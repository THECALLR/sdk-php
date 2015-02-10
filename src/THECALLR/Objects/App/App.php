<?php

namespace THECALLR\Objects\App;

abstract class App
{
    protected $api = null;
    public $name;
    public $date_creation;
    public $date_update;
    public $did;
    public $hash;
    public $p;
    public $package;

    public function __construct(\THECALLR\API\Client $api)
    {
        $this->api = $api;
    }

    /**
     * Create a new Voice App
     * @return boolean true or throws Exception otherwise
     */
    public function create()
    {
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
