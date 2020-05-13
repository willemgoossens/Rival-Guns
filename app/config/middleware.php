<?php
    /*************************************
    *
    *   MIDDLEWARE
    *
    **************************************/

    // The page to which LOGGEDIN middleware redirects
    define('MIDDLEWARE_LOGGEDIN_REDIRECT', 'Users/login');
    // The page to which LOGGEDOUT middleware redirects
    define('MIDDLEWARE_LOGGEDOUT_REDIRECT', 'posts');
    // The page to which PRISONCHECK middleware redirects if you're not in prision
    define('MIDDLEWARE_PRISONCHECK_INPRISONREDIRECT', 'prison/inside');
    // The page to which PRISONCHECK middleware redirects if you're not in prision
    define('MIDDLEWARE_PRISONCHECK_OUTPRISONREDIRECT', 'prison/');
    // The page to which PRISONCHECK middleware redirects if you're not in prision
    define('MIDDLEWARE_PUNISHMENTCHECK_PERMANENTREDIRECT', 'punishments/permanent');
    // The page to which PRISONCHECK middleware redirects if you're not in prision
    define('MIDDLEWARE_PUNISHMENTCHECK_TEMPORARYREDIRECT', 'punishments/temporary');
    // The page to which PRISONCHECK middleware redirects if you're not in prision
    define('MIDDLEWARE_PUNISHMENTCHECK_NOPUNISHMENTREDIRECT', 'posts');

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