jQuery(function() {

    function tallyEvent($items)
    {
        jQuery('.select-multi').select2();

        const $criteria = jQuery("#criteria");
        $criteria.sortable({ handle: '.dashicons'});

        // add criteria
        jQuery('.add-criteria').on('click', function (e) {
            e.preventDefault();
            let item = '<span class="dashicons dashicons-sort"></span>' +
                '<input type="text" name="criteria[]" size="40" placeholder="Title" required>' +
                '<input type="text" name="weight[]" size="8" placeholder="Weight %" required>' +
                '<button type="button" class="delete-criteria">Delete</button>';
            $criteria.append(`<div>${item}</div>`);
            $criteria.sortable({ handle: '.dashicons'});
        });

        // delete criteria
        $items.on('click', '.delete-criteria', function (e) {
            e.preventDefault();
            jQuery(this).parent().remove();
            $criteria.sortable({ handle: '.dashicons'});
        });
    }

    const $events = jQuery('#tallyEvents');
    if ($events.length) {
        tallyEvent($events);
    }
});




