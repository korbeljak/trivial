<?php namespace Core;

/**
 * Attribute interface for Entities.
 * 
 * @author Jakub Korbel, korbel.jak@gmail.com
 *
 */
interface Attribute
{
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
    public function Validate($value);
    
    /**
     * true if the attribute is required, false otherwise.
     */
    public function IsRequired();
    
    /**
     * Gets attribute identifier name.
     */
    public function GetName();

}
