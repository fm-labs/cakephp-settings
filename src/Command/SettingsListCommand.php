<?php
declare(strict_types=1);

namespace Settings\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * SettingsList command.
 *
 * @property \Settings\Model\Table\SettingsTable $Settings
 */
class SettingsListCommand extends Command
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
        return 'settings list';
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
            'help' => 'Only show settings with given scope',
            'default' => null,
        ]);
        $parser->addOption('plugin', [
            'short' => 'p',
            'required' => false,
            'help' => 'Only show settings from given plugin name',
            'default' => null,
        ]);
        $parser->addOption('key', [
            'required' => false,
            'help' => 'Only show settings with given key',
            'default' => null,
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
        $query = $this->Settings->find();
        $conditions = [];
        foreach (['scope', 'plugin', 'key'] as $field) {
            $arg = $args->getOption($field);
            if ($arg && $arg != 'all') {
                $conditions[$field . ' LIKE'] = '%' . $arg . '%';
            }
        }
        $query->where($conditions);

        $settings = $query->all();
        $io->out(sprintf('Found %d settings', count($settings)));

        $data = [
            ['Scope', 'Plugin', 'Key', 'Value', 'Locked'],
        ];
        $settings->each(function ($setting) use (&$data): void {
            $value = $setting->value ?? '<null>';
            $data[] = [$setting->scope, $setting->plugin, $setting->key, $value, (string)intval($setting->locked)];
        });
        $io->helper('Table')->output($data);
    }
}
