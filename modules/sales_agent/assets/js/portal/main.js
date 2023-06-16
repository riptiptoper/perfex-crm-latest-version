var side_bar = $('#side-menu');
var setup_menu = $('#setup-menu-wrapper');
var content_wrapper = $('#wrapper');
(function($) {
    "use strict";
    
    // Predefined global variables
    $("body").on('change', '#mass_select_all', function() {
        var to, rows, checked;
        to = $(this).data('to-table');

        rows = $('.table-' + to).find('tbody tr');
        checked = $(this).prop('checked');
        $.each(rows, function() {
            var input = $($($(this).find('td').eq(0)).find('input'));
            if(!input.is(':disabled')){
                input.prop('checked', checked);
            }
        });
    });
    // Check for active class in sidebar links
    

})(jQuery);

// General helper function for $.get ajax requests
function requestGet(uri, params) {
    "use strict";
    
    params = typeof(params) == 'undefined' ? {} : params;
    var options = {
        type: 'GET',
        url: uri.indexOf(site_url) > -1 ? uri : site_url + uri
    };
    return $.ajax($.extend({}, options, params));
}


// General function for all datatables serverside
function initDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder) {
    "use strict";
    var table = typeof (selector) == 'string' ? $("body").find('table' + selector) : selector;

    if (table.length === 0) {
        return false;
    }

    fnserverparams = (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') ? [] : fnserverparams;

    // If not order is passed order by the first column
    if (typeof (defaultorder) == 'undefined') {
        defaultorder = [
            [0, 'asc']
        ];
    } else {
        if (defaultorder.length === 1) {
            defaultorder = [defaultorder];
        }
    }

    var user_table_default_order = table.attr('data-default-order');

    if (!empty(user_table_default_order)) {
        var tmp_new_default_order = JSON.parse(user_table_default_order);
        var new_defaultorder = [];
        for (var i in tmp_new_default_order) {
            // If the order index do not exists will throw errors
            if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
                new_defaultorder.push(tmp_new_default_order[i]);
            }
        }
        if (new_defaultorder.length > 0) {
            defaultorder = new_defaultorder;
        }
    }

    var length_options = [10, 25, 50, 100];
    var length_options_names = [10, 25, 50, 100];

    app.options.tables_pagination_limit = parseFloat(app.options.tables_pagination_limit);

    if ($.inArray(app.options.tables_pagination_limit, length_options) == -1) {
        length_options.push(app.options.tables_pagination_limit);
        length_options_names.push(app.options.tables_pagination_limit);
    }

    length_options.sort(function (a, b) {
        return a - b;
    });
    length_options_names.sort(function (a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(app.lang.dt_length_menu_all);

    var dtSettings = {
        "language": app.lang.datatables,
        "processing": true,
        "retrieve": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 750,
        "bDeferRender": true,
        "autoWidth": false,
        dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>",
        "pageLength": app.options.tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "columnDefs": [{
            "searchable": false,
            "targets": notsearchable,
        }, {
            "sortable": false,
            "targets": notsortable
        }],
        "fnDrawCallback": function (oSettings) {
            _table_jump_to_page(this, oSettings);
            if (oSettings.aoData.length === 0) {
                $(oSettings.nTableWrapper).addClass('app_dt_empty');
            } else {
                $(oSettings.nTableWrapper).removeClass('app_dt_empty');
            }
        },
        "fnCreatedRow": function (nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title);
            $(nRow).attr('data-toggle', aData.Data_Toggle);
        },
        "initComplete": function (settings, json) {
            var t = this;
            var $btnReload = $('.btn-dt-reload');
            $btnReload.attr('data-toggle', 'tooltip');
            $btnReload.attr('title', app.lang.dt_button_reload);

            var $btnColVis = $('.dt-column-visibility');
            $btnColVis.attr('data-toggle', 'tooltip');
            $btnColVis.attr('title', app.lang.dt_button_column_visibility);

            t.wrap('<div class="table-responsive"></div>');

            var dtEmpty = t.find('.dataTables_empty');
            if (dtEmpty.length) {
                dtEmpty.attr('colspan', t.find('thead th').length);
            }

            // Hide mass selection because causing issue on small devices
            if (is_mobile() && $(window).width() < 400 && t.find('tbody td:first-child input[type="checkbox"]').length > 0) {
                t.DataTable().column(0).visible(false, false).columns.adjust();
                $("a[data-target*='bulk_actions']").addClass('hide');
            }

            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
            var th_last_child = t.find('thead th:last-child');
            var th_first_child = t.find('thead th:first-child');
            if (th_last_child.text().trim() == app.lang.options) {
                th_last_child.addClass('not-export');
            }
            if (th_first_child.find('input[type="checkbox"]').length > 0) {
                th_first_child.addClass('not-export');
            }
            mainWrapperHeightFix();
        },
        "order": defaultorder,
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function (d) {
                if (typeof (csrfData) !== 'undefined') {
                    d[csrfData['token_name']] = csrfData['hash'];
                }
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
                if (table.attr('data-last-order-identifier')) {
                    d['last_order_identifier'] = table.attr('data-last-order-identifier');
                }
            }
        },
        buttons: get_datatable_buttons(table),
    };

    table = table.dataTable(dtSettings);
    var tableApi = table.DataTable();

    var hiddenHeadings = table.find('th.not_visible');
    var hiddenIndexes = [];

    $.each(hiddenHeadings, function () {
        hiddenIndexes.push(this.cellIndex);
    });

    setTimeout(function () {
        for (var i in hiddenIndexes) {
            tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
        }
    }, 10);

    if (table.hasClass('customizable-table')) {
        var tableToggleAbleHeadings = table.find('th.toggleable');
        var invisible = $('#hidden-columns-' + table.attr('id'));
        try {
            invisible = JSON.parse(invisible.text());
        } catch (err) {
            invisible = [];
        }

        $.each(tableToggleAbleHeadings, function () {
            var cID = $(this).attr('id');
            if ($.inArray(cID, invisible) > -1) {
                tableApi.column('#' + cID).visible(false);
            }
        });

    }

    // Fix for hidden tables colspan not correct if the table is empty
    if (table.is(':hidden')) {
        table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
    }

    table.on('preXhr.dt', function (e, settings, data) {
        if (settings.jqXHR) settings.jqXHR.abort();
    });

    return tableApi;
}

