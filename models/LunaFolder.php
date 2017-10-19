<?php
/**
 * LunaFolder.class.php
 *
 * This folder type aggregates the files that belong to a LunaUser.
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
class LunaFolder implements FolderType
{
    protected $folder;

    /**
     * @param Folder|null folder The folder object for this FolderType
     */
    public function __construct($folder = null)
    {
        if ($folder instanceof LunaFolder) {
            $this->folder = $folder->folder;
        } elseif ($folder instanceof Folder) {
            $this->folder = $folder;
        } else {
            $this->folder = Folder::build($folder);
        }
        $this->folder['folder_type'] = get_class($this);
    }

    /**
     * Retrieves or creates the top folder for a LunaUser.
     *
     * @param string $client_id The client-ID of the LunaClient whose top folder
     *     shall be returned
     *
     * @return LunaFolder|null The top folder of the user identified by
     *     $user_id. If the folder can't be retrieved, null is returned.
     */
    public static function findTopFolder($user_id)
    {
        //try to find the top folder:
        $folder = Folder::findOneByrange_id($user_id);

        //check if that was successful:
        if ($folder) {
            return new LunaFolder($folder);
        }
    }

    /**
     * Creates a root folder (top folder) for a user referenced by its ID.
     *
     * @param string $client_id The ID of a user for which a root folder
     *     shall be generated.
     *
     * @return LunaFolder A new LunaFolder as root folder for a user.
     */
    public static function createTopFolder($user_id)
    {
        return new LunaFolder(
            Folder::createTopFolder(
                $user_id,
                'luna',
                'LunaFolder'
            )
        );
    }

    /**
     * This method returns always false since LunaFolder types are not
     * creatable in standard folders. They are a standalone folder type.
     */
    public static function availableInRange($range_id_or_object, $user_id)
    {
        return false;
    }

    /**
     * Returns a localised name of the LunaFolder type.
     */
    public static function getTypeName()
    {
        return dgettext('luna', 'Ordner für Luna-Dateien');
    }

    /**
     * Returns the Icon object for the LunaFolder type.
     */
    public function getIcon($role)
    {
        $shape = count($this->getSubfolders()) + count($this->getFiles()) === 0
            ? 'folder-empty'
            : 'folder-full';
        return Icon::create($shape, $role);
    }

    /**
     * Returns the ID of the folder object of this LunaFolder.
     */
    public function getId()
    {
        return $this->folder->id;
    }

    /**
     * See method LunaFolder::isReadable.
     */
    public function isVisible($user_id)
    {
        return $this->isReadable($user_id);
    }

    /**
     * This method checks if a specified user can read the LunaFolder object.
     *
     * @param string $user_id The ID of the user whose read permission
     *     shall be checked.
     *
     * @return True, if the user, specified by $user_id, can read the folder,
     *     false otherwise.
     */
    public function isReadable($user_id)
    {
        return LunaClient::findCurrent()->hasReadAccess($user_id);
    }

    /**
     * LunaFolders are never writable.
     */
    public function isWritable($user_id)
    {
        return LunaClient::findCurrent()->hasWriteAccess($user_id);
    }

    /**
     * LunaFolders are never editable.
     */
    public function isEditable($user_id)
    {
        return false;
    }

    /**
     * LunaFolders will never allow subfolders.
     */
    public function isSubfolderAllowed($user_id)
    {
        return false;
    }

    /**
     * LunaFolders don't have a description template.
     */
    public function getDescriptionTemplate()
    {
        return '';
    }

    /**
     * @return FolderType[]
     */
    public function getSubfolders()
    {
        return [];
    }

    /**
     * Returns the files of this LunaFolder (e.g. the files attached to a LunaUser).
     *
     * @return FileRef[] An array of FileRef objects containing all files
     *     that are placed inside this folder.
     */
    public function getFiles()
    {
        if ($this->folder) {
            return $this->folder->file_refs->getArrayCopy();
        }
        return [];
    }

    /**
     * Returns the parent-folder as a StandardFolder
     * @return FolderType
     */
    public function getParent()
    {
        return $this->folderdata->parentfolder
            ? $this->folderdata->parentfolder->getTypedFolder()
            : null;
    }

    /**
     * MessageFolders don't have an edit template.
     */
    public function getEditTemplate()
    {
        return '';
    }

    /**
     * MessageFolders don't have an edit template and therefore cannot
     * handle requests from such templates.
     */
    public function setDataFromEditTemplate($request)
    {
    }

    /**
     * This method handles file upload validation.
     *
     * @param array $uploaded_file The uploaded file that shall be validated.
     * @param string $user_id The user who wishes to upload a file
     *     in this MessageFolder.
     *
     * @return string|null An error message on failure, null on success.
     */
    public function validateUpload($uploaded_file, $user_id)
    {
        $status      = $GLOBALS['perm']->get_perm($user_id);
        $upload_type = $GLOBALS['UPLOAD_TYPES']['default'];

        if ($upload_type['file_sizes'][$status] < $uploaded_file['size']) {
            return sprintf(
                _('Die maximale Größe für einen Upload (%s) wurde überschritten.'),
                relsize($upload_type['file_sizes'][$status])
            );
        }

        $extension = strtolower(
            pathinfo(
                $uploaded_file['name'],
                PATHINFO_EXTENSION
            )
        );
        $types = array_map('strtolower', $upload_type['file_types']);

        if (!in_array($extension, $types) && $upload_type['type'] === 'deny') {
            return sprintf(
                _('Sie dürfen nur die Dateitypen %s hochladen!'),
                join(',', $upload_type['file_types'])
            );
        }
        if (in_array($extension, $types) && $upload_type['type'] === 'allow') {
            return sprintf(
                _('Sie dürfen den Dateityp %s nicht hochladen!'),
                $extension
            );
        }
    }

    /**
     * This method handles creating a file inside the MessageFolder.
     *
     * @param File|array $file The file that shall be created inside
     *     the MessageFolder.
     *
     * @return FileRef|null On success, a FileRef for the given file
     *     is returned. Null otherwise.
     */
    public function createFile($file)
    {
        if (!$this->folder) {
            return MessageBox::error(
                _('Datei kann nicht erstellt werden, da kein Ordner angegeben wurde, in dem diese erstellt werden kann!')
            );
        }

        $new_file = $file;
        $file_ref_data = [];

        if (!is_a($new_file, 'File')) {
            $new_file = new File();
            $new_file->name      = $file['name'];
            $new_file->mime_type = $file['type'];
            $new_file->size      = $file['size'];
            $new_file->storage   = 'disk';
            $new_file->id        = $new_file->getNewId();
            $new_file->connectWithDataFile($file['tmp_name']);
        }

        if ($new_file->isNew()) {
            $new_file->store();
        }

        $file_ref_data['name'] = $file['name'];
        $file_ref_data['description'] = '';

        $default_license = ContentTermsOfUse::find(
            'UNDEF_LICENSE'
        );
        $file_ref_data['content_terms_of_use_id'] = $default_license->id;

        return $this->folder->linkFile(
            $new_file,
            array_filter($file_ref_data)
        );
    }

    /**
     * Handles the deletion of a file inside this folder.
     *
     * @param string $file_ref_id The ID of the FileRef whose file
     *     shall be deleted.
     *
     * @return True, if the file has been deleted successfully, false otherwise.
     */
    public function deleteFile($file_ref_id)
    {
        $file_ref = $this->folderdata->file_refs->find($file_ref_id);

        if ($file_ref) {
            return $file_ref->delete();
        }
    }

    /**
     * Stores the LunaFolder object.
     *
     * @return True, if the LunaFolder has been stored successfully,
     *     false otherwise.
     */
    public function store()
    {
        return $this->folder->store();
    }

    /**
     * @param FolderType $foldertype
     * @return bool
     */
    public function createSubfolder(FolderType $folderdata)
    {
    }

    /**
     * @param string $subfolder_id
     * @return bool
     */
    public function deleteSubfolder($subfolder_id)
    {
    }

    /**
     * Deletes theLunaFolder object.
     *
     * @return True, if the LunaFolder has been deleted successfully,
     *     false otheriwse.
     */
    public function delete()
    {
        return $this->folder->delete();
    }

    /**
     * See method LunaFolder::isReadable
     */
    public function isFileDownloadable($file_ref_id, $user_id)
    {
        return $this->isReadable($user_id);
    }

    /**
     * Files inside LunaFolders are not editable.
     */
    public function isFileEditable($file_ref_id, $user_id)
    {
        return false;
    }

    /**
     * Files inside LunaFolders are not writable.
     */
    public function isFileWritable($file_ref_id, $user_id)
    {
        return false;
    }
}
