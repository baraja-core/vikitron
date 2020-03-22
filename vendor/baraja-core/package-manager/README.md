Package manager
===============

Search all package dependencies automatically and register to your project.

**Please help improve code and documentation in English. Pull requests and issues are very welcomed!**

Install
-------

Simply use Composer:

```
composer require baraja-core/package-manager
```

And add to your project `composer.json` this `scripts` section:

```json
"scripts": {
   "post-autoload-dump": "Baraja\\PackageManager\\PackageRegistrator::composerPostAutoloadDump"
}
```

Now is your project configured.

After each `composer ...` command this Package Manager will be called automatically.

How to use
----------

In `Booting` class add create new instance of `PackageRegistrator`:

```php
$packageRegistrator = new PackageRegistrator(
   __DIR__ . '/../',    // root path
   __DIR__ . '/../temp' // temp path
);
```

**Notice:** PackageRegistrator can work alone, Nette framework is not required, only recommended.

Package.neon
------------

Imagine you want install new package. Then it you must set specific configuration to your project `common.neon`.

PackageRegistrator can scan all your installed packages and automatically create `package.neon` file with merged configuration. In your `common.neon` you define changes only and required parameters.

For correct work to `app/Booting.php` add generated configuration.

```php
$configurator->addConfig(__DIR__ . '/config/package.neon')
```

> **Warning:** Configuration file can be different in all environment. Commit to repository is not recommended.

Tasks
-----

After creating internal container with list of packages, call list of special tasks.

Default task list (but you can add more):

- Config local neon creator and normalizer
- Assets from packages copier
- Project `composer.json` normalizer
- Clear cache

If you want add your own task, simply create class with name `*Task` implementing `ITask` interface. Package manager will find your class automatically in your project or shared package.

Order of tasks can be defined by `Priority: xxx` doc comment anotation.

Default project assert manager
------------------------------

In case of your package contain directory with name `install` or `update`, all inner content will be copied to your project automatically.

Structure in directory is same as your project root.

Name convention:

- `install` copy file and directories only in case when does not exist in your project structure,
- `update` rewrite your project files in all composer actions.

If you want create file `jquery.js` to `/www/js` for example, simply define package structure:

```
/src
   - files...
/install
   /www
      /js
         - jquery.js
- composer.json
```
