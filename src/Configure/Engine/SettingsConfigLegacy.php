<?php
namespace Settings\Configure\Engine;

use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Settings\Model\Entity\Setting;
use Cake\Database\Exception as DatabaseException;

/**
 * Class SettingsConfigLegacy
 *
 * @package Settings\Configure\Engine
 * @deprecated
 */
class SettingsConfigLegacy extends PhpConfig
{
    /**
     * @var string Path to config dir
     */
    protected $_path;

    /**
     * @var string Path to settings dir
     */
    protected $_settingsPath;

    /**
     * @var string Settings model. Set to FALSE, to disable database loader
     */
    protected $_modelClass = 'Settings.Settings';

    /**
     * @var string Config key prefix
     */
    protected $_configPrefix = false; // 'Settings';

    /**
     * @var string Config file extension
     */
    protected $_extension = ".php";

    /**
     * @var bool Autodump compiled settings
     */
    protected $_autoDump = true;

    /**
     * @param string|null $configPath Path to config dir. Defaults to ROOT/config.
     * @param string|null $settingsPath Path to settings dir. Defaults to ROOT/config/settings.
     * @param string|null $modelClass Settings model class name
     */
    public function __construct($configPath = null, $settingsPath = null, $modelClass = null)
    {
        parent::__construct($configPath);

        if ($settingsPath === null && defined('SETTINGS')) {
            $settingsPath = SETTINGS;
        } elseif ($settingsPath === null) {
            $settingsPath = $this->_path . 'settings' . DS;
        }
        $this->_settingsPath = $settingsPath;

        if ($modelClass !== null) {
            $this->_modelClass = $modelClass;
        }

        //if (Configure::read('debug')) {
        //    $this->_autoDump = false;
        //}
    }

    /**
     * Read Settings configuration
     *
     * First, attempt to read from compiled settings in SETTINGS/[key].php
     * If no compiled settings are present, attempt to read the settings config file
     * and return default settings config.
     *
     * In non-debug-mode, a missing compiled settings config file will raise an exception.
     *
     * @param string $key Config name
     * @return array|mixed
     * @throws \Cake\Core\Exception\Exception
     */
    public function read($key)
    {
        $file = $this->_path . $key . '.php';
        if (!is_file($file)) {
            return [];
        }

        // attempt to load compiled settings
        $file = $this->_getCompiledSettingsFilePath($key, false);

        // generate compiled settings if compiled settings file is missing
        if (!is_file($file)) {
            list ($settings, $compiled) = $this->_generateCompiledSettings($key, $this->_autoDump);

            return $compiled;
        }

        $return = include $file;
        if (is_array($return)) {
            return $return;
        }

        if (!isset($config)) {
            throw new Exception(sprintf('Settings file "%s" did not return an array', $key . '.php'));
        }

        return $config;
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

        $filename = $this->_getCompiledSettingsFilePath($key, false);

        return file_put_contents($filename, $contents);
    }

    /**
     * Converts the provided $data into a string of JSON that can
     * be saved into a file and loaded later.
     *
     * @param string $key The identifier to write to. If the key has a . it will be treated
     *  as a plugin prefix.
     * @param array $data Data to dump.
     * @return int Bytes saved.
     */
    public function dumpSchema($key, array $data)
    {
        $contents = json_encode($data, JSON_PRETTY_PRINT);

        $filename = $this->_getCompiledSettingsSchemaFilePath($key, false);

        return file_put_contents($filename, $contents);
    }

