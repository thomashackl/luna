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
            $.ajax({
                url: $('#luna-newfilter').data('filternames-url'),
                dataType: 'json',
                beforeSend: function (xhr, settings) {
                    $('#luna-newfilter-name').html(
                        $('<img>').
                            attr('src', STUDIP.ASSETS_URL + 'images/ajax_indicator_small.gif'));
                },
                success: function (json) {
                    var select = $('<select>').
                        attr('name', 'column').
                        on('change', function() {
                            $.ajax({
                                url: $('#luna-newfilter').data('filterdata-url'),
                                data: { column: $('select[name="column"] option:selected').val() },
                                dataType: 'json',
                                beforeSend: function (xhr, settings) {
                                    $('#luna-newfilter-config').html(
                                        $('#luna-newfilter-config').html() +
                                        $('<img>').
                                        attr('src', STUDIP.ASSETS_URL + 'images/ajax_indicator_small.gif'));
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

                                    $('#luna-newfilter-config').html(compSelect + valSelect);
                                }
                            });
                        });
                    $.each(json, function(index, value) {
                        var option = $('<option>').
                            attr('value', index).
                            html(value);
                        select.append(option);
                    });
                    $('#luna-newfilter').html(select);
                }
            });
        }

    };

    $(document).ready(function () {
        $('#luna-add-filter').on('click', function(event) {
            STUDIP.Luna.getFilterNames();
            return false;
        });
    });

}(jQuery, STUDIP));
