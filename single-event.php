<?php
/*
 * Template Name: Event Page
 * Template post Type: Event
 */
require_once(__DIR__ . "/../../../wp-load.php");

global $wp;

$data = get_post_meta($post->ID, 'event_data', true);
$contestants = get_posts([
    'post_type' => 'contestant',
    'post_status' => 'publish',
    'posts_per_page' => -1,
]);
$judges = get_posts([
    'post_type' => 'judge',
    'post_status' => 'publish',
    'posts_per_page' => -1,
]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/wp-content/plugins/tally/assets/event.css?v=1.0">
</head>
<body class="text-bg-light">
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Miss Kanorau NZ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?=home_url($wp->request)?>">Score</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?=home_url($wp->request)?>?table">Table</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container" style="background: #fff">
    <div class="row">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3">
            <div id="app" class="py-4">
                <section data-type="judges">
                    <?php
                    foreach ($judges as $item) {
                        if (in_array($item->ID, @$data['judges'])) {
                            ?>
                            <div class="item display-6"><?=$item->post_title?></div>
                        <?php }} ?>
                </section>
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
                        <div class="h4"><?=$item['title']?></div>
                        <button type="button" class="btn btn-light">0</button>
                    </div>
                    <?php } ?>
                </section>
            </div>
        </div>
    </div>
</div>
<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 border-top">
    <div class="col-md-4 d-flex align-items-center">
        <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
            <svg class="bi" width="30" height="24"><use xlink:href="#bootstrap"></use></svg>
        </a>
        <span class="mb-3 mb-md-0 text-body-secondary">© <?=date('Y')?> Miss Kanorau NZ</span>
    </div>
</footer>
<div class="modal fade" id="scoreModal" data-bs-backdrop="static"  tabindex="-1" data-index="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Score</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select class="form-select form-select-lg" name="score" id="score">
                    <option value="0" selected>-- Set Score --</option>
                    <?php for ($i=1; $i <= 10; $i++) { ?>
                    <option value="<?=$i?>"><?=$i?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" id="updateScore" class="btn btn-primary">Update Score</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="/wp-content/plugins/tally/assets/event.js"></script>
</body>
</html>
