<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension\ValueObject;

use Kodus\Mail\Address;
use Stefna\MailServiceExtension\Exception\InvalidMessage;

final class Message extends \Kodus\Mail\Message
{
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct()
	{
		$this->setDate(time());
	}

	public function getFrom(): array
	{
		$from = parent::getFrom();
		if (!$from) {
			throw InvalidMessage::missingSender();
		}
		return $from;
	}

	public function getTo(): array
	{
		$to = parent::getTo();
		if (!$to) {
			throw InvalidMessage::missingReceiver();
		}
		return $to;
	}
}
