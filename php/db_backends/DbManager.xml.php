<?php
/**
 * This file is part of BibORB
 * 
 * Copyright (C) 2003-2008 Guillaume Gardey <glinmac+biborb@gmail.com>
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

/**
 * File: DbManager.xml.php
 *
 * Description:
 *
 */

class_exists('XSLT_Processor') || include('./php/xslt_processor.php');
class_exists('FileToolKit')    || include('./php/FileToolKit.php');

class DbManager
{
    var $_databases;
    var $_entryTypes;

    /**
     * Constructor
     */   
    function DbManager()
    {
        $this->loadDbNames();

        $aXsltp = new XSLT_Processor('file://'.BIBORB_PATH,'UTF-8');
        $aXml = FileToolKit::getContent('./xsl/model.xml');
        $aXsl = FileToolKit::getContent('./xsl/get_all_bibtex_types.xsl');
        $aResult = $aXsltp->transform($aXml, $aXsl);
        $aXsltp->free();
        $this->_entryTypes = explode(" ",trim($aResult));
    }

    function getDbNames()
    {
        return $this->_databases;
    }
    
    /**
     * Get the name of recorded bibliographies.
     * It returns an array which keys are the bibliography's file system name
     * and values are the bibliography's real name.
     * @return An array
     */
    function loadDbNames()
    {
        myUnset($this->_databases);
        $this->_databases = array();        
        $aDbDir = dir("bibs");
        while ( ($aFile = $aDbDir->read()) !== false)
        {   
            if ( is_dir('bibs/'.$aFile) &&
                 $aFile[0] != '.')
            {
                $this->_databases[$aFile] = FileToolKit::getContent('bibs/'.$aFile.'/fullname.txt');
            }
        }
    }

    /**
     * Create a new bibliography.
     *
     * @param $iName The name of the bibliography.
     * @param $iDescription A short description of the bibliography.
     */
    function createDb($iName, $iDescription)
    {
        // break if $iName is null
        if ($iName == null)
        {
            $_SESSION['errorManager']->triggerError(msg("BIB_EMPTY_NAME"),"bibname => $iName","mode=select");
            return array(0, $aResArray);
        }

        // Create the new bibliography if it doesn't exist.
        if (in_array($iName, array_values($this->_databases)))
        {
            $aResArray['error'] = msg("BIB_EXISTS");
            $_SESSION['errorManager']->triggerError(msg("BIB_EXISTS"),"bibname => $iName","mode=select");
            return array(0, $aResArray);
        }
        
        $aFullname = $iName;
        
        // check it is not a pathname
        if (!ereg('^[^./][^/]*$', $iName))
        {
            $aResArray['error'] = msg("Invalid name for bibliography!");
            return array(0, $aResArray);
        }
        
        $aName = remove_accents($iName);
        $aName = str_replace(array(' ','\'','"','\\'),'_',$aName);
        umask(DMASK);
        // create directory
        if (mkdir("./bibs/$aName"))
        {
            $aResArray['message'] = msg("BIB_CREATION_SUCCESS");
        }
        else
        {
            $aResArray['message'] = msg("BIB_CREATION_ERROR");
        }
        // papers directory
        mkdir("./bibs/$aName/papers");
        
        // xml and bibtex files
        umask(UMASK);
        copy("./data/template/template.bib","./bibs/$aName/$aName.bib");
        copy("./data/template/template.xml","./bibs/$aName/$aName.xml");
        
        // description
        $aFp = fopen("./bibs/$aName/description.txt","w");
        fwrite($aFp, $iDescription);
        fclose($aFp);
        
        // full-name
        $aFp = fopen("./bibs/$aName/fullname.txt","a+");
        fwrite($aFp,$aFullname);
        fclose($aFp);
        
        // init xml file
        $aXml = FileToolKit::getContent("./bibs/$aName/$aName.xml");
        $aXml = str_replace("template",$aName,$aXml);
        $aFp = fopen("./bibs/$aName/$aName.xml","w");
        fwrite($aFp,$aXml);
        fclose($aFp);
        
        $this->_databases[$aName] = $aFullname;
        
        return array(1,$aResArray);
    }

    /**
     * Delete a bibliography
     * @param $name The name of a bibliography.
     */
    function deleteDb($name)
    {
        //check if $name is a valid bibliography name
        if(!in_array($name,array_keys($this->_databases))){
            trigger_error("Wrong database name: $name",ERROR);
        }
        // create .trash folder if it does not exit
        if(!file_exists("./bibs/.trash")){
            $oldmask = umask();
            umask(DMASK);
            mkdir("./bibs/.trash");
            umask($oldmask);
        }
        $fullname = $this->_databases[$name];
        // save the bibto .trash folder
        rename("bibs/$name","bibs/.trash/$name-".date("Ymd")) or trigger_error("Error while moving $fullname to .trash folder",ERROR);
        // result message
        $res = sprintf(msg("Database %s moved to trash."),$fullname)."<br/>";
        $res .= sprintf(msg("Remove %s to definitively delete it."),"<code>./bibs/.trash/$name-".date("Ymd")."</code>").'<br/>';
        unset($this->_databases[$name]);
        return $res;
    }

    /**
     * Return a list of available types of references
     */
    function getReferenceTypes()
    {
        return $this->_entryTypes;
    }

    /**
     * Does a given bibliography exists?
     */
    function exists($iDbName)
    {   
        return in_array($iDbName,array_keys($this->_databases));
    }
    
}
?>