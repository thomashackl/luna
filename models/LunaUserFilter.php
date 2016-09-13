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
                'table' => 'luna_user',
                'column' => 'firstname'
            ),
            'lastname' => array(
                'name' => dgettext('luna', 'Nachname'),
                'table' => 'luna_user',
                'column' => 'lastname'
            ),
            'gender' => array(
                'name' => dgettext('luna', 'Geschlecht'),
                'table' => 'luna_user',
                'column' => 'gender',
                'values' => array(
                    0 => _('unbekannt'),
                    1 => _('männlich'),
                    2 => _('weiblich')
                )
            ),
            'street' => array(
                'name' => dgettext('luna', 'Straße'),
                'table' => 'luna_user',
                'column' => 'street'
            ),
            'city' => array(
                'name' => dgettext('luna', 'Stadt'),
                'table' => 'luna_user',
                'column' => 'city'
            ),
            'country' => array(
                'name' => dgettext('luna', 'Land'),
                'table' => 'luna_user',
                'column' => 'country'
            ),
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

    public static function getFilterValues($field)
    {
        $values = LunaUser::getDistinctValues($field);
        $fields = self::getFilterFields();
        $filter = $fields[$field];
        $result = array(
            'compare' => array(
                '==' => dgettext('luna', 'ist'),
                '!=' => dgettext('luna', 'ist nicht'),
            ),
            'values' => array()
        );
        foreach ($values as $v) {
            $current = $v;
            if ($filter['values'] && $filter['values'][$v]) {
                $current = $filter['values'][$v];
            }
            $result['values'][$v] = $current;
        }
        return $result;
    }

}
