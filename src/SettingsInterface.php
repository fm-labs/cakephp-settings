<?php
declare(strict_types=1);

namespace Settings;

/**
 * @deprecated
 */
interface SettingsInterface
{
    /**
     * @param \Settings\SettingsManager $settings
     * @deprecated
     */
    public function buildSettings(SettingsManager $settings);
}
