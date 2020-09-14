<?php

    /**
     * 
     * 
     * getValidationClass
     * @param Bool inputError
     * @return String
     * 
     * 
     */
    function getValidationClass (?bool $inputError): String
    {
        if(! isset($inputError))
        {
            return "";
        }
        elseif(!$inputError)
        {
            return "is-valid";
        }
        else
        {
            return "is-invalid";
        }
    }
