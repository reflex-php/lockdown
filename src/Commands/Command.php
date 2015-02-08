<?php

namespace Reflex\Lockdown\Commands;

use Illuminate\Console\Command as LaravelCommand;
use Reflex\Lockdown\Models;
use Reflex\Lockdown\Lockdown;
use App;

abstract class Command extends LaravelCommand
{
    protected $lockdown;

    public function __construct(Lockdown $lockdown)
    {
        $this->lockdown =   $lockdown;

        parent::__construct();
    }

    /**
     * Interpolate message
     * @param  string $message String to interpolate
     * @param  array  $args    Array of values to interpolate
     * @return string          
     */
    protected function interpolate($message, array $args)
    {
        return isprintf($message, $args);
    }
    
    /**
     * Write a string as comment output.
     *
     * @param  string  $string String to output
     * @param  array   $args   Array of values to interpolate
     * @return void
     */
    public function comment($string, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::comment($message);
    }

    /**
     * Write a string as information output.
     *
     * @param  string  $string String to output
     * @param  array   $args   Array of values to interpolate
     * @return void
     */
    public function info($string, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::info($message);
    }

    /**
     * Write a string as question output.
     *
     * @param  string  $string String to output
     * @param  array   $args   Array of values to interpolate
     * @return void
     */
    public function question($string, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::question($message);
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string String to output
     * @param  array   $args   Array of values to interpolate
     * @return void
     */
    public function error($string, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        parent::error($message);
    }

    /**
     * Confirm a question with the user.
     *
     * @param  string  $question
     * @param  bool    $default
     * @param  array   $args 
     * @return bool
     */    
    public function confirm($string, $default = true, array $args = [])
    {
        $message    =   $this->interpolate($string, $args);
        return parent::confirm($message, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param  string  $string  String to output
     * @param  mixed   $default Default value
     * @param  array   $args    Array of values to interpolate
     * @return void
     */
    public function ask($question, $default = null, array $args = [])
    {
        $message    =   $this->interpolate($question, $args);
        return parent::ask($message, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param  string  $string   String to output
     * @param  boolean $fallback Fallback value
     * @param  array   $args     Array of values to interpolate
     * @return void
     */
    public function secret($question, $fallback = true, array $args = [])
    {
        $message    =   $this->interpolate($question, $args);
        return parent::secret($message, $fallback);
    }
}
