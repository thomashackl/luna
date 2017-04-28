<?php
/**
 * LunaSearch.php - QuickSearch for Luna
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

class LunaSearch extends StandardSearch
{

    /**
     *
     * @param string $search
     *
     * @return void
     */
    public function __construct($search)
    {
        $this->avatarLike = $this->search = $search;
        $this->sql = $this->getSQL();
    }

    /**
     * returns an object of type SQLSearch with parameters to constructor
     *
     * @param string $search
     *
     * @return SQLSearch
     */
    static public function get($search)
    {
        return new LunaSearch($search);
    }

    /**
     * returns the title/description of the searchfield
     *
     * @return string title/description
     */
    public function getTitle()
    {
        switch ($this->search) {
            case 'user_id':
                return dgettext('luna', 'Person suchen');
            case 'company_id':
                return dgettext('luna', 'Unternehmen suchen');
        }
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
        $client = LunaClient::getCurrentClient();
        switch ($this->search) {
            case 'user_id':
                return "SELECT DISTINCT `user_id`,
                    CONCAT(CONCAT_WS(' ', `title_front`, `firstname`), ' ',
                        CONCAT_WS(', ', `lastname`, IF(`title_rear` != '', `title_rear`, NULL)))
                    FROM `luna_users`
                    WHERE `client_id` = '".$client->id."'
                        AND (`firstname` LIKE :input
                            OR `lastname` LIKE :input
                            OR CONCAT_WS(' ', `firstname`, `lastname`) LIKE :input
                            OR CONCAT_WS(' ', `lastname`, `firstname`) LIKE :input)
                    ORDER BY `lastname`, `firstname`";
            case 'company_id':
                return "SELECT DISTINCT `company_id`, `name`
                    FROM `luna_companies`
                    WHERE `client_id` = '".$client->id."'
                        AND `name` LIKE :input
                    ORDER BY `name`";
        }
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
