<?php

class User extends \Core\Entity
{
    protected $id;
    
    protected $name;
    
    protected $unit;
    
    protected $share;
    
    protected $address;
    
    protected $phone;
    
    protected $email;
    
    protected $functions;
    
    protected $permissions;
    
    public function GetHtmlForm($type)
    {
        switch ($type)
        {
            case Entity::FORM_TYPE_ADD:
                break;
            case Entity::FORM_TYPE_EDIT:
                break;
            case Entity::FOTM_TYPE_DELETE:
                break;
        }
        
    }
    
    public function GetJsValidation($type)
    {
        
    }
    
    public function Validate()
    {
        
    }
    
    public function Save()
    {
        
    }
    
    public function Load()
    {
        
    }
    
    public function GetById()
    {
        
    }
    
    public static function Install()
    {
        $rClass = new \ReflectionClass($this);
        $sql = "CREATE TABLE ".$rClass->getName();
        
    }
}