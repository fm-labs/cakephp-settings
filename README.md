# Settings plugin for CakePHP

Store configuration settings in database.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```shell
$ composer require fm-labs/cakephp-settings:^5

# For CakePHP 4.x
$ composer require fm-labs/cakephp-settings:^4
```

### Run migrations
```shell
$ ./bin/cake migrations migrate --plugin Settings
```

## Usage

### Manage settings via Console
```shell
// Initialize settings from schema
$ ./bin/cake settings init

// List available settings
$ ./bin/cake settings list

// List configured settings values
$ ./bin/cake settings values

// Get setting value
$ ./bin/cake settings get-value

// Update setting value
$ ./bin/cake settings set-value
```

### Manage settings programmatically

```php
@TODO
```


### Load settings

To load settings for your app:

```php
// In your bootstrap.php or in Plugin::bootstrap()
\Cake\Core\Configure::load('app', 'settings');
```

To load plugin settings:

```php
// In your bootstrap.php or in Plugin::bootstrap()
\Cake\Core\Configure::load('PluginName', 'settings');
```



## Settings Schema file

App and plugin settings are defined in settings schema file, which should be in your app's or plugins's config directory.
`APP/config/settings.php` or `PLUGINS/MyPlugin/config/settings.php` respectively.

```php
<?php
// Example settings.php for User plugin
return [
    'Settings' => [
        'User' => [
            'groups' => [
                'User.Auth' => ['label' => __('User Authentication')],
                'User.Signup' => ['label' => __('User Signup')],
            ],
            'schema' => [
                'User.Login.disabled' => [
                    'group' => 'User.Auth',
                    'type' => 'boolean',
                    'default' => true,
                ],
                'User.Signup.disabled' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => true,
                ],
                'User.Signup.verifyEmail' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ],
    ],
];


```


## Events

The `Settings.build` event will be triggered, when the global settings schema gets initialized.

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
     * @param \Settings\Settings $settings The settings object
     * @return void
     */
    public function buildSettings(Event $event, $settings)
    {
        // load a settings schema config file
        //$settings->load('User.settings');
        // add a setting group
        $settings->addGroup('User.Password', [
            'label' => 'User Password Settings'
        ]);
        // add a setting
        $settings->add('User.Password.expireInDays', [
            'group' => 'User.Password',
            'type' => 'int',
            'label' => 'Password expiry (in days)',
            'help' => 'The password will expire in X days and a new password needs to be entered by the user at the next login.'
        ]);
    }
```

