<?php
    /*************************************
    *
    *   MIDDLEWARE
    *
    **************************************/

    /**
     * When set to true, the middleware will run
     * e.g. The login check will only run for the pages where it's true
     *      The logout check will only run for the pages where it's true
     */
    define('MIDDLEWARE', [
        'LoggedIn'          => ['interface' => ['default' => true,
                                                'except'  => ['pages' => ['default' => false],
                                                                'users' => ['default' => true,
                                                                            'except'  => ['login', 'register', 'forgottenpassword']],
                                                                'tests' => ['default' => false]
                                                                ]
                                                ],
                                'api'       => ['default' => true],
                                'sequenced' => [
                                                    'PunishmentCheck',
                                                    'HealthCheck',
                                                    'PrisonCheck'
                                               ]
                                ],
        'LoggedOut'          => ['interface' => ['default' => false,
                                                'except'  => ['users' => [
                                                                            'default' => false,
                                                                            'except'  => ['login', 'register', 'forgottenpassword']
                                                                        ],
                                                                'pages' => [
                                                                            'default' => false,
                                                                            'except'  => ['index']
                                                                            ]
                                                                ]
                                                ]
                                ],
        'DebuggingToolbar' =>  ['interface' => ['default' => (ENVIRONMENT == "development") ? true : false]]
    ]);