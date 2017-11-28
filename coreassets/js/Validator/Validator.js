
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
    try
    {
        if (validationRules instanceof Array)
        {
            var overallOk = true;

            for (i in validationRules)
            {
                var rule = validationRules[i];

                // Launch the validation function.
                new window["Validator"+rule.validatorFunc](rule.args);
            }
        }
    }
    catch (ex)
    {
        Utils.Assert(ex);
    }
}