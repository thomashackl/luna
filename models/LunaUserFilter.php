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

    public static function getFilterFields()
    {
        return array(
            'firstname' => array(
                'name' => dgettext('luna', 'Vorname'),
                'table' => 'luna_users',
                'ids' => 'firstname',
                'dbvalues' => 'firstname',
                'class' => 'LunaUser'
            ),
            'lastname' => array(
                'name' => dgettext('luna', 'Nachname'),
                'table' => 'luna_users',
                'ids' => 'lastname',
                'dbvalues' => 'lastname',
                'class' => 'LunaUser'
            ),
            'gender' => array(
                'name' => dgettext('luna', 'Geschlecht'),
                'table' => 'luna_users',
                'ids' => 'gender',
                'dbvalues' => 'gender',
                'values' => array(
                    0 => _('unbekannt'),
                    1 => _('m�nnlich'),
                    2 => _('weiblich')
                ),
                'class' => 'LunaUser'
            ),
            'street' => array(
                'name' => dgettext('luna', 'Stra�e'),
                'table' => 'luna_users',
                'ids' => 'street',
                'dbvalues' => 'street',
                'class' => 'LunaUser'
            ),
            'city' => array(
                'name' => dgettext('luna', 'Stadt'),
                'table' => 'luna_users',
                'ids' => 'city',
                'dbvalues' => 'city',
                'class' => 'LunaUser'
            ),
            'country' => array(
                'name' => dgettext('luna', 'Land'),
                'table' => 'luna_users',
                'ids' => 'country',
                'dbvalues' => 'country',
                'class' => 'LunaUser'
            ),
            'company' => array(
                'name' => dgettext('luna', 'Firma'),
                'table' => 'luna_user_company',
                'ids' => 'company_id',
                'dbvalues' => 'name',
                'class' => 'LunaCompany'
            )
        );
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
                '!=' => dgettext('luna', 'ist nicht'),
                'LIKE' => dgettext('luna', 'enth�lt'),
                'NOT LIKE' => dgettext('luna', 'enth�lt nicht'),
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
        $filters = studip_json_decode(UserConfig::get($user_id)->LUNA_USER_FILTER);
        if ($client) {
            $filters = $filters[$client];
        }
        return $filters;
    }

    public static function setFilters($client, $filters)
    {
        $data = self::getFilters($GLOBALS['user']->id);
        $data[$client] = $filters;
        UserConfig::get($GLOBALS['user']->id)->store('LUNA_USER_FILTER', studip_json_encode($data));
    }

    public function addFilter($client, $column, $compare, $value)
    {
        $filters = self::getFilters($GLOBALS['user']->id);
        $filters[$client][] = array(
            'column' => $column,
            'compare' => $compare,
            'value' => $value
        );
        UserConfig::get($GLOBALS['user']->id)->store('LUNA_USER_FILTER', studip_json_encode($filters));
    }

}