<?php
declare(strict_types=1);

namespace Settings\Form;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Settings\Settings\SettingsManager;

/**
 * Class SettingsForm
 *
 * @package Settings\Form
 */
class SettingsForm extends Form
{

    /**
     * @var string Form field delimiter
     */
    protected const FIELD_DELIMITER = '___';

    /**
     * @var string Setting delimiter
     */
    protected const SETTING_DELIMITER = '.';

    /**
     * @var \Settings\Settings\SettingsManager
     */
    protected ?SettingsManager $settings;

    /**
     * @var bool|mixed If True, the form input's default value will be read from app's current configuration.
     */
    protected bool $defaultFromGlobalConfig;

    /**
     * @param \Settings\Settings\SettingsManager|null $settings Settings manager instance
     */
    public function __construct(?SettingsManager $settings = null, $defaultFromGlobalConfig = false)
    {
        parent::__construct(null);

        $this->settings = $settings;
        $this->defaultFromGlobalConfig = $defaultFromGlobalConfig;
    }

    /**
     * @return \Settings\Settings\SettingsManager Settings manager instance
     */
    public function getSettingsManager(): SettingsManager
    {
        if (!$this->settings) {
            $this->settings = new SettingsManager();
        }

        return $this->settings;
    }

    /**
     * @inheritDoc
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        foreach ($this->getSettingsManager()->getSchema()->getSettings() as $key => $setting) {
            $columnConfig = $setting['input'] ?? [];
            $schema->addField($key, $columnConfig);
        }

        return $schema;
    }

    /**
     * Build form validator event hook.
     *
     * @param Validator $validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        foreach ($this->getSettingsManager()->getSchema()->getSettings() as $key => $setting) {
            $fieldName = $this->_buildFieldName($key);
            if ($setting['required']) {
                $validator
                    ->requirePresence($fieldName)
                    ->notEmptyString($fieldName);
            }
        }

        return $validator;
    }

    /**
     * @param string|null $group Only get filters from given group
     * @return array
     */
    public function getInputs(?string $group = null): array
    {
        $settings = $this->getSettingsManager()->getSchema()->getSettings();
        if ($group !== null) {
            if (is_string($group)) {
                $settings = array_filter($settings, function ($config) use ($group) {
                    return $config['group'] == $group;
                });
            } elseif (is_array($group)) {
                $settings = array_filter($settings, function ($key) use ($group) {
                    return in_array($key, $group);
                }, ARRAY_FILTER_USE_KEY);
            }
        }

        $inputs = [];
        foreach ($settings as $key => $config) {
            $fieldName = $this->_buildFieldName($key);
            $inputs[$fieldName] = $this->_buildFormInput($key, $config);
        }

        return $inputs;
    }

    /**
     * Build form field name from setting key.
     * @param string $key
     * @return string
     */
    protected function _buildFieldName(string $key): string
    {
        return join(self::FIELD_DELIMITER, explode(self::SETTING_DELIMITER, $key));
    }

    /**
     * Build key name from form field.
     *
     * @param string $field
     * @return string
     */
    protected function _buildKeyName(string $field): string
    {
        return join(self::SETTING_DELIMITER, explode(self::FIELD_DELIMITER, $field));
    }

    /**
     * @param string $key Setting key
     * @param array $config Setting config
     * @return array Form control options
     */
    protected function _buildFormInput(string $key, array $config = []): array
    {
        $config += [
            'input' => [],
            'default' => null,
            'type' => null,
            'help' => null,
            'label' => null,
            'required' => null,
        ];

        $input = $config['input'];
        unset($config['input']);
        if (is_string($input)) {
            $input = ['label' => $input];
        }

        $label = Inflector::humanize(Text::slug($key, ' '));
        if (isset($config['label'])) {
            $label = $config['label'];
            unset($config['label']);
        }

        $desc = null;
        if (isset($config['desc'])) {
            $desc = $config['desc'];
            unset($config['desc']);
        }
        if (isset($config['help'])) {
            $desc = $config['help'];
            unset($config['help']);
        }
        if (!$config['default'] && $this->defaultFromGlobalConfig === true) {
            $config['default'] = Configure::read($key);
        }

        $defaultInput = [
            'type' => null,
            'label' => $label,
            'default' => $config['default'],
            'value' => $this->value($key),
            'help' => $desc,
            'required' => (bool)$config['required'],
        ];
        $input = array_merge($defaultInput, $input);
        $input = $this->_buildInput($input, $config);

        return $input;
    }

    /**
     * @param array $input Input schema
     * @param array $config Input config
     * @return array Input schema
     */
    protected function _buildInput(array $input, array $config = [])
    {
        if (!$input['type']) {
            switch ($config['type']) {
                case 'boolean':
                    $input['type'] = 'checkbox';
                    $input['val'] = $input['value'];
                    $input['value'] = 1;
                    break;

                case 'text':
                case 'html':
                    $input['type'] = 'textarea';
                    break;

                case 'integer':
                case 'double':
                case 'decimal':
                    $input['type'] = 'numeric';
                    break;

                case 'string':
                    $input['type'] = 'text';
                    break;

                default:
                    $input['type'] = $config['type'];
                    break;
            }
        }

        if (isset($input['options'])) {
            $input['type'] = 'select';
        }
        if (isset($input['options']) && is_callable($input['options'])) {
            $input['options'] = call_user_func($input['options'], $this->settings);
        }

        return $input;
    }

    /**
     * @param string $key Setting key
     * @return mixed|null
     */
    public function value(string $key)
    {
        return $this->getSettingsManager()->getValue($key);
    }

    public function execute(array $data, $options = []): bool
    {
        $values = [];
        foreach ($data as $field => $val) {
            $key = $this->_buildKeyName($field);
            $values[$key] = $val;
        }
        $this->getSettingsManager()->apply($values);

        return parent::execute($data);
    }

    /**
     * @param array $data Form data
     * @return $this|bool
     */
    protected function _execute(array $data = []): bool
    {
        /*
        $values = [];
        foreach ($data as $field => $val) {
            $key = $this->_buildKeyName($field);
            $values[$key] = $val;
        }
        $this->getSettingsManager()->apply($values);
        */

        return true;
    }
}
