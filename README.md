# Rival-Guns

**Welcome to the Rival Guns repository!** 
Rival Guns is a text-based MMORPG in which the player is a criminal who has to fight his way up in the underworld.

Rival Guns is merely a project to improve my PHP skills. It's based on an PHP MVC tutorial that I followed. I extended to original code with extra helpers, middleware functionalities as well as routing.
The frontend uses Bootstrap 4 and jQuery.
Note: it's merely in an early stage and far from completion. Check out the code and feel free to provide some feedback.
___

## Setting up the game

It's quite easy. Import the sql file in your database and edit the `URL_ROOT` and `DB` settings in the Config file in `app/config/system.php`
___

## Logging in
The attached database already contains 3 accounts that you can use to log in. Otherwise you can create your own account.
1. Admin
  - *e-mail:* admin@test.com
  - *password:* password
2. Testaccount1
  - *e-mail:* test1@test.com
  - *password:* password
3. Testaccount2
  - *e-mail:* test2@test.com
  - *password:* password
___
 
 ## Good to know!
 The models all extend the `Model` class. It has been designed in such a way that it uses PHP's magic functions.
 
 For example:
 `$this->userModel->getById(0);`
 Will perform this query:
 `SELECT * FROM users WHERE id IN [0]`
