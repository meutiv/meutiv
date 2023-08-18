<?php

class INSTALL_Storage 
{
    private static $classInstance;
    
    /**
     *
     * @return INSTALL_Storage
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    private $storage = array();
    
    protected function __construct()
    {
        $this->storage = MT::getSession()->get('MT-INSTALL-DATA');
        
        $this->storage = empty($this->storage) ? array() : $this->storage;
    }
    
    public function __destruct()
    {
        if ( empty($this->storage) )
        {
            MT::getSession()->delete('MT-INSTALL-DATA');
        }
        else
        {
            MT::getSession()->set('MT-INSTALL-DATA', $this->storage);
        }
        
    }
    
    public function set($name, $value)
    {
        $this->storage[$name] = $value;
    }
    
    public function get($name)
    {
        return $this->storage[$name];
    }
    
    public function getAll()
    {
        return empty($this->storage) ? array() : $this->storage;
    }
    
    public function clear()
    {
        $this->storage = null;
    }
}