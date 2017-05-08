<?php namespace Core;

/**
 * Entity class.
 * 
 * Represents:
 *    1. A form.
 *    2. A DB entity.
 *    3. An PHP object.
 * 
 * @author Jakub Korbel, korbel.jak@gmail.com
 *
 */
class Entity
{
    /**
     * Transition table. e .. Entity state.
     *                   a .. Attribute state.
     * Algorithm: STATE(entity) + 3 * STATE(new attribute)
     * Rules:
     *      1. At least 1 unknown => overall unknown.
     *      2. No unknown and at least 1 invalid => overall invalid.
     *      3. All valid => overall valid.
     */
    const TRANSITION_TABLE = array(
            \Core\Attribute::STATE_UNKNOWN, // 0e + 3 * 0a
            \Core\Attribute::STATE_UNKNOWN, // 1e + 3 * 0a
            \Core\Attribute::STATE_UNKNOWN, // 2e + 3 * 0a
            \Core\Attribute::STATE_UNKNOWN, // 0e + 3 * 1a
            \Core\Attribute::STATE_INVALID, // 1e + 3 * 1a
            \Core\Attribute::STATE_INVALID, // 2e + 3 * 1a
            \Core\Attribute::STATE_UNKNOWN, // 0e + 3 * 2a
            \Core\Attribute::STATE_INVALID, // 1e + 3 * 2a
            \Core\Attribute::STATE_VALID  , // 2e + 3 * 2a
    );
    
    /** Attributes array. This is the hearth of the Entity. */
    protected $attr = array();
    
    /** Entity name. */
    protected $name;
    
    /** Overall entity validation state */
    protected $state;
    
    /**
     * Constructor.
     * 
     * @param string $name Name of the Entity.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        
        $this->state = \Core\Attribute::STATE_UNKNOWN;
    }
    
    /**
     * Gets HTML form representing the entity.
     * 
     * @param int $lvl Indentation level, defaults to 0.
     * @return string HTML of the form.
     */
    public function GetHtmlForm(int $lvl = 0)
    {
        if (count($this->attr) === 0)
        {
            return "";
        }
        
        $formHtml = ILVL[$lvl].'<form action="" method="post">\n';
        
        foreach($this->attr as $name => $attr)
        {
            $formHtml .= ILVL[$lvl+1].$attr->GetHtmlFormElem()."\n";
        }
        
        $formHtml .= ILVL[$lvl]."</form>";
        
        return $formHtml;
    }
    
    /**
     * Gets JSON Validation data in a script.
     * 
     * @param int $lvl Indentation level, defaults to 0.
     * @return string HTML of the form.
     */
    public function GetJsValidation(int $lvl = 0)
    {
        if (count($this->attr) === 0)
        {
            return "";
        }
        
        $jsonScript = ILVL[$lvl].'<script type="text/js">\n';
        
        foreach($this->attr as $name => $attr)
        {
            $formHtml .= ILVL[$lvl+1].$attr->GetJsonValidationParams($this->name)."\n";
        }
        
        $jsonScript .= ILVL[$lvl].'</script>\n';
        
        return $jsonScript;
    }
    
    /**
     * Forces the validation of all attributes.
     * 
     * Sets internal state of the Entity after the operation to either
     * valid or invalid.
     * 
     * @return boolean true if valid, false if invalid.
     */
    public function Validate()
    {
        if (count($this->attr) === 0)
        {
            $this->state = \Core\Attribute::STATE_VALID;
            return true;
        }
        
        $ok = true;

        foreach($this->attr as $name => $attr)
        {
            if (!$attr->Validate())
            {
                $ok = false;
            }
        }
        
        if ($ok)
        {
            $this->state = \Core\Attribute::STATE_VALID;
        }
        else
        {
            $this->state = \Core\Attribute::STATE_INVALID;
        }
        
        return $ok;
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
    
    
    /**
     * Adds an attribute to the Entity.
     * 
     * @param \Core\Attribute $attr Attribute to add. This can be any Attribute
     * implementing the interface.
     * 
     * @throws \InvalidArgumentException
     */
    public function AddAttribute(\Core\Attribute $attr)
    {
        if ($attr != null)
        {
            $this->column[] = $attr;
            
            $this->state = TRANSITION_TABLE[$this->state + 3 * $attr->GetState()];
            
        }
        else
        {
            throw new \InvalidArgumentException("Null pointer in column.");
        }
    }
    
    /**
     * Attribute Value getter.
     * 
     * This makes the class polymorphic, it proxies the values of the attributes
     * to the Attribute objects.
     * 
     * @param string $name Attribute name.
     * @return unknown|NULL
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->attr))
        {
            return $this->attr[$name]->GetValue();
        }

        return null;
    }
    
    /**
     * Attribute Value setter.
     *
     * This makes the class polymorphic, it proxies the values of the attributes
     * to the Attribute objects.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return unknown|NULL
     */
    public function __set(string $name, $value)
    {
        if (array_key_exists($name, $this->attr))
        {
            $this->attr[$name]->SetValue($value);
        }
    }
}

