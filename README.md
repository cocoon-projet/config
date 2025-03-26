[![codecov](https://codecov.io/gh/cocoon-projet/config/graph/badge.svg?token=KM7Y127Z7J)](https://codecov.io/gh/cocoon-projet/config) [![PHP Composer](https://github.com/cocoon-projet/config/actions/workflows/ci.yml/badge.svg)](https://github.com/cocoon-projet/config/actions/workflows/ci.yml)

## Introduction

cocoon-projet/config est une librairie PHP moderne qui permet de gérer les configurations de votre application de manière flexible et sécurisée. Elle supporte plusieurs environnements (development, production, testing) et offre des fonctionnalités avancées comme le cache, la validation et l'historique des modifications.

## Pré-requis

- PHP 8.0 ou supérieur
- Composer

## Installation

Via Composer :
```bash
composer require cocoon-projet/config
```

Insertion dans votre composer.json :
```json
{
    "require": {
        "cocoon-projet/config": "^1.0"
    }
}
```

## Fonctionnalités

- 🔄 Gestion multi-environnements (development, production, testing)
- 📦 Support des fichiers de configuration PHP
- 🚀 Système de cache intégré pour optimiser les performances
- 🔒 Validation des données avec des types stricts
- 📝 Historique des modifications
- 🎨 Interface web intégrée pour visualiser et gérer les configurations
- 🔍 Recherche et filtrage des configurations
- 📤 Export des configurations au format JSON
- 🔐 Gestion sécurisée des valeurs sensibles
- 🔄 Support des environnements multiples avec héritage

## Utilisation

### Configuration de base

1. Créez un dossier pour vos fichiers de configuration :
```
config/
  ├── app.php
  ├── app.production.php
  ├── database.php
  └── database.production.php
```

2. Exemple de fichier de configuration (app.php) :
```php
<?php

declare(strict_types=1);

return [
    'url' => 'http://www.monsite.com',
    'debug' => true,
    'timezone' => 'Europe/Paris'
];
```

3. Configuration spécifique à l'environnement (app.production.php) :
```php
<?php

declare(strict_types=1);

return [
    'url' => 'https://www.monsite.com',
    'debug' => false
];
```

### Initialisation et utilisation

```php
<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Factory\ConfigFactory;
use Cocoon\Config\Cache\FileCache;

// Initialiser l'environnement
Environment::init('development');

// Créer une instance du cache (optionnel)
$cache = new FileCache(__DIR__ . '/cache');

// Charger les configurations
$config = ConfigFactory::fromDirectory(__DIR__ . '/config', $cache);

// Utiliser la configuration
$url = $config->get('app.url');
$debug = $config->get('app.debug');
```

### Gestion du cache

Le cache est recommandé en production pour optimiser les performances :

```php
use Cocoon\Config\Cache\FileCache;

// Créer une instance du cache
$cache = new FileCache(__DIR__ . '/cache');

// Utiliser le cache avec la factory
$config = ConfigFactory::fromDirectory(__DIR__ . '/config', $cache);

// Vider le cache si nécessaire
$cache->clear();
```

### Validation des données

La bibliothèque offre des méthodes de validation de type :

```php
// Vérifier l'existence d'une clé
if ($config->has('app.url')) {
    $url = $config->get('app.url');
}

// Obtenir une valeur avec une valeur par défaut
$timeout = $config->get('app.timeout', 30);

// Validation des types
if ($config->isString('app.url')) {
    // Traitement des chaînes
}

if ($config->isInt('app.port')) {
    // Traitement des entiers
}

if ($config->isBool('app.debug')) {
    // Traitement des booléens
}

if ($config->isArray('database.mysql')) {
    // Traitement des tableaux
}
```

### Interface web

L'interface web intégrée permet de :
- Visualiser les configurations par environnement
- Modifier les valeurs en temps réel
- Exporter les configurations
- Consulter l'historique des modifications

```php
// Dans votre index.php
require 'vendor/autoload.php';

use Cocoon\Config\Environment\Environment;
use Cocoon\Config\Factory\ConfigFactory;

// Initialiser l'environnement
$env = $_GET['env'] ?? 'development';
Environment::init($env);

// Charger les configurations
$config = ConfigFactory::fromDirectory(__DIR__ . '/config');
```

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.