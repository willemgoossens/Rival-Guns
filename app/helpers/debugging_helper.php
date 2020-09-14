<?php

    /****************
    This helper has functions that can be used for debugging
    *****************/

    function variablePrint ($var)
    {
      return '<pre>' . var_export($var, true) . '</pre>';
    }
