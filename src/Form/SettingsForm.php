<?php
declare(strict_types=1);

namespace Settings\Form;

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Settings\SettingsManager;

/**
 * Class SettingsForm
 *
 * @package Settings\Form
 */
class SettingsForm extends Form
{
    /**
     * @var \Settings\SettingsManager
     */
    protected $_manager;

    /**
     * @param \Cake\Event\EventManager $eventManager
     */
    public function __construct(?EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
    }

    public function setSettingsManager(SettingsManager $manager)
    {
        $this->_manager = $manager;

        return $this;
    }

    public function getSettingsManager()
    {
        if (!$this->_manager) {
            $this->_manager = new SettingsManager();
        }

        return $this->_manager;
    }

    /**
     * @param \Cake\Form\Schema|null $schema
     * @return \Cake\Form\Schema
     */
    public function schema(?Schema $schema = null): Schema
    {
        return parent::schema($schema);
    }

    /**
     * @param \Cake\Form\Schema $schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        foreach ($this->getSettingsManager()->getSettings() as $key => $config) {
            $columnConfig = array_diff_key($config, ['inputType' => null, 'input' => null, 'default' => null]);
            $schema->addField($key, $columnConfig);
        }

        return $schema;
    }

    /**
     * @param array $inputs
     * @return array
     * @deprecated Use getInputs() instead.
     */
    public function inputs()
    {
        return $this->getInputs();
    }

    public function getInputs($subset = null)
    {
        $settings = $this->getSettingsManager()->getSettings();
        if ($subset !== null) {
            if (is_string($subset)) {
                $settings = array_filter($settings, function ($config) use ($subset) {
                    return $config['scope'] == $subset;
                });
            } elseif (is_array($subset)) {
                $settings = array_filter($settings, function ($key) use ($subset) {
                    return in_array($key, $subset);
                }, ARRAY_FILTER_USE_KEY);
            }
        }

        $inputs = [];
        foreach ($settings as $key => $config) {
            $inputs[$key] = $this->_buildFormInput($key, $config);
        }

        return $inputs;
    }

    protected function _buildFormInput($key, array $config = [])
    {
        $config += ['input' => [], 'default' => null, 'type' => null, 'desc' => null, 'label' => null];

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

        $defaultInput = [
            'type' => null,
            'label' => $label,
            'default' => $config['default'],
            'value' => $this->value($key), //($this->value($key)) ?: Configure::read($key),
            'help' => $desc,
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
                case "boolean":
                    $input['type'] = "checkbox";
                    $input['val'] = $input['value'];
                    $input['value'] = 1;
                    break;

                case "text":
                case "html":
                    $input['type'] = "textarea";
                    break;

                case "integer":
                case "double":
                case "decimal":
                    $input['type'] = "numeric";
                    break;

                case "string":
                    $input['type'] = "text";
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
            $input['options'] = call_user_func($input['options'], $this->getSettingsManager());
        }

        return $input;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function value($key)
    {
        $value = $this->getSettingsManager()->value($key);
        if ($value === null && Configure::check($key)) {
            $value = Configure::read($key);
        }

        return $value;
    }

    /**
     * @param array $data
     * @return $this|bool
     */
    public function execute(array $data = []): bool
    {
        $this->getSettingsManager()->apply($data);

        return true;
    }
}