    /**
     * Read settings schema file and generate compiled settings with default values.
     *
     * @param string $key Settings key
     * @param bool $dump TRUE, if compiled settings will be dumped. Defaults to FALSE.
     * @return mixed Array of compiled settings
     */
    protected function _generateCompiledSettings($key, $dump = false)
    {
        list($plugin, $sKey) = pluginSplit($key, true);
        $sKey = $plugin . 'settings';
        $settings = $compiled = [];

        // settings file reader
        $sFileLoader = function ($key, $settingsFile) use (&$settings, &$compiled) {
            $settingsConfig = include $settingsFile;
            if (!is_array($settingsConfig) || !isset($settingsConfig['Settings'])) {
                throw new Exception(sprintf('Settings file "%s" has no Settings defined', $key . '.php'));
            }

            foreach ($settingsConfig['Settings'] as $scope => $scopeSettings) {
                foreach ($scopeSettings as $setting => $sConfig) {
                    $setting = array_merge([
                        'id' => null,
                        'ref' => $key,
                        'scope' => $scope,
                        'name' => $setting,
                        'type' => 'string',
                        'value' => null,
                        'default' => null,
                    ], $sConfig);

                    // map type string to type code
                    $setting['type'] = Setting::mapType($setting['type']);

                    $compiledKey = join('.', array_filter([$this->_configPrefix, $setting['scope'], $setting['name']]));
                    $compiledValue = ($setting['value'] !== null) ? $setting['value'] : $setting['default'];
                    if ($setting['value'] === null) {
                        $setting['value'] = $setting['default'];
                        $setting['_default'] = true;
                    }
                    $compiled[$compiledKey] = $setting['value'];

                    $settings[$compiledKey] = $setting;
                }
            }
        };

        // settings db reader
        $sDbLoader = function ($key, $modelClass) use (&$settings, &$compiled) {
            if (!$modelClass) {
                return;
            }

            try {
                $Settings = TableRegistry::get($modelClass);
                $dbSettings = $Settings->find()->where(['Settings.ref' => $key])->all();
                foreach ($dbSettings as $setting) {
                    $value = $setting->value;
                    $compiled[$setting->key] = $value;

                    if (isset($settings[$setting->key])) {
                        $settings[$setting->key]['id'] = $setting->id;
                        $settings[$setting->key]['value'] = $value;
                    } else {
                        $settings[$setting->key] = [
                            'id' => $setting->id,
                            'ref' => $setting->ref,
                            'type' => $setting->type,
                            'value' => $value,
                            'default' => null,
                            '_custom' => true
                        ];
                    }
                }
            //} catch (DatabaseException $ex) {
            //} catch (Exception $ex) {
            } catch (\Exception $ex) {
                Log::warning(
                    'Failed to load settings from database. Set $modelClass to FALSE to disable database loading.',
                    'settings'
                );
            }
        };

        // invoke file- and database settings loader
        $sFileLoader($key, $this->_getFilePath($sKey, true));
        $sDbLoader($key, $this->_modelClass);

        if ($dump === true) {
            $this->dump($key, $compiled);
            $this->dumpSchema($key, $settings);
        }

        return [$settings, $compiled];
    }

    /**
     * Return path to settings file
     *
     * @param string $key Settings key
     * @param bool $checkExists If TRUE, check file existence. Defaults to FALSE
     * @return string
     * @throws \Cake\Core\Exception\Exception
     */
    protected function _getCompiledSettingsSchemaFilePath($key, $checkExists = false)
    {
        return self::buildSettingsFilePath($this->_settingsPath, $key, '.schema.json', $checkExists);
    }

    /**
     * Return path to settings file
     *
     * @param string $key Settings key
     * @param bool $checkExists If TRUE, check file existence. Defaults to FALSE
     * @return string
     * @throws \Cake\Core\Exception\Exception
     */
    protected function _getCompiledSettingsFilePath($key, $checkExists = false)
    {
        return self::buildSettingsFilePath($this->_settingsPath, $key, $this->_extension, $checkExists);
    }

    /**
     * @param string $settingsPath Path to settings
     * @param string $key Settings key
     * @param string $ext Custom file extension
     * @param bool $checkExists Check for file existence. Defaults to FALSE.
     * @return string Path to file
     * @throws \Cake\Core\Exception\Exception
     */
    public static function buildSettingsFilePath($settingsPath, $key, $ext = '.php', $checkExists = false)
    {
        if (strpos($key, '..') !== false) {
            throw new Exception('Cannot load/dump settings schema with ../ in them.');
        }

        list($plugin, $key) = pluginSplit($key, true);

        if ($plugin) {
            $file = $settingsPath . 'plugin.' . $plugin . $key;
        } else {
            $file = $settingsPath . $key;
        }

        $file .= $ext;

        if ($checkExists && !is_file($file)) {
            throw new Exception(sprintf('Could not load settings file: %s', $file));
        }

        return $file;
    }

    public static function resetSettingsFilePath($settingsPath, $key, $ext = '.php')
    {
        // delete compiled settings file
        $file = self::buildSettingsFilePath($settingsPath, $key, $ext);
        if (is_file($file)) {
            unlink($file);
        }
    }
}
