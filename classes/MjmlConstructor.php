<?php

namespace Waka\MailMjml\Classes;

use Closure;

class MjmlConstructor
{
    public $mail;
    public $vars;
    private $sendMode;
    private $pjsPaths;
    private $htm;
    private $subject;
    private $sender;
    private $reply_to;
    private $tos;
    private $ccs;
    private $ccis;
    private $pjs;
    private $headers;
    private $sourceClass;
    private $sourceId;
    private $open_log;
    private $click_log;
    private $is_embed;
    private $sendBoxId;

    /**
     * Sauvegarde le PDF généré à partir d'un template HTML vers un chemin spécifié.
     *
     * @param string $template
     * @param string $path
     * @param array $vars
     * @param Closure $callback
     * @return string
     */
    public function __construct($template, $vars, $options = [])
    {
        $this->mail = \Waka\MailMjml\WakaMail::where('template', $template);
        $this->sendMode = $options['send_mode'] ?? 'mailgun';
        $this->vars = $vars;
        $this->pjsPaths = [];
    }


    public function send()
    {

        $this->prepareModelData();

        if (!$this->tos) {
            throw new \ApplicationException('tos non definis dans le code ou le template');
        }

         \Mail::raw([], function ($message)  {
                if($this->is_embed) {
                    $contenu = $this->embedAllImages($message);
                }
                
                $message->html($contenu);
                $message->to($this->tos);
                if($this->sender) {
                    $senders = array_map('trim', explode(',', $this->sender));
                    $message->from($senders[0], $senders[1] ?? null );
                }
                if($this->reply_to) {
                    $replys = array_map('trim', explode(',', $this->reply_to));
                    $message->replyTo($replys[0], $replys[1] ?? null);
                }
                if($this->cci) {
                    $message->bcc($this->cci);
                }
                $message->subject($this->name);
                $headers = $message->getSymfonyMessage()->getHeaders();
                $mailVars = array_merge($this->headers, ['send_box_id' => $this->sendBoxId]);
                $headers->addTextHeader('X-Mailgun-Variables', json_encode($mailVars));
                if($this->open_log) {
                    $headers->addTextHeader('X-Mailgun-Track-Opens', true);
                }
               //trace_log("ok3");
                if($this->click_log) {
                    $headers->addTextHeader('X-Mailgun-Track-Clicks', true);
                }
                if ($this->pjs->count()) {
                    //trace_log("Il y a des pjs");
                    foreach ($this->pjs as $pj) {
                        //trace_log($pj->getLocalPath());
                        $message->attach($pj->getLocalPath(), ['as' => $pj->title]);
                    }
                }
               //trace_log("ok4");
            });
        $mailSendBox = \Waka\Mailer\Models\SendBox::create([
            'name' => $this->subject,
            'content' => $this->htm,
            'maileable_type' => $this->headers['mail_type'] ?? null, //todotargeteable
            'maileable_id' => $this->headers['mail_id'] ?? null,
            'targeteable_type' => $this->headers['ds'] ?? null, //todotargeteable
            'targeteable_id' => $this->headers['ds_id'] ?? null,
            'metas' => [
                'tos' => $this->tos,
                'cci' => $this->cci,
                'reply_to' => $this->reply_to,
                '' => $this->open_log,
                '' => $this->click_log,
                '' => $this->headers,


            ]
        ]);
        foreach($this->pjs as $pj) {
            $mailSendBox->pjs()->add($pj);
        }
    }

    public function show()
    {
        $this->prepareModelData();
        return $this->htm;
    }



    /**
     * Set METHOD
     */

    public function setSendMode($sendMode)
    {
        $this->sendMode = $sendMode;
    }
    public function addPjs($paths = [])
    {
        $this->pjsPaths = $paths;
    }
    public function removePjs()
    {
        $this->pjsPaths = null;
    }
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    public function setSender($sender)
    {
        $this->sender = $sender;
    }
    public function setReplyTo($reply_to)
    {
        $this->reply_to = $reply_to;
    }
    public function setTos($tos)
    {
        $this->tos = $tos;
    }
    public function setCcs($ccs)
    {
        $this->ccs = $ccs;
    }
    public function setCcis($ccis)
    {
        $this->ccis = $ccis;
    }
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
    /**
     * FONCTIONS PRIVATE
     */
    private function prepareModelData()
    {
        $this->tos = $this->tos ?? $this->parseModelField($this->mail->tos, $this->vars);
        $this->ccs = $this->ccs ?? $this->parseModelField($this->mail->ccs, $this->vars);
        $this->ccis = $this->ccis ?? $this->parseModelField($this->mail->ccis, $this->vars);
        $this->subject = $this->subject ?? $this->parseModelField($this->mail->subject, $this->vars);
        $this->sender = $this->sender ?? $this->parseModelField($this->mail->sender, $this->vars);
        $this->reply_to = $this->reply_to ?? $this->parseModelField($this->mail->reply_to, $this->vars);
        $this->headers = $this->mergeHeaders();
        $this->pjs = $this->preparePjs();
        $this->open_log = $this->mail->open_log;
        $this->click_log = $this->mail->click_log;
        $this->is_embed = $this->mail->is_embed;
        $this->htm = $this->parseModelField($this->mail->htm, $this->vars);
    }


    private function mergeHeaders()
    {
        $baseHeaders = [
            'maileable_type' => $this->getMorhClassName($this->mail),
            'maileable_id' => $this->mail->id ?? null
        ];
        $this->headers = array_merge($baseHeaders, $this->headers);
    }
    /**
     * Permet de retrouver le Morhp name de la classe si il esxite
     */
    private function getMorhClassName($class) {
        $instance = new $class();
        return  $instance->getMorphClass();
    }

    private function parseModelField($modelValue, $value)
    {
        if ($modelValue) {
            return \Twig::parse($modelValue, $this->vars);
        } else {
            return null;
        }
    }

    
}
