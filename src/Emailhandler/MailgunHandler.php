<?php
/**
 * Created 09.09.18 18:52
 * @author Mediengstalt Heimbach - Johannes Heimbach
 */

namespace PicoSymfonyForm\Emailhandler;


use Mailgun\Mailgun;

class MailgunHandler extends EmailHandler
{
    /**
     * @var Mailgun
     */
    private $mailer;

    /**
     * @param $recipients
     * @param $subject
     * @param $data
     * @param $template
     * @return mixed
     */
    public function sendMail($recipients, $subject, $data, $template)
    {
        $this->createSender();
        try {
            $message = $this->createMessage($recipients, $subject, $data, $template);

            $contentParts = [];
            foreach ($message->getChildren() as $child) {
                if($child->getContentType() === 'text/plain') {
                    $contentParts['text'] =  $child->getBody();
                }
            }

            $send  = $this->mailer->messages()->send($this->config['mailgun']['domain'], [
                'from'    => $this->getEmailAdresses($message->getFrom()),
                'to'      => $this->getEmailAdresses($message->getTo()),
                'subject' => $message->getSubject(),
                'html'    => $message->getBody(),
                'text'    => $contentParts['text']
            ]);

            if ($send->getMessage() === 'Queued. Thank you.') {
                return count($message->getTo());
            } else {
                return 0;
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return 0;
    }

    /**
     * @return Mailgun
     */
    protected function createSender()
    {
        $this->mailer = Mailgun::create($this->config['mailgun']['key']);
        return $this->mailer;
    }

    /**
     * @param array $arr
     * @return array
     */
    private function getEmailAdresses($arr) {
        return array_keys($arr);
    }
}