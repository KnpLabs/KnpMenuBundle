<ul <?php echo $view['menu']->attributes($item->getAttributes()) ?>>
    <?php foreach($item->getChildren() as $child): ?>
        <?php echo $view->render('KnplabsMenu:Menu:item.html.php', array('item' => $child)) ?>
    <?php endforeach ?>
</ul>
