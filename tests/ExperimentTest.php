<?php
	namespace DaybreakStudios\Analyst\Tests;

	use DaybreakStudios\Analyst\Exception\MismatchException;
	use DaybreakStudios\Analyst\Experiment;
	use DaybreakStudios\Analyst\ExperimentInterface;
	use DaybreakStudios\Analyst\ObservationInterface;

	class ExperimentTest extends \PHPUnit_Framework_TestCase {
		public function testExperimentHasDefaultName() {
			$exp = Experiment::create();

			$this->assertEquals('experiment', $exp->getName());
		}

		public function testExperimentKnowsItsName() {
			$exp = Experiment::create('test-experiment');

			$this->assertEquals('test-experiment', $exp->getName());

			return $exp;
		}

		/**
		 * @depends testExperimentKnowsItsName
		 */
		public function testExperimentIsEnabled(ExperimentInterface $experiment) {
			$this->assertTrue($experiment->isEnabled());

			return $experiment;
		}

		/**
		 * @depends testExperimentIsEnabled
		 */
		public function testExperimentHoldsDefaultContext(ExperimentInterface $experiment) {
			$this->assertNull($experiment->getContext());

			return $experiment;
		}

		/**
		 * @depends testExperimentHoldsDefaultContext
		 */
		public function testExperimentRespectsContext(ExperimentInterface $experiment) {
			$context = [
				'Tyler Lartonoix|Lord Codemonkey'
			];

			$experiment->setContext($context);

			$this->assertEquals($context, $experiment->getContext());

			return $experiment;
		}

		/**
		 * @depends testExperimentRespectsContext
		 */
		public function testExperimentRespectsCustomComparator(ExperimentInterface $experiment) {
			$comparator = function($a, $b) {
				return $a == $b;
			};

			$experiment->setComparator($comparator);

			$this->assertEquals($comparator, $experiment->getComparator());

			return $experiment;
		}

		/**
		 * @depends testExperimentRespectsCustomComparator
		 */
		public function testExperimentRespectsThrowOnMismatch(ExperimentInterface $experiment) {
			$this->assertFalse($experiment->getThrowOnMismatch());

			$experiment->setThrowOnMismatch(true);

			$this->assertTrue($experiment->getThrowOnMismatch());

			return $experiment;
		}

		/**
		 * @depends testExperimentRespectsThrowOnMismatch
		 */
		public function testExperimentAcceptsCandidates(ExperimentInterface $experiment) {
			$experiment
				->control($control = function($name) {
					$names = explode('|', $name);
					$aliases = '';

					foreach ($names as $i => $name) {
						$aliases .= $name;

						if ($i < sizeof($names) - 1)
							$aliases .= ', a.k.a. ';
					}

					return $aliases;
				})
				->candidate($candidate = function($name) {
					$aliases = strtok($name, '|');

					while ($name = strtok('|'))
						$aliases .= ', a.k.a. ' . $name;

					return $aliases;
				});

			$behaviors = $experiment->getBehaviors();

			$this->assertEquals($control, $behaviors['control']);
			$this->assertEquals($candidate, $behaviors['candidate']);

			return $experiment;
		}

		/**
		 * @depends testExperimentAcceptsCandidates
		 */
		public function testExperimentCanBeRun(ExperimentInterface $experiment) {
			$result = $experiment->run();

			$this->assertEquals('Tyler Lartonoix, a.k.a. Lord Codemonkey', $result);

			return $experiment;
		}

		/**
		 * @depends testExperimentCanBeRun
		 */
		public function testExperimentThrowsMismatchException(ExperimentInterface $experiment) {
			$experiment->candidate(function($name) {
				return 'WRONG!';
			}, 'incorrect-candidate');

			$ex = null;

			try {
				$experiment->run();
			} catch (\Exception $e) {
				$ex = $e;
			}

			$this->assertNotNull($ex);
			$this->assertInstanceOf('DaybreakStudios\\Analyst\\Exception\\MismatchException', $ex);

			return $experiment;
		}

		/**
		 * @expectedException \Exception
		 * @expectedExceptionMessage Control code failed
		 */
		public function testExperimentForwardsThrownExceptionsFromControl() {
			$experiment = Experiment::create()
				->control(function() {
					throw new \Exception('Control code failed');
				})
				->candidate(function() {
					return 'noop';
				});

			$experiment->run();
		}
	}