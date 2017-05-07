function ValidateString(value, id, required, hint, type, min, max, regexp)
{
    var len = value.length;
    
    if (len == 0 && required)
    {
        return true;
    }
    
    if (len >= min && len <=max)
    {
        if (regexp.exec(value))
        {
            return true;
        }
    }
    
    return false;
}