<?php
	class Sample {
		public function test() {
			Analyst::create()
				->control([ $this, 'oldThing' ])
				->candidate([ $this, 'newThing' ]);
		}

		public function oldThing() {
			// do stuff the old way
		}

		public function newThing() {
			// do stuff the new way
		}
	}