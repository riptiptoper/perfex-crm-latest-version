var side_bar = $('#side-menu');

(function($) {
    "use strict";

    var $linkSidebarActive = side_bar.find('li > a[href="' + location + '"]');
    if ($linkSidebarActive.length) {
        $linkSidebarActive.parents('li').not('.quick-links').addClass('active');
        // Set aria expanded to true
        $linkSidebarActive.prop('aria-expanded', true);
        $linkSidebarActive.parents('ul.nav-second-level').prop('aria-expanded', true);
        $linkSidebarActive.parents('li').find('a:first-child').prop('aria-expanded', true);
    }

    // Handle minimalize sidebar menu
    $('.hide-menu').click(function(e) {

        e.preventDefault();
        if ($('body').hasClass('hide-sidebar')) {
            $('body').removeClass('hide-sidebar').addClass('show-sidebar');
        } else {
            $('body').removeClass('show-sidebar').addClass('hide-sidebar');
        }
        

    });

    side_bar.metisMenu();


      if (typeof(contracts_by_type) != 'undefined') {
    new Chart($('#contracts-by-type-chart'), {
        type: 'bar',
        data: JSON.parse(contracts_by_type),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true,
                    }
                }]
            }
        }
    });
}
})(jQuery);