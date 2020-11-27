<?php
declare(strict_types=1);

namespace Settings\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\EntityInterface;

/**
 * SettingsAdd command.
 */
class SettingsAddCommand extends BaseCommand
{
    /**
     * @return string
     */
    public static function defaultName(): string
    {
        return 'settings add';
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
        $parser->addArgument('scope', [
            'required' => true,
            'help' => 'Settings scope',
        ]);
        $parser->addArgument('key', [
            'required' => true,
            'help' => 'Settings key',
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
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out(sprintf('scope: %s', $args->getArgument('scope')));
        $io->out(sprintf('key: %s', $args->getArgument('key')));
        $io->out(sprintf('value: %s', $args->getArgument('value')));

        $scope = $args->getArgument('scope');
        $key = $args->getArgument('key');
        $value = $args->getArgument('value');

        $this->Settings = $this->loadModel('Settings.Settings');
        $setting = $this->Settings->findOrCreate([
            'scope' => $scope,
            'key' => $key,
        ]);
        $setting->set('value', $value);

        if (!$this->Settings->save($setting)) {
            $io->abort("${scope}:${key} - FAILED");
        }

        $io->success("${scope}:${key} - SAVED");
    }
}
