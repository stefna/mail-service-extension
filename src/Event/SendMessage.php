<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension\Event;

use Kodus\Mail\Message;

final class SendMessage
{
	public function __construct(
		public Message $message,
	) {}
}
