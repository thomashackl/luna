<?php
/**
 * LunaContactPersonSearch.php - QuickSearch for Luna contact persons
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

class LunaContactPersonSearch extends StandardSearch
{

    /**
     *
     * @param string $company_id
     *
     * @return void
     */
    public function __construct($company_id)
    {
        $this->avatarLike = $this->search = 'user_id';
        $this->company = $company_id;
        $this->sql = $this->getSQL();
    }

    /**
     * returns the title/description of the searchfield
     *
     * @return string title/description
     */
    public function getTitle()
    {
        return dgettext('luna', 'Kontaktperson suchen');
    }

    /**
     * As Luna has no avatar pictures, nothing will happen here.
     *
     * @param string $id id of the item which can be username, user_id, Seminar_id or Institut_id
     *
     * @return null
     */
    public function getAvatar($id)
    {
        return null;
    }

    /**
     * As Luna has no avatar pictures, nothing will happen here.
     *
     * @param string $id id of the item which can be username, user_id, Seminar_id or Institut_id
     *
     * @return null
     */
    public function getAvatarImageTag($id, $size = Avatar::SMALL, $options = [])
    {
        return null;
    }

    /**
     * returns a sql-string appropriate for the searchtype of the current class
     *
     * @return string
     */
    private function getSQL()
    {
        $client = LunaClient::findCurrent();
        return "SELECT DISTINCT u.`user_id`,
            CONCAT(CONCAT_WS(' ', u.`title_front`, u.`firstname`), ' ',
                CONCAT_WS(', ', u.`lastname`, IF(u.`title_rear` != '', u.`title_rear`, NULL))) AS name
            FROM `luna_users` u
                JOIN `luna_user_company` uc ON (uc.`user_id` = u.`user_id`)
            WHERE u.`client_id` = '" . $client->id . "'
                AND (`firstname` LIKE :input
                    OR `lastname` LIKE :input
                    OR CONCAT_WS(' ', `firstname`, `lastname`) LIKE :input
                    OR CONCAT_WS(' ', `lastname`, `firstname`) LIKE :input)
                AND uc.`company_id` = '" . $this->company . "'
            UNION
            SELECT DISTINCT u.`user_id`,
                CONCAT(CONCAT_WS(' ', u.`title_front`, u.`firstname`), ' ',
                CONCAT_WS(', ', u.`lastname`, IF(u.`title_rear` != '', u.`title_rear`, NULL))) AS name
            FROM `luna_users` u
            WHERE u.`client_id` = '" . $client->id . "'
                AND (`firstname` LIKE :input
                    OR `lastname` LIKE :input
                    OR CONCAT_WS(' ', `firstname`, `lastname`) LIKE :input
                    OR CONCAT_WS(' ', `lastname`, `firstname`) LIKE :input)
                AND NOT EXISTS (
                        SELECT * FROM `luna_user_company`
                        WHERE `user_id` = u.`user_id`
                    )
            ORDER BY name";
    }

    /**
     * A very simple overwrite of the same method from SearchType class.
     * returns the absolute path to this class for autoincluding this class.
     *
     * @return: path to this class
     */
    public function includePath()
    {
        return __file__;
    }
}
