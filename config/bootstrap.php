<?php

if (!defined('SETTINGS')) {
    define('SETTINGS');
}


if (\Cake\Core\Plugin::loaded('Backend')) {
    \Backend\Lib\Backend::hookPlugin('Settings');
}

if (\Cake\Core\Plugin::loaded('Banana')) {

}