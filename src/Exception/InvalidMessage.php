<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension\Exception;

final class InvalidMessage extends \DomainException
{
	public static function missingSender(): self
	{
		return new self('Invalid message. Missing sender information');
	}

	public static function missingReceiver(): self
	{
		return new self('Invalid message. Missing receivers');
	}
}
