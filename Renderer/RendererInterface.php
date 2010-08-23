<?php

namespace Bundle\MenuBundle\Renderer;
use Bundle\MenuBundle\MenuItem;

interface RendererInterface
{
  /**
   * Renders menu tree.
   *
   * Depth values corresppond to:
   *   * 0 - no children displayed at all (would return a blank string)
   *   * 1 - directly children only
   *   * 2 - children and grandchildren
   *
   * @param MenuItem    $item         Menu item
   * @param integer     $depth        The depth of children to render
   *
   * @return string
   */
  public function render(MenuItem $item, $depth = null);
}
