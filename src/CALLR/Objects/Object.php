<?php

namespace CALLR\Objects;

abstract class Object
{
    /**
     * Export public properties as an object
     * @return object Public properties
     */
    public function export()
    {
        $result = new \stdClass;
        $class  = new \ReflectionClass(get_class($this));
        $plist  = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($plist as $property) {
            $result->{$property->getName()} = $this->{$property->getName()};
        }
        return $result;
    }
}
