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
    this.regexp = Utils.StrToRegExp(args.regexp);
    
    // Find DOM elements.
    this.inputText = document.getElementById(this.inputId);
    this.inputLabel = document.getElementById(this.labelId);
    
    if (this.inputText.form)
    {
        this.parentForm = this.inputText.form;
    }
    else
    {
        this.parentForm = null;
    }
    
    // Register callbacks for change events.
    var self = this;
    
    Utils.AddEvent(this.inputText, "blur", function() {
        self.Validate(this); 
    });
    
    // If parent form exists, register the validation handler to it.
    if (this.parentForm)
    {
        if (!this.parentForm._3V_Validation)
        {
            this.parentForm._3V_Validation = new Array();
        }
    
        this.parentForm._3V_Validation.push(this);
    }
    else
    {
        throw new Error("Non-existent parent form.");
    }
}

/**
 * Validates String Fields.
 * 
 * @retval true String is Valid.
 * @retval false String in Invalid.
 */
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