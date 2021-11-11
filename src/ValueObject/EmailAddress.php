<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension\ValueObject;

use Kodus\Mail\Address;

final class EmailAddress extends Address
{
	public function setName(string $name): void
	{
		$this->name = $name;
	}
}
