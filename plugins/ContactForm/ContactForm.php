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

	private $headingLevel;
	private $content;

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

	public function setRecipientEmail($email) {
		if (CMS\Library\Validate::email($email)) {
			$this->recipientEmail = $email;
		} else {
			throw new \InvalidArgumentException("Invalid email address supplied to setRecipientEmail.");
		}
	}

	public function senderEmailAllowed() {
		return $this->allowSenderEmail;
	}

	public function allowSenderEmail($bool) {
		$this->allowSenderEmail = ($bool) ? true : false;
	}

	public function senderEmailRequired() {
		return $this->requireSenderEmail;
	}

	public function requireSenderEmail($bool) {
		$this->requireSenderEmail = ($bool) ? true : false;
	}

	public function senderNameAllowed() {
		return $this->allowSenderName;
	}

	public function allowSenderName($bool) {
		$this->allowSenderName = ($bool) ? true : false;
	}

	public function senderNameRequired() {
		return $this->requireSenderName;
	}

	public function requireSenderName($bool) {
		$this->requireSenderName = ($bool) ? true : false;
	}

	public function subjectAllowed() {
		return $this->allowSubject;
	}

	public function allowSubject($bool) {
		$this->allowSubject = ($bool) ? true : false;
	}

	public function subjectRequired() {
		return $this->requireSubject;
	}

	public function requireSubject($bool) {
		$this->requireSubject = ($bool) ? true : false;
	}

	public function messageAllowed() {
		return $this->allowMessage;
	}

	public function allowMessage($bool) {
		$this->allowMessage = ($bool) ? true : false;
	}

	public function messageRequired() {
		return $this->requireMessage;
	}

	public function requireMessage($bool) {
		$this->requireMessage = ($bool) ? true : false;
	}

	public function attachmentlAllowed() {
		return $this->allowAttachment;
	}

	public function allowAttachment($bool) {
		$this->allowAttachment = ($bool) ? true : false;
	}

	public function attachmentRequired() {
		return $this->requireAttachment;
	}

	public function requireAttachment($bool) {
		$this->requireAttachment = ($bool) ? true : false;
	}

	protected function getValuesAsArray() {
		$values = array(
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
		);
		return $values;
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

	public function getPublicVersion() {

	}

	public function getEditableVersion() {

	}
}