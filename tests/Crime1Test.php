<?php
    use PHPUnit\Framework\TestCase;

    require dirname(__FILE__, 2) . '.\app\executables\crimes\Crime1.php';

    class Crime1Test extends TestCase
    {
        protected static $completedEndings = [];
        protected static $endings;

        public static function setUpBeforeClass (): void
        {
            self::$endings = range(1, 4);
        }

        /**
         * @dataProvider userProvider
         */
        public function testSpecificScen(...$user)
        {
            $user = (object) $user[0];
            $user->bonusesIncluded = (object) $user->bonusesIncluded;
            $exec = new Crime1($user);
            $exec->init();

            $summary = $exec->returnSummary();

            $this->assertIsBool($summary["arrested"]);
            
            $this->assertIsArray($summary["userRewards"]);
            $this->assertIsArray($summary["crimeRecords"]);
            $this->assertIsArray($summary["storyline"]);
            $this->assertIsArray($summary["items"]);

            $this->assertIsInt($summary["testPHPUnitEnding"]);
            if(! in_array($summary["testPHPUnitEnding"], self::$completedEndings) )
            {
                array_push(self::$completedEndings, $summary["testPHPUnitEnding"]);
            }
            
        }

        /**
         * @depends testSpecificScen
         */
        public function testRanAll()
        {
            $this->assertEqualsCanonicalizing(self::$completedEndings, self::$endings);
        }

        public function userProvider()
        {
            $a = array_fill(0, 10, [[
                                        "health" => 100,
                                        "energy" => 100,
                                        "strengthSkills" => 50,
                                        "charismaSkills" => 50,
                                        "boxingSkills" => 50,
                                        "bonusesIncluded" => 
                                        [
                                            "strengthSkills" => 50,
                                            "charismaSkills" => 50,
                                            "boxingSkills" => 50
                                        ]
                                    ]]);
            $b = array_fill(0, 10, [[
                                        "health" => 100,
                                        "energy" => 100,
                                        "strengthSkills" => 50,
                                        "charismaSkills" => 50,
                                        "boxingSkills" => 50,
                                        "bonusesIncluded" => 
                                        [
                                            "strengthSkills" => 0,
                                            "charismaSkills" => 0,
                                            "boxingSkills" => 100
                                        ]
                                    ]]);
            $c = array_fill(0, 10, [[
                                        "health" => 100,
                                        "energy" => 100,
                                        "strengthSkills" => 50,
                                        "charismaSkills" => 50,
                                        "boxingSkills" => 50,
                                        "bonusesIncluded" => 
                                        [
                                            "strengthSkills" => 0,
                                            "charismaSkills" => 0,
                                            "boxingSkills" => 1400
                                        ]
                                    ]]);

            return array_merge($a, $b, $c);
        }
    }