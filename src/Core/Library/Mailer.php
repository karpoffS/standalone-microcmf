<?php

namespace Core\Library;

use PHPMailer;

class Mailer extends PHPMailer
{
    private $errors;

    private $HTMLTemplate = [];

    public function __construct(array $params = [], string $replyTo = null, string $unsubscribe = null)
    {
        parent::__construct();

        $this->isSMTP();
        $this->Host       = $params['host'];
        $this->SMTPAuth   = $params['auth'];
        $this->SMTPSecure = $params['secure'];
        $this->Username   = $params['username'];
        $this->Password   = $params['password'];
        $this->Port       = $params['port'];
        $this->CharSet    = $params['charset'];

        //Как сгенерировано письмо
        if (key_exists('autosubmitted', $params) and !empty($params['autosubmitted'])) {
            $this->addCustomHeader('Auto-Submitted', $params['autosubmitted']);
        }

        //Отписка
        if ($unsubscribe) {
            $this->addCustomHeader('List-Unsubscribe', '<' . $unsubscribe . '>');
        }

        //Куда ответить
        if ($replyTo) {
            $this->addReplyTo($replyTo);
        }

        //Отправитель
        $this->setFrom($params['username'], $params['displayname']);
    }

    private function addError(array $msg = [])
    {
        $this->errors = $msg;
    }

    public function hasErrors()
    {
        return $this->errors;
    }

    /**
     * Отправка почты с шаблоном
     *
     * @param string|array|null $recipient
     * @param string|null       $templateFile
     * @param array             $options
     *
     * @return array|bool|string
     */
    public function sendMessageHTMLTemplate($recipient = null, string $templateFile = null, array $options = [])
    {
        //Не указан получатель
        if (!$recipient) {
            $this->addError(['Recipient is empty!']);

            return false;
        }

        //Получить шаблон
        if (!$this->getTemplate($templateFile)) {
            return false;
        }

        //Получатель
        if (!is_array($recipient)) {
            $this->addAddress($recipient);
        } else {
            foreach ($recipient as $email) {
                $this->addAddress($email);
            }
        }

        $this->isHTML(true);

        //Ассоциировать данные с шаблоном
        $this->Subject = $this->HTMLTemplate['subject'];
        $this->Body = $this->addAssoc($this->HTMLTemplate['body'], $options);

        if (!$this->send()) {
            $this->addError([
                'Message could not be sent.',
                'Mailer Error: ' . $this->ErrorInfo
            ]);

            return false;
        }

        return true;
    }

    /**
     * Распарсить шаблон (тема и тело письма)
     *
     * @param String|null $templateName
     *
     * @return array|bool
     */
    private function getTemplate(string $templateName = null)
    {
        //Если шаблон уже в памяти
        if (key_exists('file', $this->HTMLTemplate) and $this->HTMLTemplate['file'] === $templateName) {
            return true;
        }

        $templateFile = __DIR__ . '/EmailTemplates/' . $templateName . '.html';

        //Есть ли файл
        if (!file_exists($templateFile)) {
            $this->addError(['Template "' . $templateFile . '" not found!']);

            return false;
        }

        //Считать файл в массив
        $template = file($templateFile);

        //Вырезать заголовок (тема письма)
        $subject = trim(preg_replace(['/<\/?title>/'], '', $template[3]));
        unset($template[3]);

        //Тело письма
        $body = implode(PHP_EOL, $template);

        $this->HTMLTemplate = [
            'file'    => $templateName,
            'subject' => $subject,
            'body'    => $body
        ];

        return true;
    }

    /**
     * Ассоциировать данные с шаблоном
     *
     * @param string $body
     * @param array  $options
     *
     * @return mixed|string
     */
    private function addAssoc(string $body, array $options = [])
    {
        if (empty($body) or empty($options)) {
            return $body;
        }

        $pattern = [];
        $replace = [];

        foreach ($options as $k => $v) {
            $pattern[] = '/{' . $k . '}/';
            $replace[] = $v;
        }

        return preg_replace($pattern, $replace, $body);
    }
}
