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

		public function testResultPartitionsMismatchingObservations() {
			$a = new Observation('test-a', function() { return 1; });
			$b = new Observation('test-b', function() { return 1; });
			$c = new Observation('test-c', function() { return 2; });

			$result = new Result(self::$experiment, $a, [ $a, $b, $c ]);

			$this->assertEquals([ $c ], $result->getMismatches());
		}
	}