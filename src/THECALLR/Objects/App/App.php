<?php

namespace THECALLR\Objects\App;

use \THECALLR\API\Client;
use \THECALLR\API\Exception\LocalException;

/**
 * App class
 * @author  Florent CHAUVEAU <fc@thecallr.com>
 * @see     http://thecallr.com/docs/objects/#App
 */
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
     * @throws \THECALLR\API\Exception\LocalException if missing $apiObjectName
     * @return boolean true or throws Exception otherwise
     */
    public function create()
    {
        if ($this->apiObjectName === null) {
            throw new LocalException('Missing Object Name in "'.get_class($this).'"');
        }
        $result = $this->api->call('apps.create', [$this->apiObjectName, $this->name, $this->p]);
        $this->setProperties($result);
        return true;
    }

    private function setProperties(\stdClass $properties)
    {
        $class = new \ReflectionClass(get_class($this));
        $plist = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($plist as $property) {
            $key = $property->getName();
            if (property_exists($properties, $key)) {
                $this->$key = $properties->$key;
            }
        }
    }
}
