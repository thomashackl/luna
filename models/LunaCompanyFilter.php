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
        $fields = array(
            'name' => array(
                'name' => dgettext('luna', 'Name'),
                'table' => 'luna_companies',
                'ids' => 'name',
                'dbvalues' => 'name',
                'class' => 'LunaCompany'
            ),
            'city' => array(
                'name' => dgettext('luna', 'Stadt'),
                'table' => 'luna_companies',
                'ids' => 'city',
                'dbvalues' => 'city',
                'class' => 'LunaCompany'
            ),
            'country' => array(
                'name' => dgettext('luna', 'Land'),
                'table' => 'luna_company',
                'ids' => 'country',
                'dbvalues' => 'country',
                'class' => 'LunaCompany'
            )
        );
        return $fields;
    }

    public static function getFilterNames()
    {
        $names = array();
        foreach (self::getFilterFields() as $key => $f) {
            $names[$key] = $f['name'];
        }
        return $names;
    }

    public static function getFilterValues($client, $field)
    {
        $fields = self::getFilterFields();
        $filter = $fields[$field];
        $values = $filter['class']::getDistinctValues($client, $field);
        $result = array(
            'compare' => array(
                '=' => dgettext('luna', 'ist'),
                '!=' => dgettext('luna', 'ist nicht')
            ),
            'values' => array()
        );
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
        if ($client) {
            $filters = $filters[$client];
        }
        return $filters;
    }

    public static function setFilters($client, $filters)
    {
        $data = self::getFilters($GLOBALS['user']->id);
        $data[$client] = $filters;
        return UserConfig::get($GLOBALS['user']->id)->store('LUNA_COMPANY_FILTER', studip_json_encode($data));
    }

    public static function addFilter($client, $column, $compare, $value)
    {
        $filters = self::getFilters($GLOBALS['user']->id);
        $filters[$client][] = array(
            'column' => $column,
            'compare' => $compare,
            'value' => $value
        );
        return UserConfig::get($GLOBALS['user']->id)->store('LUNA_COMPANY_FILTER', studip_json_encode($filters));
    }

    public static function getFilterPresets($client)
    {
        $presets = UserConfig::get($GLOBALS['user']->id)->LUNA_COMPANY_FILTER_PRESETS;
        if ($presets) {
            $decoded = studip_json_decode($presets);
            $presets = $decoded[$client] ?: array();
        } else {
            $presets = array();
        }
        return $presets;
    }

    public static function saveFilterPreset($client, $name)
    {
        $config = UserConfig::get($GLOBALS['user']->id);
        $presets = $config->LUNA_COMPANY_FILTER_PRESETS ? studip_json_decode($config->LUNA_COMPANY_FILTER_PRESETS) : array();
        $presets[$client][$name] = self::getFilters($GLOBALS['user']->id, $client);
        return $config->store('LUNA_COMPANY_FILTER_PRESETS', studip_json_encode($presets));
    }

    public static function saveFilterPresets($client, $data)
    {
        $config = UserConfig::get($GLOBALS['user']->id);
        $presets = $config->LUNA_COMPANY_FILTER_PRESETS ? studip_json_decode($config->LUNA_COMPANY_FILTER_PRESETS) : array();
        $presets[$client] = $data;
        return $config->store('LUNA_COMPANY_FILTER_PRESETS', studip_json_encode($presets));
    }

}
