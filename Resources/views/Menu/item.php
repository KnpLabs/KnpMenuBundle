<?php if($item->shouldBeRendered()): ?>
    <li <?php echo $view['menu']->attributes($view['menu']->getItemAttributes($item)) ?>">
        <?php echo (string) $item ?>
    </li>
<?php endif ?>
