<?php
declare(strict_types=1);

namespace Settings\Configure\Engine;

use Cake\Cache\Cache;
use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class SettingsConfig
 *
 * @package Settings\Configure\Engine
 */
class SettingsConfig implements ConfigEngineInterface
{
    public const CACHE_CONFIG = 'settings';

    /**
     * @var string Path to settings dir
     */
    protected $_modelClass = "Settings.Settings";

    /**
     * @var string File extension
     */
    protected $_extension = '.php';

    /**
     * @var string File prefix.
     */
    protected $_prefix = 'settings/';

    /**
     * @var string Table column name where the setting scope is stored
     */
    protected $_scopeField = 'plugin';

    /**
     * Clear the settings cache.
     *
     * @param string $key Cache key
     * @return bool
     */
    public static function clearCache(string $key): bool
    {
        return Cache::delete($key, static::CACHE_CONFIG);
    }

    /**
     * @param string|null $modelClass Model class name
     * @param string|null $scopeField Scope field name
     */
    public function __construct($modelClass = null, $scopeField = null)
    {
        if ($modelClass) {
            $this->_modelClass = $modelClass;
        }
        if ($scopeField) {
            $this->_scopeField = $scopeField;
        }
    }

    /**
     * @param string $scope Settings scope
     * @return array
     * @throws \Exception
     */
    public function read(string $scope): array
    {
        $settings = Cache::read($scope, static::CACHE_CONFIG);
        if (!$settings) {
            try {
                $Table = TableRegistry::getTableLocator()->get($this->_modelClass);
                $query = $Table->find('list', keyField: 'key', valueField: 'value')
                    ->where([$this->_scopeField => $scope]);
                $settings = $query->toArray();

                Cache::write($scope, $settings, static::CACHE_CONFIG);
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
    public function dump(string $key, array $data): bool
    {
        $filename = $this->_getFilePath($key);
        $contents = '<?php' . "\n" . 'return ' . var_export($data, true) . ';' . "\n";

        return file_put_contents($filename, $contents) > 0;
    }

    /**
     * Return path to settings file
     *
     * @param string $key Settings key
     * @return string
     */
    protected function _getFilePath($key)
    {
        return CONFIG . DS . $this->_prefix . $key . $this->_extension;
    }
}
