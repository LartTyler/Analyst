<?php
	namespace DaybreakStudios\Analyst\Exception;

	class MismatchException extends \Exception {
		/**
		 * MismatchException constructor.
		 */
		public function __construct($name) {
			parent::__construct(sprintf('Result mismatch in experiment named %s', $name));
		}
	}