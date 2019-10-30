<?php

/**
 * LunaUserFilter.php
 * provides filters and searching for LunaUsers.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Luna
 */

class LunaUserFilter
{

    public static function getFilterFields($all = false)
    {
        $fields = [
            'firstname' => [
                'name' => dgettext('luna', 'Vorname'),
                'table' => 'luna_users',
                'ids' => 'firstname',
                'dbvalues' => 'firstname',
                'class' => 'LunaUser',
                'is_id' => false
            ],
            'lastname' => [
                'name' => dgettext('luna', 'Nachname'),
                'table' => 'luna_users',
                'ids' => 'lastname',
                'dbvalues' => 'lastname',
                'class' => 'LunaUser',
                'is_id' => false
            ],
            'gender' => [
                'name' => dgettext('luna', 'Geschlecht'),
                'table' => 'luna_users',
                'ids' => 'gender',
                'dbvalues' => 'gender',
                'values' => [
                    0 => _('unbekannt'),
                    1 => _('männlich'),
                    2 => _('weiblich')
                ],
                'class' => 'LunaUser',
                'is_id' => false
            ],
            'informal' => [
                'name' => dgettext('luna', 'Anrede per du'),
                'table' => 'luna_users',
                'ids' => 'informal',
                'dbvalues' => 'informal',
                'values' => [
                    1 => _('ja'),
                    0 => _('nein')
                ],
                'class' => 'LunaUser',
                'is_id' => false
            ],
            'address' => [
                'name' => dgettext('luna', 'Anschrift'),
                'table' => 'luna_users',
                'ids' => 'address',
                'dbvalues' => 'address',
                'class' => 'LunaUser',
                'is_id' => false
            ]
        ];
        if ($all) {
            $fields = $fields + [
                    'zip' => [
                        'name' => dgettext('luna', 'Postleitzahl'),
                        'table' => 'luna_users',
                        'ids' => 'zip',
                        'dbvalues' => 'zip',
                        'class' => 'LunaUser',
                        'is_id' => false
                    ]
                ];
        }
        $fields = $fields + [
            'city' => [
                'name' => dgettext('luna', 'Stadt'),
                'table' => 'luna_users',
                'ids' => 'city',
                'dbvalues' => 'city',
                'class' => 'LunaUser',
                'is_id' => false
            ],
            'country' => [
                'name' => dgettext('luna', 'Land'),
                'table' => 'luna_users',
                'ids' => 'country',
                'dbvalues' => 'country',
                'class' => 'LunaUser',
                'is_id' => false
            ]
        ];
        if ($all) {
            $fields = $fields + [
                'emails' => [
                    'name' => dgettext('luna', 'E-Mailadresse'),
                    'table' => 'luna_email',
                    'ids' => 'user_id',
                    'dbvalues' => 'email',
                    'class' => 'LunaEMail',
                    'is_id' => true
                ],
                'phonenumbers' => [
                    'name' => dgettext('luna', 'Telefonnummer'),
                    'table' => 'luna_phone',
                    'ids' => 'user_id',
                    'dbvalues' => 'number',
                    'class' => 'LunaPhone',
                    'is_id' => true
                ],
                'fax' => [
                    'name' => dgettext('luna', 'Fax'),
                    'table' => 'luna_users',
                    'ids' => 'fax',
                    'dbvalues' => 'fax',
                    'class' => 'LunaUser',
                    'is_id' => false
                ],
                'homepage' => [
                    'name' => dgettext('luna', 'Homepage'),
                    'table' => 'luna_users',
                    'ids' => 'homepage',
                    'dbvalues' => 'homepage',
                    'class' => 'LunaUser',
                    'is_id' => false
                ]
            ];
        }
        $fields = $fields + [
            'companies' => [
                'name' => dgettext('luna', 'Unternehmen'),
                'table' => 'luna_user_company',
                'ids' => 'company_id',
                'dbvalues' => 'name',
                'class' => 'LunaCompany',
                'is_id' => true,
                'linked' => 'luna_companies'
            ],
            'skills' => [
                'name' => dgettext('luna', 'Kompetenz'),
                'table' => 'luna_user_skills',
                'ids' => 'skill_id',
                'dbvalues' => 'name',
                'class' => 'LunaSkill',
                'is_id' => true,
                'linked' => 'luna_skills'
            ],
            'tags' => [
                'name' => dgettext('luna', 'Schlagwort'),
                'table' => 'luna_user_tag',
                'ids' => 'tag_id',
                'dbvalues' => 'name',
                'class' => 'LunaTag',
                'is_id' => true,
                'linked' => 'luna_tags'
            ]
        ];
        $fields = $fields + [
                'status' => [
                    'name' => dgettext('luna', 'Status'),
                    'table' => 'luna_users',
                    'ids' => 'status',
                    'dbvalues' => 'status',
                    'class' => 'LunaUser',
                    'is_id' => false
                ]
            ];
        if ($all) {
            $fields = $fields + [
                'graduation' => [
                    'name' => dgettext('luna', 'Hochschulabschluss'),
                    'table' => 'luna_users',
                    'ids' => 'graduation',
                    'dbvalues' => 'graduation',
                    'class' => 'LunaUser',
                    'is_id' => false
                ],
                'notes' => [
                    'name' => dgettext('luna', 'Notizen'),
                    'table' => 'luna_users',
                    'ids' => 'notes',
                    'dbvalues' => 'notes',
                    'class' => 'LunaUser',
                    'is_id' => false
                ]
            ];
        }
        return $fields;
    }

