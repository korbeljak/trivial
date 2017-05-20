
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
    if (is_array(validationRules))
    {
        var overallOk = true;
        
        for (rule in validationRules)
        {
            var ok = window["Validator_"+rule.validatorFunc](rule.args);
            if (!ok)
            {
                overallOk = false;
            }
        }
    }
}