<span class="badge text-bg-primary"><?=$judgeName?></span>
<section data-type="contestants">
    <?php
    $options = '';
    foreach ($contestants as $index => $item) {
        if (in_array($item->ID, @$data['contestants'])) {
            $options.= "<option value='{$item->ID}'>{$item->post_title}</option>";
            $image = wp_get_attachment_image_src(
                get_post_thumbnail_id($item->ID),
                'single-post-thumbnail'
            );
            ?>
            <div class="image <?=$index == 0 ? 'active' : ''?>" id="c_<?=$item->ID?>"
                 style="background-image: url(<?=@$image[0]?>)"></div>
        <?php }} ?>
    <select class="form-select form-select-lg mb-4" name="contestant" id="contestant"><?=$options?></select>

    <?php foreach (@$data['criteria'] as $index => $item) { ?>
        <div class="criteria" data-index="<?=$index?>" data-weight="<?=$item['weight']?>">
            <div class="title h3"><?=$item['title']?></div>
            <button type="button" class="btn btn-lg btn-light">0</button>
        </div>
    <?php } ?>
</section>
