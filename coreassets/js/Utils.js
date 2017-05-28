var Utils = new function()
{
    /**
     * Assertion URL - a public property.
     */
    this.assertUrl = "JsAssert.php";
    
    
    /**
     * Asserts - sends out a JS error onto the server for debugging.
     */
    this.Assert = function (ex)
    {
        // Error exception object.
        exStr = "name=";
        exStr += encodeURIComponent(ex.name) + "&message=";
        exStr += ex.message + "&description=";
        if (ex.description)
        {
            // IE.
            exStr += encodeURIComponent(ex.description);
        }
        
        exStr += "&stack=";
        if (ex.stack)
        {
            // Well-behaving browsers.
            exStr += encodeURIComponent(ex.stack);
        }
        
        // Send out the string.
        SendAjaxStrRequest(exStr, assertUrl);
    }
    
    /**
     * Adds an Event Listener to an object.
     * 
     * @param[in] elem Element to attach the listener to.
     * @param[in] event Event name.
     * @param[in] func Function to attach.
     */
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
    
    /**
     * Converts string to RegExp object.
     * 
     * @param[in] str String to convert.
     * 
     * @return RegExp object.
     */
    this.StrToRegExp = function (str)
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
    
    /**
     * Private property of internal AJAX state.
     * 
     * 0 .. Well-behaving browser.
     * 1 .. New IE.
     * 2 .. Old IE.
     * 3 .. Undefined.
     * 4 .. AJAX is not supported.
     */
    var ajaxReqType = 3;
    
    /**
     * Gets AJAX request (caches browser type for future use).
     */
    this.GetAjaxRequest = function()
    {
        switch(ajaxReqType)
        {
        case 0:
            // Well-behaving browsers.
            return new XMLHttpRequest();
        case 1:
            // IE.
            return new ActiveXObject("Msxml2.XMLHTTP");
        case 2:
            // IE again.
            return new ActiveXObject("Microsoft.XMLHTTP");
        case 3:
            // Unknown.
            var xhttpreq = null;
            try
            {
                // Well-behaving browsers.
                xhr = new XMLHttpRequest();
                ajaxReqType = 0;
            }
            catch (ex)
            {
                // Internet Explorer.
                try
                {
                    xhr = new ActiveXObject("Msxml2.XMLHTTP");
                    ajaxReqType = 1;
                }
                catch (ex)
                {
                    try
                    {
                        xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        ajaxReqType = 2;
                    }
                    catch (ex)
                    {
                        // Unrecognized.
                        ajaxReqType = 4;
                        throw new Error("Unrecognized AJAX support type.");
                    }
                }
            }
            
            return xhr;
        }
    }
    
    /**
     * Encodes JS object into a query string for POST and GET.
     * 
     * @param[in] obj Object to encode.
     * 
     * @return Query string.
     */
    this.AjaxObjEncode = function (obj)
    {
        var qstr = "";
        for (var property in obj)
        {
            qstr += encodeURIComponent(property) 
                    + "=" + encodeURIComponent(obj[property]) + "&";
        }
        
        return qstr.substr(0, qstr.length - 1);
    }
    
    /**
     * Sends object to server as an AJAX request.
     * 
     * @param[in] obj Object to send.
     * @param[in] url URL to send it to.
     * 
     * @return response text or false if an error occurs.
     */
    this.SendAjaxObjRequest = function (obj, url)
    {
        try
        {
            xhr = GetAjaxRequest();
            xhr.open("POST", url, false);
            xhr.send(AjaxObjEncode(obj));
            
            if (xhr.readyState == 4 && xhr.status == 200)
            {
                return xhr.responseText;
            }
            else
            {
                return false;
            }
        }
        catch (ex)
        {
            return false;
        }
    }
    
    /**
     * Sends encoded string to server as an AJAX request.
     * 
     * @param[in] str An encoded string.
     * @param[in] url URL to send it to.
     * 
     * @return response text or false if an error occurs.
     */
    this.SendAjaxStrRequest = function (str, url)
    {
        try
        {
            xhr = GetAjaxRequest();
            xhr.open("POST", url, false);
            xhr.send(str);
            
            if (xhr.readyState == 4 && xhr.status == 200)
            {
                return xhr.responseText;
            }
            else
            {
                return false;
            }
        }
        catch (ex)
        {
            return false;
        }
    }
}