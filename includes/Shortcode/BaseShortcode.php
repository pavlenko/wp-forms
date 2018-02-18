<?php

namespace PE\WP\Forms\Shortcode;

abstract class BaseShortcode
{
    /**
     * Convert string value to boolean
     *
     * @param string $value
     *
     * @return bool
     */
    protected function parseBoolean($value)
    {
        return $value === true || $value === '1' || $value === 'true';
    }

    /**
     * Convert value to one of allowed values or use default
     *
     * @param mixed $value
     * @param array $allowed
     * @param mixed $default
     *
     * @return mixed
     */
    protected function parseEnum($value, array $allowed, $default = null)
    {
        if (in_array($value, $allowed, false)) {
            return $value;
        }

        return $default;
    }
}