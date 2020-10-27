<?php

    /**
     * 
     * 
     * dateTimeFormat
     * @param DateTime datetime object
     * @param String formatting
     * @return String
     * 
     * 
     */
    function dateTimeFormat( \DateTime $dateTime, String $format = 'Y-m-d H:i:s' ): String
    {
        $timeZone = $_SESSION['userTimeZone'] ?? date_default_timezone_get();

        $dateTime->setTimezone( new \DateTimeZone( $timeZone ) );

        $formatted = $dateTime->format( $format );

        return $formatted;
    }