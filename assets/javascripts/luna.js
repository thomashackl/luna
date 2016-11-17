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

        loadFilterPreset: function() {
            var element = $('#luna-userfilter-preset');

            if (element.children('option:selected').attr('value') != '') {
                var fullUrl = element.data('update-url').split('?');
                var url = fullUrl[0] + '/' + element.children('option:selected').attr('value');
                if (fullUrl[1] != '') {
                    url += '?' + fullUrl[1];
                }
                $.ajax({
                    url: url,
                    dataType: 'html',
                    beforeSend: function (xhr, settings) {
                        $('div#luna-applied-filters').
                            html($('<img>').
                            attr('width', 32).
                            attr('height', 32).
                            attr('src', STUDIP.ASSETS_URL + 'images/ajax-indicator-black.svg'));
                    },
                    success: function (data) {
                        var el = $('div#luna-applied-filters');
                        el.html(data);
                        el.removeClass('hidden-js');
                        STUDIP.Luna.loadPersons(0);
                    }
                });
            }
        },

        loadPersons: function(startPage) {
            var dataEl = $('#luna-data');
            var fullUrl = $(dataEl).data('update-url').split('?');
            var url = fullUrl[0] + '/' + startPage;
            if (fullUrl[1] != '') {
                url += '?' + fullUrl[1];
            }
            $.ajax({
                url: url,
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
            return false;
        },

        addTag: function(tag) {
            var div = $('div#luna-person-tags');
            if (div.children('div#luna-tag-' + tag).length == 0) {
                var tagDiv = $('<div>').
                    addClass('luna-tag').
                    attr('id', 'luna-tag-' + tag).
                    html(tag);
                var input = $('<input>').
                    attr('type', 'hidden').
                    attr('name', 'tags[]').
                    attr('value', tag);
                var a = $('<a>').
                    attr('href', '').
                    addClass('luna-tag-remove').
                    on('click', function() {
                        STUDIP.Luna.removeTag(this);
                        return false;
                    });
                var img = $('<img>').
                    attr('src', STUDIP.ASSETS_URL + 'images/icons/blue/trash.svg').
                    attr('width', '16').
                    attr('height', '16').
                    addClass('icon-role-clickable').
                    addClass('icon-role-trash');
                a.append(img);
                tagDiv.append(input);
                tagDiv.append(a);
                div.append(tagDiv);
            }
        },

        removeTag: function(element) {
            $(element).parent().remove();
        },

        remove_attachment: function () {
            jQuery.ajax({
                url: STUDIP.ABSOLUTE_URI_STUDIP + "dispatch.php/messages/delete_attachment",
                data: {
                    'document_id' : jQuery(this).closest("li").data('document_id'),
                    'message_id' : jQuery(this).closest("form").find('input[name=message_id]').val()
                },
                type: "POST",
            });
            jQuery(this).closest("li").fadeOut(300, function() { jQuery(this).remove(); });
        },

        prepareFileUpload: function(input) {
            var ul = $('ul#luna-newdocs');
            for (var i = 0 ; i < input.files.length ; i++) {
                var li = $('<li>');
                var newdoc = $('<input>').
                    attr('type', 'hidden').
                    attr('name', 'newdocs[]').
                    attr('value', input.files[i].name)
                var text = $(document.createTextNode(input.files[i].name));
                var a = $('<a>').
                    on('click', function(event) {
                        $(this).parent().remove();
                    });
                var img = $('<img>').
                    attr('src', STUDIP.ASSETS_URL + 'images/icons/blue/trash.svg').
                    attr('width', '16').
                    attr('height', '16').
                    addClass('icon-role-clickable').
                    addClass('icon-role-trash');
                ul.append(
                    li.append(newdoc).append(text).append(
                        a.append(img)
                    )
                );
            }
        },

        init: function() {
            $('#luna-add-filter').on('click', function() {
                STUDIP.Luna.getFilterNames();
                return false;
            });

            $('#luna-userfilter-preset').on('change', function() {
                STUDIP.Luna.loadFilterPreset();
                return false;
            });

            $('a.luna-tag-remove').on('click', function() {
                STUDIP.Luna.removeTag(this);
                return false;
            });

            var statusInput = $('input[name="status"]');
            statusInput.autocomplete({
                source: statusInput.data('available-status'),
                minLength: 2
            });

            var tagInput = $('input[name="tag"]');
            $('a.luna-tag-add').on('click', function() {
                STUDIP.Luna.addTag(tagInput.val());
                tagInput.val('');
                return false;
            });
            tagInput.autocomplete({
                source: tagInput.data('available-tags'),
                minLength: 2,
                select: function(event, ui) {
                    STUDIP.Luna.addTag(ui.item.value);
                    tagInput.val('');
                }
            });
            tagInput.on('keypress', function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    event.preventDefault();
                    STUDIP.Luna.addTag(tagInput.val());
                    tagInput.val('');
                }
            });

            $('input[name="docs[]"]').on('change', function(event) {
                STUDIP.Luna.prepareFileUpload(this);
            })
        }

    };

    $(document).ready(function () {
        STUDIP.Luna.init();
        $(document).on('dialog-open', function () {
            STUDIP.Luna.init();
        });
        if ($('div#luna-data').length > 0) {
            STUDIP.Luna.loadPersons(0);
        }
    });

}(jQuery, STUDIP));
