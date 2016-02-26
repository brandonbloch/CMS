<?php

namespace CMS;

class MessageCollector implements \IteratorAggregate, \Countable {

	private $level = 0;
	private $messages = array();

	const INFO = 1;
	const SUCCESS = 2;
	const WARNING = 4;
	const DANGER = 8;

	private static $levels = array(
		self::INFO => "info",
		self::SUCCESS => "success",
		self::WARNING => "warning",
		self::DANGER => "danger",
	);

	public function addMessage($message, $level) {

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

	public function removeMessage($index) {
		if (array_key_exists($index, $this->messages)) {

		} else {
			throw new \BadMethodCallException("Attempt to remove non-existent message from MessageCollector.");
		}
	}

	public function reset() {
		$this->messages = array();
	}

	public function hasMessages() {
		return (count($this->messages) > 0) ? true : false;
	}

	public function __toString() {
		if (count($this->messages) == 0) {
			return "";
		}

		$str = '<div class="message-collector message-collector-' . self::$levels[$this->level] . '">' . PHP_EOL;
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

	public function getIterator() {
		return new \ArrayIterator($this->messages);
	}

	public function count() {
		return count($this->messages);
	}
}