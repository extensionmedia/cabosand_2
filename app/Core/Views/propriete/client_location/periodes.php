<?php foreach($periodes as $periode): ?>
    <div class="">
        <?= $periode['date_debut'] . ' -> ' . $periode['date_fin'] . ' : ' . $periode['status']?>
    </div>
<?php endforeach ?>