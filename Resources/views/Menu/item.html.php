<?php if($item->shouldBeRendered()): ?>
    <li <?php echo $view['menu']->attributes($view['menu']->getItemAttributes($item)) ?>>
        <a href="<?php echo $item->getUri() ?>"><?php echo $item->getName() ?></a>
        <?php if($item->hasChildren()): ?>
            <?php echo $view->render('KnplabsMenu:Menu:menu.html.php', array('item' => $item)) ?>
        <?php endif; ?>
    </li>
<?php endif; ?>
