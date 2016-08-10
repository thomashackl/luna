
<?php
/**
 * LunaUser.php
 * model class for users.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Luna
 *
 * @property string  user_id database column
 * @property string  id alias column for user_id
 * @property string  firstname database column
 * @property string  lastname database column
 * @property string  title_front database column
 * @property string  title_rear database column
 * @property int     gender database column
 * @property string  studip_user_id database column
 * @property string  street database column
 * @property string  zip database column
 * @property string  city database column
 * @property string  country database column
 * @property string  email_office database column
 * @property string  email_private database column
 * @property string  phone_office database column
 * @property string  phone_private database column
 * @property string  phone_mobile database column
 * @property string  fax database column
 * @property string  homepage database column
 * @property string  mkdate database column
 * @property string  chdate database column
 * @property LunaUserInfo info has_one LunaUserInfo
 * @property LunaSkill skills has_and_belongs_to_many LunaSkill
 * @property LunaCompany companies has_and_belongs_to_many LunaCompany
 */
class LunaUser extends SimpleORMap
{

    protected static function configure($config = array())
    {
        $config['db_table'] = 'luna_users';
        $config['has_one']['info'] = array(
            'class_name' => 'LunaUserInfo',
            'foreign_key' => 'user_id',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        $config['has_and_belongs_to_many']['skills'] = array(
            'class_name' => 'LunaSkill',
            'thru_table' => 'luna_user_skills',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );
        $config['has_and_belongs_to_many']['companies'] = array(
            'class_name' => 'LunaCompany',
            'thru_table' => 'luna_user_company',
            'on_delete' => 'delete',
            'on_store' => 'store'
        );

        parent::configure($config);
    }

    public function getFullname($format = 'full_rev')
    {
        switch ($format) {
            case 'full':
                $name = $this->firstname . ' ' . $this->lastname;
                if ($this->title_front) {
                    $name = $this->title_front . ' ' . $name;
                }
                if ($this->title_rear) {
                    $name .= ', ' . $this->title_rear;
                }
                break;
            case 'full_rev':
            default:
                $name = $this->lastname . ', ' . $this->firstname;
                if ($this->title_front) {
                    $name .= ', ' . $this->title_front;
                }
                if ($this->title_rear) {
                    $name .= ', ' . $this->title_rear;
                }
        }
        return $name;
    }

}
