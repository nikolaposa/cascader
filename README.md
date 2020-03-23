# Cascader

[![Build Status][ico-build]][link-build]
[![Code Quality][ico-code-quality]][link-code-quality]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Latest Version][ico-version]][link-packagist]
[![PDS Skeleton][ico-pds]][link-pds]

Cascader enables the creation of objects from array definitions that represent constructor parameters. Given the class name and creation options array, it will try to create a target object, also creating nested objects that may exist. Convenient as a factory for generic kind of objects.

## Installation

The preferred method of installation is via [Composer](http://getcomposer.org/). Run the following command to install the latest version of a package and add it to your project's `composer.json`:

```bash
composer require nikolaposa/cascader
```

## Usage

```php
$cascader = new Cascader();

$object = $cascader->create(RootObject::class, [
    'name' => 'foo',
    'sub_object' => [
        'category' => 'bar',
        'count' => 10,
    ],
    'is_active' => true,
]);
```

See [more examples][link-examples].

## Credits

- [Nikola Poša][link-author]
- [All Contributors][link-contributors]

## License

Released under MIT License - see the [License File](LICENSE) for details.


[ico-version]: https://img.shields.io/packagist/v/nikolaposa/cascader.svg
[ico-build]: https://travis-ci.com/nikolaposa/cascader.svg?branch=master
[ico-code-coverage]: https://scrutinizer-ci.com/g/nikolaposa/cascader/badges/coverage.png?b=master
[ico-code-quality]: https://scrutinizer-ci.com/g/nikolaposa/cascader/badges/quality-score.png?b=master
[ico-pds]: https://img.shields.io/badge/pds-skeleton-blue.svg

[link-examples]: examples
[link-packagist]: https://packagist.org/packages/nikolaposa/cascader
[link-build]: https://travis-ci.com/nikolaposa/cascader
[link-code-coverage]: https://scrutinizer-ci.com/g/nikolaposa/cascader/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nikolaposa/cascader
[link-pds]: https://github.com/php-pds/skeleton
[link-author]: https://github.com/nikolaposa
[link-contributors]: ../../contributors
