<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension;

use Kodus\Mail\MIMEWriter;

final class SesMIMEWriter extends MIMEWriter
{
	public function writeHeader(string $name, string $value): void
	{
		$value = $this->escapeHeaderValue($value);
		if (strpos($value, "\n")) {
			$value = str_replace("=\r\n", '', $value);
		}

		$this->writeLine("{$name}: {$value}");
	}
}
