<?php
namespace Reflex\Lockdown;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Factory as FactoryContract;
use Closure;

class LockdownCacheLayer
{
    /**
     * CacheManager instance
     * @var Illuminate\Contracts\Cache\Factory
     */
    protected $cache;

    /**
     * Cache enabled
     * @var boolean
     */
    protected $enabled;

    /**
     * Cache expiry
     * @var integer
     */
    protected $expire;

    /**
     * Cache ID
     * @var string
     */
    protected $id;

    /**
     * Constructor
     * @param Illuminate\Contracts\Cache\Factory $cache   
     * @param boolean                            $enabled 
     * @param integer                            $expire  
     * @param string                             $id      
     * @return void 
     */
    public function __construct(FactoryContract $cache, $enabled, $expire, $id)
    {
        $this->cache    =   $cache;
        $this->enabled  =   $enabled;
        $this->expire   =   $expire;
        $this->id       =   $id;
    }

    /**
     * Is cache enabled?
     * @return boolean 
     */
    public function isCacheEnabled()
    {
        return (bool) $this->enabled;
    }

    /**
     * Get enabled
     * @return bool 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     * @param  boolean $enabled 
     * @return Reflex\Lockdown\LockdownCacheLayer 
     */
    public function setEnabled($enabled)
    {
        $this->enabled  =   (bool) $enabled;

        return $this;
    }

    /**
     * Get expiry
     * @return integer 
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Set expire
     * @param  integer $expire 
     * @return Reflex\Lockdown\LockdownCacheLayer 
     */
    public function setExpire($expire)
    {
        $this->expire   =   (int) $expire;

        return $this;
    }

    /**
     * Get cache ID
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     * @param  mixed $id 
     * @return Reflex\Lockdown\LockdownCacheLayer 
     */
    public function setId($id)
    {
        $this->id   =   $id;

        return $this;
    }

    /**
     * Build cache key
     * @param  mixed  $miscData 
     * @return string           
     */
    protected function buildKey($miscData)
    {
        return md5(
            base64_encode(
                http_build_query(
                    [
                        'cacheId'   =>  $this->getId(),
                        'miscData'  =>  $miscData,
                    ]
                )
            )
        );
    }

    /**
     * Get cache result
     * @param  Closure $callback 
     * @param  string  $key      
     * @return mixed            
     */
    protected function getResult(Closure $callback, $key)
    {
        $cache      =   $this->cache;
        $expire     =   $this->getExpire();

        return $cache->remember($key, $expire, $callback);
    }

    /**
     * Get result from cache or not
     * @param  Closure $callback 
     * @param  mixed   $miscData 
     * @return mixed            
     */
    public function get(Closure $callback, $miscData)
    {
        // Check to see if cache is enabled
        if (false === $this->isCacheEnabled()) {
            return value($callback);
        }

        $key    =   $this->buildKey($miscData);
        return $this->getResult($callback, $key);
    }
}
