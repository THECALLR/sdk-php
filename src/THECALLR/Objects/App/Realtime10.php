<?php

namespace THECALLR\Objects\App;

use \THECALLR\API\Client;

/**
 * REALTIME10 Voice App
 * @author  Florent CHAUVEAU <fc@thecallr.com>
 * @see     http://thecallr.com/docs/objects/#REALTIME10
 */
class Realtime10 extends App
{
    protected $apiObjectName = 'REALTIME10';
    /**
     * Properties
     * @var object {
     *     @var  string $url URL to send requests to
     *     @var  string $login Login if using Basic HTTP Authentication
     *     @var  string $password Password if using Basic HTTP Authentication
     *     @var  string $data_format 'JSON' for now
     * }
     */
    public $p;

    /**
     * Constructor
     * @param string $url      URL to send requests to
     * @param string $login    Login if using Basic HTTP Authentication
     * @param string $password Password if using Basic HTTP Authentication
     */
    public function __construct(Client $api)
    {
        parent::__construct($api);

        $this->p = new \stdClass;
        $this->p->url = '';
        $this->p->login = '';
        $this->p->password = '';
        $this->p->data_format = 'JSON';
    }
}
