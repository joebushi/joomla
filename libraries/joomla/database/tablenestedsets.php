<?php
/**
 * @version     $Id: nestedsets.php 2009-05-15 10:43:09Z bembelimen $
 * @package     Joomla!.Framework
 * @subpackage  Database.Table
 * @license     GNU/GPL, see http://www.gnu.org/copyleft/gpl.html and LICENSE.php
 * 
 * Abstract class for handling nested sets
 * 
 * Joomla! is free software. you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * Joomla! is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Joomla!; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


// Ensure, that the file was included by Joomla!
defined('_JEXEC') or jexit();

// jimport('joomla.database.table');

/**
 * JTable Nested Sets class
 *
 * @abstract
 * @category    Database
 * @package     Joomla!.Framework
 * @subpackage  Database.Table
 * @author      Benjamin Trenkle <bembelimen@web.de>
 * @license     GNU/GPL, see http://www.gnu.org/copyleft/gpl.html and LICENSE.php
 * @since       1.6
 * 
 */
abstract class JTableNestedSets extends JTable {
    
    /**
     * Name of the table in the db schema
     * 
     * @access protected
     * @var string
     */
    protected $_tbl;

    /**
     * Name of the primary key field in the table
     *
     * @access    protected
     * @var       string
     */
    protected $_tbl_key;
    
    /**
     * Database connector {@see JDatabase}
     *
     * @access    protected
     * @var       JDatabase
     */
    protected $_db;
    
    /**
     * Name for the 'lft' field in the database schema
     *
     * @access    protected
     * @var       string
     */
    protected $_lft;
    
    /**
     * Name for the 'rgt' field in the database schema
     *
     * @access    protected
     * @var       string
     */
    protected $_rgt;
    
    /**
     * Name for the 'parent' field in the database schema
     *
     * @access    protected
     * @var       string
     */
    protected $_parent;
    
    /**
     * Name for the 'ordering' field in the database schema
     *
     * @access    protected
     * @var       string
     */
    //protected $_ordering;
    
     /**
     * The NestedSets constructor for initializing the variables
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       string $table The name of the table in the db schema.
     * @param       string $key The name of the primary key field in the table.
     * @param       object $db The JDatabase object {@see JDatabase}
     * @param       string $lft Name for the 'lft' field in the database schema. Default: lft
     * @param       string $rgt Name for the 'rgt' field in the database schema Default: rgt
     * @param       string $rgt Name for the 'parent' field in the database schema Default: parent
     * @param       string $rgt Name for the 'ordering' field in the database schema Default: ordering
     * @return      void
     * @since       1.6
     */
    public function __construct($table, $key, $db, $lft='lft', $rgt='rgt', $parent='parent_id'/*, $ordering='ordering'*/) {
        
        // assign the table name to the class variable $this->_tbl
        $this->_tbl = $table;
        // assign the table key name to the class variable $this->_tbl_key
        $this->_tbl_key = $key;
        // assign the JDatabase object {@see JDatabase} to the class variable $this->_db
        $this->_db = $db;
        // assign the name of the 'lft' field to the class variable $this->_lft
        $this->_lft = $lft;
        // assign the name of the 'rgt' field to the class variable $this->_rgt
        $this->_rgt = $rgt;
        // assign the name of the 'parent' field to the class variable $this->_parent
        $this->_parent = $parent;
        // assign the name of the 'ordering' field to the class variable $this->_ordering
        //$this->_ordering = $ordering;
        
    }
    
