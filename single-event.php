<?php
/*
 * Template Name: Event Page
 * Template post Type: Event
 */
require_once(__DIR__ . "/../../../wp-load.php");

global $wp, $wpdb;
$judges = get_posts([
    'post_type' => 'judge',
    'post_status' => 'publish',
    'posts_per_page' => -1,
]);
$contestants = get_posts([
    'post_type' => 'contestant',
    'post_status' => 'publish',
    'posts_per_page' => -1,
]);

$data = get_post_meta($post->ID, 'event_data', true);
$judgeId = 0;
$judgeName = '';

function checkLogin()
{
    global $post;
    $loginId = 0;
    if (isset($_POST) && @$_POST['username'] && @$_POST['password']) {
        // validate
        $args = array(
            'name' => $_POST['username'],
            'post_type' => 'judge',
            'posts_per_page' => 1
        );
        $results = get_posts($args);
        if (count($results) && $_POST['password'] == $post->post_password) {
            $loginId = $results[0]->ID;
            wp_redirect(eventLink('?token=' . base64_encode($post->post_password . '|' . $loginId)));
        }
        $loginId = -1;
    } else {
        // check for URL
        $token = @$_GET['token'];
        if ($token) {
            $token = base64_decode($token);
            $tokenParts = explode('|', $token);
            if (count($tokenParts) == 2) {
                $loginId = $tokenParts[1];
            }
        }
    }
    return $loginId;
}

function eventLink($uri='')
{
    return get_permalink() . $uri;
}

function dump($data)
{
    echo '<pre style="background: #dedede; color #484848">';
    var_dump($data);
    echo '</pre>';
}
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
<?php
$loginId = checkLogin();
if ($loginId <= 0) { ?>
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3 col-xl-4 offset-xl-4 py-4">
            <form action="" method="post" class="py-4">
                <?php if ($loginId == -1) { ?>
                    <div class="alert alert-danger text-center" role="alert">
                        Incorrect username or password!
                    </div>
                <?php } else { ?>
                    <div class="alert alert-primary text-center" role="alert">
                        Enter the passcode to view this private event!
                    </div>
                <?php } ?>
                <div class="mb-3 row">
                    <label for="username" class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="username"
                           name="username" value="admin" autofocus required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="inputPassword" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="inputPassword"
                            name="password" value="test" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
} else {
    $action = in_array($loginId, $data['judges']) ? 'tally' : 'table';
    foreach ($judges as $item) {
        if ($item->ID == $loginId) {
            $judgeName = $item->post_title;
        }
    }

    $allowedCriteria = [];
    foreach ($data['criteria'] as $item) {
        $allowedCriteria[] = $item['id'];
    };

    $allowedCriteriaWeight = [];
    foreach ($data['criteria'] as $item) {
        $allowedCriteriaWeight[$item['id']] = floatval($item['weight']) / 100;
    };

    $allowedContestants = [];
    foreach ($contestants as $index => $item) {
        if (in_array($item->ID, $data['contestants'])) {
            $allowedContestants[] = $item->ID;
        }
    }
?>
<nav class="navbar navbar-expand-lg fixed-top bg-body-tertiary border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?=eventLink()?>">Miss Kanorau NZ <?=$post->post_title?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
            <span class="navbar-text pe-2"><?=$judgeName?></span>
        </div>
    </div>
</nav>
<div class="my-4 p-1">&nbsp;</div>
<div class="container-fluid my-4" style="background: #fff">
    <div id="app" class="py-4">
        <?php
        switch ($action) {
            case 'tally':
                include 'single-event__tally.php';
                break;
            default:
                include 'single-event__table.php';
        }
        ?>
    </div>
</div>
<footer class="py-3 border-top text-center">
    <small class="mb-3 mb-md-0 text-body-secondary">© <?=date('Y')?> Miss Kanorau NZ</small>
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
<?php } ?>
</body>
</html>
