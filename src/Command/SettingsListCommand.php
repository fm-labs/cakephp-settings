<?php
declare(strict_types=1);

namespace Settings\Command;

use Cake\Console\Arguments;
use Cake\Command\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * SettingsList command.
 */
class SettingsListCommand extends BaseCommand
{
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
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->addArgument('scope', [
            'required' => false,
            'help' => 'Settings scope (Default: \'all\')',
        ]);
        $parser->addArgument('key', [
            'required' => false,
            'help' => 'Settings key (Default: \'all\')',
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
        $scope = $args->getArgument('scope');
        $key = $args->getArgument('key');

        $query = $this->Settings->find();
        $conditions = [];
        foreach (['scope', 'key'] as $field) {
            $arg = $args->getArgument($field);
            if ($arg && $arg != "all") {
                $conditions[$field . ' LIKE'] = '%' . $arg . '%';
            }
        }
        $query->where($conditions);

        $settings = $query->all();
        $io->out(sprintf("Found %d settings", count($settings)));

        $data = [
            ['Scope', 'Key', 'Value', 'Schema'],
        ];
        $settings->each(function ($setting) use (&$data) {
            $value = $setting->value ?? '<null>';
            $data[] = [$setting->scope, $setting->key, $value, '<n/a>'];
        });
        $io->helper('Table')->output($data);
    }
}
