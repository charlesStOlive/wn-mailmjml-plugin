<?php

namespace Waka\MailMjml\Models;

use Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Waka\MailMjml\Classes\ModelFileParser;
use File as FileHelper;
use View;

/**
 * MailMjml Model
 */
class MailMjml extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_mailmjml_mail_mjmls';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'subject' => 'required',
        'name' => 'required',
        'slug' => 'required|unique:waka_mailmjml_mail_mjmls',
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [
        'config'
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [
        'layout' => [Layout::class]
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [
        // 'rule_asks' => [
        //     'Waka\WakaBlocs\Models\RuleAsk',
        //     'name' => 'askeable',
        //     'delete' => true
        // ],
        // 'rule_blocs' => [
        //     'Waka\WakaBlocs\Models\RuleBloc',
        //     'name' => 'bloceable',
        //     'delete' => true
        // ],
    ];
    public $attachOne = [];
    public $attachMany = [];


    public function beforeSave()
    {
        if ($this->mjml) {
            $mjmlSections = $this->mjml;
            $mjmlLayout = $this->layout->template;
            $finalMjml = \Winter\Storm\Parse\Bracket::parse($mjmlLayout, ['MjmlContents' => $mjmlSections]);
            $this->html = $this->sendApi($finalMjml);
        }
    }

    public static function findBySlug($slug)
    {
        $model = self::where('slug', $slug)->first();
        if (!$model) {
            $model = self::findFileModels($slug);
        }
        if (!$model) {
            throw new \ApplicationException('aucun modèl etrouvé avec le code : ' . $slug);
        }
        return $model;
    }

    public static function findFileModels($slug)
    {
        $model = null;
        if (View::exists($slug)) {
            $model = new self;
            $model->slug = $slug;
            $model->fillFromView($slug);
        } else {
            \Log::error('la modèle n existe pas dans findFileModels' . self::class);
        }
        return $model;
    }

    /**
     * Fill model using a view file path.
     *
     * @param string $path
     * @return void
     */
    public function fillFromView($path)
    {
        $this->fillFromSections(self::getFileModelSections($path));
    }

    /**
     * Fill model using provided section array.
     *
     * @param array $sections
     * @return void
     */
    protected function fillFromSections($sections)
    {
        $mjml = \Arr::get($sections, 'mjml');
        $this->subject = \Arr::get($sections, 'settings.subject', 'sujet manquant');
        $this->config['open_log'] = \Arr::get($sections, 'settings.open_log', false);
        $this->config['click_log'] = \Arr::get($sections, 'settings.click_log', false);
        $this->config['sender'] = \Arr::get($sections, 'settings.sender', null);
        $this->config['reply_to'] = \Arr::get($sections, 'settings.reply_to', false);
        $this->config['cci'] = \Arr::get($sections, 'settings.cci', null);
        $layoutSlug = \Arr::get($sections, 'settings.layout', 'base');
        $this->layout = Layout::where('slug', $layoutSlug)->first();
        if (!$this->layout) {
            throw new \ApplicationException('Le layout du template n existe pas');
        }
        $env = \Config::get("waka.wutils::env");
        if ($env == 'local' || $env == 'dev') {
            $mjmlLayout = $this->layout->template;
            $finalMjml = \Winter\Storm\Parse\Bracket::parse($mjmlLayout, ['MjmlContents' => $mjml]);
            trace_log($finalMjml);
           $this->html = $this->sendApi($finalMjml);
        } else {
            $this->html =   \Cache::rememberForever('mjml_to_htm.' . $this->slug, function () use ($mjml) {
                $mjmlLayout = $this->layout->template;
                $finalMjml = \Winter\Storm\Parse\Bracket::parse($mjmlLayout, ['MjmlContents' => $mjml]);
                return $this->sendApi($finalMjml);
            });
        }
    }

    /**
     * Get section array from a view file retrieved by code.
     *
     * @param string $code
     * @return array|null
     */
    protected static function getFileModelSections($slug)
    {
        if (!View::exists($slug)) {
            return null;
        }
        $view = View::make($slug);
        return ModelFileParser::parse(FileHelper::get($view->getPath()));
    }

    // public function getProductorAsks()
    // {
    //     if(!$this->rule_asks->count()) {
    //         return [];
    //     }
    //     $asksList = [];
    //     $asks = $this->rule_asks;
    //     foreach ($asks as $ask) {
    //         if($ask->isEditable()) {
    //             $askCode = $ask->getCode();
    //             $askField = $ask->getEditableField();
    //             $asksList[$askCode] = $ask->getEditableConfig();
    //             $asksList[$askCode]['default'] = $ask->getConfig($askField);
    //         }
    //     }
    //     return $asksList;
    // }


    public function sendApi($mjml)
    {
        $applicationId = env('MJML_API_ID');
        $secretKey = env('MJML_API_SECRET');
        $client = new Client(['base_uri' => 'https://api.mjml.io/v1/']);
        $response = $client->request('POST', 'render', [
            'auth' => [$applicationId, $secretKey],
            'body' => json_encode(['mjml' => $mjml]),
        ]);
        if ($response->getReasonPhrase()) {
            $body = json_decode($response->getBody()->getContents());
            return $body->html;
        } else {
            throw new ValidationException(['mjml' => 'Problème de transcodage MJML contactez votre administrateur']);
        }
    }

    public function filterFields($fields, $context = null)
    {
        $user = \BackendAuth::getUser();
        //La limite du  nombre de asks est géré dans le controller.
        if (isset($fields->slug)) {
            if ($user->isSuperUser()  || $context == 'create') {
                $fields->slug->readOnly = false;
            } else {
                $fields->slug->readOnly = true;
            }
        }
    }
}
