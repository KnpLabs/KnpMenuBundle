<?php

spl_autoload_register(function($class) {
    $class = ltrim($class, '\\');
    if (0 === strpos($class, 'Knp\Bundle\MenuBundle\\')) {
        $file = __DIR__.'/../'.str_replace('\\', '/', substr($class, strlen('Knp\Bundle\MenuBundle\\'))).'.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

if (!defined('SYMFONY_SRC_DIR') || 'NOT_SET' === SYMFONY_SRC_DIR) {
    throw new \RuntimeException('You must set the Symfony src dir');
}

if (!defined('KNPMENU_SRC_DIR') || 'NOT_SET' === KNPMENU_SRC_DIR) {
    throw new \RuntimeException('You must set the KnpMenu src dir');
}

require_once SYMFONY_SRC_DIR.'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespace('Symfony', SYMFONY_SRC_DIR);
$loader->registerNamespace('Knp\Menu', KNPMENU_SRC_DIR);
$loader->register();
