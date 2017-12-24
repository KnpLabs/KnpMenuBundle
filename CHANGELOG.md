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
