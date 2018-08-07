<?php

/**
 * LunaCompanyFilter.php
 * provides filters and searching for LunaCompanies.
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

class LunaCompanyFilter
{

    public static function getFilterFields($all = false)
    {
        $fields = [
            'name' => [
                'name' => dgettext('luna', 'Name'),
                'table' => 'luna_companies',
                'ids' => 'name',
                'dbvalues' => 'name',
                'class' => 'LunaCompany',
                'is_id' => false
            ]
        ];
        if ($all) {
            $fields = $fields + [
                'address' => [
                    'name' => dgettext('luna', 'Anschrift'),
                    'table' => 'luna_companies',
                    'ids' => 'address',
                    'dbvalues' => 'address',
                    'class' => 'LunaCompany',
                    'is_id' => false
                ],
                'zip' => [
                    'name' => dgettext('luna', 'PLZ'),
                    'table' => 'luna_companies',
                    'ids' => 'zip',
                    'dbvalues' => 'zip',
                    'class' => 'LunaCompany',
                    'is_id' => false
                ]
            ];
        }
        $fields = $fields + [
            'city' => [
                'name' => dgettext('luna', 'Stadt'),
                'table' => 'luna_companies',
                'ids' => 'city',
                'dbvalues' => 'city',
                'class' => 'LunaCompany',
                'is_id' => false
            ],
            'country' => [
                'name' => dgettext('luna', 'Land'),
                'table' => 'luna_companies',
                'ids' => 'country',
                'dbvalues' => 'country',
                'class' => 'LunaCompany',
                'is_id' => false
            ]
        ];
        if ($all) {
            $fields = $fields + [
                'email' => [
                    'name' => dgettext('luna', 'E-Mailadresse'),
                    'table' => 'luna_companies',
                    'ids' => 'email',
                    'dbvalues' => 'email',
                    'class' => 'LunaCompany',
                    'is_id' => false
                ],
                'phone' => [
                    'name' => dgettext('luna', 'Telefonnummer'),
                    'table' => 'luna_companies',
                    'ids' => 'phone',
                    'dbvalues' => 'phone',
                    'class' => 'LunaCompany',
                    'is_id' => false
                ],
                'fax' => [
                    'name' => dgettext('luna', 'Fax'),
                    'table' => 'luna_companies',
                    'ids' => 'fax',
                    'dbvalues' => 'fax',
                    'class' => 'LunaCompany',
                    'is_id' => false
                ],
                'homepage' => [
                    'name' => dgettext('luna', 'Homepage'),
                    'table' => 'luna_companies',
                    'ids' => 'homepage',
                    'dbvalues' => 'homepage',
                    'class' => 'LunaCompany',
                    'is_id' => false
                ]
            ];
        }
        $fields = $fields + [
            'sector' => [
                'name' => dgettext('luna', 'Branche'),
                'table' => 'luna_companies',
                'ids' => 'sector',
                'dbvalues' => 'sector',
                'class' => 'LunaCompany',
                'is_id' => false
            ],
            'skills' => [
                'name' => dgettext('luna', 'Kompetenz'),
                'table' => 'luna_company_skill',
                'ids' => 'skill_id',
                'dbvalues' => 'name',
                'class' => 'LunaSkill',
                'is_id' => true,
                'linked' => 'luna_skills'
            ],
            'tags' => [
                'name' => dgettext('luna', 'Schlagwort'),
                'table' => 'luna_company_tag',
                'ids' => 'tag_id',
                'dbvalues' => 'name',
                'class' => 'LunaTag',
                'is_id' => true,
                'linked' => 'luna_tags'
            ]
        ];
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
        $values = $filter['class']::getDistinctValues($client, $field, 'company');
        $result = [
            'compare' => [
                '=' => dgettext('luna', 'ist'),
                '!=' => dgettext('luna', 'ist nicht')
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
        $filters = studip_json_decode(UserConfig::get($user_id)->LUNA_COMPANY_FILTER);
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
        return $GLOBALS['user']->cfg->store('LUNA_COMPANY_FILTER', studip_json_encode($data));
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
        return $GLOBALS['user']->cfg->store('LUNA_COMPANY_FILTER', studip_json_encode($filters));
    }

    public static function getFilterPresets($client)
    {
        $presets = $GLOBALS['user']->cfg->LUNA_COMPANY_FILTER_PRESETS;
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
        $presets = $config->LUNA_COMPANY_FILTER_PRESETS ? studip_json_decode($config->LUNA_COMPANY_FILTER_PRESETS) : [];
        $presets[$client][$name] = self::getFilters($GLOBALS['user']->id, $client);
        return $config->store('LUNA_COMPANY_FILTER_PRESETS', studip_json_encode($presets));
    }

    public static function saveFilterPresets($client, $data)
    {
        $config = $GLOBALS['user']->cfg;
        $presets = $config->LUNA_COMPANY_FILTER_PRESETS ? studip_json_decode($config->LUNA_COMPANY_FILTER_PRESETS) : [];
        $presets[$client] = $data;
        return $config->store('LUNA_COMPANY_FILTER_PRESETS', studip_json_encode($presets));
    }

}
