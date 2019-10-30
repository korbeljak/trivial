<?php namespace Core\Attribute;

/**
 * Plain String attribute. This type is the most configurable one.
 * 
 * @author Jakub Korbel, korbel.jak@gmail.com
 *
 */
class PlainInt implements \Core\Attribute
{
    /** If the attribute is required */
    protected $required;

    /** Minimum value of the number. */
    protected $min;

    /** Maximum value of the number. */
    protected $max;

    /** Hint for filling the attribute value. */
    protected $hint;
    
    /** Description of the attribute. */
    protected $description;
    
    /** Attribute value. */
    protected $value;
    
    /** Attribute state. */
    protected $state;

    /** Default value */
    protected $default;

    /**
     * Constructor.
     * 
     * @param string $name Identifier name.
     * @param bool $required Attribute is required (true) or not (false).
     * @param string $hint Hint for filling the attribute value.
     * @param string $description Description of the attribute.
     * If empty, value from hint will be used instead.
     * @param int $default Default value of the attribute - if unset, it is
     * NULL.
     * @param int $max Maximum value of the number.
     * @param int $min Maximum value of the number.
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name,
                                bool $required,
                                string $hint,
                                string $description = "",
                                int $default = NULL,
                                int $min = PHP_INT_MIN,
                                int $max = PHP_INT_MAX)
    {
        if (empty($name))
        {
            throw new \InvalidArgumentException("Field name is invalid");
        }

        if ($max < 0 || $min < 0)
        {
            throw new \InvalidArgumentException("Limits are invalid");
        }

        if (empty($description) && !empty($hint))
        {
            $description = $hint;
        }

        $this->name = $name;
        $this->required = $required;
        $this->max = $max;
        $this->min = $min;
        $this->hint = $hint;
        $this->state = \Core\Attribute::STATE_UNKNOWN;
        $this->value = null;
        $this->default = $default;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::GetName()
     */
    public function GetName()
    {
        return $this->name;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::IsRequired()
     */
    public function IsRequired()
    {
        return $this->required === true;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::GetHtmlFormElem()
     */
    public function GetHtmlFormElem(string $formName)
    {
        $fullId = $formName . "_" . $this->name;
        $fullName = $formName."[$this->name]";
        $form = "<input type=\"text\" name=\"$fullName\" id=\"$fullId\">";
        return $form;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::GetSqlColumn()
     */
    public function GetSqlColumn(string $template)
    {
        $fullName = $template . "_" . $this->name;
        $sql = $fullName." INTEGER";
        return $sql;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::GetJsonValidationParams()
     */
    public function GetJsonValidationParams(string $formName)
    {
        $func = (new \ReflectionClass($this))->getShortName();
        
        $jsonArray = array(
            "validatorFunc" => $func,
            "args" => array(
                "id" => $formName . "_" . $this->name,
                "required" => $this->required,
                "hint" => $this->hint,
                "min" => $this->min,
                "max" => $this->max));

        return json_encode($jsonArray, JSON_PRETTY_PRINT);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::Validate()
     */
    public function Validate()
    {
        $len = strlen($this->value);

        if ($len === 0 && !$this->required)
        {
            $this->state = \Core\Attribute::STATE_VALID;
            return true;
        }

        if ($len >= $this->min && $len <= $this->max)
        {
            if (preg_match($this->regexp, $this->value) === 1)
            {
                $this->state = \Core\Attribute::STATE_VALID;
                return true;
            }
        }

        $this->state = \Core\Attribute::STATE_INVALID;
        return false;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::GetValue()
     */
    public function GetValue()
    {
        return $this->value;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::SetValue()
     */
    public function SetValue($value)
    {
        $this->value = $value;
        $this->state = \Core\Attribute::STATE_UNKNOWN;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Core\Attribute::GetState()
     */
    public function GetState()
    {
        return $this->state;
    }
}
