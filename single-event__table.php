<section data-type="table">
    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <?php foreach (@$data['criteria'] as $item) { ?>
            <th scope="col"><?=$item['title']?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($contestants as $index => $item) {
            if (in_array($item->ID, @$data['contestants'])) { ?>
        <tr>
            <th scope="row"><?=$index+1?></th>
            <td><?=$item->post_title?></td>
            <?php foreach (@$data['criteria'] as $item) { ?>
            <td>-</td>
            <?php } ?>
        </tr>
        <?php } } ?>
        </tbody>
    </table>
</section>
