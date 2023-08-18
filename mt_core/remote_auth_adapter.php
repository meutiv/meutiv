<?php

class MT_RemoteAuthAdapter extends MT_AuthAdapter 
{
    private $remoteId;
    private $type;
    
    /**
     * 
     * @var BOL_RemoteAuthService
     */
    private $remoteAuthService;
    
    public function __construct($remoteId, $type)
    {
        $this->remoteId = $remoteId;
        $this->type = trim($type);
        
        $this->remoteAuthService = BOL_RemoteAuthService::getInstance();
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getRemoteId()
    {
        return $this->remoteId;
    }
    
    public function isRegistered()
    {
        return $this->remoteAuthService->findByRemoteTypeAndId($this->type, $this->remoteId);
    }
    
    public function register( $userId, $custom = null )
    {
        $entity = new BOL_RemoteAuth();
        $entity->userId = (int) $userId;
        $entity->remoteId = $this->remoteId;
        $entity->type = $this->type;
        $entity->timeStamp = time();
        $entity->custom = $custom;

        return $this->remoteAuthService->saveOrUpdate($entity);
    }
    
    /**
     *
     * @return MT_AuthResult
     */
    public function authenticate()
    {
        $entity = $this->remoteAuthService->findByRemoteTypeAndId($this->type, $this->remoteId);
        
        if ( $entity === null )
        {
            $userId = null;
            $code = MT_AuthResult::FAILURE;
        }
        else
        {
            $userId = (int) $entity->userId;
            $code = MT_AuthResult::SUCCESS;
        }
          
        return new MT_AuthResult($code, $userId);
    }
}