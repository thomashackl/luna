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
                'class' => 'LunaUser',
                'is_id' => false
            ),
            'lastname' => array(
                'name' => dgettext('luna', 'Nachname'),
                'table' => 'luna_users',
                'ids' => 'lastname',
                'dbvalues' => 'lastname',
                'class' => 'LunaUser',
                'is_id' => false
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
                'class' => 'LunaUser',
                'is_id' => false
            ),
            'address' => array(
                'name' => dgettext('luna', 'Anschrift'),
                'table' => 'luna_users',
                'ids' => 'address',
                'dbvalues' => 'address',
                'class' => 'LunaUser',
                'is_id' => false
            )
        );
        if ($all) {
            $fields = $fields + array(
                    'zip' => array(
                        'name' => dgettext('luna', 'Postleitzahl'),
                        'table' => 'luna_users',
                        'ids' => 'zip',
                        'dbvalues' => 'zip',
                        'class' => 'LunaUser',
                        'is_id' => false
                    )
                );
        }
        $fields = $fields + array(
            'city' => array(
                'name' => dgettext('luna', 'Stadt'),
                'table' => 'luna_users',
                'ids' => 'city',
                'dbvalues' => 'city',
                'class' => 'LunaUser',
                'is_id' => false
            ),
            'country' => array(
                'name' => dgettext('luna', 'Land'),
                'table' => 'luna_users',
                'ids' => 'country',
                'dbvalues' => 'country',
                'class' => 'LunaUser',
                'is_id' => false
            )
        );
        if ($all) {
            $fields = $fields + array(
                'emails' => array(
                    'name' => dgettext('luna', 'E-Mailadresse'),
                    'table' => 'luna_email',
                    'ids' => 'user_id',
                    'dbvalues' => 'email',
                    'class' => 'LunaEMail',
                    'is_id' => true
                ),
                'phonenumbers' => array(
                    'name' => dgettext('luna', 'Telefonnummer'),
                    'table' => 'luna_phone',
                    'ids' => 'user_id',
                    'dbvalues' => 'number',
                    'class' => 'LunaPhone',
                    'is_id' => true
                ),
                'fax' => array(
                    'name' => dgettext('luna', 'Fax'),
                    'table' => 'luna_users',
                    'ids' => 'fax',
                    'dbvalues' => 'fax',
                    'class' => 'LunaUser',
                    'is_id' => false
                ),
                'homepage' => array(
                    'name' => dgettext('luna', 'Homepage'),
                    'table' => 'luna_users',
                    'ids' => 'homepage',
                    'dbvalues' => 'homepage',
                    'class' => 'LunaUser',
                    'is_id' => false
                )
            );
        }
        $fields = $fields + array(
            'companies' => array(
                'name' => dgettext('luna', 'Unternehmen'),
                'table' => 'luna_user_company',
                'ids' => 'company_id',
                'dbvalues' => 'name',
                'class' => 'LunaCompany',
                'is_id' => true,
                'linked' => 'luna_companies'
            ),
            'skills' => array(
                'name' => dgettext('luna', 'Kompetenz'),
                'table' => 'luna_user_skills',
                'ids' => 'skill_id',
                'dbvalues' => 'name',
                'class' => 'LunaSkill',
                'is_id' => true,
                'linked' => 'luna_skills'
            ),
            'tags' => array(
                'name' => dgettext('luna', 'Schlagwort'),
                'table' => 'luna_user_tag',
                'ids' => 'tag_id',
                'dbvalues' => 'name',
                'class' => 'LunaTag',
                'is_id' => true,
                'linked' => 'luna_tags'
            )
        );
        $fields = $fields + array(
                'status' => array(
                    'name' => dgettext('luna', 'Status'),
                    'table' => 'luna_users',
                    'ids' => 'status',
                    'dbvalues' => 'status',
                    'class' => 'LunaUser',
                    'is_id' => false
                )
            );
        if ($all) {
            $fields = $fields + array(
                'graduation' => array(
                    'name' => dgettext('luna', 'Hochschulabschluss'),
                    'table' => 'luna_users',
                    'ids' => 'graduation',
                    'dbvalues' => 'graduation',
                    'class' => 'LunaUser',
                    'is_id' => false
                ),
                'notes' => array(
                    'name' => dgettext('luna', 'Notizen'),
                    'table' => 'luna_users',
                    'ids' => 'notes',
                    'dbvalues' => 'notes',
                    'class' => 'LunaUser',
                    'is_id' => false
                )
            );
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
        $result = array(
            'compare' => array(
                '=' => dgettext('luna', 'ist'),
                '!=' => dgettext('luna', 'ist nicht'),
                'LIKE' => dgettext('luna', 'enthält'),
                'NOT LIKE' => dgettext('luna', 'enthält nicht'),
            ),
            'values' => []
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
        return $GLOBALS['user']->cfg->store('LUNA_USER_FILTER', studip_json_encode($data));
    }

    public static function addFilter($client, $column, $compare, $value)
    {
        $filters = self::getFilters($GLOBALS['user']->id);
        $filters[$client][] = array(
            'column' => $column,
            'compare' => $compare,
            'value' => $value
        );
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
