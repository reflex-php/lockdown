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

namespace Reflex\Lockdown\Commands;

use Illuminate\Console\Command as LaravelCommand;
use Reflex\Lockdown\Models;
use Reflex\Lockdown\Lockdown;
use App;

/**
 * Command
 * @category Package
 * @package  Reflex
 * @author   Mike Shellard <contact@mikeshellard.me>
 * @license  http://mikeshellard.me/reflex/license MIT
 * @link     http://mikeshellard.me/reflex/lockdown
 */
abstract class Command extends LaravelCommand
{
    /**
     * Lockdown instance
     * @var \Reflex\Lockdown\Lockdown
     */
    protected $lockdown;

    /**
     * Constructor
     * @param \Reflex\Lockdown\Lockdown $lockdown Lockdown instance
     */
    public function __construct(Lockdown $lockdown)
    {
        $this->lockdown =   $lockdown;

        parent::__construct();
    }

    /**
     * Interpolate message
     * @param string $message String to interpolate
     * @param array  $args    Array of values to interpolate
     * @return string
     */
    protected function interpolate($message, array $args = [])
    {
        return isprintf($message, $args);
    }
    
    /**
     * Write a string as comment output.
     *
     * @param string $string String to output
     * @param array  $args   Array of values to interpolate
     *
     * @return null
     */
    public function comment($string, $verbosity = null, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::comment($message);
    }

    /**
     * Write a string as information output.
     *
     * @param string $string String to output
     * @param array  $args   Array of values to interpolate
     *
     * @return null
     */
    public function info($string, $verbosity = NULL, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::info($message);
    }

    /**
     * Write a string as question output.
     *
     * @param string $string String to output
     * @param array  $args   Array of values to interpolate
     *
     * @return null
     */
    public function question($string, $verbosity = null, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::question($message);
    }

    /**
     * Write a string as error output.
     *
     * @param string $string String to output
     * @param array  $args   Array of values to interpolate
     *
     * @return null
     */
    public function error($string, $verbosity = null, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::error($message);
    }

    /**
     * Confirm a question with the user.
     *
     * @param string $string  Question to ask user
     * @param bool   $default Default response
     * @param array  $args    Array of values to interpolate
     *
     * @return bool
     */
    public function confirm($string, $verbosity = null, $default = true, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        return parent::confirm($message, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param string $question Question to ask
     * @param mixed  $default  Default value
     * @param array  $args     Array of values to interpolate
     *
     * @return null
     */
    public function ask($question, $verbosity = null, $default = null, array $args = [])
    {
        $message    =   $this->interpolate($question, $args);
        return parent::ask($message, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param string  $question Question to ask
     * @param boolean $fallback Fallback value
     * @param array   $args     Array of values to interpolate
     *
     * @return null
     */
    public function secret($question, $fallback = true, array $args = [])
    {
        $message    =   $this->interpolate($question, $args);
        return parent::secret($message, $fallback);
    }
}
