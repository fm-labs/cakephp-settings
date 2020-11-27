# Settings plugin for CakePHP


## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```shell
$ composer require fm-labs/cakephp-settings
```


## Usage

### Load settings like configurations

To load settings for your app:

```php
// In your bootstrap.php or in Plugin::bootstrap()
\Cake\Core\Configure::load('app', 'settings');
```

To load plugin settings:

```php
// In your bootstrap.php or in Plugin::bootstrap()
\Cake\Core\Configure::load('plugin_name', 'settings');
```


## Manage Settings

### Settings Schema


## Plugin settings

Create a settings definition file in

``` 
PLUGINS/MyPlugin/config/settings.php
```


## Events

### Settings.build

```php
    /**
     *
     */
    public function implementedEvents()
    {
        return [
            'Settings.build' => 'buildSettings',
        ];       
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @param \Settings\SettingsManager $settings The settings manager object
     * @return void
     */
    public function buildSettings(Event $event, $settings)
    {
        $settings->load('User.settings');
        $settings->addGroup('User.Password', ['label' => 'User Password Settings']);
        $settings->add('User.Password.expireInDays', [
            'group' => 'User.Password',
            'type' => 'int',
            'label' => 'Password expiry (in days)',
            'help' => 'The password will expire in X days and a new password needs to be entered by the user at the next login.'
        ]);
    }
```

