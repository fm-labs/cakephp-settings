<?php
declare(strict_types=1);

namespace Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * SettingsAdd command.
 *
 * @property \Settings\Model\Table\SettingsTable $Settings
 */
class SettingsSetValueCommand extends Command
{
    /**
     * @var string
     */
    public ?string $modelClass = 'Settings.Settings';

    /**
     * @return string
     */
    public static function defaultName(): string
    {
        return 'settings set-value';
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->addOption('scope', [
            'short' => 's',
            'required' => true,
            'help' => 'Settings scope',
            'default' => 'default',
        ]);
        $parser->addOption('plugin', [
            'short' => 'p',
            'required' => true,
            'help' => 'Only show settings from plugin with given name (Default: \'all\')',
            'default' => null,
        ]);
        $parser->addArgument('key', [
            'required' => true,
            'help' => 'Settings key (Default: \'all\')',
            'default' => null,
        ]);
        $parser->addArgument('value', [
            'required' => true,
            'help' => 'Settings value',
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $scope = $args->getOption('scope');
        $plugin = $args->getOption('plugin');
        $key = $args->getArgument('key');
        $value = $args->getArgument('value');

        $io->out(sprintf('scope: %s', $scope));
        $io->out(sprintf('plugin: %s', $plugin));
        $io->out(sprintf('key: %s', $key));
        $io->out(sprintf('value: %s', $value));

        if (!$scope || !$plugin || !$key) {
            $io->error('Invalid arguments');
            $this->abort();
        }

        //$this->Settings = $this->loadModel('Settings.Settings');
        $setting = $this->Settings->findOrCreate([
            'scope' => $scope,
            'plugin' => $plugin,
            'key' => $key,
        ]);

        $value = $value === 'null' ? null : $value;
        $setting->set('value', $value);

        if (!$this->Settings->save($setting)) {
            $io->abort("{$scope}:{$plugin}:{$key} - FAILED");
        }

        $io->success("{$scope}:{$plugin}:{$key} - SAVED");
    }
}
