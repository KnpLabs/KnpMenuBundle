<ul <?php echo $view['menu']->attributes(array()/*$item->getAttributes()*/) ?>>
    <?php foreach($item->getChildren() as $child): ?>
        <?php echo $view->render('MenuBundle:Menu:item.php', array('item' => $child)) ?>
    <?php endforeach ?>
</ul>
