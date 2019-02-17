<?php

namespace Settings;

class SettingsSchema
{
    protected $_settings = [];

    public function add($key, array $config = [])
    {
        if (is_array($key)) {
            foreach ($key as $_key => $_config) {
                $this->add($_key, $_config);
            }

            return $this;
        }
        $this->_settings[$key] = $config;

        return $this;
    }
}
