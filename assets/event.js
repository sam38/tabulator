$(function(){
    // contestant selector
    const $contestants = $('#contestant'),
        $tally = $('section[data-type="contestants"]'),
        $criteria = $('.criteria'),
        $modal = $('#scoreModal'),
        $score = $('#score'),
        modal = new bootstrap.Modal($modal, {});

    let tallyData = {};
    if ($tally.length) {
        tallyData = JSON.parse($tally.find('.data').text());
        window.data = tallyData;
        console.log(tallyData);
    }
    $contestants.on('change', function (e) {
        e.preventDefault();
        const value = $contestants.val();
        $contestants.parent().find('.image').removeClass('active');
        $(`#c_${value}`).addClass('active');

        // Enable scoring and remove alert
        if (! value) {
            $tally.removeClass('active');
            return false;
        }

        const key = `c${value}`,
            data = tallyData.hasOwnProperty(key) ? tallyData[key] : null;
        if (data) {
            data.forEach((item) => {
                const $elm = $(`#${item.id}`);
                if (! $elm.length) { return }
                setCriteriaScore($elm.find('button'), item.score);
            });
        } else {
            $criteria.each(function(index, element) {
                const $item = $(element);
                setCriteriaScore($item.find('button'), 0);
            });
        }
        $tally.addClass('active');
    });

    $criteria.on('click', 'button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $elm = $(this).parent();
        $modal.data('index', $elm.data('index'))
            .data('id', $elm.attr('id'));
        $modal.find('.modal-title').text(`Score: ${$elm.find('.title').text()}`);
        $score.val($elm.find('button').text());
        modal.show();
    });

    $('#updateScore').on('click', function (e) {
        e.preventDefault();
        const $elm = $(`.criteria[data-index="${$modal.data('index')}"]`);
        setCriteriaScore($elm.find('button'), $score.val());
        // update table value
        $(`#cr_${$contestants.val()}`).find(`div[data-ref="${$modal.data('id')}"] .average`).text($score.val());
        modal.hide();

        const allScores = [];
        $criteria.each(function(index, element) {
            const $item = $(element);
            allScores.push({
                id: $item.attr('id'),
                score: parseInt($item.find('button').text())
            });
        });

        // Persist the score update
        const data = JSON.stringify({
            event: parseInt($contestants.data('event')),
            judge: parseInt($contestants.data('judge')),
            contestant: parseInt($contestants.val()),
            scores: allScores
        });
        $.ajax({
            type: 'post',
            url: '/wp-json/v1/score',
            headers: {
                'x-brand-id': 'Kanorau'
            },
            data: data,
            success: function (data) {
                tallyData[`c${$contestants.val()}`] = allScores;
            },
            error: function (xhr, status, error) {
                console.log(xhr, status, error);
            }
        });
    });

    // build rank table
    const $table = $('.score-table');
    if ($table.length) {
        $table.on('click', '.thead button', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const $elm = $(this);
            const isActive = $elm.hasClass('active');
            // remove active class from other filters
            $table.find('.thead button').removeClass('active');
            if (! isActive) {
                $elm.addClass('active');
            }
            setTableRank();
        });
        setTableRank();
    }

    // tabs
    $('.nav-tabs .nav-link').on('click', function (e) {
        e.preventDefault();
        const $elm = $(this);
        $elm.parents('.nav-tabs').find('.nav-link').removeClass('active');
        $elm.addClass('active');
        $('[data-tab-content]').removeClass('active');
        $(`[data-tab-content="${$elm.data('target')}"]`).addClass('active');
    });

    function setTableRank()
    {
        // check for active filter
        const $activeFilter = $table.find('.thead button.active'),
            data = {};
        let referenceId = 'total';
        if ($activeFilter.length) {
            referenceId = $activeFilter.data('ref');
        }
        $table.find(`div[data-ref="${referenceId}"]`).each(function(index, element) {
            const $item = $(element),
                contestantId = $item.parents('.tr').attr('id');
            let value = parseFloat($item.find('.average').text());
            data[contestantId] = isNaN(value) ? 0 : value;
        });

        // sort by rank
        const sortedRank = [];
        for (let item in data) {
            sortedRank.push([item, data[item]]);
        }

        sortedRank.sort(function(a, b) {
            return b[1] - a[1];
        });

        console.log(sortedRank);

        // set order
        sortedRank.forEach(function(item, i) {
            let $row = $(`#${item[0]}`);
            $row.css('order', i+1).find('[data-ref="rank"]').text(i+1);
        })
    }

    function setCriteriaScore($elm, scoreText)
    {
        const score = parseInt(scoreText) || 0;
        let className= score <= 4 ? 'danger' : (score <= 7 ? 'info' : 'success');
        className = score === 0 ? 'light' : className;
        $elm.text(score)
            .removeClass('btn-light btn-danger btn-info btn-success')
            .addClass(`btn-${className}`);
    }
});
