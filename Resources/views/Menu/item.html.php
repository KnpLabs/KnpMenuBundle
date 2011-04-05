<?php if($item->shouldBeRendered()): ?>
    <li <?php echo $view['menu']->attributes($view['menu']->getItemAttributes($item)) ?>>
        <?php if (($item->getIsCurrent() && $item->getParent()->getCurrentAsLink()) || !$item->getIsCurrent()): ?>
        <a href="<?php echo $item->getUri() ?>"><?php echo $item->getLabel() ?></a>
        <?php else: ?>
        <span <?php echo $view['menu']->attributes($item->getLabelAttributes()) ?>><?php echo $item->getLabel() ?></span>
        <?php endif ?>
        <?php if($item->hasChildren()): ?>
            <?php echo $view->render('KnplabsMenuBundle:Menu:menu.html.php', array('item' => $item)) ?>
        <?php endif; ?>
    </li>
<?php endif; ?>
