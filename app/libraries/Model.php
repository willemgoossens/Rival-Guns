<?php
    namespace App\Libraries;
    /****************************
    *
    *
    * This file contains the base model
    *
    *
    *****************************/
    abstract class Model 
    {
        /* Variables */
        protected $db;

        private $tableName = NULL;
        private $tableColumns = [];
        protected $dateTimeColumns = [];



        public function __construct()
        {
            // Completely empty, just for the sake of not getting a deprication message because our we have a method named Model
        }



        /**
         * 
         * 
         * __get
         * @param String $name
         * @return Object
         * 
         * 
         */
        public function __get(String $name): Object
        {
            if(substr($name, -5) == "Model")
            {
                $modelName = substr($name, 0, -5);
                $model = $this->model($modelName);

                $this->$name = $model;
                return $this->$name;
            }

            throw new \Exception("Uh Oh! " . $name . " is not a real variable for this class (" . get_class($this) . ")" , 1);
        }        


        /***********************
        *
        *
        * magic function Call
        * @Param String method
        * @Param Array arguments
        * @Return Mixed
        *
        *
        ************************/
        public function __call (String $method, Array $arguments)
        {
            if( substr($method, 0, 5) == "getBy" )
            {
                $methodVariables = substr($method, 5);
                return $this->getBy($methodVariables, $arguments);
            }
            elseif( substr($method, 0, 18) == "getFlaggedUniqueBy" )
            {
                $methodVariables = substr($method, 18);
                return $this->getFlaggedUniqueBy($methodVariables, $arguments);
            }
            elseif( substr($method, 0, 11) == "getSingleBy" )
            {
                $methodVariables = substr($method, 11);
                return $this->getSingleBy($methodVariables, $arguments);
            }
            elseif( substr($method, 0, 10) == "getArrayBy" )
            {
                $methodVariables = substr($method, 10);
                return $this->getArrayBy($methodVariables, $arguments);
            }
            elseif( substr($method, 0, 7) == "countBy" )
            {
                $methodVariables = substr($method, 7);
                return $this->countBy($methodVariables, $arguments);
            }
            elseif( substr($method, 0, 8) == "existsBy" )
            {
                $methodVariables = substr($method, 8);
                return $this->existsBy($methodVariables, $arguments);
            }

            throw new \Exception("Uh Oh! " . $method . " is not a real function for this class (" . get_class($this) . ")" , 1);
        }


        /**
        *
        *
        * exists By
        * @Param String method
        * @Param Array arguments
        * @Return Bool
        *
        *
        */
        private function existsBy(String $method, Array $arguments): Bool
        {
            $this->prepareDynamicQuery($method, $arguments);

            if( $this->db->single() )
            {
                return true;
            }
            else
            {
                return false;
            }
        }


        /**
        *
        *
        * count By
        * @Param String method
        * @Param Array arguments
        * @Return Int
        *
        *
        */
        private function countBy (String $method, Array $arguments): Int
        {
            $this->prepareDynamicQuery($method, $arguments);

            return $this->db->rowCount();
        }


        /**
        *
        *
        * getSingleBy
        * @Param String method
        * @Param Array arguments
        * @Return Mixed
        *
        *
        */
        private function getSingleBy (String $method, Array $arguments)
        {
            $this->prepareDynamicQuery($method, $arguments);
            $object = $this->db->single();

            if( is_object( $object ) )
            {
                $array = $this->checkForDateTimes( $object );
            }


            return $object;
        }


        /**
        *
        *
        * getFlaggedUniqueBy
        * @Param String method
        * @Param Array arguments
        * @Return Null|Array
        *
        *
        */
        private function getFlaggedUniqueBy(string $method, array $arguments): ?Array
        {
            $this->prepareDynamicQuery($method, $arguments);

            $array = $this->db->resultSet(true);
            if( is_array( $array ) )
            {
                $array = $this->checkForDateTimesArray( $array );
            }


            return $array;
        }

        /**
        *
        *
        * getBy
        * @Param String method
        * @Param Array arguments
        * @Return Array
        *
        *
        */
        private function getBy(String $method, Array $arguments): ?Array
        {
            $this->prepareDynamicQuery($method, $arguments);

            $array = $this->db->resultSet();
            if( is_array( $array ) )
            {
                $array = $this->checkForDateTimesArray( $array );
            }

            return $array;
        }


        /**
        *
        *
        * get Array By
        * @Param String method
        * @Param Array arguments
        * @Return Array
        *
        *
        */
        private function getArrayBy(String $method, Array $arguments): ?Array
        {
            $this->prepareDynamicQuery($method, $arguments);

            return $this->db->resultSetArray();
        }


        /**
        *
        *
        * prepareDynamicQuery
        * @Param String method
        * @Param Array arguments
        * @Return Void
        *
        *
        */
        private function prepareDynamicQuery (String $method, Array $arguments): Void
        {
            // Strip the underscores just to do some checkups
            $methodStrippedFromUnderscores = str_replace("_", "", $method);
            $onlyColumns = preg_split("/(And(?=[A-Z]))|(Or(?=[A-Z]))|(Not(?=[A-Z]))/", $methodStrippedFromUnderscores, -1, PREG_SPLIT_NO_EMPTY);
            $onlyColumns = array_map('lcfirst', $onlyColumns);
            $different = array_diff($onlyColumns, $this->tableColumns);
            if( ! empty($different) )
            {
                throw new \Exception("One of or more of the columns (" . print_r($different) . ") that you try to select by doesn't exist in database table: " . $this->tableName, 1);
            }

            if( count($onlyColumns) > count($arguments) )
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
            do 
            {
                if($selectors[$i] == "_")
                {
                    if(
                        empty($whereString)
                        || substr($whereString, -1) == "("
                    ) {
                        $whereString .= "(";
                    }
                    elseif( substr($whereString, -1) == ")" )
                    {
                        $whereString .= ")";
                    }
                    elseif(
                        $selectors[$i - 1] == "and"
                        ||    $selectors[$i - 1] == "or"
                    ) {
                        $whereString .= "(";
                    }
                    elseif(
                        ! isset($selectors[$i + 1])
                        || $selectors[$i + 1] == "and"
                        || $selectors[$i + 1] == "or"
                        || $selectors[$i + 1] == "_"
                    ) {
                        $whereString .= ")";
                    }
                    else
                    {
                        throw new \Exception("Uh oh, it seems like something went wrong with your Dynamic Function in " . get_class($this), 1);
                    }
                    continue;
                }
                elseif( $selectors[$i] == "and" )
                {
                    $whereString .= " AND ";
                    continue;
                }
                elseif( $selectors[$i] == "or" )
                {
                    $whereString .= " OR ";
                    continue;
                }
                else
                {
                    if( $selectors[$i] == "not" )
                    {
                        $notQueryAddition = " NOT";
                        $i++;

                        if( is_bool($selectorValues[$valuesIteration]) )
                        {
                            $notQueryAddition = "!";
                        }
                    }
                    else
                    {
                        $notQueryAddition = "";
                    }

                    if( $selectorValues[$valuesIteration] === NULL )
                    {
                        $whereString .= $selectors[$i] . " IS" . $notQueryAddition . " NULL";
                    }
                    elseif( is_bool($selectorValues[$valuesIteration]) )
                    {
                        $whereString .= $selectors[$i] . " " . $notQueryAddition . "= " . ($selectorValues[$valuesIteration] ? 1 : 0) . " ";
                    }
                    else
                    {
                        if( is_array($selectorValues[$valuesIteration]) )
                        {
                            $repeater = count($selectorValues[$valuesIteration]) - 1;
                            if( $repeater < 0 )
                            {
                                $placeholders = 'NULL';
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
            } 
            while ( $i++ < count($selectors) - 1 );


            if( ! empty($returnVariables) )
            {
                if( ! in_array("id", $returnVariables) )
                {
                    array_unshift($returnVariables, "id");
                }

                $returnVariables = implode(",", $returnVariables);
            }
            else 
            {
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


        /**
        *
        *
        * set the Table name for the current model
        * @Param String table Name
        * @Return Void
        *
        */
        protected function setTableName( String $value ): Void
        {
            $this->tableName = $value;

            $this->db->query("SELECT 1 FROM " . $this->tableName . " LIMIT 1");
            // Check if the table exists
            if($this->db->execute())
            {
                // Get a list of the table Columns
                $this->setTableColumns();
            }
        }


        /**
         * 
         * 
         * getTableName
         * @return String tableName
         * 
         * 
         */
        protected function getTableName(): String
        {
            return $this->tableName;
        }

        
        /**
         * 
         * 
         * setTableColumns
         * @return Void
         * 
         * 
         */
        protected function setTableColumns(): Void
        {
            $this->db->query("SELECT `COLUMN_NAME`
                                  FROM `INFORMATION_SCHEMA`.`COLUMNS`
                                  WHERE `TABLE_SCHEMA`= :dbName
                                      AND `TABLE_NAME`= :tableName");

            $this->db->bind(':dbName', DB_NAME);
            $this->db->bind(':tableName', $this->tableName);

            $columnNamesOriginal = $this->db->resultSet();

            $this->tableColumns = array_map(
                                      function($array)
                                      {
                                          return $array->COLUMN_NAME;
                                      }, $columnNamesOriginal);
        }


        /**
         * 
         * Model
         * @param String modelName
         * @return Object
         * 
         * 
         */
        protected function model(String $model): Object
        {
            $model = MODEL_NAMESPACE . $model;
            return new $model();
        }


        /**
         * 
         * 
         * Insert
         * @param Array values
         * @param Bool returnId
         * @return Mixed
         * 
         * 
         */
        public function insert(array $values, bool $returnId = false)
        {
            $columnsString = "";
            $valuesString = "";

            foreach( $values as $key => $value )
            {
                $columnsString .= $key . ", ";
                $valuesString    .= ":" . $key . ", ";
            }
            $columnsString = rtrim($columnsString, ", ");
            $valuesString = rtrim($valuesString, ", ");

            $this->db->query("INSERT INTO " . $this->tableName . "
                                                (" . $columnsString . ")
                                                VALUES (" . $valuesString . ")");

            $this->db->bindArray($values);

            if( $returnId )
            {
                return $this->db->returnId();
            }
            else 
            {
                return $this->db->execute();
            }
        }


        /**
         * 
         * 
         * insertArrays
         * @param Array rows
         * @return Bool
         * 
         * 
         */
        public function insertArray(Array $rows): Bool
        {
            $firstKey = key($rows);
            // Check if it's a multilevel array
            if( ! is_array($rows[$firstKey]) )
            {
                $rows = ["0" => $rows];
            }

            $columnsString = "";
            $totalValuesString = "";
            $valuesArray = [];

            foreach( $rows[$firstKey] as $key => $value )
            {
                $columnsString .= $key . ", ";
            }
            $columnsString = rtrim($columnsString, ", ");

            foreach( $rows as $values )
            {
                $valuesString = "(";

                foreach( $values as $key => $value )
                {
                    $valuesString    .= "? , ";
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
        * @param Int Id
        * @param Array values
        * @param Bool
        *
        *
        ***/
        public function updateById (Int $id, Array $values): Bool
        {
            $valuesQuery = "";

            foreach( $values as $key => $value )
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


        /**
         * 
         * 
         * deleteById
         * @param Int Id
         * @return Bool
         * 
         * 
         */
        public function deleteById(int $id): Bool
        {
            $this->db->query("DELETE FROM
                                " . $this->tableName. "
                                WHERE id = :id
                                LIMIT 1");

            $this->db->bind(":id", $id);

            $return = $this->db->execute();

            return $return;
        }


        /**
         * 
         * 
         * Count the total rows for model
         * @return Int
         * 
         * 
         */
        public function count(): int
        {
            $this->db->query("SELECT id
                                FROM " . $this->tableName);

            $return = $this->db->rowCount();

            return $return;
        }


        /**
         * 
         * 
         * getAll
         * @param Bool Unique [Optional]
         * @return Array
         * 
         * 
         */
        public function get(bool $unique = false): Array
        {
            $this->db->query("SELECT *
                                FROM " . $this->tableName);

            $return = $this->db->resultSet($unique);

            return $return;
        }


        /**
         * 
         * 
         * checkForDateTimes
         * @param Object $object
         * @return Object
         * 
         * 
         */
        public function checkForDateTimes( Object $object ): Object
        {
            foreach( $this->dateTimeColumns as $column )
            {
                if( isset( $object->$column ) )
                {
                    $object->$column = new \DateTime( $object->$column );
                }
            }

            return $object;
        }


        /**
         * 
         * 
         * checkForDateTimesArray
         * @param Array $array
         * @return Array
         * 
         * 
         */
        public function checkForDateTimesArray( Array $array ): Array
        {
            if( 
                empty( $array )
                || empty ( $this->dataTimeColums )
            ) {
                return $array;
            }

            foreach( $array as &$object )
            {
                $object = $this->checkForDateTimes( $object );
            }

            return $array;
        }



        // -------------------------------------------------------
        //    UNDERNEATH ARE THE DATABASE HELPER FUNCTION
        // -------------------------------------------------------

        /**
         * 
         * 
         * Limit
         * @param Int limit
         * @return Self
         * 
         * 
         */
        public function limit (int $limit): Self
        {
            $this->db->limit($limit);

            return $this;
        }

        /**
         * 
         * 
         * getLimit
         * @return Int
         * 
         * 
         */
        public function getLimit (): ?Int
        {
            return $this->db->getLimit();
        }


        /**
         * 
         * 
         * Offset
         * @param Int offset
         * @return Self
         * 
         * 
         */
        public function offset (int $offset): Self
        {
            $this->db->offset($offset);

            return $this;
        }

        /**
         * 
         * 
         * GetOffset
         * @return Int
         * 
         * 
         */
        public function getOffset(): ?Int
        {
            return $this->db->getOffset();
        }

        /**
         * 
         * 
         * Limit
         * @param Mixed variables
         * @return Self
         * 
         * 
         */
        public function orderBy (...$variables): Self
        {
            $this->db->orderBy(...$variables);

            return $this;
        }


        /**
         * 
         * 
         * getOrderBy
         * @return Array
         * 
         * 
         */
        public function getOrderBy (): ?Array
        {
            return $this->db->getOrderBy();
        }


        /**
         * 
         * 
         * groupBy
         * @param String Group By
         * @return Self
         * 
         * 
         */
        public function groupBy (string $groupBy): Self
        {
            $this->db->groupBy($groupBy);

            return $this;
        }
}
