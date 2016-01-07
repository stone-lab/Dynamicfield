<?php

namespace Modules\Dynamicfield\Utility\Enum;

abstract class BasicEnum
{
    private static $constCacheArray = null;
    public static function getList()
    {
        $values = self::getConstants();

        return $values;
    }
    public static function getKeys()
    {
        $constants = self::getConstants();
        $keys = array_keys($constants);

        return $keys;
    }
    private static function getConstants()
    {
        if (self::$constCacheArray == null) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            $arrItems = $reflect->getConstants();
            $arrNew = array();
            foreach ($arrItems as $k => $v) {
                $newKey = strtolower($k);
                $arrNew[$newKey] = $v;
            }
            self::$constCacheArray[$calledClass] = $arrNew;
        }

        return self::$constCacheArray[$calledClass];
    }
    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));

        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value)
    {
        $values = array_values(self::getConstants());

        return in_array($value, $values, $strict = true);
    }
}
