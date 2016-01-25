<?php
	namespace DaybreakStudios\Analyst;

	class Observation implements ObservationInterface {
		private $name;
		private $duration;

		private $result = null;
		private $exception = null;

		public function __construct($name, callable $behavior, $context = []) {
			$this->name = $name;

			$start = microtime(true);

			try {
				$this->result = call_user_func_array($behavior, $context ?: []);
			} catch (\Exception $e) {
				$this->exception = $e;
			}

			$this->duration = microtime(true) - $start;
		}

		public function hasException() {
			return $this->exception !== null;
		}

		public function getException() {
			return $this->exception;
		}

		public function getDuration() {
			return $this->duration;
		}

		public function getResult() {
			return $this->result;
		}

		public function getName() {
			return $this->name;
		}

		public function compare(ObservationInterface $other = null, callable $comparator = null) {
			if (!($other instanceof ObservationInterface))
				return false;

			$equal = false;
			$neitherEx = !$this->hasException() && !$other->hasException();
			$bothEx = $this->hasException() && $other->hasException();

			if ($neitherEx) {
				if ($comparator !== null)
					$equal = call_user_func($comparator, $this->getResult(), $other->getResult());
				else
					$equal = $this->getResult() == $other->getResult();
			}

			$equalEx = $bothEx &&
				get_class($this->getException()) === get_class($other->getException()) &&
				$this->getException()->getMessage() === $other->getException()->getMessage();

			return ($neitherEx && $equal) || ($bothEx && $equalEx);
		}
	}