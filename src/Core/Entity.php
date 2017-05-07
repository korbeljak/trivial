<?php namespace Core;

class Entity
{
    protected $attr = array();
    
    const FORM_TYPE_ADD = 0;
    const FORM_TYPE_EDIT = 1;
    const FOTM_TYPE_DELETE = 2;
    
    public function __construct(string $name)
    {
        
    }
    
    public function GetHtmlForm($type)
    {
        foreach($this->attr as $attr)
        {
            
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
    
    public function Install()
    {
        
    }
    
    public function GetById()
    {
        
    }
    
    public function AddAttribute(Core\Attribute $attr)
    {
        if ($attr != null)
        {
            $this->column[] = $attr;
        }
        else
        {
            throw new \InvalidArgumentException("Null pointer in column.");
        }
    }
}

