<?php

$query = "SELECT * FROM {$wpdb->prefix}posts ";
$query.= "WHERE post_type='score' AND post_title=%d";
$scores = $wpdb->get_results($wpdb->prepare($query, $post->ID));

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
<section data-type="table">
    <div class="score-table">
        <div class="thead tr">
            <div class="column-rank">Rank</div>
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
                <button type="button" data-ref="<?=$item['id']?>" data-weight="<?=$item['weight']?>">
                    <span class="d-lg-none"><?=$acronym?></span>
                    <span class="d-none d-lg-block"><?=$item['title']?></span>
                </button>
            </div>
            <?php } ?>
            <div>Total</div>
            <!--<div>Effective Total</div>-->
        </div>
        <div class="tfoot py-4">
            Total Judges - <?=count($data['judges'])?><br>
            <small class='badge rounded-pill bg-info mb-2'>N</small>
            <small>Indicates number of judges who have voted in current category.</small>
            <div class="d-lg-none indexes"><?=$indexes?></div>
        </div>
        <div class="tbody">
        <?php foreach ($contestants as $index => $item) {
            if (in_array($item->ID, $data['contestants'])) { ?>
                <div class="tr" id="c_<?=$item->ID?>" data-order="<?=$index+1?>">
                    <div class="column-rank" data-ref="rank"></div>
                    <div class="column-name"><?=$item->post_title?></div>
                    <?php
                    $totalAverage = 0;
                    $totalEffective = 0;
                    foreach ($allowedCriteria as $id) { ?>
                    <div data-ref="<?=$id?>">
                        <?php
                        $tallyKey = "c{$item->ID}-{$id}";
                        $average = '-';
                        $votingJudgesCount = '';
                        if (array_key_exists($tallyKey, $tally)) {
                            $total = count($tally[$tallyKey]);
                            if ($total) {
                                $average = round(array_sum($tally[$tallyKey]) / $total, 1);
                                $totalEffective+= $allowedCriteriaWeight[$id] * $average;
                                $totalAverage += $average;
                                $votingJudgesCount = "<small class='badge rounded-pill bg-info ms-2'>$total</small>";
                            }
                        }
                        ?>
                        <span class="average fw-bold"><?=$average?></span><?=$votingJudgesCount?>
                    </div>
                    <?php } ?>
                    <div data-ref="total">
                        <span class="average fw-bold"><?=$totalAverage?></span>
                    </div>
                    <!--<div data-ref="totalEffective"><?=$totalEffective?></div>-->
                </div>
        <?php }
        } ?>
        </div>
    </div>
</section>
