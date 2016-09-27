(function ($, STUDIP) {
    'use strict';

    STUDIP.Luna = {

        removePerson: function(element) {
            $(element).parent().remove();
            return false;
        },

        addBeneficiary: function(id, name) {
            // Remove all formatting from name.
            var cleaned = $('<div>').html(name).text();
            var list = $('ul#luna-beneficiaries');

            if (list.children('li.' + id).length == 0) {
                var child = $('<li>').
                    addClass(id).
                    html(cleaned);

                var select = $('<select>').
                    attr('name', 'users[' + id + ']');
                var levels = list.data('levels');
                for (var i = 0 ; i < levels.length ; i++) {
                    var option = $('<option>').
                        attr('value', levels[i].value).
                        html(levels[i].name);
                    select.append(option);
                }
                child.append(select);

                list.append(child);
            }
        },

        getFilterNames: function() {
            var newFilterEl = $('#luna-newfilter');
            var nameEl = $('#luna-newfilter-name');
            var configEl = $('#luna-newfilter-config');
            $.ajax({
                url: newFilterEl.data('filternames-url'),
                dataType: 'json',
                beforeSend: function (xhr, settings) {
                    newFilterEl.removeClass('hidden-js');
                    nameEl.html(
                        $('<img>').
                            attr('width', 16).
                            attr('height', 16).
                            attr('src', STUDIP.ASSETS_URL + 'images/ajax-indicator-black.svg'));
                },
                success: function (json) {
                    newFilterEl.addClass('newfilter');
                    var select = $('<select>').
                        attr('name', 'field').
                        on('change', function() {
                            $.ajax({
                                url: $('#luna-newfilter').data('filterdata-url'),
                                data: { field: $('select[name="field"] option:selected').val() },
                                dataType: 'json',
                                beforeSend: function (xhr, settings) {
                                    configEl.html($('<img>').
                                        attr('width', 16).
                                        attr('height', 16).
                                        attr('src', STUDIP.ASSETS_URL + 'images/ajax-indicator-black.svg'));
                                },
                                success: function (json) {
                                    var compare = json.compare;
                                    var compSelect = $('<select>').
                                    attr('name', 'compare');
                                    $.each(compare, function(index, value) {
                                        var compOption = $('<option>').
                                        attr('value', index).
                                        html(value);
                                        compSelect.append(compOption);
                                    });

                                    var values = json.values;
                                    var valSelect = $('<select>').
                                    attr('name', 'value');
                                    $.each(values, function(index, value) {
                                        var valOption = $('<option>').
                                        attr('value', index).
                                        html(value);
                                        valSelect.append(valOption);
                                    });

                                    configEl.html('');
                                    configEl.append(compSelect);
                                    configEl.append(valSelect);
                                    $('button[name="apply"]').removeClass('hidden-js');
                                }
                            });
                        });
                    var option = $('<option>').
                        attr('value', '').
                        html('-- ' + newFilterEl.data('pleasechoose') + ' --');
                    select.append(option);
                    $.each(json, function(index, value) {
                        var option = $('<option>').
                            attr('value', index).
                            html(value);
                        select.append(option);
                    });
                    nameEl.html(select);
                }
            });
        },

        removeFilter: function(element) {
            $(element).parent().remove();
            var filtersEl = $('#luna-applied-filters');
            filtersEl.data('filter-count', filtersEl.data('filter-count') - 1);
            if (filtersEl.children('span.luna-filter').length == 0) {
                filtersEl.addClass('hidden-js');
            }
            STUDIP.Luna.loadPersons();
        },

        loadPersons: function() {
            var dataEl = $('#luna-data');
            $.ajax({
                url: dataEl.data('update-url'),
                data: $('input[type="hidden"][name*="filters["]').serialize(),
                dataType: 'html',
                beforeSend: function (xhr, settings) {
                    dataEl.html($('<img>').
                        attr('width', 64).
                        attr('height', 64).
                        attr('src', STUDIP.ASSETS_URL + 'images/ajax-indicator-black.svg'));
                },
                success: function (html) {
                    dataEl.html(html);
                }
            });
        }

    };

    $(document).ready(function () {
        $('#luna-add-filter').on('click', function() {
            STUDIP.Luna.getFilterNames();
            return false;
        });
        $('.luna-remove-filter').on('click', function() {
            STUDIP.Luna.removeFilter(this);
            return false;
        });
        STUDIP.Luna.loadPersons();
    });

}(jQuery, STUDIP));
