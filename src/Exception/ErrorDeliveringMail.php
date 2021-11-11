<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension\Exception;

final class ErrorDeliveringMail extends \RuntimeException
{
	public static function fromGenericException(\Throwable $e): self
	{
		return new self('Delivery error', 0, $e);
	}
}
