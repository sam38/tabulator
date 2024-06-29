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
                '<input type="hidden" name="weight[]" size="8" placeholder="Weight %" required>' +
                '<input type="hidden" name="id[]" value="' + getCriteriaId() + '">' +
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

    function getCriteriaId()
    {
        let min = 10000;
        let max = 9999999999;
        // min and max included
        return 'id' + Math.floor(Math.random() * (max - min + 1) + min);
    }

    const $events = jQuery('#tallyEvents');
    if ($events.length) {
        tallyEvent($events);
    }
});




