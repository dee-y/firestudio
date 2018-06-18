<?php

namespace Fire\Studio;

class DataMapper
{

    static public function mapDataToObject($obj, $dataObj)
    {
        return self::mergeObjRecursively($obj, $dataObj);
    }

    static public function mergeObjRecursively($obj1, $obj2) {
        if (is_object($obj2)) {
            $keys = array_keys(get_object_vars($obj2));
            foreach ($keys as $key) {
                if (
                    isset($obj1->{$key})
                    && is_object($obj1->{$key})
                    && is_object($obj2->{$key})
                ) {
                    $obj1->{$key} = self::mergeObjRecursively($obj1->{$key}, $obj2->{$key});
                } elseif (isset($obj1->{$key})
                && is_array($obj1->{$key})
                && is_array($obj2->{$key})) {
                    $obj1->{$key} = self::mergeObjRecursively($obj1->{$key}, $obj2->{$key});
                } else {
                    $obj1->{$key} = $obj2->{$key};
                }
            }
        } elseif (is_array($obj2)) {
            if (
                is_array($obj1)
                && is_array($obj2)
            ) {
                $obj1 = array_unique(array_merge_recursive($obj1, $obj2), SORT_REGULAR);
            } else {
                $obj1 = $obj2;
            }
        }

        return $obj1;
    }
}
