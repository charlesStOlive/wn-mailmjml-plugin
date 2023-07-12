<?php

namespace Waka\MailMjml\Models;

use Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
    protected $jsonable = [];

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
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [
        'rule_asks' => [
            'Waka\WakaBlocs\Models\RuleAsk',
            'name' => 'askeable',
            'delete' => true
        ],
        'rule_blocs' => [
            'Waka\WakaBlocs\Models\RuleBloc',
            'name' => 'bloceable',
            'delete' => true
        ],
    ];
    public $attachOne = [];
    public $attachMany = [];


    public function beforeSave()
    {
        if ($this->mjml) {
            $finalMjml = $this->mjml;
            // //constructtion du mjml final avec les blocs.
            $additionalBlocs  = $this->rule_blocs->pluck('mjml', 'code')->toArray();
            $finalMjml = \Winter\Storm\Parse\Bracket::parse($finalMjml, $additionalBlocs);
            $this->html = $this->sendApi($finalMjml);
        }
    }


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