// Check if field is empty
function empty(data) {
    "use strict";
    if (typeof(data) == 'number' || typeof(data) == 'boolean') {
        return false;
    }
    if (typeof(data) == 'undefined' || data === null) {
        return true;
    }
    if (typeof(data.length) != 'undefined') {
        return data.length === 0;
    }
    var count = 0;
    for (var i in data) {
        if (data.hasOwnProperty(i)) {
            count++;
        }
    }
    return count === 0;
}

// Returns datatbles export button array based on settings
// Admin area only
function get_datatable_buttons(table) {
    // pdfmake arabic fonts support
    "use strict";
    var formatExport = {
        body: function(data, row, column, node) {

            // Fix for notes inline datatables
            // Causing issues because of the hidden textarea for edit and the content is duplicating
            // This logic may be extended in future for other similar fixes
            var newTmpRow = $('<div></div>', data);
            newTmpRow.append(data);

            if (newTmpRow.find('[data-note-edit-textarea]').length > 0) {
                newTmpRow.find('[data-note-edit-textarea]').remove();
                data = newTmpRow.html().trim();
            }
            // Convert e.q. two months ago to actual date
            var exportTextHasActionDate = newTmpRow.find('.text-has-action.is-date');

            if(exportTextHasActionDate.length) {
               data = exportTextHasActionDate.attr('data-title');
            }

            if (newTmpRow.find('.row-options').length > 0) {
                newTmpRow.find('.row-options').remove();
                data = newTmpRow.html().trim();
            }

            if (newTmpRow.find('.table-export-exclude').length > 0) {
                newTmpRow.find('.table-export-exclude').remove();
                data = newTmpRow.html().trim();
            }

            if (data) {

            }

            // Datatables use the same implementation to strip the html.
            var div = document.createElement("div");
            div.innerHTML = data;
            var text = div.textContent || div.innerText || "";

            return text.trim();
        }
    };
    var table_buttons_options = [];

    if (typeof(table_export_button_is_hidden) != 'function' || !table_export_button_is_hidden()) {
        table_buttons_options.push({
            extend: 'collection',
            text: app.lang.dt_button_export,
            className: 'btn btn-default-dt-options',
            buttons: [{
                extend: 'excel',
                text: app.lang.dt_button_excel,
                footer: true,
                exportOptions: {
                    columns: [':not(.not-export)'],
                    rows: function(index) {
                        return _dt_maybe_export_only_selected_rows(index, table);
                    },
                    format: formatExport,
                },
            }, {
                extend: 'csvHtml5',
                text: app.lang.dt_button_csv,
                footer: true,
                exportOptions: {
                    columns: [':not(.not-export)'],
                    rows: function(index) {
                        return _dt_maybe_export_only_selected_rows(index, table);
                    },
                    format: formatExport,
                }
            }, {
                extend: 'pdfHtml5',
                text: app.lang.dt_button_pdf,
                footer: true,
                exportOptions: {
                    columns: [':not(.not-export)'],
                    rows: function(index) {
                        return _dt_maybe_export_only_selected_rows(index, table);
                    },
                    format: formatExport,
                },
                orientation: 'landscape',
                customize: function(doc) {
                    // Fix for column widths
                    var table_api = $(table).DataTable();
                    var columns = table_api.columns().visible();
                    var columns_total = columns.length;
                    var total_visible_columns = 0;

                    for (i = 0; i < columns_total; i++) {
                        // Is only visible column
                        if (columns[i] == true) {
                            total_visible_columns++;
                        }
                    }

                    setTimeout(function() {
                        if (total_visible_columns <= 5) {
                            var pdf_widths = [];
                            for (i = 0; i < total_visible_columns; i++) {
                                pdf_widths.push((735 / total_visible_columns));
                            }

                            doc.content[1].table.widths = pdf_widths;
                        }
                    }, 10);

                    if (app.user_language.toLowerCase() == 'persian' || app.user_language.toLowerCase() == 'arabic') {
                        doc.defaultStyle.font = Object.keys(pdfMake.fonts)[0];
                    }

                    doc.styles.tableHeader.alignment = 'left';
                    doc.defaultStyle.fontSize = 10;

                    doc.styles.tableHeader.fontSize = 10;
                    doc.styles.tableHeader.margin = [3, 3, 3, 3];

                    doc.styles.tableFooter.fontSize = 10;
                    doc.styles.tableFooter.margin = [3, 0, 0, 0];

                    doc.pageMargins = [2, 20, 2, 20];
                }
            }, {
                extend: 'print',
                text: app.lang.dt_button_print,
                footer: true,
                exportOptions: {
                    columns: [':not(.not-export)'],
                    rows: function(index) {
                        return _dt_maybe_export_only_selected_rows(index, table);
                    },
                    format: formatExport,
                }
            }],
        });
    }
    var tableButtons = $("body").find('.table-btn');

    $.each(tableButtons, function() {
        var b = $(this);
        if (b.length && b.attr('data-table')) {
            if ($(table).is(b.attr('data-table'))) {
                table_buttons_options.push({
                    text: b.text().trim(),
                    className: 'btn btn-default-dt-options',
                    action: function(e, dt, node, config) {
                        b.click();
                    }
                });
            }
        }
    });

    if (!$(table).hasClass('dt-inline')) {
        table_buttons_options.push({
            text: '<i class="fa fa-refresh"></i>',
            className: 'btn btn-default-dt-options btn-dt-reload',
            action: function(e, dt, node, config) {
                dt.ajax.reload();
            }
        });
    }


    return table_buttons_options;
}

