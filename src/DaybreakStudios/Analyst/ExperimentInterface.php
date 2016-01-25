<?php
	namespace DaybreakStudios\Analyst;

	interface ExperimentInterface {
		/**
		 * @return bool
		 */
		public function isEnabled();

		/**
		 * @param array $context an array containing the arguments, in order, that should be passed to each behavior
		 *
		 * @return $this
		 */
		public function setContext(array $context);

		/**
		 * @return array|null
		 */
		public function getContext();

		/**
		 * @param callable $comparator
		 *
		 * @return $this
		 */
		public function setComparator(callable $comparator);

		/**
		 * @return callable
		 */
		public function getComparator();

		/**
		 * @param bool $throw
		 *
		 * @return $this
		 */
		public function setThrowOnMismatch($throw);

		/**
		 * @return bool
		 */
		public function getThrowOnMismatch();

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param callable $control
		 *
		 * @return $this
		 */
		public function control(callable $control);

		/**
		 * @param callable $candidate
		 * @param string   $name
		 *
		 * @return $this
		 */
		public function candidate(callable $candidate, $name = 'candidate');

		/**
		 * @param ObservationInterface $control
		 * @param ObservationInterface $candidate
		 *
		 * @return bool
		 */
		public function compare(ObservationInterface $control, ObservationInterface $candidate);

		/**
		 * @return void
		 */
		public function publish(ResultInterface $result);

		/**
		 * @return mixed the result from the control
		 */
		public function run();
	}