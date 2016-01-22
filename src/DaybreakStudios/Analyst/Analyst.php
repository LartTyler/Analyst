<?php
	namespace DaybreakStudios\Analyst;

	use DaybreakStudios\Analyst\Exception\BehaviorMissingException;
	use DaybreakStudios\Analyst\Exception\BehaviorNotUniqueException;

	class Analyst implements AnalystInterface {
		private $name;

		private $behaviors = [];

		/**
		 * Analyst constructor.
		 *
		 * @param string $name
		 */
		public function __construct($name = 'experiment') {
			$this->name = $name;
		}

		public function control(callable $control) {
			return $this->candidate($control, 'control');
		}

		public function candidate(callable $candidate, $name = 'candidate') {
			if (isset($this->behaviors[$name]))
				throw new BehaviorNotUniqueException($name);

			$this->behaviors[$name] = $candidate;

			return $this;
		}

		public function isEnabled() {
			return true;
		}

		public function publish() {}

		public function run($name = null) {
			$name = $name ?: 'control';

			if (!isset($this->behaviors[$name]))
				throw new BehaviorMissingException($name);

			$control = $this->behaviors[$name];

			if (sizeof($this->behaviors) === 1 || !$this->isEnabled())
				return call_user_func($control);

			$observations = [];

			foreach (ArrayUtil::shuffle($this->behaviors) as $name => $behavior)
				$observations[] = new Observation($name, $behavior);
		}

		public static function create() {
			return new Analyst();
		}
	}