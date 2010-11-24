<ul <?php echo $view['menu']->attributes(array()/*$item->getAttributes()*/) ?>>
    <?php foreach($item->getChildren() as $child): ?>
        <?php echo $view->render('MenuBundle:Menu:Item', array('item' => $child)) ?>
    <?php endforeach ?>
</ul>
