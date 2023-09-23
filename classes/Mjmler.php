<?php

namespace Waka\MailMjml\Classes;

use Waka\Productor\Interfaces\BaseProductor;
use Waka\Productor\Interfaces\Email;
use Waka\Productor\Interfaces\Show;
use Closure;
use Lang;
use Arr;
use ApplicationException;
use ValidationException;

class Mjmler implements BaseProductor, Email, Show
{
    use \Waka\Productor\Classes\Traits\TraitProductor;

    public static function getConfig()
    {
        return [
            'label' => Lang::get('waka.mailmjml::lang.driver.mjmler.label'),
            'icon' => 'icon-mjml',
            'description' => Lang::get('waka.mailmjml::lang.driver.description'),
            'productorModel' => \Waka\MailMjml\Models\MailMjml::class,
            'productorCreator' => \Waka\MailMjml\Classes\MjmlCreator::class,
            'productorFilesRegistration' =>  'registerMjmlTemplates',
            'productor_yaml_config' => '~/plugins/waka/mailmjml/models/mailmjml/productor_config.yaml',
            'methods' => [
                'sendEmail' => [
                    'label' => 'Envoyer email',
                    'handler' => 'sendEmail',
                ],
                'show' => [
                    'label' => 'Afficher HTML',
                    'handler' => 'show',
                ]
            ],
        ];
    }

    public static function updateFormwidget($slug, $formWidget)
    {
        $productorModel = self::getProductor($slug);
        $formWidget->getField('subject')->value = $productorModel->subject;
        //Je n'ais pas trouvé de solution pour charger les valeurs. donc je recupère les asks dans un primer temps avec une valeur par defaut qui ne marche pas et je le réajoute ensuite.... 
        $formWidget = self::getAndSetAsks($productorModel, $formWidget);
        return $formWidget;
    }

    public static function execute($templateCode, $productorHandler, $allDatas):array {
        $modelId = Arr::get($allDatas, 'modelId');
        $modelClass = Arr::get($allDatas, 'modelClass');
        $dsMap = Arr::get($allDatas, 'dsMap', null);
        //
        $targetModel = $modelClass::find($modelId);
        $data = [];
        $dsId = null;
        $dsClass = null;
        if ($targetModel) {
            $data = $targetModel->dsMap($dsMap);
            $dsId = $targetModel->id;
            $dsClass = $targetModel->getMorphClass();
        }
        if($productorHandler == "sendEmail") {
            $mailId = self::sendEmail($templateCode, $data, [], function($mail) use($allDatas, $dsId, $dsClass) {
                $mail->setSubject(\Arr::get($allDatas, 'productorDataArray.subject'));
                $mail->setTos(\Arr::get($allDatas, 'productorDataArray.tos'));
                if($dsId && $dsClass) {
                    $mail->setHeaders([
                        'ds' => $dsClass,
                        'ds_id' => $dsId,
                    ]);
                }
                
            });
            return [
                'message' => 'Mail envoyé avec succès',
                'btn' => [
                    'label' => 'Voir l\'email dans la boite d\'envoi',
                    'request' => 'onGoToBo',
                    'link' => 'waka/maillog/sendboxs/update/'.$mailId
                ],
            ];
        } else if($productorHandler == "show") {
            $data = self::show($templateCode, $data, []);
            return [
                'message' => 'Mail à envoyer',
                'partial' => [
                    'content' => $data,
                ],
            ];
        }
    }


    public static function sendEmail(string $templateCode, array $vars, array $options, Closure $callback = null)
    {
        // Créer l'instance de pdf
        $creator = self::instanciateCreator($templateCode, $vars, $options);
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

    public static function show(string $templateCode, array $vars, array $options, Closure $callback = null) {

        $creator = self::instanciateCreator($templateCode, $vars, $options);
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
