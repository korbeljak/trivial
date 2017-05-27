/**
 * Validates String Fields.
 * 
 * @param args contains:
 *  - id ID of a field to validate.
 *  - required Whether the field is required.
 *  - hint Hint for the user.
 *  - min Minimum characters.
 *  - max Maximum number of characters.
 *  - regexp Reqular expression to validate against.
 * @returns
 */
ValidatorPlainString = function (args)
{
    // Copy arguments.
    this.inputId = args.id;
    this.labelId = "label_"+args.id;
    this.required = args.required;
    this.min = args.min;
    this.max = args.max;
    this.regexp = Utils.StrToRegexp(args.regexp);
    
    // Find DOM elements.
    this.inputText = document.getElementById(this.inputId);
    this.inputLabel = document.getElementById(this.labelId);
    
    var self = this;
    // Register callbacks for change events.
    
    Utils.AddEvent(this.inputText, "blur", function() {
        self.Validate(this); 
    });
    
}

ValidatorPlainString.prototype.Validate = function()
{
    // Get the value.
    var value = this.inputText.value;
    var len = value.length;
    
    if (len == 0 && !this.required)
    {
        return true;
    }
    
    if (len >= this.min && len <= this.max)
    {
        if (this.regexp.exec(value))
        {
            return true;
        }
    }
    
    return false;
}