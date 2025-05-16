<?php
declare(strict_types=1);

namespace Settings\Settings;

use Cake\Utility\Inflector;

/**
 * Class SettingsSchema
 *
 * @package Settings\Settings
 */
class SettingsSchema
{
    /**
     * @var string
     */
    public const DEFAULT_GROUP = '_default_';

    /**
     * @var array List of settings
     */
    protected array $_settings = [];

    /**
     * @var array List of setting groups
     */
    protected array $_groups = [];

    /**
     * SettingsSchema constructor.
     *
     * @param array $groups Map of group definitions
     * @param array $settings Map of setting definitions
     */
    public function __construct(array $groups = [], array $settings = [])
    {
        $this->addGroup(static::DEFAULT_GROUP, ['label' => __d('settings', 'General')]);
        $this->addGroup($groups);
        $this->add($settings);
    }

    /**
     * @param array|string $key Setting key or multiple keys
     * @param array $config Setting schema config
     * @return $this
     */
    public function add(string|array $key, array $config = [])
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_config) {
                $this->add($_key, $_config);
            }

            return $this;
        }

        $config += [
            'group' => null, 'label' => null, 'default' => null, 'help' => null,
            'locked' => false, 'required' => false,
        ];
        if (!$config['label']) {
            $config['label'] = Inflector::humanize(str_replace('.', ' ', $key));
        }
        if (!$config['help']) {
            $config['help'] = $config['label'];
        }
        if (!$config['group']) {
            $config['group'] = static::DEFAULT_GROUP;
        } elseif (!array_key_exists($config['group'], $this->_groups)) {
            // unknown group
            $config['group'] = static::DEFAULT_GROUP;
        }

        $this->_settings[$key] = $config;

        return $this;
    }

    /**
     * @param array|string $name Settings group name or array of group configs
     * @param array $config Settings group config
     * @return $this
     */
    public function addGroup(string|array $name, array $config = [])
    {
        if (is_array($name)) {
            foreach ($name as $_name => $_config) {
                $this->addGroup($_name, $_config);
            }

            return $this;
        }

        $config += ['label' => null];
        if (!$config['label']) {
            $config['label'] = Inflector::humanize(str_replace('.', ' ', $name));
        }

        $this->_groups[$name] = $config;

        return $this;
    }

    /**
     * @param string $name Group name
     * @return array|null
     */
    public function getGroup(string $name): ?array
    {
        return $this->_groups[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->_groups;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->_settings;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'groups' => $this->_groups,
            'settings' => $this->_settings,
        ];
    }
}
