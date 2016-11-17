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
        $fields = array(
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
                    1 => _('männlich'),
                    2 => _('weiblich')
                ),
                'class' => 'LunaUser'
            ),
            'street' => array(
                'name' => dgettext('luna', 'Straße'),
                'table' => 'luna_users',
                'ids' => 'street',
                'dbvalues' => 'street',
                'class' => 'LunaUser'
            )
        );
        if ($all) {
            $fields = $fields + array(
                    'zip' => array(
                        'name' => dgettext('luna', 'Postleitzahl'),
                        'table' => 'luna_users',
                        'ids' => 'zip',
                        'dbvalues' => 'zip',
                        'class' => 'LunaUser'
                    )
                );
        }
        $fields = $fields + array(
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
            )
        );
        if ($all) {
            $fields = $fields + array(
                'email_office' => array(
                    'name' => dgettext('luna', 'E-Mail geschäftlich'),
                    'table' => 'luna_users',
                    'ids' => 'email_office',
                    'dbvalues' => 'email_office',
                    'class' => 'LunaUser'
                ),
                'email_private' => array(
                    'name' => dgettext('luna', 'E-Mail privat'),
                    'table' => 'luna_users',
                    'ids' => 'email_private',
                    'dbvalues' => 'email_private',
                    'class' => 'LunaUser'
                ),
                'phone_office' => array(
                    'name' => dgettext('luna', 'Telefon geschäftlich'),
                    'table' => 'luna_users',
                    'ids' => 'phone_office',
                    'dbvalues' => 'phone_office',
                    'class' => 'LunaUser'
                ),
                'phone_private' => array(
                    'name' => dgettext('luna', 'Telefon privat'),
                    'table' => 'luna_users',
                    'ids' => 'phone_private',
                    'dbvalues' => 'phone_private',
                    'class' => 'LunaUser'
                ),
                'phone_mobile' => array(
                    'name' => dgettext('luna', 'Mobiltelefon'),
                    'table' => 'luna_users',
                    'ids' => 'phone_mobile',
                    'dbvalues' => 'phone_mobile',
                    'class' => 'LunaUser'
                ),
                'fax' => array(
                    'name' => dgettext('luna', 'Fax'),
                    'table' => 'luna_users',
                    'ids' => 'fax',
                    'dbvalues' => 'fax',
                    'class' => 'LunaUser'
                ),
                'homepage' => array(
                    'name' => dgettext('luna', 'Homepage'),
                    'table' => 'luna_users',
                    'ids' => 'homepage',
                    'dbvalues' => 'homepage',
                    'class' => 'LunaUser'
                )
            );
        }
        $fields = $fields + array(
            'company' => array(
                'name' => dgettext('luna', 'Firma'),
                'table' => 'luna_user_company',
                'ids' => 'company_id',
                'dbvalues' => 'name',
                'class' => 'LunaCompany'
            ),
            'skill' => array(
                'name' => dgettext('luna', 'Kompetenz'),
                'table' => 'luna_user_skills',
                'ids' => 'skill_id',
                'dbvalues' => 'name',
                'class' => 'LunaSkill'
            ),
            'tag' => array(
                'name' => dgettext('luna', 'Schlagwort'),
                'table' => 'luna_user_tag',
                'ids' => 'tag_id',
                'dbvalues' => 'name',
                'class' => 'LunaTag'
            )
        );
        $fields = $fields + array(
                'status' => array(
                    'name' => dgettext('luna', 'Status'),
                    'table' => 'luna_user',
                    'ids' => 'status',
                    'dbvalues' => 'status',
                    'class' => 'LunaUser'
                )
            );
        if ($all) {
            $fields = $fields + array(
                'graduation' => array(
                    'name' => dgettext('luna', 'Hochschulabschluss'),
                    'table' => 'luna_user',
                    'ids' => 'graduation',
                    'dbvalues' => 'graduation',
                    'class' => 'LunaUser'
                ),
                'vita' => array(
                    'name' => dgettext('luna', 'Kurzlebenslauf'),
                    'table' => 'lluna_user',
                    'ids' => 'vita',
                    'dbvalues' => 'vita',
                    'class' => 'LunaUsero'
                ),
                'qualifications' => array(
                    'name' => dgettext('luna', 'Besondere Qualifikationen'),
                    'table' => 'luna_user',
                    'ids' => 'qualification',
                    'dbvalues' => 'qualification',
                    'class' => 'LunaUser'
                ),
                'notes' => array(
                    'name' => dgettext('luna', 'Notizen'),
                    'table' => 'luna_user',
                    'ids' => 'notes',
                    'dbvalues' => 'notes',
                    'class' => 'LunaUser'
                )
            );
        }
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
                '!=' => dgettext('luna', 'ist nicht'),
                'LIKE' => dgettext('luna', 'enthält'),
                'NOT LIKE' => dgettext('luna', 'enthält nicht'),
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
        return UserConfig::get($GLOBALS['user']->id)->store('LUNA_USER_FILTER', studip_json_encode($data));
    }

    public static function addFilter($client, $column, $compare, $value)
    {
        $filters = self::getFilters($GLOBALS['user']->id);
        $filters[$client][] = array(
            'column' => $column,
            'compare' => $compare,
            'value' => $value
        );
        return UserConfig::get($GLOBALS['user']->id)->store('LUNA_USER_FILTER', studip_json_encode($filters));
    }

    public static function getFilterPresets($client)
    {
        $presets = UserConfig::get($GLOBALS['user']->id)->LUNA_USER_FILTER_PRESETS;
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
        $presets = $config->LUNA_USER_FILTER_PRESETS ? studip_json_decode($config->LUNA_USER_FILTER_PRESETS) : array();
        $presets[$client][$name] = self::getFilters($GLOBALS['user']->id, $client);
        return $config->store('LUNA_USER_FILTER_PRESETS', studip_json_encode($presets));
    }

    public static function saveFilterPresets($client, $data)
    {
        $config = UserConfig::get($GLOBALS['user']->id);
        $presets = $config->LUNA_USER_FILTER_PRESETS ? studip_json_decode($config->LUNA_USER_FILTER_PRESETS) : array();
        $presets[$client] = $data;
        return $config->store('LUNA_USER_FILTER_PRESETS', studip_json_encode($presets));
    }

}
