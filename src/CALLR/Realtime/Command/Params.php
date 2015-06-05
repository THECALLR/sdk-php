<?php

namespace CALLR\Realtime\Command;

abstract class Params
{
    /**
     * Convert properties to array with snake_case properties
     * @return array {
     *     @var  string Property name
     *     @var  mixed Property value
     * }
     */
    public function getParams()
    {
        $params = [];
        foreach ($this as $key => $value) {
            $pname = (string) ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $key)), '_');
            $params[$pname] = $value;
        }
        return $params;
    }
}
