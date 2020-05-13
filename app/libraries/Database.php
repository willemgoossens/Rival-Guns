<?php
  /*
   * PDO Database Class
   * Connect to database
   * Create prepared statements
   * Bind values
   * Return rows and results
   */
  class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    private $limit;
    private $offset;
    private $orderBy;
    private $groupBy;

    public function __construct(){
      // Set DSN
      $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
      $options = array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      );

      // Create PDO instance
      try{
        $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
      } catch(PDOException $e){
        echo '<strong>Couldn\'t connect to database.</strong><br/>';
        $this->error = $e->getMessage();
        echo $this->error;
        die();
      }
    }

    // Prepare statement with query
    public function query($sql){
      if(isset($this->groupBy))
      {
        $sql .= " GROUP BY " . $this->groupBy;
      }

      if(isset($this->orderBy))
      {
        $sql .= " ORDER BY";

        foreach($this->orderBy as $value)
        {
          $sql .= " " . $value . ",";
        }
        $sql = rtrim($sql, ",");
      }

      if(isset($this->limit))
      {
        $sql .= " LIMIT " . $this->limit;
      }

      if(isset($this->offset))
      {
        $sql .= " OFFSET " . $this->offset;
      }

      unset($this->groupBy);
      unset($this->orderBy);
      unset($this->offset);
      unset($this->limit);

      $this->stmt = $this->dbh->prepare($sql);
    }

    // Get query string
    public function getQueryString()
    {
      return $this->stmt->queryString;
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
      if(is_null($type))
      {
        switch(true)
        {
          case is_int($value):
            $type = PDO::PARAM_INT;
            break;
          case is_bool($value):
            $type = PDO::PARAM_BOOL;
            break;
          case is_null($value):
            $type = PDO::PARAM_NULL;
            break;
          default:
            $type = PDO::PARAM_STR;
        }
      }

      $this->stmt->bindValue($param, $value, $type);
    }

    // Bind array of values
    public function bindArray($array)
    {
      foreach($array as $key => $value)
      {
        switch(true){
          case is_int($value):
            $type = PDO::PARAM_INT;
            break;
          case is_bool($value):
            $type = PDO::PARAM_BOOL;
            break;
          case is_null($value):
            $type = PDO::PARAM_NULL;
            break;
          default:
            $type = PDO::PARAM_STR;
        }

        $this->stmt->bindValue($key, $value, $type);
      }
    }

    // Execute the prepared statement
    public function execute()
    {
      return $this->stmt->execute();
    }

    // Execute the prepared statement and return last inserted ID
    public function returnID()
    {
      if($this->stmt->execute())
      {
        // Return the ID
        return $this->dbh->lastInsertId();
      }
      else
      {
        return false;
      }
    }

    // Get result set as array of objects
    public function resultSet($unique = false)
    {
      $this->execute();

      if($unique)
        return $this->stmt->fetchAll(PDO::FETCH_OBJ|PDO::FETCH_UNIQUE);
      else
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get result set as array of objects
    public function resultSetArray($unique = false)
    {
      $this->execute();

      if($unique)
        return $this->stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE);
      else
        return $this->stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Get single record as object
    public function single(){
      $this->execute();
      return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get row count
    public function rowCount(){
      $this->execute();
      return $this->stmt->rowCount();
    }

    // Set offset
    public function offset(int $offset)
    {
      $this->offset = $offset;
    }

    // Set limit
    public function limit(int $limit)
    {
      $this->limit = $limit;
    }

    public function orderBy(string $variable, string $order = "ASC", array $fieldInputs = [])
    {
      if(! isset($this->orderBy))
      {
        $this->orderBy = [];
      }

      if($order == "FIELD")
      {
        $sorting = "FIELD(" . $variable . ", ";
        foreach($fieldInputs as $input)
        {
          $sorting .= " " . $input . ",";
        }
        $sorting = rtrim($sorting, ",") . ")";
        $this->orderBy[count($this->orderBy)] = $sorting;
      }
      else
      {
        $sorting = $variable . " " . $order;
        $this->orderBy[count($this->orderBy)] = $sorting;
      }
    }

    public function groupBy(string $variable)
    {
      $this->groupBy = $variable;
    }

    public function getOffset()
    {
      return $this->offset;
    }

    public function getLimit()
    {
      return $this->limit;
    }

    public function getOrderBy()
    {
      return $this->orderBy;
    }
  }
