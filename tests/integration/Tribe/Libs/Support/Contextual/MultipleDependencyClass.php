<?php declare(strict_types=1);

namespace Tribe\Libs\Support\Contextual;

use Tribe\Libs\Queues\Contracts\Task;
use Tribe\Libs\Support\Contextual\Strategy\Color;

final class MultipleDependencyClass {

	/**
	 * @var Task
	 */
	private $task;

	/**
	 * @var Color
	 */
	private $color;

	/**
	 * @var string
	 */
	private $test_string;

	public function __construct( Task $task, Color $color, string $test_string ) {
		$this->task = $task;
		$this->color = $color;
		$this->test_string = $test_string;
	}

	public function get_task(): Task {
		return $this->task;
	}

	public function get_color(): Color {
		return $this->color;
	}

	public function get_test_string(): string {
		return $this->test_string;
	}

	public function set_color( Color $color ): void {
		$this->color = $color;
	}

}
