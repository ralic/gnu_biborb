<?php
/**
 * This file is part of BibORB
 *
 * Copyright (C) 2003-2005  Guillaume Gardey (ggardey@club-internet.fr)
 * 
 * BibORB is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * BibORB is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 */

/** 
    File: error.php
    Author: Guillaume Gardey (ggardey@club-internet.fr)
    Licence: GPL

    Error Handlers

*/

define("FATAL", E_USER_ERROR);
define("ERROR", E_USER_WARNING);
define("WARNING", E_USER_NOTICE);

error_reporting(FATAL | ERROR | WARNING);

/**
    Handler for biborb errors.
    Generate a verbose output.
*/
function biborb_error_handler($errno, $errstr, $errfile, $errline){
    switch ($errno) {
        case FATAL:
        case ERROR:
            $html = html_header("BibORB - Error",CSS_FILE,null,null);
            $html .= "<div class='error_report'>";
            $html .= "<b>An error occurred</b><br />";
            $html .= "Aborting...<br />";
            $html .= "<div class='error_content'>";
            $html .= "<b>Error: </b>";
            $html .= "$errstr<br /><br />";
            $html .= "PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />";
            $html .= "BibORB ".BIBORB_VERSION." (".BIBORB_RELEASE_DATE.")<br/>";
            $html .= "Error at line $errline of file ".basename($errfile)."<br/>";
            $html .= "</div>";
            $html .= "Consider reporting this error at <a href='http://savannah.nongnu.org/projects/biborb'>http://savannah.nongnu.org/projects/biborb</a> if it is reproductible.<br/><br/>";
            $html .= "Go Back to <a href='index.php'>BibORB</a>";
            $html .= "</div>";
            $html .= html_close();
            echo $html;
            exit(1);
            break;
        case WARNING:
            echo "<b>WARNING</b> [$errno] $errstr<br />\n";
            break;
        default:
            echo "Unkown error type: [$errno] $errstr<br />\n";
            break;
    }
}
?>