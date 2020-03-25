<?php
declare(strict_types=1);

namespace Settings\View\Form;

use Cake\Http\ServerRequest as Request;
use Cake\View\Form\ContextInterface;

/**
 * Class SettingsFormContext
 *
 * @package Settings\View\Form
 */
class SettingsFormContext implements ContextInterface
{
    /**
     * @var \Settings\Form\SettingsForm
     */
    protected $_form;

    /**
     * Constructor.
     *
     * @param \Cake\Http\ServerRequest $request The request object.
     * @param array $context Context info.
     */
    public function __construct(Request $request, array $context)
    {
        $this->_request = $request;
        $context += [
            'entity' => null,
        ];

        //if (!($context['entity']) instanceof SettingsForm) {
        //    throw new \InvalidArgumentException("SettingsFormContext: Invalid context");
        //}

        $this->_form = $context['entity'];
    }

    /**
     * Get the fields used in the context as a primary key.
     *
     * @return array
     */
    public function primaryKey()
    {
        return [];
    }

    /**
     * Returns true if the passed field name is part of the primary key for this context
     *
     * @param string $field A dot separated path to the field a value
     *   is needed for.
     * @return bool
     */
    public function isPrimaryKey($field): bool
    {
        return false;
    }

    /**
     * Returns whether or not this form is for a create operation.
     *
     * @return bool
     */
    public function isCreate(): bool
    {
        return false;
    }

    /**
     * Get the current value for a given field.
     *
     * Classes implementing this method can optionally have a second argument
     * `$options`. Valid key for `$options` array are:
     *
     *   - `default`: Default value to return if no value found in request
     *     data or context record.
     *   - `schemaDefault`: Boolean indicating whether default value from
     *      context's schema should be used if it's not explicitly provided.
     *
     * @param string $field A dot separated path to the field a value
     *   is needed for.
     * @return mixed
     */
    public function val(string $field, array $options = [])
    {
        return $this->_form->value($field);
    }

    /**
     * Check if a given field is 'required'.
     *
     * In this context class, this is simply defined by the 'required' array.
     *
     * @param string $field A dot separated path to check required-ness for.
     * @return bool
     */
    public function isRequired($field): bool
    {
        return false;
    }

    /**
     * Get the fieldnames of the top level object in this context.
     *
     * @return array A list of the field names in the context.
     */
    public function fieldNames(): array
    {
        return $this->_form->schema()->fields();
    }

    /**
     * Get the field type for a given field name.
     *
     * @param string $field A dot separated path to get a schema type for.
     * @return null|string An data type or null.
     * @see \Cake\Database\Type
     */
    public function type($field): ?string
    {
        return $this->_form->schema()->fieldType($field);
    }

    /**
     * Get an associative array of other attributes for a field name.
     *
     * @param string $field A dot separated path to get additional data on.
     * @return array An array of data describing the additional attributes on a field.
     */
    public function attributes($field): array
    {
        return $this->_form->schema()->field($field);
    }

    /**
     * Check whether or not a field has an error attached to it
     *
     * @param string $field A dot separated path to check errors on.
     * @return bool Returns true if the errors for the field are not empty.
     */
    public function hasError($field): bool
    {
        return false;
    }

    /**
     * Get the errors for a given field
     *
     * @param string $field A dot separated path to check errors on.
     * @return array An array of errors, an empty array will be returned when the
     *    context has no errors.
     */
    public function error($field): array
    {
        // TODO: Implement error() method.
        return [];
    }

    public function getRequiredMessage($field): ?string
    {
        // TODO: Implement getRequiredMessage() method.
        return null;
    }

    public function getMaxLength($field): ?int
    {
        // TODO: Implement getMaxLength() method.
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryKey(): array
    {
        // TODO: Implement getPrimaryKey() method.
    }
}
