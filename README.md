# Settings plugin for CakePHP

Enables a set of configuration values to be 



## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require fm-labs/cakephp-settings
```


## Define Settings

### Define Settings as JSON

For example, to define settings for your app

Create config/settings/app.json

```json

{
    "site.title": {
        "type": "string",
        "desc": "Name of your site",
        "default": "My awesome Site",
    },
    
    "session.timeout": {
        "type": "int",
        "desc": "Time in seconds after which the session will be terminated automatically",
        "default": 3600
    },
    
    "frontend.theme": {
        "type": "string",
        "desc": "Frontend Theme"
        "default": "",
        "options": [
            "ThemeDefault",
            "ThemeClassic"
        ]
    },
    
    "maintenance.enabled": {
        "type": "boolean",
        "default": false
    },
    
}

```


- Define Settings as JSON
- Define Settings as Class

## Define Settings for plugins

Create a settings definition file in

``` 
PLUGINS/MyPlugin/config/settings/my_plugin.json
```


## How it works

1. Settings definitions get parsed



## Usage

### Load settings like configurations

To load settings for your app:

```
Configure::load('app', 'settings')
```

To load plugin settings:

```
Configure::load('underscored_plugin_name_here', 'settings')
```


## Limitations

