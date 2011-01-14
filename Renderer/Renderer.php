<?php

namespace Bundle\MenuBundle\Renderer;
use Bundle\MenuBundle\MenuItem;

abstract class Renderer
{
    /**
     * Whether or not to render menus with pretty spacing, or fully compressed.
     */
    protected $renderCompressed = false;

    protected $charset = 'UTF-8';

    public function __construct($charset = null, $renderCompressed = null)
    {
        if(null !== $charset) {
            $this->charset = (string) $charset;
        }

        if(null !== $renderCompressed) {
            $this->renderCompressed = (boolean) $renderCompressed;
        }
    }

    /**
     * Render a HTML attribute
     */
    public function renderHtmlAttribute($name, $value)
    {
        if (true === $value) {
            return sprintf('%s="%s"', $name, $this->escape($name));
        } else {
            return sprintf('%s="%s"', $name, $this->escape($value));
        }
    }

    /**
     * Render HTML atributes
     */
    public function renderHTMLAttributes(array $attributes)
    {
        return implode('', array_map(array($this, 'htmlAttributesCallback'), array_keys($attributes), array_values($attributes)));
    }

    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * It removes empty attributes.
     *
     * @param  string $name   The attribute name
     * @param  string $value  The attribute value
     *
     * @return string The HTML representation of the HTML key attribute pair.
     */
    private function htmlAttributesCallback($name, $value)
    {
        if (false === $value || null === $value) {
            return '';
        } else {
            return ' '.$this->renderHtmlAttribute($name, $value);
        }
    }

    /**
     * Escape an HTML value
     */
    public function escape($value)
    {
        return $this->fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES, $this->charset));
    }

    /**
     * Fixes double escaped strings.
     *
     * @param  string $escaped  string to fix
     * @return string A single escaped string
     */
    protected function fixDoubleEscape($escaped)
    {
        return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
    }

    /**
     * Get whether to render compressed HTML or not
     * 
     * @return bool
     */
    public function getRenderCompressed()
    {
        return $this->renderCompressed;
    }

    /**
     * Set whether to render compressed HTML or not
     */
    public function setRenderCompressed($bool)
    {
        $this->renderCompressed = (bool) $bool;
    }

    /**
     * Get the HTML charset
     * 
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set the HTML charset
     */
    public function setCharset($string)
    {
        $this->charset = (string) $string;
    }
}
