<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension;

use Kodus\Mail\Address;
use Kodus\Mail\Message as BaseMessage;
use Kodus\Mail\MailService;
use Stefna\MailServiceExtension\Exception\InvalidMessage;
use Stefna\MailServiceExtension\ValueObject\Message;

class Service implements MailService
{
	public function __construct(
		private Address $sender,
		private MailService $mailService,
		private MailRendererInterface $mailRenderer,
	) {}

	/**
	 * @param string $tpl
	 * @param array<string, mixed> $payload
	 * @return Message
	 */
	public function createEmail(string $tpl, array $payload): BaseMessage
	{
		$html = $this->mailRenderer->renderHtml($tpl, $payload);
		$text = $this->mailRenderer->renderText($tpl, $payload);
		$subject = $this->mailRenderer->renderSubject($tpl, $payload);

		$message = new Message();
		$message->setSubject($subject);
		$message->setText($text);
		$message->setHTML($html);
		$message->setFrom($this->sender);

		return $message;
	}

	/**
	 * @inheritdoc
	 */
	public function send(BaseMessage $message): void
	{
		try {
			if (!$message->getFrom()) {
				$message->setFrom($this->sender);
			}
		}
		catch (InvalidMessage) {
			$message->setFrom($this->sender);
		}

		$this->mailService->send($message);
	}
}