    public static function getFilterNames()
    {
        $names = [];
        foreach (self::getFilterFields() as $key => $f) {
            $names[$key] = $f['name'];
        }
        return $names;
    }

    public static function getFilterValues($client, $field)
    {
        $fields = self::getFilterFields();
        $filter = $fields[$field];
        $values = $filter['class']::getDistinctValues($client, $field, 'user');
        $result = [
            'compare' => [
                '=' => dgettext('luna', 'ist'),
                '!=' => dgettext('luna', 'ist nicht'),
                'LIKE' => dgettext('luna', 'enthält'),
                'NOT LIKE' => dgettext('luna', 'enthält nicht'),
            ],
            'values' => []
        ];
        foreach ($values as $v) {
            $current = $v['value'];
            if ($filter['values'] && $filter['values'][$v['id']]) {
                $current = $filter['values'][$v['id']];
            }
            $result['values'][$v['id']] = $current;
        }
        return $result;
    }

    public static function getFilters($user_id, $client = '')
    {
        $config = UserConfig::get($user_id)->LUNA_USER_FILTER;
        if ($config === null || $config === '') {
            $filters = [];
        } else {
            $filters = studip_json_decode($config);
        }
        foreach ($filters as $clientId => $clientFilters) {
            $actualFilters = is_array($clientFilters['filters']) ? $clientFilters['filters'] : [];
            foreach ($actualFilters as $index => $filter) {
                if (!$filter['column'] || !$filter['compare']) {
                    unset($filters[$clientId]['filters'][$index]);
                }
            }
        }
        if ($client) {
            $filters = $filters[$client];
        }
        return $filters;
    }

    public static function setFilters($client, $filters)
    {
        $data = self::getFilters($GLOBALS['user']->id);
        $data[$client] = $filters;
        return $GLOBALS['user']->cfg->store('LUNA_USER_FILTER', studip_json_encode($data));
    }

    public static function addFilter($client, $column, $compare, $value)
    {
        $filters = self::getFilters($GLOBALS['user']->id);
        if ($column && $compare) {
            $filters[$client]['filters'][] = [
                'column' => $column,
                'compare' => $compare,
                'value' => $value
            ];
        }
        return $GLOBALS['user']->cfg->store('LUNA_USER_FILTER', studip_json_encode($filters));
    }

    public static function getFilterPresets($client)
    {
        $presets = $GLOBALS['user']->cfg->LUNA_USER_FILTER_PRESETS;
        if ($presets) {
            $decoded = studip_json_decode($presets);
            $presets = $decoded[$client] ?: [];
        } else {
            $presets = [];
        }
        return $presets;
    }

    public static function saveFilterPreset($client, $name)
    {
        $config = $GLOBALS['user']->cfg;
        $presets = $config->LUNA_USER_FILTER_PRESETS ? studip_json_decode($config->LUNA_USER_FILTER_PRESETS) : [];
        $presets[$client][$name] = self::getFilters($GLOBALS['user']->id, $client);
        return $config->store('LUNA_USER_FILTER_PRESETS', studip_json_encode($presets));
    }

    public static function saveFilterPresets($client, $data)
    {
        $config = $GLOBALS['user']->cfg;
        $presets = $config->LUNA_USER_FILTER_PRESETS ? studip_json_decode($config->LUNA_USER_FILTER_PRESETS) : [];
        $presets[$client] = $data;
        return $config->store('LUNA_USER_FILTER_PRESETS', studip_json_encode($presets));
    }

}
