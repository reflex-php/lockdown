<?php
/**
 * Lockdown ACL
 *
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */

if (! function_exists('has')) {
    /**
     * Has permission
     * @param  string  $permission
     * @param  boolean $all
     * @param  integer $userId
     * @return boolean
     */
    function has($permission, $all = true, $userId = null)
    {
        $userId =   $userId ?: Auth::id();

        return Lockdown::has($userId, $permission, $all);
    }
}

if (! function_exists('hasnt')) {
    /**
     * Doesn't have permission
     * @param  string  $permission
     * @param  boolean $all
     * @param  integer $userId
     * @return boolean
     */
    function hasnt($permission, $all = true, $userId = null)
    {
        $userId =   $userId ?: Auth::id();

        return Lockdown::hasnt($userId, $permission, $all);
    }
}

if (! function_exists('is')) {
    /**
     * Is a role member
     * @param  string  $role
     * @param  boolean $all
     * @param  integer $userId
     * @return boolean
     */
    function is($role, $all = true, $userId = null)
    {
        $userId =   $userId ?: Auth::id();

        return Lockdown::is($userId, $role, $all);
    }
}

if (! function_exists('not')) {
    /**
     * Is not a role member
     * @param  string  $role
     * @param  boolean $all
     * @param  integer $userId
     * @return boolean
     */
    function not($role, $all = true, $userId = null)
    {
        $userId =   $userId ?: Auth::id();

        return Lockdown::not($userId, $role, $all);
    }
}

if (! function_exists('isprintf')) {
    /**
     * String Interpolation/Named sprintf()
     *
     * @param string $format Format
     * @param array  $args   Associative array with arguments
     *
     * @return string
     *
     * @link http://www.frenck.nl/2013/06/string-interpolation-in-php.html
     */
    function isprintf($format, array $args)
    {
        $matches=   [];
        $values =   [];

        // Find all keys
        preg_match_all('/%\((.*?)\)/', $format, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            // Check if the key is in the args
            if (false === isset($args[ $match[1] ])) {
                $errorMessage   =   sprintf('Missing key "%s"', $match[1]);
                throw new RuntimeException($errorMessage);
            }
            // Add value to array for vsprintf
            $values[] = $args[ $match[1] ];
        }

        // Remove all keys from the format string
        $format =   preg_replace('/%\((.*?)\)/', '%', $format);

        // Now we can execute a normal vsprintf
        return vsprintf($format, $values);
    }
}

if (! function_exists('get_caller')) {
    /**
     * Get calling function
     * @param string $function
     * @param array  $useStack
     * @return string
     */
    function get_caller($function = null, $useStack = null)
    {
        $stack      =   is_array($useStack)
            ? $useStack
            : debug_backtrace();

        $function   =   is_null($function)
            ? get_caller(__FUNCTION__, $stack)
            : $function;

        if (is_string($function)
            && '' !== $function
        ) {
            // If we are given a function name as a string,
            // go through the function stack and find
            // it's caller.
            $count  =   count($stack);
            for ($i = 0; $i < $count; $i++) {
                $currentStack   =   $stack[ $i ];
                // Make sure that a caller exists, a
                // function being called within the main script
                // won't have a caller.
                if ($currentStack['function'] == $function
                    && ($i + 1) < $count
                ) {
                    return $stack[ $i + 1 ]['function'];
                }
            }
        }

        // At this stage, no caller has been found, bummer.
        return '';
    }
}
