<?php

  function getValidationClass ($inputError)
  {
    if(!isset($inputError))
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
