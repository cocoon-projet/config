[![codecov](https://codecov.io/gh/cocoon-projet/config/graph/badge.svg?token=KM7Y127Z7J)](https://codecov.io/gh/cocoon-projet/config) [![PHP Composer](https://github.com/cocoon-projet/config/actions/workflows/ci.yml/badge.svg)](https://github.com/cocoon-projet/config/actions/workflows/ci.yml)

## Introduction

cocoon-projet/config est une librairie PHP moderne qui permet de gérer les configurations de votre application de manière flexible et sécurisée. Elle supporte plusieurs environnements (development, production, testing) et offre des fonctionnalités avancées comme le cache, la validation.

## Pré-requis

- PHP 8.0 ou supérieur
- Composer

## Installation

Via Composer :
```bash
composer require cocoon-projet/config
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

// Initialiser l'environnement
Environment::init('development');

// Charger les configurations depuis un répertoire
$config = ConfigFactory::fromDirectory(__DIR__ . '/config');

// Utiliser la configuration
$url = $config->get('app.url');
$debug = $config->get('app.debug');

// Réinitialiser l'environnement si nécessaire
Environment::reset(); // Retourne à l'environnement par défaut ('development')

// Vous pouvez aussi créer une configuration directement à partir d'un tableau
$config = ConfigFactory::fromArray([
    'app' => [
        'url' => 'http://www.monsite.com',
        'debug' => true,
        'timezone' => 'Europe/Paris'
    ],
    'database' => [
        'host' => 'localhost',
        'name' => 'ma_base',
        'user' => 'utilisateur'
    ]
]);
```

### Gestion du cache

La bibliothèque propose deux systèmes de cache complémentaires :

#### 1. Cache de configuration (ConfigurationCache)

Optimisé pour la gestion des fichiers de configuration en production :

```php
use Cocoon\Config\Cache\ConfigurationCache;

// Vérifier si le cache est valide
if (ConfigurationCache::isFresh($configDir)) {
    $config = ConfigurationCache::load();
} else {
    $config = ConfigFactory::fromDirectory($configDir);
    ConfigurationCache::save($config->all());
}

// Vider le cache si nécessaire
ConfigurationCache::clear();
```

#### 2. Cache générique (GenericFileCache)

Pour un cache plus flexible et générique :

```php
use Cocoon\Config\Cache\GenericFileCache;
use Cocoon\Config\Factory\ConfigFactory;
use Cocoon\Config\Config;

// Initialiser le cache
$cache = new GenericFileCache(__DIR__ . '/cache');

// Utiliser le cache avec la factory
$config = ConfigFactory::fromDirectory(__DIR__ . '/config', $cache);

// Créer une configuration manuellement
$config = new Config([
    'app' => [
        'url' => 'http://www.monsite.com',
        'debug' => true
    ]
]);

// Opérations de cache manuelles
$cache->set('ma_cle', $valeur);
$valeur = $cache->get('ma_cle');
$cache->delete('ma_cle');
$cache->clear();
```

### Bonnes pratiques de cache

1. **En production**
   - Utilisez `ConfigurationCache` pour les performances optimales
   - Activez le cache en production uniquement
   - Videz le cache lors des déploiements

2. **En développement**
   - Désactivez le cache pour voir les changements en temps réel
   - Utilisez `GenericFileCache` pour des tests de performance

3. **Sécurité**
   - Placez le dossier de cache hors de la racine web
   - Définissez les bonnes permissions sur le dossier
   - Ne stockez pas d'informations sensibles dans le cache

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