[![Build Status](https://travis-ci.org/cocoon-projet/di.svg?branch=master)](https://travis-ci.org/cocoon-projet/di) [![Coverage Status](https://coveralls.io/repos/github/cocoon-projet/config/badge.svg?branch=master)](https://coveralls.io/github/cocoon-projet/config?branch=master)

## introduction

cocoon-projet/config est une librairie php qui mappe plusieurs fichiers de configuration pour stocker les paramètres dans un unique tableau php. les paramètres sont retournés au format: dot notation ex: ` $config->get('app.url'); ` pour un fichier php nommé app.php contenant le tableau ` return [ 'url' => 'http://www.monsite.com']`

## Pré-requis

Php version 7.0.0 ou plus

## installation

via composer
```
composer require cocoon-projet/config
```

Insertion dans votre composer.json

```
 "require": {
        "cocoon-projet/config": "^0.1"
    }
```

## Utilisation

Créer un dossier pour stocker vos fichiers de configuration. Les fichiers doivent être au format **nom_du_fichier.php**

exemple:

* config/
  * app.php
  * database.php

contenu du fichier app.php

```php
<?php

return [
    'url' => 'http://www.monsite.com',
    'debug' => true
]
```

contenu du fichier database.php

```php
<?php>

return [
    'engine' => 'mysql',
    'mysql' => [
        'dsn' => 'mysql:host=localhost;dbname=testdb',
        'user' => 'username',
        'password' => 'password'
        ],
    'sqlite' => [
        'dsn' => 'sqlite:/path/mydb.sqlite'
        ]
]
```

#### Usage

```php
<?php

require 'vendor/autoload.php';

use Cocoon\Config\Config;
use Cocoon\Config\LoadConfigFiles;

// On mappe les fichiers de configuration et on enregistre les paramètres dans un tableau php.

$items = LoadConfigFiles::load('path/to/the/config/folder');

// On initialise la classe Config et on charge le tableau php contenant les paramètres des fichiers de configuration.

$config = Config::getInstance($items);

// Maintenant on peut retourner les paramètres de configuration

// format dot notation: filename.key  ou filename.key1.key2

$config->get('app.url');  // http://www.monsite.com
$config->get('database.mysql.dsn'); // mysql:host=localhost;dbname=testdb
```

> Note: Retour de valeur null ou donner une valeur par défaut.

exemple:

```php
<?php

// le paramètre mail n'éxiste dans pas dans le fichier app.php
// la valeur retournée est null
$config->get('app.mail'); // null

// si une valeur par défaut est indiquée et que le paramètre n'éxiste pas, la valeur par défaut est retournée.
$config->get('app.mail', 'adresse@gmail.com'); // adresse@gmail.com
```

> Note: Vérifier l'existence d'un paramètre.

```php
<?php

if ($config->has('app.mail')) {
    $mail = $config->get('app.mail');
}
```