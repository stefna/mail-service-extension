<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension\EventHandler;

use Kodus\Mail\Address;
use Kodus\Mail\MailService;
use Stefna\MailServiceExtension\Event\SendMessage;
use Stefna\MailServiceExtension\Exception\InvalidMessage;

final class SendMessageHandler
{
	public function __construct(
		private MailService $mailService,
		private Address|null $sender = null,
	) {}

	public function __invoke(SendMessage $sendMessage): void
	{
		$message = $sendMessage->message;
		if ($this->sender) {
			try {
				if (!$message->getFrom()) {
					$message->setFrom($this->sender);
				}
			}
			catch (InvalidMessage) {
				$message->setFrom($this->sender);
			}
		}

		$this->mailService->send($message);
	}
}
