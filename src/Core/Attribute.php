<?php namespace Core;

/**
 * Attribute interface for Entities.
 * 
 * @author Jakub Korbel, korbel.jak@gmail.com
 *
 */
interface Attribute
{
    /** Unknown attribute state (validation method not run yet). */
    const STATE_UNKNOWN = 0;
    
    /** Invalid attribute state (validation run: failed). */
    const STATE_INVALID = 1;
    
    /** Valid attribute state (validation run: succeeded). */
    const STATE_VALID = 2;
    
    /**
     * Gets A HTML form element of the attribute with the ability to inject
     * a form name prefix.
     * 
     * @param string $formName Form name prefix (distinguish between multiple 
     * forms).
     */
    public function GetHtmlFormElem(string $formName);
    
    /**
     * Gets SQL representation of the attribute (in form of a SQL column).
     * 
     * @param string $template Template name or database prefix.
     */
    public function GetSqlColumn(string $template);
    
    /**
     * Validates an attribute value according to attribute constraints.
     * 
     * @param mixed $value Any value relevant to an attribute.
     */
    public function Validate(mixed $value);
    
    /**
     * true if the attribute is required, false otherwise.
     */
    public function IsRequired();
    
    /**
     * Gets attribute identifier name.
     */
    public function GetName();
    
    /**
     * Gets validation parameters for javascript checkers in JSON format.
     * 
     * @param string $formName Name of the form the attribute is mentioned in.
     */
    public function GetJsonValidationParams(string $formName);
    
    /**
     * Gets attribute value.
     */
    public function GetValue();
    
    /**
     * Gets validation state of the attribute.
     * 
     * @see STATE_UNKNOWN
     * @see STATE_INVALID
     * @see STATE_VALID
     */
    public function GetState();
    
    /**
     * Sets attribute value.
     * @param mixed $value
     */
    public function SetValue(mixed $value);

}
