<?php
/**
 * LunaMarker.php
 * model class for Luna text markers that can be replaced.
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
 * @property string marker_id database column
 * @property string id alias column for marker_id
 * @property string marker database column
 * @property string name database column
 * @property string type database column
 * @property string description database column
 * @property string replacement database column
 * @property string replacement_male database column
 * @property string replacement_female database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class LunaMarker extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'luna_markers';

        parent::configure($config);
    }

    public static function hasMarkers($message)
    {
        $allmarkers = LunaMarker::findBySQL("1");
        $markers = array_map(function ($m) { return '{' . $m->marker . '}'; }, $allmarkers);
        foreach ($markers as $marker) {
            if (strpos($message, $marker) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function getMarkers($message)
    {
        $found = [];
        $markers = LunaMarker::findBySQL("1");
        foreach ($markers as $marker) {
            if (strpos($message, $marker->marker) !== false) {
                $found[] = $marker;
            }
        }
        return $found;
    }

    public static function replaceMarkers($message, $user, $useInformal = false)
    {
        $find = [];
        $replace = [];
        foreach (LunaMarker::getMarkers($message) as $marker) {
            if (strpos($message, '{' . $marker->marker . '}') !== false) {
                $find[] = '{' . $marker->marker . '}';
                $replace[] = $marker->getMarkerReplacement($user, $useInformal, 0);
            }
        }
        return str_replace($find, $replace, $message);
    }

    public function getMarkerReplacement($user, $useInformal = false, $level = 0)
    {
        if ($user->informal && $this->replacement_informal !== '' && $useInformal) {
            return LunaMarker::findOneByMarker($this->replacement_informal)
                ->getMarkerReplacement($user, $useInformal);
        } else {
            $replacement = $this->replacement;

            switch ($user->gender) {
                case 1:
                    if ($this->replacement_male) {
                        $replacement = $this->replacement_male;
                    }
                    break;
                case 2:
                    if ($this->replacement_female) {
                        $replacement = $this->replacement_female;
                    }
                    break;
            }

            switch ($this->type) {
                // Just plain text replacing the marker.
                case 'text':
                    if (strpos($replacement, '{') !== false) {
                        $matches = [];
                        preg_match_all('/{([a-zA-Z0-9\-_]+)}/m', $replacement, $matches);
                        foreach ($matches[1] as $match) {
                            $replacement = str_replace('{' . $match . '}',
                                LunaMarker::findOneByMarker(trim($match))->getMarkerReplacement($user, $useInformal, $level + 1),
                                $replacement);
                        }
                    }
                    return $replacement;

                // Content from one or more database columns replaces the marker.
                case 'database':
                    $data = words($replacement);
                    $find = [];
                    $replace = [];
                    foreach ($data as $entry) {
                        if (strpos($entry, '{') !== false) {
                            $matches = [];
                            preg_match_all('/{([a-zA-Z0-9\-_]+)}/m', $entry, $matches);
                            foreach ($matches[1] as $match) {
                                $replacement = str_replace($entry,
                                    LunaMarker::findOneByMarker(trim($match))->getMarkerReplacement($user, $useInformal, $level + 1),
                                    $replacement);
                            }
                        } else {
                            // Extract the database fields...
                            list($table, $column) = explode('.', $entry);
                            // ... and query database for values to insert.
                            $stmt = DBManager::get()->prepare("SELECT `:column`
                                FROM `:table` WHERE `user_id` = :userid LIMIT 1");
                            $stmt->bindParam('column', $column, StudipPDO::PARAM_COLUMN);
                            $stmt->bindParam('table', $table, StudipPDO::PARAM_COLUMN);
                            $stmt->bindParam('userid', $user->id);
                            $stmt->execute();
                            $dbdata = $stmt->fetch(PDO::FETCH_ASSOC);
                            $replacement = str_replace($entry, $dbdata[$column], $replacement);
                        }
                    }
                    // If have empty values from database, there could be excess whitespace -> remove.
                    return trim(preg_replace('/(\s)+/', ' ', $replacement));

                // Content from one or more database columns replaces the marker.
                case 'database-relation':
                    $data = words($replacement);
                    $find = [];
                    $replace = [];
                    foreach ($data as $entry) {
                        if (strpos($entry, '{') !== false) {
                            $matches = [];
                            preg_match_all('/{([a-zA-Z0-9\-_]+)}/m', $entry, $matches);
                            foreach ($matches[1] as $match) {
                                $replacement = str_replace($entry,
                                    LunaMarker::findOneByMarker(trim($match))->getMarkerReplacement($user, $useInformal, $level + 1),
                                    $replacement);
                            }
                        } else {
                            // Extract the database fields...
                            list($table1, $join_on, $table2, $column, $where) = explode('->', $entry);
                            // ... and query database for values to insert.
                            $query = "SELECT `:column`
                                FROM `:table1` JOIN `:table2` USING(`:join`)
                                WHERE `user_id` = :userid";
                            if ($where) {
                                $query .= " AND " . $where;
                            }
                            $query .= " LIMIT 1";
                            $stmt = DBManager::get()->prepare($query);
                            $stmt->bindParam('column', $column, StudipPDO::PARAM_COLUMN);
                            $stmt->bindParam('table1', $table1, StudipPDO::PARAM_COLUMN);
                            $stmt->bindParam('table2', $table2, StudipPDO::PARAM_COLUMN);
                            $stmt->bindParam('join', $join_on, StudipPDO::PARAM_COLUMN);
                            $stmt->bindParam('userid', $user->id);
                            $stmt->execute();
                            $dbdata = $stmt->fetch(PDO::FETCH_ASSOC);
                            $replacement = str_replace($entry, $dbdata[$column], $replacement);
                        }
                    }
                    // If have empty values from database, there could be excess whitespace -> remove.
                    return trim(preg_replace('/(\s)+/', ' ', $replacement));

                // The marker is replaced by the result of a function call.
                case 'function':
                    $data = words($replacement);
                    $function = array_shift($data);
                    return call_user_func_array($function, $data);
            }
        }
    }

}
