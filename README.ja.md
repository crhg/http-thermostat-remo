# 概要
[homebridge-thermostat](https://www.npmjs.com/package/homebridge-thermostat)が要求するapiに従って[Nature Remo](https://nature.global)によりエアコンを制御するためのサーバです。

# 使い方

* 適当な場所にこのリポジトリを展開します

* ```composer install```

* ```.env```を用意します

    * ```.env.example```を```.env```にコピーします

    * ```php artisan key:generate```を実行してアプリケーションキーを生成します

    * ```REMO_TOKEN```に[Nature Remo Cloud API](https://developer.nature.global)を利用するためのアクセストークンを設定します。アクセストークンは[home.nature.global](http://home.nature.global/)から取得します。
    
# homebridge-thermostatの設定

homebridgeのconfig.jsonに記述を追加します。必要な記述の例は```php artisan thermostat:config```コマンドで作成できます。
インストールした本サーバのbase uriを引数に指定して実行してください。

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

すぐにAPIのrate limitに到達するので対応予定です。

