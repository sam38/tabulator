<?php
$query = "SELECT * FROM {$wpdb->prefix}posts ";
$query.= "WHERE post_type='score' AND post_title=%d AND post_excerpt=%d";
$scores = $wpdb->get_results($wpdb->prepare($query, $post->ID, $loginId));
$json = [];
foreach ($scores as $item) {
    $json['c' . $item->post_content_filtered] = @unserialize($item->post_content);
}

$votes = [];
foreach ($scores as $score) {
    if (! in_array($score->post_content_filtered, $allowedContestants)) { continue; }

    $vote = [
        'contestant' => $score->post_content_filtered,
        'judge' => $score->post_excerpt,
    ];
    $items = unserialize($score->post_content);
    foreach ($items as $item) {
        if (in_array($item->id, $allowedCriteria)) {
            $vote[$item->id] = $item->score;
        }
    }
    $votes[] = $vote;
}

$tally = [];
foreach ($votes as $vote) {
    $key = 'c' . $vote['contestant'];
    foreach ($allowedCriteria as $item) {
        $criteriaKey = $key . '-' . $item;
        if (! array_key_exists($criteriaKey, $tally)) {
            $tally[$criteriaKey] = [];
        }
        if ($vote[$item]) {
            $tally[$criteriaKey][] = $vote[$item];
        }
    }
}
?>
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" data-target="profile">Profile View</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-target="table">Table View</a>
    </li>
</ul>
<div data-tab-content="profile" class="active container">
    <div class="row">
        <section data-type="contestants">
            <div>
                <div class="alert alert-info" role="alert">Select a contestant to start!</div>
                <?php
                $options = '';
                foreach ($contestants as $index => $item) {
                    if (in_array($item->ID, $data['contestants'])) {
                        $options.= "<option value='{$item->ID}'>{$item->post_title}</option>";
                        $image = wp_get_attachment_image_src(
                            get_post_thumbnail_id($item->ID),
                            'single-post-thumbnail'
                        );
                        ?>
                        <div class="image" id="c_<?=$item->ID?>"
                             style="background-image: url(<?=@$image[0]?>)"></div>
                    <?php }} ?>
                <select class="form-select form-select-lg my-4" name="contestant" id="contestant"
                        data-event="<?=$post->ID?>" data-judge="<?=$loginId?>"
                ><option value="">-- Select a contestant --</option><?=$options?></select>
            </div>
            <div class="scores mx-2">
                <?php foreach (@$data['criteria'] as $index => $item) { ?>
                    <div id="<?=$item['id']?>" class="criteria" data-index="<?=$index?>" data-weight="<?=$item['weight']?>">
                        <div class="title h3"><?=$item['title']?></div>
                        <button type="button" class="btn btn-lg btn-light">0</button>
                    </div>
                <?php } ?>
            </div>
            <div class="d-none data"><?=json_encode($json)?></div>
        </section>
    </div>
</div>
<div data-tab-content="table">
    <div class="score-table">
        <div class="thead tr">
            <div class="column-name">Contestant</div>
            <?php
            $indexes = '';
            foreach ($data['criteria'] as $item) {
            ?>
                <div title="<?=$item['title']?>">
                    <?php
                    $words = explode(' ', $item['title']);
                    $acronym = '';
                    foreach ($words as $word) {
                        $acronym .= $word[0];
                    }
                    $indexes.= "<span class='badge text-bg-info'>{$acronym}</span> - " . $item['title'] . '<br>';
                    ?>
                    <div class="d-flex justify-content-center">
                        <span class="d-lg-none"><?=$acronym?></span>
                        <span class="d-none d-lg-block"><?=$item['title']?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="tfoot py-4">
            <div class="d-lg-none indexes"><?=$indexes?></div>
        </div>
        <div class="tbody">
            <?php foreach ($contestants as $index => $item) {
                if (in_array($item->ID, $data['contestants'])) { ?>
                    <div class="tr" id="cr_<?=$item->ID?>" data-order="<?=$index+1?>">
                        <div class="column-name"><?=$item->post_title?></div>
                        <?php
                        $totalAverage = 0;
                        foreach ($allowedCriteria as $id) { ?>
                            <div data-ref="<?=$id?>">
                                <?php
                                $tallyKey = "c{$item->ID}-{$id}";
                                $average = '-';
                                if (array_key_exists($tallyKey, $tally)) {
                                    $total = count($tally[$tallyKey]);
                                    if ($total) {
                                        $average = round(array_sum($tally[$tallyKey]) / $total, 1);
                                    }
                                }
                                ?>
                                <span class="average fw-bold"><?=$average?></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
</div>
