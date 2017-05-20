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
function Validator_PlainString(args)
{
    // Get the item.
    var inputText = document.getElementById(args.id);
    var inputLabel = document.getElementById("label_"+args.id);
    
    // Get the value.
    var value = inputText.value;
    
    var len = value.length;
    
    if (len == 0 && args.required)
    {
        return true;
    }
    
    if (len >= args.min && len <= args.max)
    {
        if (args.regexp.exec(value))
        {
            return true;
        }
    }
    
    return false;
}