    /**
     * Stores a new row if {@see $this->tbl_key} is null/zero or updates an existing row in the database table
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       void
     * @return      bool returns true if successful otherwise returns false and sets an error message {@see JException}
     * @since       1.6
     */
    public function store($updateNulls=false) {
        
        // is the primary key null? then prepare the database table for a new row
        if (!$this->{$this->_tbl_key}) :
        
            // Select the 'rgt' value of the last entry with $this->_parent == $parent
            $query = "
                SELECT
                    ".$this->_db->nameQuote($this->_rgt)." as rgt
                FROM
                    ".$this->_db->nameQuote($this->_tbl)."
                WHERE
                    ".$this->_db->nameQuote($this->_parent)." = ".$this->_db->Quote($this->{$this->_parent})."
                ORDER BY
                    ".$this->_db->nameQuote($this->_rgt)." DESC
            ";
            
            // set the query and set LIMIT to 0,1
            $this->_db->setQuery($query, 0, 1);
            
            /**
             * load the result into $result
             * 
             * @param object $result an JDatabase Object with the following keys
             * - $this->_rgt (default: 'rgt')
             * 
             */
            $result = $this->_db->loadObject();
            
            // the parent ID does not exists
            if (is_null($result) && $this->{$this->_parent} != 0) :
            
                $query = "
                    SELECT
                        ".$this->_db->nameQuote($this->_tbl_key)."
                    FROM
                        ".$this->_db->nameQuote($this->_tbl)."
                    WHERE
                        ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_parent})
                ;
                
                $this->_db->setQuery($query);
                
                $parent = $this->_db->loadResult();
                
                if ($parent != $this->{$this->_parent}) :
            
                    $this->setError(get_class($this).'::store failed - Parent ID does not exists');
                    return false;
                
                endif;
                
                $query = "
                    SELECT
                        ".$this->_db->nameQuote($this->_lft)." as rgt
                    FROM
                        ".$this->_db->nameQuote($this->_tbl)."
                    WHERE
                        ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_parent})
                ;
            
                // set the query and set LIMIT to 0,1
                $this->_db->setQuery($query, 0, 1);
                
                /**
                 * load the result into $result
                 * 
                 * @param object $result an JDatabase Object with the following keys
                 * - $this->_rgt (default: 'rgt')
                 * 
                 */
                $result = $this->_db->loadObject();
            
            // the database is empty and we'll add the first row
            elseif (is_null($result) && $this->{$this->_parent} == 0) :
            
                $query = "
                    SELECT
                        COUNT(*) as count
                    FROM
                        ".$this->_db->nameQuote($this->_tbl)."
                    WHERE
                        ".$this->_db->nameQuote($this->_parent)." = ".$this->_db->Quote(0)
                ;
                
                $this->_db->setQuery($query);
                
                $count = $this->_db->loadResult();
                
                if (!$count) :
                
                    // set the 'lft' value of the new row
                    $this->{$this->_lft} = 1;
                    // calculate the 'rgt' value of the new row
                    $this->{$this->_rgt} = 2;
                    // set the 'ordering' value of the new row
                    //$this->{$this->_ordering} = null;
                    // set $this->_tbl_key to null for new row
                    $this->{$this->_tbl_key} = null;
            
                    // store/update the row
                    if (!parent::store($updateNulls)) :
                    
                        // exit the method with false
                        return false;
                    
                    endif;
                    
                    // true on success
                    return true;
                    
                else :
                
                    $query = "
                        SELECT
                            ".$this->_db->nameQuote($this->_rgt)." as rgt
                        FROM
                            ".$this->_db->nameQuote($this->_tbl)."
                        WHERE
                            ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_parent})
                    ;
                
                    // set the query and set LIMIT to 0,1
                    $this->_db->setQuery($query, 0, 1);
                    
                    /**
                     * load the result into $result
                     * 
                     * @param object $result an JDatabase Object with the following keys
                     * - $this->_rgt (default: 'rgt')
                     * 
                     */
                    $result = $this->_db->loadObject();
                    
                endif;
            
            endif;
            
            // update all rows with $this->_rgt >= $result->rgt for making space for the new row
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->rgt."+2")."
                WHERE
                    ".$this->_db->nameQuote($this->_rgt)." >= ".$this->_db->Quote($result->rgt)
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::store failed - Cannot move '.$this->_rgt.' values');
                // exit the method with false
                return false;
            
            endif;
            
            // update all rows with $this->_lft >= $result->rgt for making space for the new row
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->lft."+2")."
                WHERE
                    ".$this->_db->nameQuote($this->_lft)." >= ".$this->_db->Quote($result->rgt)
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::store failed - Cannot move '.$this->_lft.' values');
                // exit the method with false
                return false;
            
            endif;
            
            // count the existing rows with $this->_parent == $parent
            /*$query = "
                SELECT
                    COUNT(*) as count
                FROM
                    ".$this->_db->nameQuote($this->_tbl)."
                WHERE
                    ".$this->_db->nameQuote($this->_parent)." = ".$this->_db->Quote($this->{$this->_parent})
            ;
            
            $this->_db->setQuery($query);
            
            $ordering = $this->_db->loadObject();*/
            
            // set the 'lft' value of the new row
            $this->{$this->_lft} = $result->rgt+1;
            // calculate the 'rgt' value of the new row
            $this->{$this->_rgt} = $result->rgt+2;
            // set the 'ordering' value of the new row
            //$this->{$this->_ordering} = $ordering->count+1;
            // set $this->_tbl_key to null for new row
            $this->{$this->_tbl_key} = null;
        
        // endif for the new row handling
        endif;
        
        // store/update the row
        if (!parent::store()) :
        
            // exit the method with false
            return false;
        
        endif;
        
        // return on success
        return true;
        
    }
    
    /**
     * Moves a tree
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $parent The id of the parent row (the moved tree will be a child of it). If the tree should be a root element, then set $parent=0
     * @param       bool $first specified if the tree should be the first (true) or the last (false) child of the $parent row
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @return      bool returns true if successful otherwise returns false and sets an error message {@see JException}
     * @since       1.6
     */
    public function move($parent, $first=false, $oid=null) {
        
        // load the current row from the database
        if (!parent::load($oid)) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot load item '.$oid);
            // exit the method with false
            return false;
        
        endif;
        
        // generate the query for loading all $this->_tbl_key from the tree which should be moved
        $query = "
            SELECT
                ".$this->_db->nameQuote($this->_tbl_key)."
            FROM
                ".$this->_db->nameQuote($this->_tbl)."
            WHERE
                (".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                ".$this->_db->Quote($this->{$this->_lft})."
            AND
                ".$this->_db->Quote($this->{$this->_rgt})
        ;
        
        // set the query
        $this->_db->setQuery($query);
        
        // load the result
        $result = $this->_db->loadResultArray();
        
        // don't be child of yourself
        if (in_array($parent, $result)) :

            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot be child of itself');

            // exit the method with false
            return false;
                
        endif;
        
        // create the query for removing the tree which should be moved (save it temporary in the negative area)
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->_lft."*(-1)").",
                ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_rgt."*(-1)")."
            WHERE
                ".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                ".$this->_db->Quote($this->{$this->_lft})."
            AND
                ".$this->_db->Quote($this->{$this->_rgt})
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot negate the values');
            // exit the method with false
            return false;
        
        endif;
        
        // hold the lft
        $lft = $this->{$this->_lft};
        // hold the lft
        $rgt = $this->{$this->_rgt};
        
        // create query for compressing the lft values
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->_lft."-(".$this->{$this->_rgt}."-".$this->{$this->_lft}."+1)")."
            WHERE
               ".$this->_db->nameQuote($this->_lft)." > ".$this->_db->Quote($this->{$this->_rgt})
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot compress '.$this->_lft);
            // exit the method with false
            return false;
        
        endif;
        
        // create query for compressing the rgt values
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_rgt."-(".$this->{$this->_rgt}."-".$this->{$this->_lft}."+1)")."
            WHERE
                ".$this->_db->nameQuote($this->_rgt)." > ".$this->_db->Quote($this->{$this->_rgt})
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot compress '.$this->_rgt);
            // exit the method with false
            return false;
        
        endif;
        
        // load the row with $this->_tbl_key = $parent
        if (!parent::load($parent)) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot load parent information');
            // exit the method with false
            return false;
        
        endif;
        
        // clear $where
        unset($where);
        
        // should the row be saved as first item of $parent?
        if ($first) :
            
            // then update the specific values
            $where = $this->_db->nameQuote($this->_lft)." >  ".$this->{$this->_lft};
        
        // or do we want it as least?
        else :
            
            // then update the specific values
            $where = $this->_db->nameQuote($this->_lft)." >  ".$this->{$this->_rgt};
        
        endif;
        
        // generate query for making space for the new row (move $this->_rgt)
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_rgt."+".$rgt."-".$lft."+1")."
            WHERE
                ".$where
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot move '.$this->_rgt.' value');
            // exit the method with false
            return false;
        
        endif;
        
        // generate query for making space for the new row (move $this->_lft)
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->_rgt."+".$rgt."-".$lft."+1")."
            WHERE
                ".$where
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot move '.$this->_lft.' value');
            // exit the method with false
            return false;
        
        endif;
        
        // bring the tree back to the positive are and move $this->_lft to the correct position
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->{$this->_lft}."+".$this->_lft."*(-1)-".$lft."+1")."
            WHERE
                ".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                ".$this->_db->Quote(-$rgt)."
            AND
                ".$this->_db->Quote(-$lft)
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot readd moved tree');
            // exit the method with false
            return false;
        
        endif;
        
        // bring the tree back to the positiv are and move $this->_rgt to the correct position
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_lft."+".$rgt."-".$lft)."
            WHERE
                ".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                ".$this->_db->Quote(-$rgt)."
            AND
                ".$this->_db->Quote(-$lft)
        ;
            
        // set the query
        $this->_db->setQuery($query);
            
        // execute the query
        if (!$this->_db->query()) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::move failed - Cannot readd moved tree');
            // exit the method with false
            return false;
        
        endif;
        
        // everything works...puh
        return true;
        
    }
    
    /**
     * Deletes a tree
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @param       bool $sub should only the parent row be deleted or the whole branche?
     * @return      bool returns true if successful otherwise returns false and sets an error message {@see JException}
     * @since       1.6
     */
    public function delete($oid, $sub=true) {
        
        // load the current row from the database
        if (!parent::load($oid)) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::delete failed - Cannot load item '.$oid);
            // exit the method with false
            return false;
        
        endif;
        
        // should we delete the whole tree?
        if ($sub) :
        
            // generate deletion query for the whole branche
            $query = "
                DELETE
                FROM
                    ".$this->_db->nameQuote($this->_tbl)."
                WHERE
                    ".$this->_db->nameQuote($this->_lft)."
                BETWEEN
                    ".$this->_db->Quote($this->{$this->_lft})."
                AND
                    ".$this->_db->Quote($this->{$this->_rgt})
            ;
            
            // set the query
            $this->_db->setQuery($query);
                
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::delete failed - Cannot delete the branche');
                // exit the method with false
                return false;
            
            endif;
            
            // generate the update query for filling the $this->_lft space, the deleted rows left
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->_lft."-(".$this->{$this->_rgt}."-".$this->{$this->_lft}."+1)")."
                WHERE
                    ".$this->_db->nameQuote($this->_lft)." > ".$this->_db->Quote($this->{$this->_rgt})
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::delete failed - Cannot move '.$this->_lft.' value');
                // exit the method with false
                return false;
            
            endif;
            
            // generate the update query for filling the $this->_rgt space, the deleted rows left
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_rgt."-(".$this->{$this->_rgt}."-".$this->{$this->_lft}."+1)")."
                WHERE
                    ".$this->_db->nameQuote($this->_rgt)." > ".$this->_db->Quote($this->{$this->_rgt})
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::delete failed - Cannot move '.$this->_rgt.' value');
                // exit the method with false
                return false;
            
            endif;
        
        // or should we only delete the current row?
        else :
        
            // delete the current row
            if(!parent::delete()) :
            
                // exit the method with false
                return false;
            
            endif;
            
            // move all childs to $this->_parent and adjust $this->_lft+$this->_rgt
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->_lft."-1").",
                    ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_rgt."-1").",
                    ".$this->_db->nameQuote($this->_parent)." = ".$this->_db->Quote($this->{$this->_parent})."
                WHERE
                    ".$this->_db->nameQuote($this->_lft)."
                BETWEEN
                    ".$this->_db->Quote($this->{$this->_lft})."
                AND
                    ".$this->_db->Quote($this->{$this->_rgt})
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::delete failed - Cannot move the childs to '.$this->{$this->_parent});
                // exit the method with false
                return false;
            
            endif;
            
            // update all rows to fill the $this->_lft space the deleted row left
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($this->_lft."-2")."
                WHERE
                    ".$this->_db->nameQuote($this->_lft)." > ".$this->_db->Quote($this->{$this->_rgt})
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::delete failed - Cannot compress '.$this->_lft.' value');
                // exit the method with false
                return false;
            
            endif;
            
            // update all rows to fill the $this->_lft space the deleted row left
            $query = "
                UPDATE
                    ".$this->_db->nameQuote($this->_tbl)."
                SET
                    ".$this->_db->nameQuote($this->_rgt)." = ".$this->_db->Quote($this->_rgt."-2")."
                WHERE
                    ".$this->_db->nameQuote($this->_rgt)." > ".$this->_db->Quote($this->{$this->_rgt})
            ;
            
            // set the query
            $this->_db->setQuery($query);
            
            // execute the query
            if (!$this->_db->query()) :
            
                // set Error, if the query fails
                $this->setError(get_class($this).'::delete failed - Cannot move '.$this->_rgt.' value');
                // exit the method with false
                return false;
            
            endif;
        
        endif;
        
        // if all worked then return true
        return true;
        
        
    }
    
    /**
     * moves a row one step up
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @return      bool returns true if successful otherwise returns false
     * @since       1.6
     */
    public function orderUp($oid=null) {
        
        // is $oid null?
        if (is_null($oid)) :
        
            // then set $this->_tbl_key as $oid
            $oid = $this->{$this->_tbl_key};
            
        endif;
        
        // check, if $oid exists and is not 0
        if (!$oid = (int) $oid) :
        
            // otherwise return false
            return false;
            
        endif;
        
        $query = "
            SELECT
                n1.".$this->_db->nameQuote($this->_tbl_key)."
            FROM
                ".$this->_db->nameQuote($this->_tbl)." as n1
            INNER JOIN
                ".$this->_db->nameQuote($this->_tbl)." as n2
            ON
                n1.".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                n2.".$this->_db->nameQuote($this->_lft)."
            AND
                n2.".$this->_db->nameQuote($this->_rgt)."
            WHERE
                n2.".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_tbl_key})."
            ORDER BY
                n1.".$this->_db->nameQuote($this->_lft)
        ;
            
        $this->_db->setQuery( $query );
        $return = $this->_db->loadResultArray();
        
        $count1 = count($return);
          
        if ($count1 < 1) :
           
            return false;
           
        endif;
            
        $query = "
            SELECT
                n1.".$this->_db->nameQuote($this->_tbl_key)."
            FROM
                ".$this->_db->nameQuote($this->_tbl)." as n1
            INNER JOIN
                ".$this->_db->nameQuote($this->_tbl)." as n2
            ON
                n1.".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                n2.".$this->_db->nameQuote($this->_lft)."
            AND
                n2.".$this->_db->nameQuote($this->_rgt)."
            JOIN
                ".$this->_db->nameQuote($this->_tbl)." as n3
            ON
                n3.".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_tbl_key})."
            WHERE
                n2.".$this->_db->nameQuote($this->_parent)." = n3.".$this->_db->nameQuote($this->_parent)."
            AND 
                n2.".$this->_db->nameQuote($this->_rgt)." = n3.".$this->_db->nameQuote($this->_lft)."-1
            ORDER BY
                n1.".$this->_db->nameQuote($this->_lft)
        ;
            
        $this->_db->setQuery( $query );
        $return2 = $this->_db->loadResultArray();
        $count2 = count($return2);
            
        if ($count2 < 1) :
            
            return false;
            
        endif;
            
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = (".$this->_db->nameQuote($this->_lft)."+".($count1*2)."),
                ".$this->_db->nameQuote($this->_rgt)." = (".$this->_db->nameQuote($this->_rgt)."+".($count1*2).")
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." IN (".implode(',',$return2).")
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
            
            return false;
            
        endif;
            
        /*$query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_ordering)." = (".$this->_db->nameQuote($this->_ordering)."+"."1)
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($return2[0])."
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
            
           return false;
            
        endif;*/
            
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = (".$this->_db->nameQuote($this->_lft)."-".($count2*2)."),
                ".$this->_db->nameQuote($this->_rgt)." = (".$this->_db->nameQuote($this->_rgt)."-".($count2*2).")
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." IN (".implode(',',$return).")
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
            
           return false;
            
        endif;
            
        /*$query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_ordering)." = (".$this->_db->nameQuote($this->_ordering)."-1)
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($return[0])."
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
            
            return false;
            
        endif;*/

        return true;
        
    }
    
     /**
     * moves a row one step down
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @return      bool returns true if successful otherwise returns false
     * @since       1.6
     */
    public function orderDown($oid=null) {
        
        $query = "
            SELECT
                n1.".$this->_db->nameQuote($this->_tbl_key)."
            FROM
                ".$this->_db->nameQuote($this->_tbl)." as n1
            INNER JOIN
                ".$this->_db->nameQuote($this->_tbl)." as n2
            ON
                n1.".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                n2.".$this->_db->nameQuote($this->_lft)."
            AND
                n2.".$this->_db->nameQuote($this->_rgt)."
            WHERE
                n2.".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_tbl_key})."
            ORDER BY
                n1.".$this->_db->nameQuote($this->_lft)
        ;
            
        $this->_db->setQuery( $query );
        $return = $this->_db->loadResultArray();
        $count1 = count($return);
            
        if ($count1 < 1) :
            
            return false;
            
        endif;
            
        $query = "
            SELECT
                n1.".$this->_db->nameQuote($this->_tbl_key)."
            FROM
                ".$this->_db->nameQuote($this->_tbl)." as n1
            INNER JOIN
                ".$this->_db->nameQuote($this->_tbl)." as n2
            ON
                n1.".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                n2.".$this->_db->nameQuote($this->_lft)."
            AND
                n2.".$this->_db->nameQuote($this->_rgt)."
            JOIN
                ".$this->_db->nameQuote($this->_tbl)." as n3
            ON
                n3.".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($this->{$this->_tbl_key})."
            WHERE
                n2.".$this->_db->nameQuote($this->_parent)." = n3.".$this->_db->nameQuote($this->_parent)."
            AND 
                n2.".$this->_db->nameQuote($this->_rgt)." = n3.".$this->_db->nameQuote($this->_lft)."+1
            ORDER BY
                n1.".$this->_db->nameQuote($this->_lft)
        ;
            
        $this->_db->setQuery( $query );
        $return2 = $this->_db->loadResultArray();
        $count2 = count($return2);
        
        if ($count2 < 1) :
        
            return false;
        
        endif;
            
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = (".$this->_db->nameQuote($this->_lft)."-".($count1*2)."),
                ".$this->_db->nameQuote($this->_rgt)." = (".$this->_db->nameQuote($this->_rgt)."-".($count1*2).")
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)."
            IN
                (".implode(',',$return2).")
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
           
            return false;
           
        endif;
            
        /*$query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_ordering)." = (".$this->_db->nameQuote($this->_ordering)."-1)
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($return2[0])."
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
            
            return false;
            
        endif;*/
            
        $query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_lft)." = (".$this->_db->nameQuote($this->_lft)."+".($count2*2)."),
                ".$this->_db->nameQuote($this->_rgt)." = (".$this->_db->nameQuote($this->_rgt)."+".($count2*2).")
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." IN (".implode(',',$return).")
        ";

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
            
        return false;
            
        endif;
            
        /*$query = "
            UPDATE
                ".$this->_db->nameQuote($this->_tbl)."
            SET
                ".$this->_db->nameQuote($this->_ordering)." = (".$this->_db->nameQuote($this->_ordering)."+1)
            WHERE
                ".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($return[0])
        ;

        $this->_db->setQuery( $query );
            
        if (!$this->_db->query()):
        
            return false;
            
        endif;*/

        return true;
        
    }
    
    /**
     * get the Path of a row
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @return      object|bool returns the path as object if successful otherwise returns false
     * @since       1.6
     */
    public function getPath ($oid=null) {
        
        // is $oid null?
        if (is_null($oid)) :
        
            // then set $this->_tbl_key as $oid
            $oid = $this->{$this->_tbl_key};
            
        endif;
        
        // check, if $oid exists and is not 0
        if (!$oid = (int) $oid) :
        
            // otherwise return false
            return false;
            
        endif;
        
        // create the query for the path
        $query = "
            SELECT
                p.*
            FROM
                ".$this->_db->nameQuote($this->_tbl)." as c
            JOIN
                ".$this->_db->nameQuote($this->_tbl)." as p
            WHERE
                (c.".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                p.".$this->_db->nameQuote($this->_lft)."
            AND
                p.".$this->_db->nameQuote($this->_rgt).")
            AND
                c.".$this->_db->nameQuote($this->_tbl_key)." = ".$this->_db->Quote($oid)."
            ORDER BY
                p.".$this->_db->nameQuote($this->_tbl_key)
        ;
        
        // set the query
        $this->_db->setQuery($query);
        
        // return the path as object
        return $this->_db->loadObjectList();
        
    }
    
    /**
     * get the tree of a row
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @return      object|bool returns the path as object if successful otherwise returns false
     * @since       1.6
     */
    public function getTree($oid=null) {
        
        // is $oid null?
        if (is_null($oid)) :
        
            // then set $this->_tbl_key as $oid
            $oid = $this->{$this->_tbl_key};
            
        endif;
        
        // check, if $oid exists and is not 0
        if (!$oid = (int) $oid) :
        
            // otherwise return false
            return false;
            
        endif;
        
        // create the query for the tree
        $query = "
            SELECT
                c.*
            FROM
                ".$this->_db->nameQuote($this->_tbl)." as c
            INNER JOIN
                ".$this->_db->nameQuote($this->_tbl)." as p
            ON
                c.".$this->_db->nameQuote($this->_lft)."
            BETWEEN
                p.".$this->_db->nameQuote($this->_lft)."
            AND
                p.".$this->_db->nameQuote($this->_rgt)."
            WHERE
                p.".$this->_db->nameQuote($this->_lft)." = ".$this->_db->Quote($oid)."
            ORDER BY
                c.".$this->_db->nameQuote($this->_lft).", 
                p.".$this->_db->nameQuote($this->_lft)
        ;
        
        // set the query
        $this->_db->setQuery($query);
        
        // return the path as object
        return $this->_db->loadObjectList();
        
    }
    
    /**
     * check, if a row is a leaf
     *
     * @author      Benjamin Trenkle
     * @access      public
     * @param       int $oid the id of the row which should be moved. If not set, the loaded $this->_tbl_key will be used
     * @return      bool returns true if is leaf otherwise returns false
     * @since       1.6
     */
    public function isLeaf($oid=null) {
        
        // load the current row from the database
        if (!parent::load($oid)) :
        
            // set Error, if the query fails
            $this->setError(get_class($this).'::isLeaf failed - Cannot load item '.$oid);
            // exit the method with false
            return false;
        
        endif;
        
        // return true/false
        return ($this->{$this->_rgt}-$this->{$this->_lft} == 1);
        
    }
    
}

?>
