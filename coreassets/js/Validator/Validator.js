
/**
 * Validates all forms according to validation rules.
 * 
 * Rules are in form: 
 *  {validatorFunc: validatortype, args:{"validatorarg1": value, "validatorarg2": value2}}
 * 
 * Walks through all rules, invokes Validator_<type>
 * 
 * @param validationRules Rules to validate against.
 * @returns
 */
function Validator_Validate(validationRules)
{
    if (validationRules instanceof Array)
    {
        var overallOk = true;
        
        for (i in validationRules)
        {
            var rule = validationRules[i];
            
            // Launch the validation function.
            var ok = window["Validator_"+rule.validatorFunc](rule.args);
            if (!ok)
            {
                overallOk = false;
            }
        }
    }
}