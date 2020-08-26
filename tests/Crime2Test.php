<?php
    use PHPUnit\Framework\TestCase;

    require dirname(__FILE__, 2) . '.\app\executables\crimes\Crime2.php';

    class Crime2Test extends TestCase
    {
        protected static $completedEndings = [];
        protected static $endings;

        public static function setUpBeforeClass (): void
        {
            self::$endings = range(1, 5);
        }

        /**
         * @dataProvider userProvider
         */
        public function testSpecificScen(...$user)
        {
            $user = (object) $user[0];
            $user->bonusesIncluded = (object) $user->bonusesIncluded;
            $exec = new Crime2($user);
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
            $expectedCount = count(self::$endings);
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
                                            "boxingSkills" => 300
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
                                            "boxingSkills" => 5000
                                        ]
                                    ]]);

            return array_merge($a, $b, $c);
        }
    }