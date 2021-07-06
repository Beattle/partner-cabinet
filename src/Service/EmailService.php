<?php

namespace App\Service;

use Swift_Mailer;
use Twig\Environment as TwigEngine;

/**
 * Отсылка email.
 */
class EmailService
{
    /** @var string Название блока в шаблоне для HTML тела письма */
    const BLOCK_HTML = 'htmlBody';
    /** @var string Название блока в шаблоне для альтернативного текста письма */
    const BLOCK_TEXT = 'textBody';
    /** @var string Название блока в шаблоне для темы письма */
    const BLOCK_SUBJECT = 'subject';
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var TwigEngine
     */
    private $templating;
    /**
     * @var string Email
     */
    private $senderEmail;

    /**
     * @param TwigEngine $templating
     * @param Swift_Mailer $mailer
     * @param string $senderEmail Email адрес от которого будут отсылаться письма
     */
    public function __construct(string $senderEmail,  Swift_Mailer $mailer, TwigEngine $templating)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->templating = $templating;
    }

    /**
     * @param string $to Email на который отсылать письмо
     * @param string $template шаблон из папки templates (вида emails/registration)
     *      в этом случае:
     *      блок subject из шаблона emails/registration.twig - тема письма,
     *      блок htmlBody из шаблона emails/registration.twig - для HTML тела письма,
     *      блок textBody из шаблона emails/registration.twig -  для Альтернативного текста письма
     * @param array $data Массив данных для шаблона
     * @return bool false- при неудачной отправке
     */
    public function send(string $to, string $template, array $data = []): bool
    {
        $subject = trim(strip_tags($this->render($template, self::BLOCK_SUBJECT, $data)));
        $body = $this->render($template, self::BLOCK_HTML, $data);
        $altBody = $this->render($template, self::BLOCK_TEXT, $data);
        $message = (new \Swift_Message($subject))->setFrom($this->senderEmail)->setTo($to)
            ->setBody($body, 'text/html')->addPart($altBody, 'text/plain');

        return (bool)$this->mailer->send($message);
    }

    /**
     * @param string $template шаблон для рендеринга
     * @param string $block Блок в шаблоне для рендеринга
     * @param array $data массив данных для шаблона
     * @return string Результат отрисовки шаблона или пустая строка если шаблон не найден
     */
    private function render(string $template, string $block, array $data = []): string
    {
        try {
            $temp = $this->templating->load($template);

            return $temp->renderBlock($block, $data);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
