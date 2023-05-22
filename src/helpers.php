<?php

if (!function_exists('array_take_off_recursive')) {
    /**
     * @param array &$array
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */
    function array_take_off_recursive(&$array, $key, $default = null)
    {
        $keys = explode('.', $key);
        $currentKey = array_shift($keys);

        if ($currentKey === '*') {
            $values = array();

            foreach ($array as $subKey => &$subArray) {
                if (empty($keys)) {
                    $values[] = $subArray;
                    unset($array[$subKey]);
                } else {
                    $values[$subKey] = array_take_off_recursive($subArray, implode('.', $keys));
                }
            }

            return $values;
        }

        if (array_key_exists($currentKey, $array)) {
            if (empty($keys)) {
                $target = $array[$currentKey];
                unset($array[$currentKey]);

                return $target;
            } else {
                return array_take_off_recursive($array[$currentKey], implode('.', $keys));
            }
        }

        return $default;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}
