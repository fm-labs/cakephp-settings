<?php
namespace Settings\Configure\Engine;

use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\Exception as DatabaseException;

/**
 * Class SettingsConfig
 *
 * @package Settings\Configure\Engine
 */
class SettingsConfig extends PhpConfig
{
    /**
     * @var string Path to settings dir
     */
    protected $_path;

    /**
     * File extension.
     *
     * @var string
     */
    protected $_extension = '.php';

    /**
     * @param string|null $configPath Path to config dir. Defaults to ROOT/config.
     */
    public function __construct($configPath = null)
    {
        if ($configPath === null && defined('SETTINGS')) {
            $configPath = constant('SETTINGS');
        }
        parent::__construct($configPath);
    }

    public function read($key)
    {
        return parent::read($key);
    }

    /**
     * Converts the provided $data into a string of PHP code that can
     * be used saved into a file and loaded later.
     *
     * @param string $key The identifier to write to. If the key has a . it will be treated
     *  as a plugin prefix.
     * @param array $data Data to dump.
     * @return int Bytes saved.
     */
    public function dump($key, array $data)
    {
        $contents = '<?php' . "\n" . 'return ' . var_export($data, true) . ';' . "\n";

        $filename = $this->_getFilePath($key);
        return file_put_contents($filename, $contents);
    }

    /**
     * Return path to settings file
     *
     * @param string $key Settings key
     * @return string
     */
    protected function _getFilePath($key, $checkExists = false)
    {
        return parent::_getFilePath('settings_' . $key, $checkExists);
    }
}
