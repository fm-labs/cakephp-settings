<?php
namespace Settings\Shell;

use Cake\Console\Shell;

class SettingsShell extends Shell
{
    public function main()
    {
        $this->out("[Settings Shell]");
        $this->out("import [key]");
        $this->out("clean [key]");
    }

    public function import($key = null)
    {

    }

    public function clean($key = null)
    {

    }
}