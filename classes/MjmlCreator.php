<?php

namespace Waka\MailMjml\Classes;

use Closure;
use \Waka\MailMjml\Models\MailMjml;

class MjmlCreator
{
    public $mail;
    public $vars;
    private $sendMode;
    private $pjsDatas;
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
        $this->pjsDatas = [];
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
    public function addDsPjs($objects = [])
    {
        foreach($objects as $dsKey) {
            $objectFromDs = $this->vars['ds'][$dsKey] ?? null;
            if($objectFromDs) {
                array_push($this->pjsDatas,$objectFromDs);
            } else {
                //trace_log($this->vars['ds']);
                \Log::error('***addDsPjs**** : Impossible de créer la PJ depuis DS : '.$dsKey);
            }
            
        }   
        

    }

    public function addPjs($objects = [])
    {
        $this->pjsDatas = array_merge($this->pjsDatas,$objects);

    }


    public function removeDsPjs()
    {
        $this->pjsDatas = null;
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

        $this->createPjsInSendBoxs( $this->pjsDatas, $mailSendBox);
        try {
            $mailSendBox->send();
        } catch(\Exception $ex) {
            throw $ex;
        }
        return $mailSendBox->id;
    }

    private function createPjsInSendBoxs($pjs, $mailSendBox) {
        if($pjs) {
            //trace_log($pjs);
            foreach($pjs as $pj) {
                $filePath = $pj['path'] ?? null;
                if(!$filePath) {
                    \Log::error('Il manque le path de la PJ');
                }
                $fileName = $pj['label'] ?? 'inc';
                $fileExtention = pathinfo($filePath)['extension'];
                $file = new \System\Models\File;
                $file->data = $filePath;
                $file->title = $fileName.'.'.$fileExtention;
                $file->is_public = false;
                $mailSendBox->pjs()->add($file);

            }
        } 
    }

    private function prepareModelData()
    {
        $this->subject =  $this->parseModelField($this->subject ?? null, $this->mail->subject);
        $this->tos = $this->parseModelField($this->tos ?? null,  $this->mail->config['tos'] ?? null);
        $this->ccs =  $this->parseModelField($this->ccs ?? null, $this->mail->config['ccs'] ?? null);
        $this->ccis =  $this->parseModelField($this->ccis ?? null,  $this->mail->config['ccis'] ?? null);
        $this->sender =  $this->parseModelField($this->sender ?? null, $this->mail->config['sender'] ?? null);
        $this->reply_to =  $this->parseModelField($this->reply_to ?? null,  $this->mail->config['reply_to'] ?? null);
        $this->open_log = $this->mail->config['open_log'] ?? null;
        $this->click_log = $this->mail->config['click_log'] ?? null;
        $this->is_embed = $this->mail->config['is_embed'] ?? null;
        $this->headers = $this->mergeHeaders();
        // $this->pjs = $this->preparePjs();
        //
        //trace_log('ok-----------------------------------------');
        $this->html = $this->parseModelField($this->mail->html);
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

    private function parseModelField($baseValue, $modelValue = null)
    {
        $valueToReturn = null;
        if($baseValue) {
            $valueToReturn = $baseValue;
        } else {
            $valueToReturn = $modelValue;
        }
        if ($valueToReturn && is_string($valueToReturn)) {
            return \Twig::parse($valueToReturn, $this->vars);
        } else if($valueToReturn) {
            return $valueToReturn;
        } else {
            return null;
        }
    }
}
