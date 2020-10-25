<?php
    /*************************************
    *
    *   MIDDLEWARE
    *
    **************************************/

    /**
     * When setByDefault is true, the middleware will run
     * You can add exceptions. Either a controller name or a controller/method.
     * To add and api exception use api/controller/method
     * Sequenced middleware will run directly after the parent middleware has ran (with the same exceptions)
     */
    define('MIDDLEWARE', [
        'DebuggingToolbar' =>
            [
                'runByDefault' => (ENVIRONMENT == "development") ? true : false
            ],
        'LoggedIn' =>   
            [
                'runByDefault' => true,
                'exceptions' => 
                    [
                        'pages',
                        'users/login',
                        'users/register',
                        'users/forgottenPassword',
                        'tests'
                    ],
                'sequenced' => 
                    [                        
                        'UserUpdater',
                        'PunishmentCheck'
                    ]
            ],
        'HealthCheck' => 
            [
                'runByDefault' => true,
                'exceptions' =>
                    [
                        'pages',
                        'users/login',
                        'users/register',
                        'users/forgottenPassword',
                        'users/editProfile',
                        'users/logout',
                        'tests',
                        'adminRoles',
                        'conversationReports'
                    ],
                'sequenced' => 
                    [
                        'PrisonCheck'
                    ]
            ],
        'LoggedOut' =>
            [
                'runByDefault' => false,
                'exceptions' =>
                    [
                        'users/login',
                        'users/register',
                        'users/forgottenPassword',
                        'pages'
                    ]
            ]
    ]);