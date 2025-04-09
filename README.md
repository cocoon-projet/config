[![codecov](https://codecov.io/gh/cocoon-projet/config/graph/badge.svg?token=KM7Y127Z7J)](https://codecov.io/gh/cocoon-projet/config) [![PHP Composer](https://github.com/cocoon-projet/config/actions/workflows/ci.yml/badge.svg)](https://github.com/cocoon-projet/config/actions/workflows/ci.yml)

# Cocoon Config

Une bibliothèque PHP 8+ moderne et flexible pour la gestion de configuration avec support des variables d'environnement.

## Fonctionnalités

- ✨ Support PHP 8.0+
- 🔄 Gestion multi-environnements (development, production, testing)
- 🌍 Variables d'environnement avec la fonction helper `env()`
- 📦 Chargement automatique des fichiers de configuration
- 🔒 Validation des types et des valeurs

## Installation

```bash
composer require cocoon-projet/config
```

## Configuration

1. Créez un dossier `config` à la racine de votre projet
2. Ajoutez vos fichiers de configuration PHP :

```php
// config/database.php
return [
    'default' => 'mysql',
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'database' => env('DB_NAME', 'database'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ]
];
```

3. Créez un fichier `.env` à la racine :

```env
APP_ENV=development
DB_HOST=localhost
DB_PORT=3306
DB_NAME=my_database
DB_USER=root
DB_PASSWORD=secret
```

### Gestion des environnements

La librairie supporte nativement la gestion de différents environnements (development, production, testing) via une convention de nommage des fichiers :

```
config/
├── database.php           # Configuration par défaut
├── database.production.php # Configuration spécifique à la production
├── database.development.php # Configuration spécifique au développement
└── database.testing.php   # Configuration spécifique aux tests
```

Le système fonctionne de la manière suivante :

1. **Fichiers spécifiques à l'environnement** :
   - Format : `nom.environnement.php` (ex: `database.production.php`)
   - Ces fichiers sont chargés uniquement pour l'environnement correspondant
   - Ils écrasent les valeurs du fichier de configuration par défaut

2. **Fichiers de configuration par défaut** :
   - Format : `nom.php` (ex: `database.php`)
   - Servent de configuration de base
   - Sont utilisés si aucun fichier spécifique à l'environnement n'existe


3. **Exemple de chargement** :
   ```php
   // En environnement production
   $config = ConfigFactory::fromDirectory(__DIR__ . '/config');
   $dbHost = $config->get('database.mysql.host'); // Valeur de database.production.php ou database.php
   ```

4. **Priorité de chargement** :
   - Le fichier spécifique à l'environnement est chargé en priorité
   - Si non trouvé, le fichier par défaut est utilisé
   - Les valeurs sont fusionnées de manière récursive

## Utilisation

### Chargement des variables d'environnement

```php
use Cocoon\Config\Environment\EnvironmentVariables;

// Charger depuis un fichier .env (indiquer le repertoire) 
EnvironmentVariables::load(__DIR__);

// Ou charger manuellement
EnvironmentVariables::set('APP_ENV', 'development');
// ou
env('APP_ENV', 'development');
EnvironmentVariables::set('DB_HOST', 'localhost');
// ou
env('DB_HOST', 'localhost');
```

### Chargement de la configuration .env et fichier de configuration

```php
use Cocoon\Config\Factory\ConfigFactory;
use Cocoon\Config\Environment\Environment;

// Charger depuis un fichier .env (indiquer le repertoire) 
EnvironmentVariables::load(__DIR__);

// Initialiser l'environnement
Environment::init(EnvironmentVariables::get('APP_ENV', 'development'));

// Charger la configuration
$config = ConfigFactory::fromDirectory(__DIR__ . '/config');

// Accéder aux valeurs
$dbHost = $config->get('database.mysql.host');

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

### Variables d'environnement

```php
// Récupérer une variable d'environnement
$dbHost = env('DB_HOST', 'localhost');

// Vérifier l'existence d'une variable
if (env('DEBUG', false)) {
    // ...
}
```

## Bonnes pratiques

1. **Organisation des fichiers**
   - Un fichier par domaine (database.php, mail.php, etc.)
   - Utilisation de sous-tableaux pour organiser les configurations
   - Documentation des options dans les commentaires

2. **Variables d'environnement**
   - Toujours fournir des valeurs par défaut
   - Utiliser des noms explicites et cohérents
   - Documenter les variables requises
   - Ne jamais commiter le fichier `.env`
   - Créer un fichier `.env.example` pour documenter les variables nécessaires

3. **Sécurité**
   - Ne jamais commiter le fichier `.env`
   - Utiliser des valeurs sécurisées en production
   - Valider les entrées utilisateur

4. **En production**
   - Utilisez `ConfigurationCache` pour les performances optimales
   - Activez le cache en production uniquement
   - Videz le cache lors des déploiements

5. **En développement**
   - Désactivez le cache pour voir les changements en temps réel
   - Utilisez `GenericFileCache` pour des tests de performance

6. **Sécurité**
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

## Licence

MIT License - voir le fichier [LICENSE](LICENSE) pour plus de détails.