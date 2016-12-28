<?php
namespace Settings\Shell;

use Cake\Console\Shell;
use Cake\Core\Plugin;

class SettingsShell extends Shell
{
    public $modelClass = 'Settings.Settings';

    public function main()
    {
        $this->out("[Settings Shell]");
        $this->out("import");
        $this->out("clean");
    }

    public function import()
    {
        $this->out('<info>Import settings</info>');
        $this->out('<notice>Importing app settings ...</notice>');
        try {
            $this->Settings->import(null);
        } catch (\Exception $ex) {
            $this->warn($ex->getMessage());
        }

        $plugins = Plugin::loaded();
        foreach ($plugins as $plugin) {
            $this->out('<notice>Importing ' . $plugin . ' settings ...</notice>');

            try {
                $this->Settings->import($plugin);
            } catch (\Exception $ex) {
                $this->warn($ex->getMessage());
            }
        }

        $this->Settings->dump();
        $this->out('<success>Import complete</success>');
    }

    public function clean()
    {

    }
}