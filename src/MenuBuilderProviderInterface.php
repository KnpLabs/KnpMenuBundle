<?php

namespace Knp\Bundle\MenuBundle;

interface MenuBuilderProviderInterface
{
    /**
     * Gets the list of menu builders provided by this class.
     *
     * The return value is a map of "menu name" => "builder method".
     * The builder method must be a public method. It will receive the
     * array of options as first argument.
     *
     * @return array
     */
    public static function getMenuBuilders();
}
