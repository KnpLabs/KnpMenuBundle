## 3.3.0 (2023-05-xx)

* Increased minimum PHP version to 8.0 (to be consistent with KnpMenu 3.4)
* Dropped support for Symfony 3 and 4
* Deprecated use of Symfony templating component

## 3.2.0 (2021-10-30)

* Added support for Symfony 6

## 3.1.0 (2020-11-29)

* Added support for PHP 8

## 3.0.0 (2019-12-01)

* [BC break] removed deprecated features
* [BC break] made final all classes that implement an interface
* Dropped support for KnpMenu 2
* Increased minimum PHP version to 7.2 (to be consistent with KnpMenu 3)
* Removed class parameters name from service definitions

## 2.3.0 (2019-09-19)

* Bumped minimum PHP version to 7.1
* Enforced some coding standards
* Deprecated some options
* Removed deprecations in tests

## 2.2.2 (2019-06-17)

New features:

* Symfony 3.3+ autowiring support for `Knp\Menu\Provider\MenuProviderInterface`
* Tested with PHP 7.3

Bugfixes:

* Do not use deprecated method with Symfony 4.2

## 2.2.1 (2017-12-24)

Bugfixes:

* Fixed registration of the KnpMenu templates when not using the Templating component.

## 2.2.0 (2017-11-29)

New features:

* Added support for Symfony 3.3+ autowiring for `Knp\Menu\FactoryInterface`, `Knp\Menu\Matcher\MatcherInterface` and `Knp\Menu\Util\MenuManipulator`
* Added support for autoconfiguring menu voters
* Added support for Symfony 4
* Added support for private services for menu builders and renderers
* Added lazy-loading for menu providers and voters when using Symfony DI 3.3+

Removed:

* Removed support for PHP 5.5 and older

## 2.1.3 (2016-10-03)

* Added support for `getCurrentItem` in the templating helper

## 2.1.2 (2016-06-21)

* Menu extensions now also work if you replace the knp_menu.factory service with an alias
* Menu items are translated in the default template

## 2.1.1 (2015-12-15)

* Support Symfony 3
* Documentation fixes

## 2.1.0 (2015-09-28)

* Added a priority to allow controlling the order of voters
* Added new templating features to the templating helper
* Added the necessary configuration for new Twig features of KnpMenu 2.1
* Added a menu provider registering builders as services
* Removed usage of deprecated API to run on Symfony 2.7 without warning

## 2.0.0 (2014-08-01)

* Updated to KnpMenu 2 stable

## 2.0.0 alpha 1 (2013-06-23)

* Updated the bundle for KnpMenu 2.0.0 alpha1

## 1.1.2 (2013-05-25)

* Updated the composer constraint to allow Symfony 2.3 and above

## 1.1.1 (2012-11-28)

* Made the bundle compatible with Symfony 2.2

## 1.1.0 (2012-05-17)

* Updated bundle for KnpMenu 1.1
* Added bundle inheritance support in the BundleAliasProvider
* Added parameters for the default options of the ListRenderer and TwigRenderer

## 1.0.0 (2012-05-03)

* Initial release of the new bundle based on KnpMenu
