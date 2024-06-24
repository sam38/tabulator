$(function(){
    // contestant selector
    const $contestants = $('#contestant');
    $contestants.on('change', function (e) {
        e.preventDefault();
        const value = $contestants.val();
        $contestants.parent().find('.image').removeClass('active');
        $(`#c_${value}`).addClass('active');
    });

    const $criteria = $('.criteria'),
        $modal = $('#scoreModal'),
        $score = $('#score'),
        modal = new bootstrap.Modal($modal, {});
    $criteria.on('click', 'button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $elm = $(this).parent();
        $modal.data('index', $elm.data('index'));
        $modal.find('.modal-title').text(`Score: ${$elm.find('.h4').text()}`);
        $score.val($elm.find('button').text());
        modal.show();
    });

    $('#updateScore').on('click', function (e) {
        e.preventDefault();
        const $elm = $(`.criteria[data-index="${$modal.data('index')}"]`);
        const score = parseInt($score.val()) || 0;
        let className= score <= 4 ? 'danger' : (score <= 7 ? 'info' : 'success');
        className = score === 0 ? 'light' : className;
        $elm.find('button').text(score).removeClass('btn-light btn-danger btn-info btn-success')
            .addClass(`btn-${className}`);
        modal.hide();
    });
});
