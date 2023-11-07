<?php namespace Waka\MailMjml\Console;

use Winter\Storm\Console\Command;
use System\Classes\PluginManager;
use Waka\MailMjml\Models\MailMjml;

class SyncMailMjml extends Command
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'waka:syncmailmjml';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'waka:syncmailmjml
        {--f|force : Force the operation to run and ignore production warnings and confirmation questions.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'No description provided yet...';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $templates = PluginManager::instance()->getRegistrationMethodValues('registerMjmlTemplates');
        $templates = self::flattenPluginBundle($templates);
        foreach($templates as $slug => $template) {
            $existingModel = MailMjml::where('slug',$slug )->first();
            if($existingModel && !$this->option('force')) {
                $this->warn('Le modèle : '.$slug.' ne sera pas crée il existe déjà');
                continue;
            } else if($existingModel) {
                $newData = MailMjml::findFileModels($slug)->toArray();
                unset($newData['id']);
                unset($newData['layout']);
                $newData['name'] = $template['name'];
                $newData['is_synced'] = true;
                $existingModel->update($newData);
                $this->info('Le modèle : '.$slug.' existe et sera mis à jours puisque vous avez forcé la création');
            } else {
                $this->info('Le modèle : '.$slug.' va être crée');
                $modelToCreate = MailMjml::findFileModels($slug);
                $modelToCreate->name = $template['name'];
                $modelToCreate->is_synced = true;
                $modelToCreate->save();
            }
            
            
        }
    }

    private static function flattenPluginBundle($array)
    {
        $newArray = [];

        foreach ($array as $subArray) {
            foreach ($subArray as $key => $value) {
                $newArray[$key] = $value;
            }
        }
        return $newArray;
    }

    /**
     * Provide autocomplete suggestions for the "myCustomArgument" argument
     */
    // public function suggestMyCustomArgumentValues(): array
    // {
    //     return ['value', 'another'];
    // }
}
