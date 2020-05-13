<?php
  /****************************
  *
  *
  * This file contains the base model
  *
  *
  *****************************/
  class Model {
    /* Variables */
    protected $db;

    private $tableName = NULL;
    private $tableColumns = [];

    /*
    ['name'] => [
      'relationTable' =>
      'currentKey' =>
      'foreignKey' = >
      'connectionTable' =>
      'model' = >
    ]

    */
    private $children = [];
    private $parents = [];

    /***********************
    *
    *
    * magic function Call
    * @PARAM: method
    * @PARAM: arguments
    *
    *
    ************************/
    public function __call($method, $arguments)
    {

        if(substr($method, 0, 5) == "getBy")
        {
          $methodVariables = substr($method, 5);
          return $this->getBy($methodVariables, $arguments);
        }
        elseif(substr($method, 0, 18) == "getFlaggedUniqueBy")
        {
          $methodVariables = substr($method, 18);
          return $this->getFlaggedUniqueBy($methodVariables, $arguments);
        }
        elseif(substr($method, 0, 11) == "getSingleBy")
        {
          $methodVariables = substr($method, 11);
          return $this->getSingleBy($methodVariables, $arguments);
        }
        elseif(substr($method, 0, 10) == "getArrayBy")
        {
          $methodVariables = substr($method, 10);
          return $this->getArrayBy($methodVariables, $arguments);
        }
        elseif(substr($method, 0, 7) == "countBy")
        {
          $methodVariables = substr($method, 7);
          return $this->countBy($methodVariables, $arguments);
        }
        elseif(substr($method, 0, 8) == "existsBy")
        {
          $methodVariables = substr($method, 8);
          return $this->existsBy($methodVariables, $arguments);
        }

        throw new \Exception("Uh Oh! " . $method . " is not a real function for this class (" . get_class($this) . ")" , 1);

    }

    /***********************
    *
    *
    * exists By
    * @PARAM: string method
    * @PARAM: arguments
    *
    *
    ************************/
    private function existsBy(string $method, array $arguments)
    {
      $this->prepareDynamicQuery($method, $arguments);

      if($this->db->single())
      {
        return true;
      }
      else
      {
        return false;
      }
    }

    /***********************
    *
    *
    * count By
    * @PARAM: string method
    * @PARAM: arguments
    *
    *
    ************************/
    private function countBy(string $method, array $arguments)
    {
      $this->prepareDynamicQuery($method, $arguments);

      return $this->db->rowCount();
    }

    /***********************
    *
    *
    * get single object by
    * @PARAM: string method
    * @PARAM: arguments
    *
    *
    ************************/
    private function getSingleBy(string $method, array $arguments)
    {
      $this->prepareDynamicQuery($method, $arguments);

      return $this->db->single();
    }

    /***********************
    *
    *
    * get array of objects with unique value as first key (most often id) (PDO::FETCH_UNIQUE)
    * @PARAM: string method
    * @PARAM: arguments
    *
    *
    ************************/
    private function getFlaggedUniqueBy(string $method, array $arguments)
    {
      $this->prepareDynamicQuery($method, $arguments);

      return $this->db->resultSet(true);
    }

    /***********************
    *
    *
    * get array of objects
    * @PARAM: string method
    * @PARAM: arguments
    *
    *
    ************************/
    private function getBy(string $method, array $arguments)
    {
      $this->prepareDynamicQuery($method, $arguments);

      return $this->db->resultSet();
    }

    /***********************
    *
    *
    * get array with arrays
    * @PARAM: string method
    * @PARAM: arguments
    *
    *
    ************************/
    private function getArrayBy(string $method, array $arguments)
    {
      $this->prepareDynamicQuery($method, $arguments);

      return $this->db->resultSetArray();
    }

    /***********************
    *
    *
    * prepare Dynamic Query
    * @PARAM: string - method
    * @PARAM: array  - arguments
    *
    *
    ************************/
    private function prepareDynamicQuery(string $method, array $arguments)
    {
      // Strip the underscores just to do some checkups
      $methodStrippedFromUnderscores = str_replace("_", "", $method);
      $onlyColumns = preg_split("/(And(?=[A-Z]))|(Or(?=[A-Z]))|(Not(?=[A-Z]))/", $methodStrippedFromUnderscores, -1, PREG_SPLIT_NO_EMPTY);
      $onlyColumns = array_map('lcfirst', $onlyColumns);

      if(! empty(array_diff($onlyColumns, $this->tableColumns)))
      {
        throw new \Exception("One of or more of the columns that you try to select by doesn't exist in database table: " . $this->tableName, 1);
      }

      if(count($onlyColumns) > count($arguments))
      {
        throw new \Exception("One or more selectors doesn't have data. " . count($onlyColumns) . " selectors given but only " . count($arguments) . " input values.", 1);
      }
      // We survived the tests
      // Now split up of the entire query
      $fullSplit = preg_split("/(And(?=[A-Z]))|(Or(?=[A-Z]))|(Not(?=[A-Z]))|(_)/", $method, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
      $selectors = array_map('lcfirst', $fullSplit);

      $selectorValues = array_slice($arguments, 0, count($onlyColumns));
      $returnVariables = array_slice($arguments, count($onlyColumns));

      // We've done the dissection of the function name
      // Time to turn it into a query
      $whereString = "";
      $toBindValues = [];

      $i = 0;
      $valuesIteration = 0;
      do {
        if($selectors[$i] == "_")
        {
          if(empty($whereString)
            || substr($whereString, -1) == "(")
          {
            $whereString .= "(";
          }
          elseif(substr($whereString, -1) == ")")
          {
            $whereString .= ")";
          }
          elseif($selectors[$i - 1] == "and"
                ||  $selectors[$i - 1] == "or")
          {
            $whereString .= "(";
          }
          elseif(!isset($selectors[$i + 1])
                 || $selectors[$i + 1] == "and"
                 || $selectors[$i + 1] == "or"
                 || $selectors[$i + 1] == "_")
          {
            $whereString .= ")";
          }else
          {
            throw new \Exception("Uh oh, it seems like something went wrong with your Dynamic Function in " . get_class($this), 1);
          }
          continue;
        }
        elseif($selectors[$i] == "and")
        {
          $whereString .= " AND ";
          continue;
        }
        elseif($selectors[$i] == "or")
        {
          $whereString .= " OR ";
          continue;
        }else
        {
          if($selectors[$i] == "not")
          {
            $notQueryAddition = " NOT";
            $i++;

            if(is_bool($selectorValues[$valuesIteration]))
            {
              $notQueryAddition = "!";
            }
          }
          else
          {
            $notQueryAddition = "";
          }

          if($selectorValues[$valuesIteration] === NULL)
          {
            $whereString .= $selectors[$i] . " IS" . $notQueryAddition . " NULL";
          }
          elseif(is_bool($selectorValues[$valuesIteration]))
          {
            $whereString .= $selectors[$i] . " " . $notQueryAddition . "= " . ($selectorValues[$valuesIteration] ? 1 : 0) . " ";
          }
          else
          {
            if(is_array($selectorValues[$valuesIteration]))
            {
              $repeater = count($selectorValues[$valuesIteration]) - 1;
              if($repeater < 0)
              {
                $placeholders = '';
              }
              else
              {
                $placeholders = '?'.str_repeat(',?', $repeater);
              }
              array_push($toBindValues, ...$selectorValues[$valuesIteration]);
            }
            else
            {
              $placeholders = '?';
              array_push($toBindValues, $selectorValues[$valuesIteration]);
            }
            $whereString .= $selectors[$i] . $notQueryAddition . " IN (" . $placeholders . ") ";
          }
        }

        $valuesIteration++;
      } while ($i++ < count($selectors) - 1);


      if(! empty($returnVariables)){
        $returnVariables = implode(",", $returnVariables);
      }
      else {
        $returnVariables = "*";
      }


      $this->db->query("SELECT " . $returnVariables . "
                        FROM " . $this->tableName . "
                        WHERE " . $whereString);

      foreach($toBindValues as $key => $value)
      {
        $this->db->bind($key + 1, $value);
      }
    }


    /***********************
    *
    *
    * set the Table name for the current model
    * @PARAM: table Name
    *
    *
    ************************/
    protected function setTableName($value)
    {
      $this->tableName = $value;
      // Get a list of the table Columns
      $this->setTableColumns();
    }


    /***********************
    *
    *
    * set the children for this array
    * @PARAM: table Name
    *
    *
    ************************/
    protected function setChildren(array $children)
    {
      // check if everything is okay
      /*
      ['name'] => [
        'currentKey' =>
        'relationTable' =>
        'foreignKey' = >
        'connectionTable' =>
        'model' = >
      ]

      */
      foreach($children as $name => $child)
      {
        // connectionTable is not mandatory, only many-to-many connections have this
        if(isset($child['foreignTable'])
          && isset($child['currentKey'])
          && isset($child['foreignKey'])
          && isset($child['model'])
        ) {
          $this->children[$name] = (object) $child;
          $this->children[$name]->model = $this->loadModel($child['model']);
        }
        else
        {
          throw new \Exception("You should set up the children properly in class ". get_class($this), 1);
        }
      }
    }


    /**
     * set the parents relations of this model
     * @param array $parents
     */
    protected function setParents(array $parents)
    {
      // check if everything is okay
      /*
      ['name'] => [
        'currentKey' =>
        'relationTable' =>
        'foreignKey' = >
        'connectionTable' =>
        'model' = >
      ]

      */
      foreach($parents as $name => $parent)
      {
        // connectionTable is not mandatory, only many-to-many connections have this
        if(isset($parent['foreignTable'])
          && isset($parent['currentKey'])
          && isset($parent['foreignKey'])
          && isset($parent['model'])
        ) {
          $this->parents[$name] = (object) $parent;
          $this->parents[$name]->model = $this->loadModel($parent['model']);
        }
        else
        {
          throw new \Exception("You should set up the parents properly in class ". get_class($this), 1);
        }
      }
    }


    /***********************
    *
    *
    * return the child model
    * @PARAM: table Name
    *
    *
    ************************/
    public function child(string $child)
    {
      return $this->children[$child]->model;
    }


    /**
     * returns the parent model
     * @param  string $parent parent name
     * @return the parent model
     */
    public function parent(string $parent)
    {
      return $this->parents[$parent]->model;
    }


    /***********************
    *
    *
    * return the child model
    * @PARAM: string - name of the child class
    * @PARAM: string / int - selectorValue
    *
    *
    ************************/
    public function getManyToManyIds(string $name, $selectorValue)
    {
      if(isset($this->children[$name]->connectionTable))
      {
        $relation = $this->children[$name];
      }
      elseif(isset($this->parents[$name]->connectionTable))
      {
        $relation = $this->parents[$name];
      }
      else
      {
        return false;
      }

      $selectorName = substr($this->tableName, 0, -1) . $relation->currentKey;
      $foreignKey = substr($relation->foreignTable, 0, -1) . $relation->foreignKey;

      $this->db->query("SELECT " . $foreignKey . "
                        FROM " . $relation->connectionTable . "
                        WHERE " . $selectorName . " = :selectorValue");

      $this->db->bind(":selectorValue", $selectorValue);
      $ids = $this->db->resultSetArray();

      return $ids;
    }


    /***********************
    *
    *
    * load another model
    * @PARAM: model name
    *
    *
    ************************/
    private function loadModel(string $model)
    {
      // Require model file
      require_once APPROOT . '/models/' . $model . '.php';

      // Instatiate model
      return new $model();
    }


    /***********************
    *
    *
    * get the columns for the table of this model
    *
    *
    ************************/
    protected function setTableColumns()
    {
      $this->db->query("SELECT `COLUMN_NAME`
                        FROM `INFORMATION_SCHEMA`.`COLUMNS`
                        WHERE `TABLE_SCHEMA`= :dbName
                            AND `TABLE_NAME`= :tableName");

      $this->db->bind(':dbName', DB_NAME);
      $this->db->bind(':tableName', $this->tableName);

      $columnNamesOriginal = $this->db->resultSet();

      $this->tableColumns = array_map(function($array){
                                return $array->COLUMN_NAME;
                              }, $columnNamesOriginal);
    }


    /***********************
    *
    *
    * insert
    * @PARAM: array - values
    * @PARAM: false - return the insert id or not
    *
    *
    ************************/
    public function insert(array $values, bool $returnId = false)
    {
      $columnsString = "";
      $valuesString = "";

      foreach($values as $key => $value)
      {
        $columnsString .= $key . ", ";
        $valuesString  .= ":" . $key . ", ";
      }
      $columnsString = rtrim($columnsString, ", ");
      $valuesString = rtrim($valuesString, ", ");

      $this->db->query("INSERT INTO " . $this->tableName . "
                        (" . $columnsString . ")
                        VALUES (" . $valuesString . ")");

      $this->db->bindArray($values);

      if($returnId)
      {
        return $this->db->returnId();
      }else {
        return $this->db->execute();
      }
    }


    /***********************
    *
    *
    * insert
    * @PARAM: array values
    *
    *
    ************************/
    public function insertArray(array $rows)
    {
      $firstKey = key($rows);
      // Check if it's a multilevel array
      if(!is_array($rows[$firstKey]))
      {
        $rows = ["0" => $rows];
      }

      $columnsString = "";
      $totalValuesString = "";
      $valuesArray = [];

      foreach($rows[$firstKey] as $key => $value)
      {
        $columnsString .= $key . ", ";
      }
      $columnsString = rtrim($columnsString, ", ");

      foreach($rows as $values)
      {
        $valuesString = "(";

        foreach($values as $key => $value)
        {
          $valuesString  .= "? , ";
          $valuesArray[ count($valuesArray) + 1 ] = $value;
        }
        $valuesString = rtrim($valuesString, ", ");
        $totalValuesString .= $valuesString . "),";
      }

      $totalValuesString = rtrim($totalValuesString, ",");

      $this->db->query("INSERT INTO " . $this->tableName . "
                        (" . $columnsString . ")
                        VALUES " . $totalValuesString);

      $this->db->bindArray($valuesArray);

      return $this->db->execute();
    }


    /**
    *
    *
    * update By Id
    * @param: int id
    * @param: array values
    *
    *
    ***/
    public function updateById(int $id, array $values): bool
    {
      $valuesQuery = "";

      foreach($values as $key => $value)
      {
        $valuesQuery .= $key . " = :" . $key . ", ";
      }
      $valuesQuery = rtrim($valuesQuery, ", ");

      $this->db->query("UPDATE
                      " . $this->tableName . "
                      SET " . $valuesQuery . "
                      WHERE id = :id
                      LIMIT 1");

      $this->db->bindArray($values);
      $this->db->bind(":id", $id);

      $return = $this->db->execute();

      return $return;
    }


    /***********************
    *
    *
    * delete By Id
    * @PARAM: int id
    *
    *
    ************************/
    public function deleteById(int $id): bool
    {
      $this->db->query("DELETE FROM
                      " . $this->tableName. "
                      WHERE id = :id
                      LIMIT 1");

      $this->db->bind(":id", $id);

      $return = $this->db->execute();

      return $return;
    }


    /***********************
    *
    *
    * count Rows
    *
    *
    ************************/
    public function count(): int
    {
      $this->db->query("SELECT id
                      FROM " . $this->tableName);

      $return = $this->db->rowCount();

      return $return;
    }


    /***********************
    *
    *
    * get
    *
    *
    ************************/
    public function get(bool $unique = false)
    {
      $this->db->query("SELECT *
                      FROM " . $this->tableName);

      $return = $this->db->resultSet($unique);

      return $return;
    }




    // -------------------------------------------------------
    //  UNDERNEATH ARE THE DATABASE HELPER FUNCTION
    // -------------------------------------------------------

    /***********************
    *
    *
    * limit
    * @PARAM: int - limit
    *
    *
    ************************/
    public function limit (int $limit)
    {
      $this->db->limit($limit);

      return $this;
    }


    /***********************
    *
    *
    * getLimit
    *
    *
    ************************/
    public function getLimit()
    {
      return $this->db->getLimit();
    }


    /***********************
    *
    *
    * offset
    * @PARAM: int - offset
    *
    *
    ************************/
    public function offset (int $offset)
    {
      $this->db->offset($offset);

      return $this;
    }


    /***********************
    *
    *
    * getOffset
    *
    *
    ************************/
    public function getOffset()
    {
      return $this->db->getOffset();
    }


    /***********************
    *
    *
    * order By
    * @PARAM: string variable
    * @PARAM: optional - string - order
    *
    *
    ************************/
    public function orderBy (...$variables)
    {
      $this->db->orderBy(...$variables);

      return $this;
    }


    /***********************
    *
    *
    * getOrderBy
    *
    *
    ************************/
    public function getOrderBy ()
    {
      return $this->db->getOrderBy();
    }


    /***********************
    *
    *
    * group by
    * @PARAM: string group by
    *
    *
    ************************/
    public function groupBy (string $groupBy)
    {
      $this->db->groupBy($groupBy);

      return $this;
    }
}
