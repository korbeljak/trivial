var Utils = new function()
{
    this.AddEvent = function (elem, event, func)
    {
        if (elem.attachEvent)
        {
            // IE <= 9.
            elem.attachEvent("on"+event, func);

        }
        else // elem.addEventListener
        {
            // Others.
            elem.addEventListener(event, func, false);
        }
    }
    
    this.StrToRegexp = function (str)
    {
        var regParts = str.match(/^\/(.*?)\/([gim]*)$/);
        if (regParts)
        {
            // the parsed pattern had delimiters and modifiers. handle them. 
            return new RegExp(regParts[1], regParts[2]);
        }
        else
        {
            // we got pattern string without delimiters
            return new RegExp(str);
        }
    }
}