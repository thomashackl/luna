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
                $('section#luna-save-filters').addClass('hidden-js');
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

        addEmail: function() {
            var template = $('section#luna-email-template');
            var newEl = template.clone();
            var count = parseInt(template.data('number-of-emails'), 10) + 1;
            newEl.attr('id', null).
                attr('data-number-of-emails', null);
            newEl.find('input[name="email-template-address"]').attr('name', 'email[' + count + '][address]');
            newEl.find('select[name="email-template-type"]').attr('name', 'email[' + count + '][type]');
            newEl.find('input[name="email-template-default"]').
                attr('name', 'email-default').
                attr('value', count);
            newEl.insertBefore('a.luna-email-add');
            template.data('number-of-emails', count);
        },

        addPhonenumber: function() {
            var template = $('section#luna-phone-template');
            var newEl = template.clone();
            var count = template.data('number-of-phonenumbers') + 1;
            newEl.attr('id', null).
                data('number-of-phonenumbers', null);
            newEl.find('input[name="phone-template-number"]').attr('name', 'phone[' + count + '][number]');
            newEl.find('select[name="phone-template-type"]').attr('name', 'phone[' + count + '][type]');
            newEl.find('input[name="phone-template-default"]').
                attr('name', 'phone-default').
                attr('value', count);
            newEl.insertBefore('a.luna-phone-add');
            template.data('number-of-phonenumbers', count);
        },

        addSkill: function(skill) {
            var div = $('div#luna-person-skills');
            if (div.children('div#luna-skill-' + skill.replace(' ', '-')).length == 0) {
                var skillDiv = $('<div>').
                    addClass('luna-skill').
                    attr('id', 'luna-skill-' + skill.replace(' ', '-')).
                    html(skill);
                var input = $('<input>').
                    attr('type', 'hidden').
                    attr('name', 'skills[]').
                    attr('value', skill);
                var a = $('<a>').
                    attr('href', '').
                    addClass('luna-skill-remove').
                    on('click', function() {
                        STUDIP.Luna.removeEntry(this);
                        return false;
                    });
                var img = $('<img>').
                    attr('src', STUDIP.ASSETS_URL + 'images/icons/blue/trash.svg').
                    attr('width', '16').
                    attr('height', '16').
                    addClass('icon-role-clickable').
                    addClass('icon-role-trash');
                a.append(img);
                skillDiv.append(input);
                skillDiv.append(a);
                div.append(skillDiv);
            }
        },

        addTag: function(tag) {
            var div = $('div#luna-person-tags');
            if (div.children('div#luna-tag-' + tag.replace(' ', '-')).length == 0) {
                var tagDiv = $('<div>').
                    addClass('luna-tag').
                    attr('id', 'luna-tag-' + tag.replace(' ', '-')).
                    html(tag);
                var input = $('<input>').
                    attr('type', 'hidden').
                    attr('name', 'tags[]').
                    attr('value', tag);
                var a = $('<a>').
                    attr('href', '').
                    addClass('luna-tag-remove').
                    on('click', function() {
                        STUDIP.Luna.removeEntry(this);
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

        removeEntry: function(element) {
            $(element).parent().remove();
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

        loadCompanies: function(startPage) {
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

        setEntriesPerPage: function(type, element) {
            var fullUrl = $(element).data('set-url').split('?');
            var url = fullUrl[0];
            if (fullUrl[1] != '' && fullUrl[1] != null) {
                url += '?' + fullUrl[1];
            }
            var parent = $(element).parent();
            var newValue = $(element).children('option:selected').attr('value');
            $.ajax({
                url: url,
                data: {
                    'count': newValue,
                    'type': type
                },
                dataType: 'json',
                beforeSend: function (xhr, settings) {
                    parent.html($('<img>').
                        attr('width', 16).
                        attr('height', 16).
                        attr('src', STUDIP.ASSETS_URL + 'images/ajax-indicator-black.svg'));
                },
                success: function() {
                    switch (type) {
                        case 'persons':
                            STUDIP.Luna.loadPersons(0);
                            break;
                        case 'companies':
                            STUDIP.Luna.loadCompanies(0);
                            break;
                    }
                }
            });
            return false;
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

            var statusInput = $('input[name="status"]');
            statusInput.autocomplete({
                source: statusInput.data('available-status'),
                minLength: 2
            });

            $('a.luna-email-add').on('click', function() {
                STUDIP.Luna.addEmail();
                return false;
            });

            $('a.luna-phone-add').on('click', function() {
                STUDIP.Luna.addPhonenumber();
                return false;
            });

            $('a.luna-skill-remove').on('click', function() {
                STUDIP.Luna.removeEntry(this);
                return false;
            });

            var skillInput = $('input[name="skill"]');
            $('a.luna-skill-add').on('click', function() {
                STUDIP.Luna.addSkill(skillInput.val());
                skillInput.val('');
                return false;
            });
            skillInput.autocomplete({
                source: skillInput.data('available-skills'),
                minLength: 2,
                select: function(event, ui) {
                    STUDIP.Luna.addSkill(ui.item.value);
                    skillInput.val('');
                }
            });
            skillInput.on('keypress', function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    event.preventDefault();
                    STUDIP.Luna.addSkill(skillInput.val());
                    skillInput.val('');
                }
            });

            $('a.luna-tag-remove').on('click', function() {
                STUDIP.Luna.removeEntry(this);
                return false;
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

            $('input[name="docs[]"]').on('change', function() {
                STUDIP.Luna.prepareFileUpload(this);
            });

            $('a#luna-toggle-recipients').on('click', function() {
                $('#luna-recipients').toggle();
                $('#luna-show-recipients').toggle();
                $('#luna-hide-recipients').toggle();
                return false;
            });

            var markers = $('label#luna-markers');
            var addMarker = $('#luna-add-marker');
            markers.children('select').on('change', function() {
                var selected = $(this).children('option:selected');
                $('#luna-marker-description').html(selected.data('description'));
                if (selected.attr('value') != '') {
                    addMarker.removeClass('hidden-js');
                } else {
                    addMarker.addClass('hidden-js');
                }
            });
            markers.insertAfter('div.buttons');
            addMarker.on('click', function() {
                markers.parent().children('textarea').
                    insertAtCaret($('label#luna-markers select option:selected').attr('value'));
                return false;
            });
        }
    };

    $(document).ready(function () {
        STUDIP.Luna.init();
        $(document).on('dialog-open', function () {
            STUDIP.Luna.init();
        });
        var dataDiv = $('div#luna-data');
        if (dataDiv.length > 0) {
            switch (dataDiv.data('type')) {
                case 'persons':
                    STUDIP.Luna.loadPersons(0);
                    break;
                case 'companies':
                    STUDIP.Luna.loadCompanies(0);
                    break;
            }
        }
    });

}(jQuery, STUDIP));
