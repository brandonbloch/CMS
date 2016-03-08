<?php

namespace CMS\Library;

class MessageList implements \IteratorAggregate, \Countable {

	private $level;
	private $messages;

	const INFO = 1;
	const SUCCESS = 2;
	const WARNING = 4;
	const DANGER = 8;

	private static $levels = [
		self::INFO => "info",
		self::SUCCESS => "success",
		self::WARNING => "warning",
		self::DANGER => "danger",
	];

	public function __construct() {
		$this->level = 0;
		$this->messages = [];
	}

	/**
	 * Add a new message to the MessageList
	 *
	 * @param string $message
	 * @param int    $level
	 *
	 * @return int
	 */
	public function addMessage(string $message, int $level): int {

		if (!is_int($level)) {
			try {
				$level = (int) $level;
			} catch (\Exception $e) {
				throw new \InvalidArgumentException("Expected int for message level, got " . gettype($level) . " instead.");
			}
		}
		if ($level <= 0 || ($level & ($level - 1)) != 0) {        // using the constants above, the message level should be a power of two
			throw new \InvalidArgumentException("Invalid message level supplied as argument.");
		}

		$count = array_push($this->messages, $message);
		// bitwise OR the error level. This way you can quickly tell the highest level set, but also if any given level is set
		$this->level = $this->level | $level;
		// return the index of the message, for later removal if desired
		return $count - 1;

	}

	/**
	 *
	 */
	public function reset() {
		$this->level = 0;
		$this->messages = [];
	}

	/**
	 * @return bool
	 */
	public function hasMessages(): bool {
		return (count($this->messages) > 0) ? true : false;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		if (count($this->messages) == 0) {
			return "";
		}

		$str = '<div class="cms-message-list cms-message-status-' . self::$levels[$this->level] . '">' . PHP_EOL;
		if (count($this->messages) == 1) {
			$str .= $this->messages[0];
		} else {
			$str .= "<ul>" . PHP_EOL;
			foreach ($this->messages as $error) {
				$str .= "<li>" . $error . "</li>" . PHP_EOL;
			}
			$str .= "</ul>" . PHP_EOL;
		}
		$str .= "</div>" . PHP_EOL;

		return $str;
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->messages);
	}

	/**
	 * @return int
	 */
	public function count(): int {
		return count($this->messages);
	}
}