// Fix for height on the wrapper
function mainWrapperHeightFix() {
    "use strict";
    // Get and set current height
    var headerH = 63;
    var navigationH = side_bar.height();
    var contentH = $("#wrapper").find('.content').height();
    setup_menu.css('min-height', ($(document).outerHeight(true) - (headerH * 2)) + 'px');

    content_wrapper.css('min-height', $(document).outerHeight(true) - headerH + 'px');
    // Set new height when content height is less then navigation
    if (contentH < navigationH) {
        content_wrapper.css("min-height", navigationH + 'px');
    }

    // Set new height when content height is less then navigation and navigation is less then window
    if (contentH < navigationH && navigationH < $(window).height()) {
        content_wrapper.css("min-height", $(window).height() - headerH + 'px');
    }
    // Set new height when content is higher then navigation but less then window
    if (contentH > navigationH && contentH < $(window).height()) {
        content_wrapper.css("min-height", $(window).height() - headerH + 'px');
    }
    // Fix for RTL main admin menu height
    if (is_mobile() && isRTL == 'true') {
        side_bar.css('min-height', $(document).outerHeight(true) - headerH + 'px');
    }
}

function init_selectpicker() {
    "use strict";
    appSelectPicker();
}

function appSelectPicker(element) {
    "use strict";
    if (typeof(element) == 'undefined') {
        element = $("body").find('select.selectpicker');
    }

    if (element.length) {
        element.selectpicker({
            showSubtext: true
        });
    }
}

