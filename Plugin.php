<?php namespace Waka\MailMjml;

use Backend;
use Backend\Models\UserRole;
use System\Classes\PluginBase;
use App;
use Lang;

/**
 * MailMjml Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = [
        'Waka.productor',
    ];
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'waka.mailmjml::lang.plugin.name',
            'description' => 'waka.mailmjml::lang.plugin.description',
            'author'      => 'Waka',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void
    {
        $driverManager = App::make('waka.productor.drivermanager');
        $driverManager->registerDriver('mjmler', function () {
            return new \Waka\MailMjml\Classes\Mjmler();
        });

        $this->registerConsoleCommand('waka:syncmailmjml', 'Waka\MailMjml\Console\SyncMailMjml');

    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void
    {

    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents(): array
    {
        return []; // Remove this line to activate
    }

    public function registerWakaRules()
    {
        return [
            'blocs' => [
                ['\Waka\MailMjml\WakaRules\Blocs\Mjml', 'onlyClass' => ['mailMjml']],
            ],
        ];
    }


    /**
     * Registers any backend permissions used by this plugin.
     */
    public function registerPermissions(): array
    {
        return [
            'waka.mailmjml.user.base' => [
                'tab' => 'waka.mailmjml::lang.plugin.name',
                'label' => 'waka.mailmjml::lang.permissions.user_base',
            ],
            'waka.mailmjml.user.admin' => [
                'tab' => 'waka.mailmjml::lang.plugin.name',
                'label' => 'waka.mailmjml::lang.permissions.user_base',
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerSettings(): array
    {
        return [
           'mailMjmls' => [
                'label' => Lang::get('waka.mailmjml::lang.menu.mailMjmls.label'),
                'description' => Lang::get('waka.mailmjml::lang.menu.mailMjmls.description'),
                'category' => Lang::get('waka.wutils::lang.menu.model_category'),
                'icon' => 'icon-envelope',
                'url' => Backend::url('waka/mailmjml/mailmjmls'),
                'permissions' => ['waka.mailmjml.user.admin'],
                'order' => 30,
            ],
        ];
    }
}
