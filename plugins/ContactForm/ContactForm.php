<?php

namespace CMS\Plugin;
use CMS;

class ContactForm extends CMS\Plugin {

	protected static $pluginName = "Contact Form";
	protected static $pluginVersion = "1.0.0";

	private $recipientEmail;
	private $allowSenderEmail;
	private $requireSenderEmail;
	private $allowSenderName;
	private $requireSenderName;
	private $allowSubject;
	private $requireSubject;
	private $allowMessage;
	private $requireMessage;
	private $allowAttachment;
	private $requireAttachment;

	protected function initialize() {
		$this->allowSenderEmail = true;
		$this->requireSenderEmail = true;
		$this->allowSenderName = true;
		$this->requireSenderName = true;
		$this->allowSubject = true;
		$this->requireSubject = false;
		$this->allowMessage = true;
		$this->requireMessage = true;
		$this->allowAttachment = false;
		$this->requireAttachment = false;
	}

	public function getRecipientEmail() {
		return $this->recipientEmail;
	}

	public function setRecipientEmail(string $email) {
		if (!CMS\Library\Validate::email($email)) {
			throw new \InvalidArgumentException("Invalid email address supplied to setRecipientEmail.");
		}
		$this->recipientEmail = $email;
	}

	public function senderEmailAllowed(): bool {
		return $this->allowSenderEmail;
	}

	public function allowSenderEmail(bool $allowed) {
		$this->allowSenderEmail = ($allowed) ? true : false;
	}

	public function senderEmailRequired(): bool {
		return $this->requireSenderEmail;
	}

	public function requireSenderEmail(bool $required) {
		$this->requireSenderEmail = ($required) ? true : false;
	}

	public function senderNameAllowed(): bool {
		return $this->allowSenderName;
	}

	public function allowSenderName(bool $allowed) {
		$this->allowSenderName = ($allowed) ? true : false;
	}

	public function senderNameRequired(): bool {
		return $this->requireSenderName;
	}

	public function requireSenderName(bool $required) {
		$this->requireSenderName = ($required) ? true : false;
	}

	public function subjectAllowed(): bool {
		return $this->allowSubject;
	}

	public function allowSubject(bool $allowed) {
		$this->allowSubject = ($allowed) ? true : false;
	}

	public function subjectRequired(): bool {
		return $this->requireSubject;
	}

	public function requireSubject(bool $required) {
		$this->requireSubject = ($required) ? true : false;
	}

	public function messageAllowed(): bool {
		return $this->allowMessage;
	}

	public function allowMessage(bool $allowed) {
		$this->allowMessage = ($allowed) ? true : false;
	}

	public function messageRequired(): bool {
		return $this->requireMessage;
	}

	public function requireMessage(bool $required) {
		$this->requireMessage = ($required) ? true : false;
	}

	public function attachmentlAllowed(): bool {
		return $this->allowAttachment;
	}

	public function allowAttachment(bool $allowed) {
		$this->allowAttachment = ($allowed) ? true : false;
	}

	public function attachmentRequired(): bool {
		return $this->requireAttachment;
	}

	public function requireAttachment(bool $required) {
		$this->requireAttachment = ($required) ? true : false;
	}

	protected function getValuesAsArray(): array {
		return [
			"pluginVersion" => self::$pluginVersion,
			"recipientEmail" => $this->recipientEmail,
			"allowSenderEmail" => $this->allowSenderEmail,
			"requireSenderEmail" => $this->requireSenderEmail,
			"allowSenderName" => $this->allowSenderName,
			"requireSenderName" => $this->requireSenderName,
			"allowSubject" => $this->allowSubject,
			"requireSubject" => $this->requireSubject,
			"allowMessage" => $this->allowMessage,
			"requireMessage" => $this->requireMessage,
			"allowAttachment" => $this->allowAttachment,
			"requireAttachment" => $this->requireAttachment,
		];
	}

	protected function setValuesWithArray(array $values) {
		$this->setRecipientEmail($values["recipientEmail"]);
		$this->allowSenderEmail($values["allowSenderEmail"]);
		$this->requireSenderEmail($values["requireSenderEmail"]);
		$this->allowSenderName($values["allowSenderName"]);
		$this->requireSenderName($values["requireSenderName"]);
		$this->allowSubject($values["allowSubject"]);
		$this->requireSubject($values["requireSubject"]);
		$this->allowMessage($values["allowMessage"]);
		$this->requireMessage($values["requireMessage"]);
		$this->allowAttachment($values["allowAttachment"]);
		$this->requireAttachment($values["requireAttachment"]);
	}

	public function getPublicVersion(): string {

	}

	public function getEditableVersion(): string {

	}
}