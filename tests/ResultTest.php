<?php
	namespace DaybreakStudios\Analyst\Tests;

	use DaybreakStudios\Analyst\Experiment;
	use DaybreakStudios\Analyst\Observation;
	use DaybreakStudios\Analyst\Result;

	class ResultTest extends \PHPUnit_Framework_TestCase {
		private static $experiment;

		public static function setUpBeforeClass() {
			self::$experiment = new Experiment();
		}

		public function testResultEvaluatesObservations() {
			$a = new Observation('test-a', function() { return 1; });
			$b = new Observation('test-b', function() { return 1; });

			$this->assertTrue($a->compare($b));

			$result = new Result(self::$experiment, $a, [ $a, $b ]);

			$this->assertTrue($result->isMatching());
			$this->assertEquals([], $result->getMismatches());

			$x = new Observation('test-x', function() { return 1; });
			$y = new Observation('test-y', function() { return 2; });
			$z = new Observation('test-z', function() { return 3; });

			$result = new Result(self::$experiment, $x, [ $x, $y, $z ]);

			$this->assertFalse($result->isMatching());
			$this->assertEquals([ $y, $z ], $result->getMismatches());
		}

		public function testResultHasNoMismatchWhenControlOnly() {
			$a = new Observation('test-a', function() { return 1; });

			$result = new Result(self::$experiment, $a, [ $a ]);

			$this->assertTrue($result->isMatching());
		}

		public function testResultPartitionsCandidates() {
			$a = new Observation('test-a', function() { return 1; });
			$b = new Observation('test-b', function() { return 2; });
			$c = new Observation('test-c', function() { return 1; });

			$result = new Result(self::$experiment, $a, [ $a, $b, $c ]);

			$this->assertTrue($this->containsAll([ $b, $c ], $result->getCandidates()));
		}

		public function testResultPartitionsControl() {
			$a = new Observation('test-a', function() { return 1; });
			$b = new Observation('test-b', function() { return 2; });
			$c = new Observation('test-c', function() { return 1; });

			$result = new Result(self::$experiment, $a, [ $a, $b, $c ]);

			$this->assertEquals($a, $result->getControl());
		}

		public function testResultGroupsAllObservations() {
			$a = new Observation('test-a', function() { return 1; });
			$b = new Observation('test-b', function() { return 2; });
			$c = new Observation('test-c', function() { return 1; });

			$result = new Result(self::$experiment, $a, [ $a, $b, $c ]);

			$this->assertTrue($this->containsAll([ $a, $b, $c ], $result->getObservations()));
		}

		public function testResultPartitionsMismatchingObservations() {
			$a = new Observation('test-a', function() { return 1; });
			$b = new Observation('test-b', function() { return 1; });
			$c = new Observation('test-c', function() { return 2; });

			$result = new Result(self::$experiment, $a, [ $a, $b, $c ]);

			$this->assertEquals([ $c ], $result->getMismatches());
		}

		private function containsAll($array, $compare) {
			$array = array_unique($array, SORT_REGULAR);

			if (sizeof($array) !== sizeof($compare))
				return false;

			foreach ($array as $e)
				if (!in_array($e, $compare))
					return false;

			return true;
		}
	}