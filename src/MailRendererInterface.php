<?php declare(strict_types=1);

namespace Stefna\MailServiceExtension;

interface MailRendererInterface
{
	/**
	 * @param array<string, mixed> $data
	 */
	public function renderText(string $tpl, array $data): string;

	/**
	 * @param array<string, mixed> $data
	 */
	public function renderHtml(string $tpl, array $data): string;

	/**
	 * @param array<string, mixed> $data
	 */
	public function renderSubject(string $tpl, array $data): string;
}
