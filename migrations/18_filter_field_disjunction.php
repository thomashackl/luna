<?php

/**
 * Restructures the filters JSON so that disjunction between filter fields can be configured.
 */

class FilterFieldDisjunction extends Migration {

    public function up() {

        /*
         * Migrate user and company filters.
         */
        $data = DBManager::get()->fetchAll(
            "SELECT `userconfig_id`, `user_id`, `field`, `value`
            FROM `user_config`
            WHERE `field` IN (?)",
            [['LUNA_COMPANY_FILTER', 'LUNA_USER_FILTER']]
        );

        // Prepare statement for updating.
        $stmt = DBManager::get()->prepare("UPDATE `user_config`
            SET `value` = :value
            WHERE `userconfig_id`= :id
                AND `user_id` = :user
                AND `field` = :field");

        foreach ($data as $one) {
            $new = [];

            $all = studip_json_decode($one['value'], true);
            // Add column for disjunction.
            foreach ($all as $client => $filters) {
                if (count($filters) > 0) {
                    $new[$client] =  [
                        'disjunction' => 0,
                        'filters' => $filters
                    ];
                }
            }

            if (count($new) > 0) {
                $stmt->execute([
                    'value' => studip_json_encode($new),
                    'id' => $one['userconfig_id'],
                    'user' => $one['user_id'],
                    'field' => $one['field']
                ]);
            }
        }

        /*
         * Migrate filter presets.
         */
        $data = DBManager::get()->fetchAll(
            "SELECT `userconfig_id`, `user_id`, `field`, `value`
            FROM `user_config`
            WHERE `field` IN (?)",
            [['LUNA_COMPANY_FILTER_PRESETS', 'LUNA_USER_FILTER_PRESETS']]
        );

        // Prepare statement for updating.
        $stmt = DBManager::get()->prepare("UPDATE `user_config`
            SET `value` = :value
            WHERE `userconfig_id`= :id
                AND `user_id` = :user
                AND `field` = :field");

        foreach ($data as $one) {
            $new = [];

            $all = studip_json_decode($one['value'], true);
            // Add column for disjunction.
            foreach ($all as $client => $presets) {
                foreach ($presets as $name => $filters) {
                    if (count($filters) > 0) {
                        $new[$client][$name] = [
                            'disjunction' => 0,
                            'filters' => $filters
                        ];
                    }
                }
            }

            if (count($new) > 0) {
                $stmt->execute([
                    'value' => studip_json_encode($new),
                    'id' => $one['userconfig_id'],
                    'user' => $one['user_id'],
                    'field' => $one['field']
                ]);
            }
        }
    }

    public function down() {
        /*
         * Migrate user and company filters.
         */
        $data = DBManager::get()->fetchAll(
            "SELECT `userconfig_id`, `user_id`, `field`, `value`
            FROM `user_config`
            WHERE `field` IN (?)",
            [['LUNA_COMPANY_FILTER', 'LUNA_USER_FILTER']]
        );

        // Prepare statement for updating.
        $stmt = DBManager::get()->prepare("UPDATE `user_config`
            SET `value` = :value
            WHERE `userconfig_id`= :id
                AND `user_id` = :user
                AND `field` = :field");

        foreach ($data as $one) {
            $new = [];

            $all = studip_json_decode($one['value'], true);
            // Remove column for disjunction.
            foreach ($all as $client => $filters) {
                if (count($filters) > 0) {
                    $new[$client] = $filters;
                }
            }

            if (count($new) > 0) {
                $stmt->execute([
                    'value' => studip_json_encode($new),
                    'id' => $one['userconfig_id'],
                    'user' => $one['user_id'],
                    'field' => $one['field']
                ]);
            }
        }

        /*
         * Migrate filter presets.
         */
        $data = DBManager::get()->fetchAll(
            "SELECT `userconfig_id`, `user_id`, `field`, `value`
            FROM `user_config`
            WHERE `field` IN (?)",
            [['LUNA_COMPANY_FILTER_PRESETS', 'LUNA_USER_FILTER_PRESETS']]
        );

        // Prepare statement for updating.
        $stmt = DBManager::get()->prepare("UPDATE `user_config`
            SET `value` = :value
            WHERE `userconfig_id`= :id
                AND `user_id` = :user
                AND `field` = :field");

        foreach ($data as $one) {
            $new = [];

            $all = studip_json_decode($one['value'], true);
            // Add column for disjunction.
            foreach ($all as $client => $presets) {
                foreach ($presets as $name => $filters) {
                    if (count($filters) > 0) {
                        $new[$client][$name] = $filters;
                    }
                }
            }

            if (count($new) > 0) {
                $stmt->execute([
                    'value' => studip_json_encode($new),
                    'id' => $one['userconfig_id'],
                    'user' => $one['user_id'],
                    'field' => $one['field']
                ]);
            }
        }
    }

}
