<?php

namespace Waka\MailMjml\Classes;

use Closure;
use Waka\Productor\Interfaces\Productor;
use Waka\Productor\Classes\BaseProductor;
use Lang;

class Mjmler implements Productor
{
    use \Waka\Productor\Classes\Traits\TraitProductor; 

    public static function getConfig()
    {
        return [
            'label' => Lang::get('waka.mailmjml::lang.driver.mjmler.label'),
            'icon' => 'icon-mjml',
            'description' => Lang::get('waka.mailmjml::lang.driver.description'),
            'productorModel' => \Waka\MailMjml\Models\MailMjml::class,
            'productorCreator' => \Waka\MailMjml\Models\MjmlConstructor::class,
            'methods' => ['send', 'show'],
        ];
    }

    
    

    
}
