<div class="alert alert-info mb-4" role="alert">
    Select a judge to start!
</div>
<section data-type="judges">
    <?php
    foreach ($judges as $item) {
        if (in_array($item->ID, @$data['judges'])) {
            ?>
            <a href="<?=eventLink('?action=tally&judge=' . base64_encode($item->ID))?>"
               class="item display-6"><?=$item->post_title?></a>
        <?php }} ?>
</section>
