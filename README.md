# Overview
This is a server that controls thermostat using [Nature Remo](https://nature.global) according to api requested by [homebridge-thermostat](https://www.npmjs.com/package/homebridge-thermostat).

# How to use

* ```composer install```

* Prepare ```.env```

    * Copy ```.env.example``` to ```.env```

    * Execute ```php artisan key: generate``` to generate the application key

    * Set the access token for [Nature Remo Cloud API](https://developer.nature.global) to ```REMO_TOKEN```. Access token can be obtained from [home.nature.global](http://home.nature.global/).
    
# homebridge-thermostat setting

Add setting in `config.json` of homebridge. Examples of necessary settings can be created with ```php artisan thermostat: config``` command.
Specify the base uri of the installed server as an argument and execute it.

```console
% php artisan thermostat:config http://localhost:8002                                                                           [~/Dropbox/src/http-thermostat-remo]
{
    "accessories": [
        {
            "accessory": "Thermostat",
            "name": "Daikin AC 008",
            "apiroute": "http://localhost:8002/api/thermostat/9407a43e-ce43-4523-a68c-3bd632626da9",
            "maxTemp": 32,
            "minTemp": 14
        }
    ]
}
```

# BUGS

There is a problem that it easily reaches the rate limit of Nature API.


