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

			$this->duration = (microtime(true) - $start) * 1000;
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
	}