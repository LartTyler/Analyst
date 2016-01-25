<?php
	namespace DaybreakStudios\Analyst;

	interface ObservationInterface {
		/**
		 * @return bool
		 */
		public function hasException();

		/**
		 * @return \Exception|null
		 */
		public function getException();

		/**
		 * @return float
		 */
		public function getDuration();

		/**
		 * @return mixed
		 */
		public function getResult();

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param ObservationInterface $other
		 * @param callable|null        $comparator
		 *
		 * @return bool
		 */
		public function compare(ObservationInterface $other, callable $comparator = null);
	}