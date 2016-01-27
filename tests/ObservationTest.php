<?php
	namespace DaybreakStudios\Analyst\Tests;

	use DaybreakStudios\Analyst\Experiment;
	use DaybreakStudios\Analyst\Observation;
	use PHPUnit_Framework_TestCase;

	class ObservationTest extends PHPUnit_Framework_TestCase {
		public function testObservationKnowsItsName() {
			$ob = new Observation('test-observation', function() { return 0; });

			$this->assertEquals('test-observation', $ob->getName());
		}

		public function testObservationExecutesAndRecordsBlock() {
			$ob = new Observation('test', function() {
				usleep(100000);

				return 'test';
			});

			$this->assertEquals('test', $ob->getResult());
			$this->assertFalse($ob->hasException());
			$this->assertEquals(0.1, $ob->getDuration(), '', 0.01);
		}

		public function testObservationStashesExceptions() {
			$ob = new Observation('test', function() {
				throw new \Exception('exception');
			});

			$this->assertTrue($ob->hasException());
			$this->assertEquals('exception', $ob->getException()->getMessage());
			$this->assertNull($ob->getResult());
		}

		public function testObservationComparesExceptionMessages() {
			$a = new Observation('test-a', function() {
				throw new \Exception('error');
			});

			$b = new Observation('test-b', function() {
				throw new \Exception('error');
			});

			$this->assertTrue($a->compare($b));

			$x = new Observation('test-x', function() {
				throw new \Exception('error');
			});

			$y = new Observation('test-y', function() {
				throw new \Exception('ERROR');
			});

			$this->assertFalse($x->compare($y));
		}

		public function testObservationComparesExceptionClasses() {
			$x = new Observation('test-x', function() {
				throw new \InvalidArgumentException('error');
			});

			$y = new Observation('test-y', function() {
				throw new \OutOfBoundsException('error');
			});

			$z = new Observation('test-z', function() {
				throw new \InvalidArgumentException('error');
			});

			$this->assertTrue($x->compare($z));
			$this->assertFalse($x->compare($y));
		}

		public function testObservationUsesComparator() {
			$a = new Observation('test-a', function() {
				return 1;
			});

			$b = new Observation('test-b', function() {
				return '1';
			});

			$this->assertFalse($a->compare($b));
			$this->assertTrue($a->compare($b, function($a, $b) {
				return $a == $b;
			}));
		}
	}