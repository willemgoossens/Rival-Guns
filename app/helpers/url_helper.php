<?php
    /**
     * 
     * 
     * Redirect
     * @param String url
     * @return Void
     * 
     * 
     */
    function redirect(String $url): Void
    {
        header('location: ' . URLROOT . '/' . $url);
        exit();
    }
