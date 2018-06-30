<?php

namespace Settings;

class SettingsPlugin implements SettingsInterface
{
    public function buildSettings(SettingsManager $settings)
    {
        $settings->add('Settings', 'autoBackup', [
            'type' => 'boolean',
            'default' => false,
        ]);
    }
}