function requestGetJSON(uri, params) {
    "use strict";
    params = typeof (params) == 'undefined' ? {} : params;
    params.dataType = 'json';
    return requestGet(uri, params);
}

// Format money function
function format_money(total, excludeSymbol) {
    "use strict";
    if (typeof (excludeSymbol) != 'undefined' && excludeSymbol) {
        return accounting.formatMoney(total, {
            symbol: ''
        });
    }

    return accounting.formatMoney(total);
}

function hidden_input(name, val) {
    "use strict";
    return '<input type="hidden" name="' + name + '" value="' + val + '">';
}

function slugify(string) {
    "use strict";
    return string
        .toString()
        .trim()
        .toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w\-]+/g, "")
        .replace(/\-\-+/g, "-")
        .replace(/^-+/, "")
        .replace(/-+$/, "");
}

function _table_jump_to_page(table, oSettings) {
    "use strict";
    var paginationData = table.DataTable().page.info();
    var previousDtPageJump = $("body").find('#dt-page-jump-' + oSettings.sTableId);

    if (previousDtPageJump.length) {
        previousDtPageJump.remove();
    }

    if (paginationData.pages > 1) {

        var jumpToPageSelect = $("<select></select>", {
            "data-id": oSettings.sTableId,
            "class": "dt-page-jump-select form-control",
            'id': 'dt-page-jump-' + oSettings.sTableId
        });

        var paginationHtml = '';

        for (var i = 1; i <= paginationData.pages; i++) {
            var selectedCurrentPage = ((paginationData.page + 1) === i) ? 'selected' : '';
            paginationHtml += "<option value='" + i + "'" + selectedCurrentPage + ">" + i + "</option>";
        }

        if (paginationHtml != '') {
            jumpToPageSelect.append(paginationHtml);
        }

        $("#" + oSettings.sTableId + "_wrapper .dt-page-jump").append(jumpToPageSelect);
    }
}

function is_mobile() {
    "use strict";
    if (typeof(app) != 'undefined' && typeof(app.is_mobile) != 'undefined') {
        return app.is_mobile;
    }

    try { document.createEvent("TouchEvent"); return true; } catch (e) { return false; }
}