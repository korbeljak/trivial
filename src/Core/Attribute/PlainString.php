<?php namespace Core\Attribute;

/**
 * 
 * @author minastir
 *
 */
class PlainString implements \Core\Attribute
{
    protected $required;
    protected $min;
    protected $max;
    protected $regexp;
    protected $hint;

    public function __construct(string $name,
                                bool $required,
                                string $hint,
                                string $default = "",
                                int $max = 255,
                                int $min = 0,
                                string $regexp = "")
    {
        if (empty($name))
        {
            throw new \Exception("Field name is invalid");
        }

        if ($max < 0 || $min < 0)
        {
            throw new \Exception("Limits are invalid");
        }

        $this->name = $name;
        $this->required = $required;
        $this->max = $max;
        $this->min = $min;
        $this->regexp = $regexp;
        $this->hint = $hint;
    }

    public function GetName()
    {
        return $this->name;
    }

    public function IsRequired()
    {
        return $this->required === true;
    }


    public function GetHtmlFormElem(string $formName)
    {
        $fullName = $formName . "_" . $this->name;
        $form = "<input type=\"text\" name=\"$fullName\" id=\"$fullName\">";
        return $form;
    }

    public function GetSqlColumn(string $template)
    {
        $fullName = $template . "_" . $this->name;
        $sql = $fullName." VARCHAR(".$this->max.")";
        return $form;
    }

    public function GetJsonValidationParams(string $formName)
    {
        $jsonArray = array(
                "id" => $formName . "_" . $this->name,
                "required" => $this->required,
                "type" => (new \ReflectionClass($this))->getShortName(),
                "min" => $this->min,
                "max" => $this->max,
                "regexp" => $this->regexp
        );

        return json_encode($jsonArray, JSON_PRETTY_PRINT);
    }

    public function Validate($value)
    {
        $len = strlen($value);

        if ($len === 0 && !$this->required)
        {
            return true;
        }

        if ($len >= $this->min && $len <= $this->max)
        {
            if (preg_match($this->regexp, $value) === 1)
            {
                return true;
            }
        }

        return false;
    }
}