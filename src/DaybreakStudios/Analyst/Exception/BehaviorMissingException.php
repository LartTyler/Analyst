<?php
	namespace DaybreakStudios\Analyst\Exception;

	class BehaviorMissingException extends \Exception {
		public function __construct($name) {
			parent::__construct(sprintf('Behavior named %s could not be found', $name));
		}
	}