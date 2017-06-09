# Cascader

[![Build Status][ico-build]][link-build]
[![Code Quality][ico-code-quality]][link-code-quality]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Latest Version][ico-version]][link-packagist]
[![PDS Skeleton][ico-pds]][link-pds]

Cascader enables the creation of PHP objects from the array that represents constructor parameters. Given the class name and creation options array, it will try to create a target object, also creating nested objects if necessary.

## Installation

The preferred method of installation is via [Composer](http://getcomposer.org/). Run the following command to install the latest version of a package and add it to your project's `composer.json`:

```bash
composer require nikolaposa/cascader
```

## Usage

``` php
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

## Credits

- [Nikola Poša][link-author]
- [All Contributors][link-contributors]

## License

Released under MIT License - see the [License File](LICENSE) for details.


[ico-version]: https://img.shields.io/packagist/v/nikolaposa/cascader.svg
[ico-build]: https://travis-ci.org/nikolaposa/cascader.svg?branch=master
[ico-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/nikolaposa/cascader.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/nikolaposa/cascader.svg
[ico-pds]: https://img.shields.io/badge/pds-skeleton-blue.svg

[link-packagist]: https://packagist.org/packages/nikolaposa/cascader
[link-build]: https://travis-ci.org/nikolaposa/cascader
[link-code-coverage]: https://scrutinizer-ci.com/g/nikolaposa/cascader/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nikolaposa/cascader
[link-pds]: https://github.com/php-pds/skeleton
[link-author]: https://github.com/nikolaposa
[link-contributors]: ../../contributors
