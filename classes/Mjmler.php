<?php

namespace Waka\MailMjml\Classes;

use \Waka\Productor\Classes\Abstracts\BaseProductor;
use Closure;
use Lang;
use Arr;
use ApplicationException;
use ValidationException;

class Mjmler extends BaseProductor
{
    protected static $config = [
        'label' => 'waka.mailmjml::lang.driver.mjmler.label',
        'icon' => 'icon-paper-plane',
        'description' => 'waka.mailmjml::lang.driver.description',
        'productorModel' => \Waka\MailMjml\Models\MailMjml::class,
        'productorCreator' => \Waka\MailMjml\Classes\MjmlCreator::class,
        // 'productorFilesRegistration' =>  'registerMjmlTemplates',
        'productor_yaml_config' => '~/plugins/waka/mailmjml/models/mailmjml/productor_config.yaml',
        'methods' => [
            'prepareMjmlEmail' => [
                'label' => 'Envoyer email',
                'handler' => 'prepareMjmlEmail',
            ],
            'prepareShowMjml' => [
                'label' => 'Afficher HTML',
                'handler' => 'prepareShowMjml',
            ]
        ],
    ];

    

    public function prepareMjmlEmail($templateCode,$allDatas): array
    {
        $this->getBaseVars($allDatas);
        //
        $dsId = null;
        $dsClass = null;
        if ($this->targetModel) {
            $dsId = $this->targetModel->id;
            $dsClass = $this->targetModel->getMorphClass();
        }
        if ($aditionalConfig = $this->allDatas['addedConfig'] ?? false) {
            $additionalConfig = json_decode($aditionalConfig, true);
        }
        $addDsPjs =  $additionalConfig['addDsPjs'] ?? null;

        $mailId = self::sendEmail($templateCode, $this->data, function ($mail) use ($allDatas, $dsId, $dsClass, $addDsPjs) {
            $mail->setSubject(\Arr::get($allDatas, 'productorDataArray.subject'));
            $mail->setTos(\Arr::get($allDatas, 'productorDataArray.tos'));
            if ($dsId && $dsClass) {
                $mail->setHeaders([
                    'ds' => $dsClass,
                    'ds_id' => $dsId,
                ]);
            }
            if ($addDsPjs) {
                $mail->addDsPjs($addDsPjs);
            }
        });
        return [
            'message' => 'waka.mailmjml::lang.driver.mjmler.success.message_send',
            'btn' => [
                'label' => 'waka.mailmjml::lang.driver.mjmler.success.btn_message_send_label',
                'request' => 'onGoToBo',
                'link' => 'waka/maillog/sendboxs/update/' . $mailId
            ],
        ];
    }

    public function prepareShowMjml($templateCode, $allDatas):array
    {
        $this->getBaseVars($allDatas);
        $data = self::show($templateCode, $this->data);
        return [
            'keep_btns' => true,
            'keep_form' => true,
            'partial' => [
                'content' => $data,
            ],
        ];
    }



    /**
     * Instancieation de la class creator
     *
     */
    protected static function instanciateCreator(string $templateCode, array $vars)
    {
        $productorClass = self::getStaticConfig('productorCreator');
        $class = new $productorClass($templateCode, $vars);
        return $class;
    }

    public static function updateFormwidget($slug, $formWidget, $config = [])
    {
        $productorModel = self::getProductor($slug);
        $formWidget->getField('subject')->value = $productorModel->subject;
        return $formWidget;
    }


    public static function sendEmail(string $templateCode, array $vars, Closure $callback = null)
    {
        // Créer l'instance de pdf
        $creator = self::instanciateCreator($templateCode, $vars);
        // Appeler le callback pour définir les options
        if (is_callable($callback)) {
            $callback($creator);
        }

        try {
            return $creator->sendEmail();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function show(string $templateCode, array $vars, Closure $callback = null)
    {

        $creator = self::instanciateCreator($templateCode, $vars);
        // Appeler le callback pour définir les options
        if (is_callable($callback)) {
            $callback($creator);
        }

        try {
            return $creator->show();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
