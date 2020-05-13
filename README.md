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
 The models all extend the `Model` class. The `Model` class makes use of the PHP magic functions to avoid to amount of queries that have to be written. (If you want to use the database anyways, it's stored in the `$db`-variable of each model.)
 
 For example:<br/>
 `$this->userModel->getById(0);`<br/>
 Will perform this query:<br/>
 `SELECT * FROM users WHERE id IN [0]`<br/>
 
 
 
 Or this:<br/>
  `$this->userModel->getByIdAndName([0, 1], 'test');`<br/>
 Will perform this query:<br/>
 `SELECT * FROM users WHERE id IN [0, 1] AND name IN ['test']`<br/>
 
 
 Or this:<br/>
  `$this->userModel->getByIdOrNotName([0, 1], 'test');`<br/>
 Will perform this query:<br/>
 `SELECT * FROM users WHERE id IN [0, 1] OR name NOT IN ['test']`<br/>
 
 
 
 In case you would set throw in a boolean or `null` instead of an string of integer, the output would change to `variable = ?value` or `variable IS NULL`.
 
 
 
 If you want to go really overboard with the smart queries, you can also include parentheses:<br/>
  `$this->userModel->getByIdAnd_NameOrName2_([0], 'test', ['test2', 'test3']);`<br/>
  Which will perform the following query<br/>
  `SELECT * FROM users WHERE id IN [0] AND (name IN ['test'] OR name2 IN ['test2', 'test3'])`
  
  
  
  `getBy...()` will return an array of objects. But there also are:
  - `existsBy...()` which will return true or false.
  - `countBy...()` which will return an integer.
  - `getSingleBy...()` which will return as single object  
  - `getArrayBy...()` which will return an array of arrays.
  - `getFlaggedUniqueBy...()` which will an array of objects. But in this case, the keys are a unique identifier from the database. For example the users' ids.
 
 
 
 Do you want to select just several variables and not all of them? Just add column names after the input values.<br/>
 `$this->userModel->getById(0, 'name', 'email');`<br/>
 Will perform this query:<br/>
 `SELECT name, email FROM users WHERE id IN [0]`<br/>
 
 
 
 **On top of that**, you can also add limit, offset, groupBy<br/>
  `$this->userModel->limit(1)->offset(3)->groupBy('email')->getById(0);`<br/>
 Will perform this query:<br/>
 `SELECT * FROM users WHERE id IN [0] LIMIT 1 OFFSET 3 GROUP BY email`<br/>
 And of course, orderBy:<br/>
```
$this->userModel->orderBy('variable1', 'DESC')
                ->orderBy('variable2', 'ASC')
                ->orderBy('variable3', 'FIELD', [0,1,2])
                ->getById(0);
 ```
 Will perform this query:<br/>
 `SELECT * FROM users WHERE id IN [0] ORDER BY variable1 DESC, variable2 ASC, FIELD(variable3, 0,1,2)`
