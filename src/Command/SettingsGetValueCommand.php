<?php
declare(strict_types=1);

namespace Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Settings command.
 *
 * @property \Settings\Model\Table\SettingsTable $Settings
 */
class SettingsGetValueCommand extends Command
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
        return 'settings get-value';
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
            'required' => false,
            'help' => 'Settings scope (Default: \'all\')',
            'default' => 'default',
        ]);
        $parser->addOption('plugin', [
            'short' => 'p',
            'required' => false,
            'help' => 'Only show settings from plugin with given name (Default: \'all\')',
            'default' => null,
        ]);
        $parser->addArgument('key', [
            'required' => true,
            'help' => 'Settings key (Default: \'all\')',
            'default' => null,
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $conditions = [];
        $conditions['Settings.scope'] = $args->getOption('scope');
        if ($args->getOption('plugin')) {
            $conditions['Settings.plugin'] = $args->getOption('plugin');
        }
        $conditions['Settings.key'] = $args->getArgument('key');
        $setting = $this->Settings->find()
            ->where($conditions)
            ->first();

        if (!$setting) {
            $io->warning('<notfound>');

            return;
        }

        $io->out((string)$setting->value);
    }
}
