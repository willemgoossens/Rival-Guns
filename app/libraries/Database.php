<?php
    /*
    * PDO Database Class
    * Connect to database
    * Create prepared statements
    * Bind values
    * Return rows and results
    */
    class Database 
    {
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

        public function __construct()
        {
            // Set DSN
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            // Create PDO instance
            try
            {
                $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            } 
            catch( PDOException $e )
            {
                echo '<strong>Couldn\'t connect to database.</strong><br/>';
                $this->error = $e->getMessage();
                echo $this->error;
                die();
            }
        }

        /**
         * 
         * 
         * query [Prepare the query]
         * @param String sql [the query]
         * @return Void
         * 
         * 
         */
        public function query($sql): Void
        {
            if( isset($this->groupBy) )
            {
                $sql .= " GROUP BY " . $this->groupBy;
            }

            if( isset($this->orderBy) )
            {
                $sql .= " ORDER BY";

                foreach($this->orderBy as $value)
                {
                    $sql .= " " . $value . ",";
                }
                $sql = rtrim($sql, ",");
            }

            if( isset($this->limit) )
            {
                $sql .= " LIMIT " . $this->limit;
            }

            if( isset($this->offset) )
            {
                $sql .= " OFFSET " . $this->offset;
            }

            unset($this->groupBy);
            unset($this->orderBy);
            unset($this->offset);
            unset($this->limit);

            $this->stmt = $this->dbh->prepare($sql);
        }

        
        /**
         * 
         * 
         * getQueryString
         * @return String queryString
         * 
         * 
         */
        public function getQueryString(): String
        {
            return $this->stmt->queryString;
        }

        
        /** 
         * 
         * 
         * Bind
         * @param String parameter name
         * @param value
         * @param Int data_type
         * @return Void
         * 
         * 
        */
        public function bind(String $param, $value, Int $type = null): Void
        {
            if( is_null($type) )
            {
                switch( true )
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

        
        /**
         * 
         * 
         * bindArray [of values]
         * @param Array array
         * @return Void
         * 
         * 
         */
        public function bindArray (Array $array): Void
        {
            foreach( $array as $key => $value )
            {
                switch( true )
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

                $this->stmt->bindValue($key, $value, $type);
            }
        }


        /**
         * 
         * 
         * Execute the statement
         * @return Mixed result
         * 
         * 
         */
        public function execute ()
        {
            return $this->stmt->execute();
        }

        
        /**
         * 
         * 
         * returnId
         * @return Mixed result
         * 
         * 
         */
        public function returnID ()
        {
            if( $this->stmt->execute() )
            {
                // Return the ID
                return $this->dbh->lastInsertId();
            }
            else
            {
                return false;
            }
        }

        
        /**
         * 
         * 
         * resultSet
         * @param Bool unique
         * @return Array
         * 
         * 
         */
        public function resultSet (bool $unique = false): ?Array
        {
            $this->execute();

            if( $unique )
            {
                return $this->stmt->fetchAll(PDO::FETCH_OBJ|PDO::FETCH_UNIQUE);
            }
            else 
            {              
                return $this->stmt->fetchAll(PDO::FETCH_OBJ);
            }
        }

        
        /**
         * 
         * 
         * return a Result set of arrays
         * @param Bool Unique
         * @return Array
         * 
         * 
         */
        public function resultSetArray (bool $unique = false): ?Array
        {
            $this->execute();

            if( $unique )
            {
                return $this->stmt->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE);
            }
            else
            {
                return $this->stmt->fetchAll(PDO::FETCH_COLUMN);
        
            }
        }

        
        /**
         * 
         * 
         * getSingle
         * @return Mixed
         * 
         * 
         */
        public function single ()
        {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        }

        
        /**
         * 
         * 
         * rowCount
         * @return Int counter
         * 
         * 
         */
        public function rowCount(): Int
        {
            $this->execute();
            return $this->stmt->rowCount();
        }

        
        /**
         * 
         * 
         * Offset
         * @param Int offset
         * @return Void
         * 
         * 
         */
        public function offset (Int $offset): Void
        {
            $this->offset = $offset;
        }

        
        /**
         * 
         * 
         * Limit
         * @param Int limit
         * @return Void
         * 
         * 
         */
        public function limit(Int $limit): Void
        {
            $this->limit = $limit;
        }

        
        /**
         * 
         * 
         * orderBy
         * @param String variable
         * @param String order
         * @param Array fieldInputs [the input in case you sort by field]
         * @return Void
         * 
         * 
         */
        public function orderBy (String $variable, String $order = "ASC", Array $fieldInputs = []): Void
        {
          if( ! isset($this->orderBy) )
          {
              $this->orderBy = [];
          }

          if( $order == "FIELD" )
          {
              $sorting = "FIELD(" . $variable . ", ";
              foreach( $fieldInputs as $input )
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

        
        /**
         * 
         * 
         * Group by
         * @param String Variable
         * @return Void
         * 
         * 
         */
        public function groupBy (String $variable): Void
        {
            $this->groupBy = $variable;
        }

        
        /**
         * 
         * 
         * getOffset
         * @return Int offset
         * 
         * 
         */
        public function getOffset (): ?Int
        {
            return $this->offset;
        }

        
        /**
         * 
         * 
         * Limit
         * @return Int limit
         * 
         * 
         */
        public function getLimit (): ?Int
        {
            return $this->limit;
        }


        /**
         * 
         * 
         * get Order By
         * @return Array order
         * 
         * 
         */
        public function getOrderBy(): ?Array
        {
          return $this->orderBy;
        }
    }
