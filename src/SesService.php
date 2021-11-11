<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension;

use AsyncAws\Core\Exception\Exception as AsyncAwsException;
use AsyncAws\Core\Exception\Http\HttpException as AsyncAwsHttpException;
use AsyncAws\Ses\SesClient;
use AsyncAws\Ses\ValueObject\EmailContent;
use AsyncAws\Ses\ValueObject\RawMessage;
use Kodus\Mail\Address;
use Kodus\Mail\MailService;
use Kodus\Mail\Message;
use Psr\Log\LoggerInterface;
use Stefna\MailServiceExtension\Exception\ErrorDeliveringMail;
use Stefna\MailServiceExtension\Exception\InvalidMessage;

final class SesService implements MailService
{
	public function __construct(
		private SesClient $client,
		private LoggerInterface $logger,
	) {}

	/**
	 * @inheritDoc
	 */
	public function send(Message $message): void
	{
		$senders = $message->getFrom();
		if (!isset($senders[0])) {
			throw InvalidMessage::missingSender();
		}

		$this->sendEmailContent(new EmailContent(['Raw' => $this->messageToRaw($message)]), $message->getTo()[0]);

		// BCC recipients are not actually written to the mail headers in Kodus since it is not permitted
		// per section 3.6.3 of RFC2822. We will just send another copy instead.
		if ($message->getBCC()) {
			$bccMessage = clone $message;
			$bccMessage->setTo($message->getBCC());
			$bccMessage->setBCC([]);
			$this->sendEmailContent(
				new EmailContent(['Raw' => $this->messageToRaw($bccMessage)]),
				$message->getTo()[0],
			);
		}
	}

	private function messageToRaw(Message $message): RawMessage
	{
		$stream = fopen('php://memory', 'rwb+');
		if (!$stream) {
			throw new \RuntimeException('Failed to open resource to memory');
		}
		$writer = new SesMIMEWriter($stream);
		$writer->writeMessage($message);
		rewind($stream);
		$data = (string)stream_get_contents($stream);
		fclose($stream);

		return new RawMessage([
			'Data' => $data,
		]);
	}

	private function sendEmailContent(EmailContent $content, Address $address): void
	{
		try {
			$result = $this->client->sendEmail([
				'Destination' => [
					'ToAddresses' => [$address->getEmail()],
				],
				'Content' => $content,
			]);

			$this->logger->debug('Email sent', [
				'MessageId' => $result->getMessageId(),
			]);
		}
		catch (AsyncAwsHttpException $e) {
			$this->logger->error('Ses error while sending mail: "' . $e->getAwsMessage() . '"', [
				'exception' => $e,
				'email' => $content,
			]);
			throw ErrorDeliveringMail::fromGenericException($e);
		}
		catch (AsyncAwsException $e) {
			$this->logger->error('Unknown connection when connecting to aws ses: "' . $e->getMessage() . '"', [
				'exception' => $e,
				'email' => $content,
			]);
			throw ErrorDeliveringMail::fromGenericException($e);
		}
	}
}
