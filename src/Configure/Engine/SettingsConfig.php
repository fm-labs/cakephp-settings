<?php
namespace Settings\Configure\Engine;

use Cake\Cache\Cache;
use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Database\Exception as DatabaseException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class SettingsConfig
 *
 * @package Settings\Configure\Engine
 */
class SettingsConfig implements ConfigEngineInterface
{
    /**
     * @var string Path to settings dir
     */
    protected $_modelClass = "Settings.Settings";

    /**
     * File extension.
     *
     * @var string
     */
    protected $_extension = '.php';

    /**
     * @param string|null $modelClass
     */
    public function __construct($modelClass = null)
    {
        if ($modelClass) {
            $this->_modelClass = $modelClass;
        }
    }

    /**
     * @param string $key
     * @return array
     * @throws \Exception
     */
    public function read($key)
    {
        $settings = Cache::read($key, 'settings');
        if (!$settings) {

            try {
                $Table = TableRegistry::get('Settings.Settings');
                $query = $Table->find('list', ['keyField' => 'key', 'valueField' => 'value'])
                    ->where(['scope' => $key]);
                $settings = $query->toArray();

                Cache::write($key, $settings, 'settings');
            } catch (\Exception $ex) {
                Log::error('SettingsConfig: ' . $ex->getMessage(), ['settings']);
                throw $ex;
            }
        }

        return $settings;
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
        $filename = $this->_getFilePath($key);
        $contents = '<?php' . "\n" . 'return ' . var_export($data, true) . ';' . "\n";

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
        return CONFIG . DS . 'settings_' . $key . 'php';
    }
}
