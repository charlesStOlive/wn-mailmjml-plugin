<?php

namespace Waka\MailMjml\Classes;

use Closure;
use \Waka\MailMjml\Models\MailMjml;

class MjmlCreator
{
    public $mail;
    public $vars;
    private $sendMode;
    private $pjsPaths;
    private $html;
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
    public function __construct($template, $vars)
    {
        $this->mail = MailMjml::findBySlug($template);
        $this->sendMode = $options['send_mode'] ?? 'mailgun';
        $this->vars = $vars;
        $this->pjsPaths = [];
    }


    public function sendEmail()
    {

        $this->prepareModelData();
        return $this->sendMjml();
    }

    public function show()
    {
        $this->prepareModelData();
        return $this->html;
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
    private function sendMjml()
    {
        if (!$this->tos) {
            throw new \ApplicationException('tos non definis dans le code ou le template');
        }
        
        $mailSendBox = \Waka\MailLog\Models\SendBox::create([
            'name' => $this->subject,
            'content' => $this->html,
            'maileable_type' => $this->headers['maileable_type'] ?? null, //todotargeteable
            'maileable_id' => $this->headers['maileable_id'] ?? null,
            'targeteable_type' => $this->headers['ds'] ?? null, //todotargeteable
            'targeteable_id' => $this->headers['ds_id'] ?? null,
            'mail_vars' => $this->headers,
            'mail_tags' => [],
            'tos' => $this->tos,
            'cci' => $this->ccis,
            'ccs' => $this->ccs,
            'reply_to' => $this->reply_to,
            'open_log' => $this->open_log,
            'click_log' => $this->click_log,
            'is_embed' => $this->is_embed,
        ]);
        try {
            $mailSendBox->send();
        } catch(\Exception $ex) {
            throw $ex;
        }
        return $mailSendBox->id;
    }
    private function prepareModelData()
    {
        $this->subject = $this->subject ?? $this->parseModelField($this->mail->subject, $this->vars);
            $this->tos = $this->tos ?? $this->parseModelField($this->mail->config['tos'] ?? null, $this->vars);
        $this->ccs = $this->ccs ?? $this->parseModelField($this->mail->config['ccs'] ?? null, $this->vars);
        $this->ccis = $this->ccis ?? $this->parseModelField($this->mail->config['ccis'] ?? null, $this->vars);
        $this->sender = $this->sender ?? $this->parseModelField($this->mail->config['sender'] ?? null, $this->vars);
        $this->reply_to = $this->reply_to ?? $this->parseModelField($this->mail->config['reply_to'] ?? null, $this->vars);
        $this->open_log = $this->mail->config['open_log'] ?? null;
        $this->click_log = $this->mail->config['click_log'] ?? null;
        $this->is_embed = $this->mail->config['is_embed'] ?? null;
        $this->headers = $this->mergeHeaders();
        // $this->pjs = $this->preparePjs();
        //
        $this->html = $this->parseModelField($this->mail->html, $this->vars);
    }


    private function mergeHeaders()
    {
        $baseHeaders = [
            'maileable_type' => $this->getMorhClassName($this->mail),
            'maileable_id' => $this->mail->id ?? 9999
        ];
        if($this->headers) {
            return array_merge($baseHeaders, $this->headers);
        } else {
            return $baseHeaders;
        }
        
    }
    /**
     * Permet de retrouver le Morhp name de la classe si il esxite
     */
    private function getMorhClassName($class)
    {
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
