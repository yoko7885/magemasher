<?php
/************************************************************************
* txtSQL						  ver. 3.0 BETA *
*************************************************************************
* This program is free software; you can redistribute it and/or	        *
* modify it under the terms of the GNU General Public License           *
* as published by the Free Software Foundation; either version 2        *
* of the License, or (at your option) any later version.                *
*                                                                       *
* This program is distributed in the hope that it will be useful,       *
* but WITHOUT ANY WARRANTY; without even the implied warranty of        *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
* GNU General Public License for more details.                          *
*                                                                       *
* You should have received a copy of the GNU General Public License     *
* along with this program; if not, write to the Free Software           *
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307 *
* USA.                                                                  *
*-----------------------------------------------------------------------*
*  NOTE- Tab size in this file: 8 spaces/tab                            *
*-----------------------------------------------------------------------*
*  ©2003 Faraz Ali, ChibiGuy Production [http://txtsql.sourceforge.net] *
*  File: txtSQL.class.php                                               *
************************************************************************/

array_map('defineConstant', array('TXTSQL_CORE_PATH',
				  'TXTSQL_PARSER_PATH'));

require_once(TXTSQL_CORE_PATH   . '/txtSQL.core.php');
require_once(TXTSQL_PARSER_PATH . '/txtSQL.parser.php');

/**
 * Extracts data from a flatfile database via a limited SQL
 *
 * @package txtSQL
 * @author Faraz Ali <FarazAli at Gmail dot com>
 * @version 3.0 BETA
 * @access public
 */
class txtSQL
{
	/**
	 * If set to true, then an alternate file locking method is used
	 * @var bool
	 * @access public
	 * @see useAlternateFlock()
	 */
	var $_ALTERNATEFLOCK = FALSE;

	/**
	 * Contains a cache of any files that have been read to increase execution time
	 * @var array
	 * @access private
	 * @see readFile()
	 */
	var $_CACHE          = array();

	/**
	 * Holds the path of the txtSQL data directory
	 * @var string
	 * @access private
	 */
	var $_LIBPATH        = NULL;

	/**
	 * Holds the md5() hash of the password of the currently logged in user
	 * @var string
	 * @access private
	 * @see _isConnected()
	 * @see disconnect()
	 */
	var $_PASS           = NULL;

	/**
	 * Holds the number of queries sent to txtSQL
	 * @var int
	 * @access private
	 * @see query_count()
	 */
	var $_QUERYCOUNT     = 0;

	/**
	 * Holds the name of the currently selected database
	 * @var string
	 * @access private
	 * @see selectDb()
	 */
	var $_SELECTEDDB     = NULL;

	/**
	 * If set to true, prints all errors and warnings
	 * @var bool
	 * @access public
	 * @see strict()
	 */
	var $_STRICT	     = TRUE;

	/**
	 * Holds the name of the currently logged in user
	 * @var string
	 * @access private
	 * @see _isConnected()
	 */
	var $_USER	     = NULL;

	/**
	 * The constructor of the txtSQL class
	 * @param string $path The path to which the databases are located
	 * @return void
	 * @access public
	 */
	function txtSQL ( $path = './data' )
	{
		$this->_LIBPATH = $path;

		return TRUE;
	}

	/**
	 * Connects a user to the txtSQL service
	 * @param string $user The username of the user
	 * @param string $pass The corressponding password of the user
	 * @return void
	 * @access public
	 */
	function connect ( $user, $pass )
	{
		/* Check to see if our data exists */
		if ( !is_dir($this->_LIBPATH) )
		{
			$this->_error( E_USER_ERROR, 'Invalid data directory specified' );
		}

		/* Instantiate parser and core class */
		$this->_query = new txtSQLCore( $this->_LIBPATH );

		/* Read in the user/pass information */
		if ( ( $DATA = $this->_readFile( "$this->_LIBPATH/txtsql/txtsql.MYI" ) ) === FALSE )
		{
			return $this->_error( E_USER_WARNING, 'Database file is corrupted!' );
		}

		$this->_data = $DATA;

		/* Check to see if the username exists, and for a matching password */
		if ( !isset($DATA[ strtolower($user) ]) || ( $DATA[ strtolower($user) ] != md5($pass) ) )
		{
			return $this->_error( E_USER_NOTICE, 'Access denied for user \'' . $user . '\' (using password: ' . ( !empty($pass) ? 'yes' : 'no' ) . ')' );
		}

		/* Save the usernames and passwords */
		$this->_USER = $user;
		$this->_PASS = $pass;

		return TRUE;
	}

	/**
	 * Disconnects a user from the txtSQL Service
	 * @return void
	 * @access public
	 */
	function disconnect ()
	{
		/* Check to see that we are already connected */
		if( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'Can only disconnect when connected!');
		}

		/* Unset user, pass variables; Then remove the core and parser objects */
		unset( $this->_USER, $this->_PASS, $this->_query );

		return TRUE;
	}

	/**
	 * Selects rows of information from a selected database and a table
	 * that fits the given 'where' clause
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table', 'select', 'where', 'limit'
	 *			 and 'orderby'
	 * @return mixed $results An array that txtSQL returns that matches the given criteria
	 * @access public
	 */
	function select ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->select( $arguments );
	}

	/**
	 * Inserts a new row into a table with the given information
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table', 'values'
	 * @return int $inserted The number of rows inserted into the table
	 * @access public
	 */
	function insert ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->insert( $arguments );
	}

	/**
	 * Updates a row that matches a 'where' clause, with new information
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table', 'where', 'limit',
	 *			 and 'values'
	 * @return int $inserted The number of rows updated
	 * @access public
	 */
	function update ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->update( $arguments );
	}

	/**
	 * Deletes a row from a table that matches a 'where' clause
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table', 'where', 'limit'
	 * @return int $inserted The number of rows deleted
	 * @access public
	 */
	function delete ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->delete( $arguments );
	}

	/**
	 * Returns a list containing the current valid txtSQL databases
	 * @return mixed $databases A list containing the databases
	 * @access public
	 */
	function showdbs ()
	{
		$this->_validate( array() );
		$this->_QUERYCOUNT++;

		return $this->_query->showdatabases();
	}

	/**
	 * Creates a new database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db'
	 * @return void
	 * @access public
	 */
	function createdb ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->createdatabase( $arguments );
	}

	/**
	 * Drops a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db'
	 * @return void
	 * @access public
	 */
	function dropdb ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->dropdatabase( $arguments );
	}

	/**
	 * Renames a database
	 * @param mixed $arguments The arguments in form of "[old db name], [new db name]"
	 * @return void
	 * @access public
	 */
	function renamedb ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->renamedatabase( $arguments );
	}

	/**
	 * Returns an array containing a list of tables inside of a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db'
	 * @return mixed $tables   An array with a list of tables
	 * @access public
	 */
	function showtables ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->showtables( $arguments );
	}

	/**
	 * Creates a new table with the given criteria inside a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table', 'columns'
	 * @return int $deleted The number of rows deleted
	 * @access public
	 */
	function createtable ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->createtable( $arguments );
	}

	/**
	 * Drops a table from a database
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table'
	 * @return void
	 * @access public
	 */
	function droptable ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->droptable( $arguments );
	}

	/**
	 * Alters a database by working with its columns
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table', 'action',
	 *			 'name', and 'values'
	 * @return void
	 * @access public
	 */
	function altertable ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->altertable( $arguments );
	}

	/**
	 * Returns a description of a table using an array
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 *			 where $key can be 'db', 'table'
	 * @return int $columns An array with the description of a table
	 * @access public
	 */
	function describe ( $arguments )
	{
		$this->_validate( $arguments );
		$this->_QUERYCOUNT++;

		return $this->_query->describe( $arguments );
	}

	/**
	 * Checks for a connection, and valid arguments
	 * @param string $statement The SQL Query
	 * @return mixed $results The results given by the txtSQL.query object
	 * @access public
	 */
	function query ( $statement )
	{
		/* Check to see user is connected */
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
		}

		/* Do the actual parsing and get the arguments*/
		$parser    = new sqlParser( $statement );
		$arguments = $parser->parse();

		/* Perform the query and return results */
		if ( ( $arguments !== FALSE ) && isset($arguments['action']) )
		{
			switch ( strtolower($arguments['action']) )
			{
				case 'select' :
				{
					return $this->select($arguments);
				}

				case 'insert' :
				{
					return $this->insert($arguments);
				}

				case 'show tables' :
				{
					return $this->showtables($arguments);
				}

				case 'show users' :
				{
					return $this->getUsers($arguments);
				}

				case 'show databases' :
				{
					return $this->showdbs($arguments);
				}

				case 'drop table' :
				{
					return $this->droptable($arguments);
				}

				case 'drop database' :
				{
					return $this->dropdb($arguments);
				}

				case 'describe' :
				{
					return $this->describe($arguments);
				}

				case 'delete' :
				{
					return $this->delete($arguments);
				}

				case 'create database' :
				{
					return $this->createdb($arguments);
				}

				case 'create table' :
				{
					return $this->createtable($arguments);
				}

				case 'update' :
				{
					return $this->update($arguments);
				}

				case 'grant permissions' :
				{
					return eval('return ' . $arguments['php']);
				}

				case 'lock db' :
				{
					return $this->lockDb($arguments['db']);
				}

				case 'unlock db' :
				{
					return $this->unlockDb($arguments['db']);
				}

				case 'is locked' :
				{
					return $this->isLocked($arguments['db']);
				}

				case 'use database' :
				{
					return $this->selectDb($arguments['db']);
				}
			}
		}

		/* Something went wrong */
		return FALSE;
	}

	/**
	 * Evaluates a query with manually inputted arguments.
	 * The $action can be either 'show databases', 'create databases', 'drop database', 'rename database'
	 * 'show tables', 'create table', 'drop table', 'alter table', 'describe', 'select', 'insert', 'delete',
	 * and 'insert'. See the readme for more information.
	 * __THIS FUNCTION IS DEPRECATED__
	 *
	 * @param string $action The command txtSQL is to perform
	 * @param mixed $arguments The arguments in form of "[$key] => $value"
	 * @return mixed $results The results that txtSQL returned
	 * @access public
	 */
	function execute ( $action, $arguments = NULL )
	{
		/* Check to see user is connected */
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
		}

		/* If there is no action */
		if ( empty($action) || !is_string($action) )
		{
			return $this->_error(E_USER_NOTICE, 'You have an error in your txtSQL query');
		}

		/* Arguments have to be inside of an array */
		if ( !empty($arguments) && !is_array($arguments) )
		{
			return $this->_error(E_USER_NOTICE, 'txtSQL Can only accept arguments in an array');
		}

		/* Depending on what type of action it is, then perform right query */
		switch ( strtolower($action) )
		{
			/* ----- Database Related ----- */
			case 'show databases' :
			{
				$results = $this->_query->showdatabases();

				break;
			}

			case 'create database' :
			{
				$results = $this->_query->createdatabase($arguments);

				break;
			}

			case 'drop database' :
			{
				$results = $this->_query->dropdatabase($arguments);

				break;
			}

			case 'rename database' :
			{
				$results = $this->_query->renamedatabase($arguments);

				break;
			}

			/* ----- Table Related ----- */
			case 'show tables' :
			{
				$results = $this->_query->showtables($arguments);

				break;
			}

			case 'create table' :
			{
				$results = $this->_query->createtable($arguments);

				break;
			}

			case 'drop table' :
			{
				$results = $this->_query->droptable($arguments);

				break;
			}

			case 'alter table' :
			{
				$results = $this->_query->altertable($arguments);

				break;
			}

			case 'describe' :
			{
				$results = $this->_query->describe($arguments);

				break;
			}

			/* ----- Main functions ----- */
			case 'select' :
			{
				$results = $this->_query->select($arguments);

				break;
			}

			case 'insert' :
			{
				$results = $this->_query->insert($arguments);

				break;
			}

			case 'update' :
			{
				$results = $this->_query->update($arguments);

				break;
			}

			case 'delete' :
			{
				$results = $this->_query->delete($arguments);

				break;
			}

			default :
			{
				return $this->_error(E_USER_NOTICE, 'Unknown action: ' . $action);
			}
		}

		/* Return whatever results we got back */
		if ( isset($results) )
		{
			if ( $results !== FALSE )
			{
				$this->_QUERYCOUNT++;

				return $results;
			}

			return FALSE;
		}

		return '';
	}

	/**
	 * Turns strict property of txtSQL off/on
	 * @param bool $strict The value of the strict property
	 * @return void
	 * @access public
	 */
	function strict ( $strict = FALSE )
	{
		$this->_STRICT = $strict = ( boolean ) $strict;

		if ( $this->_isConnected() )
		{
			$this->_query->strict($strict);
		}

		return TRUE;
	}

	/**
	 * Whether to use the alternative method for file locking
	 * @param bool $alternate Whether to use alternative method or not
	 * @return bool TRUE
	 * @access public
	 */
	function useAlternateFlock ( $alternate = TRUE )
	{
		$this->_ALTERNATEFLOCK = ( boolean ) $alternate;

		return TRUE;
	}

	/**
	 * Alias of grantPermissions()
	 **/
	function grant_permissions ( $action, $user, $pass = NULL, $pass1 = NULL )
	{
		return $this->grantPermissions($action, $user, @$pass, @$pass1);
	}

	/**
	 * To set username and/or passwords, or create/delete users
	 * @param mixed $arg The arguments, an array which contains the action, the username and such
	 * @return bool
	 * @access public
	 */
	function grantPermissions ( $action, $user, $pass = NULL, $pass1 = NULL )
	{
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'Not connected');
		}

		if ( ( !is_string($action) || !is_string($user)  ) ||
		     ( !empty($pass)       && !is_string($pass)  ) ||
		     ( !empty($pass1)      && !is_string($pass1) ) )
		{
			return $this->_error(E_USER_NOTICE, 'The arguments must be a string');
		}

		if ( empty($user) )
		{
			return $this->_error(E_USER_NOTICE, 'Forgot to input username');
		}

		if ( ( $DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI") ) === FALSE )
		{
			return $this->_error(E_USER_WARNING, 'Database file is corrupted!');
		}

		switch ( strtolower($action) )
		{
			case 'add':
			{
				if ( isset($DATA[ strtolower($user) ]) )
				{
					return $this->_error(E_USER_NOTICE, 'User already exists');
				}

				$DATA[ strtolower($user) ] = md5($pass);

				break;
			}

			case 'drop':
			{
				switch ( TRUE )
				{
					case ( strtolower($user) == strtolower($this->_USER) ) :
					{
						return $this->_error(E_USER_NOTICE, 'Can\'t drop yourself');
					}

					case ( strtolower($user) == 'root' ) :
					{
						return $this->_error(E_USER_NOTICE, 'Can\'t drop user root');
					}

					case ( !isset($DATA[ strtolower($user) ]) ) :
					{
						return $this->_error(E_USER_NOTICE, 'User doesn\'t exist');
					}

					case ( md5($pass) != $DATA[ strtolower($user) ] ) :
					{
						return $this->_error(E_USER_NOTICE, 'Incorrect password');
					}
				}

				unset($DATA[strtolower($user)]);

				break;
			}

			case 'edit':
			{
				if ( !isset($DATA[ strtolower($user) ]) )
				{
					return $this->_error(E_USER_NOTICE, 'User doesn\'t exist');
				}

				elseif ( md5($pass) != $DATA[ strtolower($user) ] )
				{
					return $this->_error(E_USER_NOTICE, 'Incorrect password');
				}

				$DATA[ strtolower($user) ] = md5($pass1);

				break;
			}

			default:
			{
				return $this->_error(E_USER_NOTICE, 'Invalid action specified');
			}
		}

		/* Save the new information */
		if ( $this->_writeFile ( "$this->_LIBPATH/txtsql/txtsql.MYI", 'w', serialize($DATA) ) === FALSE )
		{
			return FALSE;
		}

		/* Save it in the cache */
		$this->_CACHE["$this->_LIBPATH/txtsql/txtsql.MYI"] = $DATA;

		return TRUE;
	}

	/**
	 * Returns an array filled with a list of current txtSQL users
	 * @return mixed $users
	 * @access public
	 */
	function getUsers ()
	{
		/* Are we connected? */
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'Not connected');
		}

		/* Read in user database */
		if ( ( $DATA = $this->_readFile("$this->_LIBPATH/txtsql/txtsql.MYI") ) === FALSE )
		{
			return $this->_error(E_USER_WARNING, 'Database file is corrupted!');
		}

		return array_keys($DATA);
	}

	/**
	 * Check whether a database is locked or not
	 * @param string $db The database to check
	 * @return bool $locked Whether it is locked or not
	 * @access public
	 */
	function isLocked ( $db )
	{
		if ( !$this->_dbExist($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Database \'' . $db . '\' doesn\'t exist');
		}

		return is_file("$this->_LIBPATH/$db/txtsql.lock") ? TRUE : FALSE;
	}

	/**
	 * To put a file lock on the database
	 * @param string $db The database to have a file lock placed on
	 * @return void
	 * @access public
	 */
	function lockDb ( $db )
	{
		/* Make sure that the user is connected */
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'You must be connected');
		}

		elseif ( !$this->_dbExist($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Database \'' . $db . '\' doesn\'t exist');
		}

		elseif ( $this->isLocked($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Lock for database \'' . $db . '\' already exists');
		}

		if ( $this->_writeFile ( "$this->_LIBPATH/$db/txtsql.lock", 'a' ) === FALSE )
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * To remove a file lock from the database
	 * @param string $db The database to have a file lock removed from
	 * @return void
	 * @access public
	 */
	function unlockDb ( $db )
	{
		/* Make sure that the user is connected */
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'You must be connected');
		}

		elseif ( !$this->_dbExist($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Database \'' . $db . '\' doesn\'t exist');
		}

		elseif ( !$this->isLocked($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Lock for database \'' . $db . '\' doesn\'t exist');
		}


		if ( !@unlink("$this->_LIBPATH/$db/txtsql.lock") )
		{
			$this->_error(E_USER_ERROR, 'Error removing lock for database \'' . $db . '\'');
		}

		return TRUE;
	}

	/**
	 * To select a database for txtsql to use as a default
	 * @param string $db The name of the database that is to be selected
	 * @return void
	 * @access public
	 */
	function selectDb ( $db )
	{
		/* Valid db name? */
		if ( empty($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Cannot select database \'' . $db . '\'');
		}

		/* Does it exist? */
		if ( !$this->_dbExist($db) )
		{
			return $this->_error(E_USER_NOTICE, 'Database \'' . $db . '\' doesn\'t exist');
		}

		/* Select the database */
		$this->_SELECTEDDB = $this->_query->_SELECTEDDB = $db;

		return TRUE;
	}

	/**
	 * Alias of tableExists()
	 */
	function table_exists ( $table, $db )
	{
		return $this->tableExists($table, $db);
	}

	/**
	 * An alias (but public) of the private function _tableExist()
	 * @param $table Table to be checked for existence
	 * @param $db The database the table is in
	 * @return bool Whether it exists or not
	 * @access public
	 */
	function tableExists ( $table, $db )
	{
		return $this->_tableExist($table, $db);
	}

	/**
	 * Alias of dbExists()
	 */
	function db_exists ( $db )
	{
		return $this->dbExists($db);
	}

	/**
	 * An alias (public) of the private function _dbExist()
	 * @param $table DB to be checked for existence
	 * @return bool Whether it exists or not
	 */
	function dbExists ( $db )
	{
		return $this->_dbExist($db);
	}

	/**
	 * Alias of table_count()
	 */
	function table_count ( $table, $database )
	{
		return $this->tableCount($table, $database);
	}

	/**
	 * To retrieve the number of records inside of a table
	 * @param string $table The name of the table
	 * @param string $database The database the table is inside of (optional)
	 * @return int $count The number of records in the table
	 * @access public
	 */
	function tableCount ( $table, $database = NULL )
	{
		/* Inside of another database? */
		if ( !empty($database) )
		{
			if ( !$this->selectDb($database) )
			{
				return FALSE;
			}
		}

		/* No database or no table specified means that we stop here */
		if ( empty($this->_SELECTEDDB) || empty($table) )
		{
			return $this->_error(E_USER_NOTICE, 'No database selected');
		}

		/* Does table exist? */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";

		if ( !is_file($filename.'.MYD') || !is_file($filename.'.FRM') )
		{
			return $this->_error(E_USER_NOTICE, 'Table \'' . $table . '\' doesn\'t exist');
		}

		/* Read in the table's records */
		if ( ( $rows = @file($filename . '.MYD') ) === FALSE )
		{
			return $this->_error(E_USER_NOTICE, 'Table \'' . $table . '\' doesn\'t exist');
		}

		$count = substr($rows[0], 2, strpos($rows[0], '{') - 3);

		/* Return the count */
		return $count;
	}

	/**
	 * Alias of lastInsertId()
	 */
	function last_insert_id ( $table, $db = '', $column = '' )
	{
		return $this->lastInsertId($table, $db, $column);
	}

	/**
	 * To retrieve the last ID generated by an auto_increment field in a table
	 * @param string $table The name of the table
	 * @param string $db The database the table is inside of (optional)
	 * @return string $column Get the last ID generated by this column instead of the priamry key (optional)
	 * @access public
	 */
	function lastInsertId ( $table, $db = '', $column = '' )
	{
		/* Select a database if one is given */
		if ( !empty($db) )
		{
			if ( !$this->selectDb($db) )
			{
				return FALSE;
			}
		}

		/* Check for a selected database */
		if ( empty($this->_SELECTEDDB) )
		{
			return $this->_error(E_USER_NOTICE, 'No database selected');
		}

		/* Read in the column definitions */
		if ( ( $cols = $this->_readFile("$this->_LIBPATH/$this->_SELECTEDDB/$table.FRM") ) === FALSE )
		{
			return $this->_error(E_USER_NOTICE, 'Table \'' . $table . '\' doesn\'t exist');
		}

		/* Check for a valid column that is auto_increment */
		if ( !empty($column) )
		{
			if ( $this->_getColPos($column, $cols) === FALSE )
			{
				return $this->_error(E_USER_NOTICE, 'Column \'' . $column . '\' doesn\'t exist');
			}

			elseif ( $cols[$column]['auto_increment'] != 1 )
			{
				return $this->_error(E_USER_NOTICE, 'Column \'' . $column . '\' is not an auto_increment field');
			}

			$cols['primary'] = $column;
		}

		/* If we are using the primary key, make sure it exists */
		elseif ( empty($cols['primary']) && empty($column) )
		{
			return $this->_error(E_USER_NOTICE, 'There is no primary key defined for table \'' . $table . '\'');
		}

		return $cols[ $cols['primary'] ]['autocount'];
	}

	/**
	 * Alias of queryCount()
	 */
	function query_count ()
	{
		return $this->queryCount();
	}

	/**
	 * To return the number of queries sent to txtSQL
	 * @return int $_QUERYCOUNT
	 * @access public
	 */
	function queryCount ()
	{
		return $this->_QUERYCOUNT;
	}

	/**
	 * Alias of lastError()
	 */
	function last_error ()
	{
		return $this->lastError();
	}

	/**
	 * To print the last error that occurred
	 * @return void
	 * @access public
	 */
	function lastError ()
	{
		if ( !empty($this->_query->_ERRORS) )
		{
			echo '<pre>';
			echo $this->_query->_ERRORSPLAIN[ count($this->_query->_ERRORS) - 1 ];
			echo '</pre>';
		}

		elseif ( !empty($this->_ERRORS) )
		{
			echo '<pre>';
			echo $this->_ERRORSPLAIN[ count($this->_ERRORS) - 1 ];
			echo '</pre>';
		}
	}

	/**
	 * Alias of getLastError()
	 */
	function get_last_error ()
	{
		return $this->getLastError();
	}

	/**
	 * To return the last error that occurred
	 * @return string $error The last error
	 * @access public
	 */
	function getLastError ()
	{
		if ( !empty($this->_query->_ERRORS) )
		{
			return $this->_query->_ERRORSPLAIN[ count($this->_query->_ERRORS) - 1 ];
		}

		elseif ( !empty($this->_ERRORS) )
		{
			return $this->_ERRORSPLAIN[ count($this->_ERRORS) - 1 ];
		}

		return FALSE;
	}

	/**
	 * To print any errors that occurred during script execution so far
	 * @return void
	 * @access public
	 */
	function errorDump ()
	{
		/* No errors? */
		if ( empty($this->_ERRORS) && empty($this->_query->_ERRORS) )
		{
			echo 'No errors occurred during script execution';

			return TRUE;
		}

		/* Errors during this part of script */
		if ( !empty($this->_ERRORS) )
		{
			foreach ( $this->_ERRORS as $key => $value )
			{
				echo 'ERROR #[' . $key . '] ' . $value;
			}
		}

		/* Errors during query execution portion */
		elseif ( !empty($this->_query->_ERRORS) )
		{
			foreach ( $this->_query->_ERRORS as $key => $value )
			{
				echo 'ERROR #[' . $key . '] ' . $value;
			}
		}

		return TRUE;
	}

	/**
	 * Removes any cache that is being stored
	 * @return void
	 * @access public
	 */
	function emptyCache()
	{
		$this->_CACHE = array();

		return TRUE;
	}

	// PRIVATE FUNCTIONS //////////////////////////////////////////////////////////////////
	/**
	 * To retrieve the number of records inside of a table
	 * @param int $errno The error type (number form)
	 * @param string $errstr The error message that will be shown
	 * @param string $errtype Prints this string before the message
	 * @return void
	 * @access private
	 */
	function _error ( $errno, $errstr, $errtype = NULL )
	{
		/* If this error is not an internal error, then generate a backtrace
		 * to the line that originally caused the error */
		$backtrace = array_reverse(@debug_backtrace());

		/* Find the right file and line number */
		foreach ( $backtrace as $key => $value )
		{
			if ( isset($value['class']) && strtolower($value['class']) == 'txtsql' )
			{
				$errfile = $value['file'];
				$errline = $value['line'];

				break;
			}
		}

		/* Determine what kind of error this is, so we can display it. */
		switch ( $errno )
		{
			case E_USER_ERROR :
			{
				$type = 'Fatal Error';

				break;
			}

			case E_USER_NOTICE :
			{
				$type = "Warning";

				break;
			}

			default :
			{
				$type = "Error";

				break;
			}
		}

		$type = isset($errtype) ? $errtype : $type;

		/* Print the message to the screen, if strict is on */
		$errormsg             = "<BR />\n<B>txtSQL $type:</B> $errstr in <B>$errfile</B> on line <B>$errline</B>\n<BR /></DIV>";
		$this->_ERRORSPLAIN[] = $errstr;
		$this->_ERRORS[]      = $errormsg;

		if ( !isset($this->_STRICT) || ( $this->_STRICT === TRUE ) )
		{
			echo $errormsg;
		}

		/* If this is a fatal error, then we are forced to exit and stop execution */
		if ( $errno == E_USER_ERROR )
		{
			exit;
		}

		return FALSE;
	}

	/**
	 * Checks for a connection, and valid arguments
	 * @param mixed $arguments The arguments to validify
	 * @return void
	 * @access private
	 */
	function _validate ( $arguments )
	{
		/* Check to see user is connected */
		if ( !$this->_isConnected() )
		{
			return $this->_error(E_USER_NOTICE, 'Can only perform queries when connected!');
		}

		/* Arguments have to be inside of an array */
		if ( !empty($arguments) && !is_array($arguments) )
		{
			return $this->_error(E_USER_ERROR, 'txtSQL can only accept arguments in an array');
		}

		return TRUE;
	}

	/**
	 * To Read a file into a string and return it
	 * @param string $filename The path to the file needed to be opened
	 * @param bool $useCache Whether to save/retrieve this file from a cache
	 * @param bool $unserialize Whether to unserialize the string or not
	 * @return string $contents The file's contents
	 * @access private
	 */
	function _readFile ( $filename, $useCache = TRUE, $unserialize = TRUE )
	{
		/* If file exists */
		if ( is_file($filename) )
		{
			/* Check for a cache if we need to use the cache */
			if ( $useCache === TRUE )
			{
				if ( isset($this->_CACHE[$filename]) )
				{
					return $this->_CACHE[$filename];
				}
			}

			/* Read in the file */
			if ( ( $contents = @implode('', @file($filename)) ) !== FALSE )
			{
				/* Unserialize the string */
				if ( $unserialize === TRUE )
				{
					if ( ( $contents = @unserialize($contents) ) === FALSE )
					{
						return FALSE;
					}
				}

				/* Save the new file in the cache */
				if ( $useCache === TRUE )
				{
					$this->_CACHE[$filename] = $contents;
				}

				return $contents;
			}
		}

		return FALSE;
	}

	/*
	 * If mkdir() is atomic,
	 * then we do not need to worry about race conditions while trying to make the lockDir,
	 * unless of course were writing to NFS, for which this function will be useless.
	 * so thats why i pulled out the usleep(rand()) peice from the last version
	 *
	 * Again, its important to tailor some of the parameters to ones indivdual usage
	 * I set the default $timeLimit to 3/10th's of a second (maximum time allowed to achieve a lock),
	 * but if your writing some extrememly large files, and/or your server is very slow, you may need to increase it.
	 * Obviously, the $staleAge of the lock directory will be important to consider as well if the writing operations might take a while.
	 * My defaults are extrememly general and you're encouraged to set your own
	 *
	 * @param string $filename The filename to which we are writing
	 * @param string $mode The mode to use when opening files (see php-docs)
	 * @param string $contents The contents of the file
	 * @param int $timeLimit Timelimit (in microseconds) of maximum time allowed to achieve a lock
	 * @param int $staleAge How long the lock should last
	 * @return bool $success
	 * @access private
	 */
	function _lockedFilewrite ( $filename, $mode, $contents = '', $timeLimit = 300000, $staleAge = 5 )
	{
		$lockDir = $filename . '.lock';

		/* Make sure if the user disconnects that this doesn't fail */
		ignore_user_abort(TRUE);

		if ( is_dir($lockDir) )
		{
			if ( ( time() - filemtime($lockDir) ) > $staleAge )
			{
				rmdir($lockDir);
			}
		}

		$locked = @mkdir($lockDir);

		if ( $locked === false )
		{
			$timeStart = microtimeFloat();

			do
			{
				if ( ( microtimeFloat() - $timeStart ) > $timeLimit )
				{
					break;
				}


				$locked = @mkdir($lockDir);
			}
			while ( $locked === false );
		}

		$success = false;

		if ( $locked === true )
		{
			$fp = @fopen($filename, $mode);

			if ( @fwrite($fp, $data) )
			{
				$success = true;
			}

			@fclose($fp);

			rmdir($lockDir);
		}

		ignore_user_abort(0);

		return $success;
	}

	/**
	 * To write a string into a file
	 * @param string $filename The path to the file needed to be opened
	 * @param string $mode The fopen() mode to use (Refer to php-docs)
	 * @return string $contents The file's contents
	 * @access private
	 */
	function _writeFile ( $filename, $mode = 'w', $contents = '' )
	{
		/* See if we need to write to the file using an alternate method for flock() */
		if ( $this->_ALTERNATEFLOCK === TRUE )
		{
			if ( $this->_lockedFilewrite($filename, $mode, $contents) === TRUE )
			{
				return TRUE;
			}
		}

		/* Create a file pointer */
		else
		{
			if ( ( $fp = @fopen($filename, $mode) ) !== FALSE )
			{
				/* Wait for an exclusive file lock */
				if ( @flock($fp, LOCK_EX) !== FALSE )
				{
					/* Write the contents to the file */
					if ( @fwrite($fp, $contents) !== FALSE )
					{
						return TRUE;
					}
					else
					{
						$this->_error(E_USER_ERROR, 'Error when writing to file \'' . $filename . '\'');
					}
				}
				else
				{
					$this->_error(E_USER_ERROR, 'Error creating file lock for \'' . $filename . '\'');
				}
			}

			else
			{
				$this->_error(E_USER_ERROR, 'Error when opening file \'' . $filename . '\' for writing');
			}
		}

		return FALSE;
	}

	/**
	 * Check to see whether a user is connected or not
	 * @return bool $connected Whether the user is connected or not
	 * @access private
	 */
	function _isConnected ()
	{
		/* If either one of the user or pass vars are empty, then return false; */
		if ( empty($this->_USER) )
		{
			return FALSE;
		}

		/* Are we authenticated? */
		if ( $this->_data[ strtolower($this->_USER) ] != md5($this->_PASS) )
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * To check whether a database exists or not
	 * @param string $db The name of the database
	 * @return bool Whether the db exists or not
	 * @access private
	 */
	function _dbExist ( $db )
	{
		return is_dir("$this->_LIBPATH/$db") ? TRUE : FALSE;
	}

	/**
	 * To check whether a table exists or not
	 * @param string $table The name of the table
	 * @param string $db The name of the database the table is in
	 * @return bool Whether the db exists or not
	 * @access private
	 */
	function _tableExist ( $table, $db )
	{
		/* Check to see if the database exists */
		if ( !empty($db) )
		{
			if ( !$this->selectDb($db) )
			{
				return FALSE;
			}
		}

		/* Check to see if the table exists */
		$filename = "$this->_LIBPATH/$this->_SELECTEDDB/$table";

		if ( is_file($filename . '.MYD') && is_file($filename . '.FRM') )
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Alias of _generateWhereClause()
	 * @param mixed $where The array containing the where clause
	 * @param mixed $cols The array containing the column definitions
	 * @return string $query The string which contains the php-equivelent to the where clause
	 * @access private
	 */
	function _buildIf ( $where, $cols )
	{
		return $this->_generateWhereClause($where, $cols);
	}

	/**
	 * Builds php code for a group of where clauses
	 * @param mixed $where The where clause (array version)
	 * @param mixed $cols The column definitions
	 * @return string $query The PHP-equivalent of the WHERE clause
	 * @access private
	 */
	function _generateWhereClause ( $where, $cols )
	{
		/* Make sure that $where is an array */
		if ( !is_array($where) || empty($where) )
		{
			return $this->_error(E_USER_NOTICE, 'Where clause must be an array');
		}

		/* Create some variables */
		$query = '( ';

		/* Start gluing together parts of the query */
		foreach ( $where as $key => $value )
		{
			/* This $value is supposed to be a logical operator */
			if ( $key % 2 == 1 )
			{
				$and = ( strtolower($value) == 'and' );
				$or  = ( strtolower($value) == 'or' );
				$xor = ( strtolower($value) == 'xor' );

				/* Check if we have a valid logical operator */
				if ( ( $and === FALSE ) && ( $or === FALSE ) && ( $xor === FALSE ) )
				{
					return $this->_error(E_USER_NOTICE, 'Only boolean seperators AND, and OR are allowed');
				}

				/* Add the PHP version of the logical operator */
				$query .= ( $and === TRUE ) ? ' && ' : ( ( $xor === TRUE ) ? ' XOR ' : ' || ' );
				continue;
			}

			/* Generate PHP code for this part */
			if ( ( $query .= $this->_generatePhpIf($value, $cols) ) === FALSE )
			{
				return FALSE;
			}
		}

		/* Return our query */
		return $query . ' )';
	}

	/**
	 * Generates PHP code for an individual WHERE clause
	 * @param string $clause An individual WHERE clause
	 * @param mixed $cols The column definitions
	 * @return string $code The PHP-equivalent of the WHERE clause
	 * @access private
	 */
	function _generatePhpIf ( $clause, $cols )
	{
		/* Instantiate a new parser for the clause, and some variables */
		$parser = new WordParser($clause);
		$column = $parser->getNextWord(TRUE);
		$op     = $parser->getNextWord(TRUE);
		$value  = $parser->getNextWord(TRUE);
		$code   = '';

		/* Check if this is column uses a function */
		if ( substr_count($column, '(') > 0 && substr_count($column, '(') == substr_count($column, ')') )
		{
			/* Grab the function name */
			$function = substr($column, 0, strpos($column, '('));

			/* Check if this is a nested function */
			if ( empty($function) )
			{
				$column    = substr($column, strpos($column, '(') + 1, -1);
				$subParser = new SqlParser($column);
				$subParser->parseWhere($column = array());

				/* Generate PHP code for the nested part */
				$code .= $test = $this->_generateWhereClause($column['where'], $cols);
			}
			else
			{
				/* Generate PHP code for this part of the clause */
				if ( ( $last = $this->_generateClause($column, $cols) ) === FALSE )
				{
					return FALSE;
				}
				$code .= $last;
			}
		}
		else
		{
			/* Generate PHP code for this part of the clause */
			$code .= $last = $this->_generateClause($column, $cols);
		}

		/* Check for an operator and generate code for the value after the operator */
		if ( !empty($op) )
		{
			$op = strtolower($op);

			/* Make sure that this operator is valid */
			switch ( $op )
			{
				/* Regex Operator */
				case '%=' :
				case '!%' :

				/* Equality Operator */
				case '!=' :

				/* LIKE Operator */
				case '!~' :
				{
					$op = ( $op == '!~' ) ? 'notlike' : $op;
				}

				case '=~' :
				{
					$op = ( $op == '=~' ) ? 'like'    : $op;
				}

				/* Inequality Operator */
				case '<=' :
				case '>=' :

				/* Equality Operator */
				case '='  :
				{
					$op = ( $op == '=' ) ? '==' : $op;
				}

				case '<>' :

				/* Inequality Operator */
				case '<'  :
				case '>'  :

				/* In-String Operator */
				case '!?' :
				case '?'  :

				/* Regex Operator */
				case 'regexp' :
				{
					$op = ( $op == 'regexp' )    ? '%=' : $op;
				}

				case 'notregexp' :
				{
					$op = ( $op == 'notregexp' ) ? '!%' : $op;
				}

				/* LIKE Operator */
				case 'notlike' :
				case 'like' :
				{
					break;
				}

				/* Invalid Operator */
				default:
				{
					/* There is an error in your where clause */
					return $this->_error(E_USER_NOTICE, 'You have an error in your where clause, (operators allowed: =, !=, <>, =~, !~, <, >, <=, >=)');
				}
			}

			/* The 'instring' operator */
			if ( $op == '!?' || $op == '?' )
			{
				$value = str_replace('"', '\\\\"', $value);
				$code  = substr($code, 0, strlen($code) - strlen($last));

				if ( ( $functionClause = $this->_generateClause($value, $cols) ) === FALSE )
				{
					return FALSE;
				}

				$code .= '( strpos(' . $last . ', ' . $functionClause .') ' . ( $op == '?' ? '!' : '=' ) . '== FALSE )';
			}

			/* LIKE query using simplified regular expressions */
			elseif ( $op == 'like' || $op == 'notlike' )
			{
				/* Get the value after the operator */
				$parser->setString($value);
				$value = $parser->getNextWord();

				/* Create the PHP Code */
				$code  = substr($code, 0, strlen($code) - strlen($last));
				$value = str_replace( array('(',   ')',  '{',  '}', '.',  '$',  '/',       '\%',  '*',     '%', '$$PERC$$'),
				                      array('\(', '\)', '\{', '\}', '\.', '\$', '\/', '$$PERC$$', '\*', '(.+)?',       '%'), $value);

				$code .= '( ' . ( $op == 'notlike' ? '!' : '' ) . 'preg_match("/^' . $value . '$/iU", ' . $last . ') )';
			}

			/* Regular expressions query */
			elseif ( $op == '%=' || $op == '!%' )
			{
				/* Get the value after the operator */
				$parser->setString($value);
				$value = $parser->getNextWord(FALSE);

				/* Create the PHP Code */
				$code  = substr($code, 0, strlen($code) - strlen($last));
				$code .= '( ' . ( $op == '!%' ? '!' : '' ) . 'preg_match("' . $value . '", ' . $last . ') )';
			}

			/* Append the PHP code for the second part of the query ( the value after the operator ) */
			else
			{
				if ( ( $last = $this->_generateClause($value, $cols) ) === FALSE )
				{
					return FALSE;
				}

				$code .= " $op " . $last;
			}
		}

		/* Return the PHP code */
		return $code;
	}

	/**
	 * To generate PHP-equivalent for a section of a WHERE clause (either the column or the value)
	 * @param string $clause The clause that needs to be evaluated
	 * @param mixed $cols The column definitions array
	 * @return string $code The php code for the clause
	 * @access private
	 */
	function _generateClause ( $clause, $cols )
	{
		/* Filter the function name and its arguments */
		$funcName = substr($clause, 0, strpos($clause, '('));

		/* The default function is EVAL */
		if ( empty($funcName) )
		{
			$funcName  = 'eval';
			$arguments = $clause;
		}

		/* Grab the arguments */
		else
		{
			$arguments = substr($clause, strpos($clause, '(') + 1, -1);
		}

		/* List of valid functions */
		$functions = array(
			'strlower'   => 'strtolower',
			'strupper'   => 'strtoupper',
			'chop'       => 'chop',
			'rtrim'      => 'rtrim',
			'ltrim'      => 'ltrim',
			'trim'       => 'trim',
			'md5'        => 'md5',
			'stripslash' => 'stripslashes',
			'strlength'  => 'strlen',
			'strreverse' => 'strrev',
			'ucfirst'    => 'ucfirst',
			'ucwords'    => 'ucwords',
			'bin2hex'    => 'bin2hex',
			'entdecode'  => 'html_entity_decode',
			'entencode'  => 'htmlentities',
			'soundex'    => 'soundex',
			'ceil'       => 'ceil',
			'floor'      => 'floor',
			'round'      => 'round',
			'isnumeric'  => 'is_numeric',
			'!isnumeric' => '!is_numeric',
			'isstring'   => '!is_string',
			'!isstring'  => '!is_string',
			'isfile'     => 'is_file',
			'!isfile'    => '!is_file',
			'isdir'      => 'is_dir',
			'!isdir'     => '!is_dir',
			'rand'       => 'rand',
			'time'       => 'time',
			'microtime'  => 'microtime',
			'eval'       => '',
			''           => '');

		/* Check for a valid function */
		if ( !isset($functions[ strtolower($funcName) ]) )
		{
			return $this->_error(E_USER_NOTICE, 'Function, \'' . $funcName . '\', hasn\'t been implemented');
		}

		/* Instantiate a new parser for the arguments */
		$lastType = 'col';
		$code     = $functions[ strtolower($funcName) ] . "(";
		$parser   = new WordParser($arguments);

		while ( $word = $parser->getNextWord(TRUE) )
		{
			$word = ( $word == '00' ) ? '0' : $word;

			/* Functions, columns etc. */
			if ( !$this->_isTextString($word) && !is_numeric($word)  )
			{
				/* Nested function, generate clause for this function */
				if ( substr_count($word, '(') > 0 && substr_count($word, '(') == substr_count($word, ')') )
				{
					if ( ( $code .= $this->_generateClause($word, $cols) ) === FALSE )
					{
						return FALSE;
					}

					$lastType = 'func';
				}

				/* Operators ( and commas ) */
				elseif ( ( $word == '+' ) || ( $word == '-' ) || ( $word == '/' ) ||
				         ( $word == '*' ) || ( $word == '%' ) || ( $word == ',' ) )
				{
					/* Determine type for the next argument */
					$nextType   = $parser->getNextWord(TRUE);
					$parser->c -= strlen($nextType) + 1;

					switch ( TRUE )
					{
						/* Function or column */
						case ( !$this->_isTextString($nextType) && !is_numeric($nextType) ) :
						{
							$nextType = 'col';

							if ( substr_count($nextType, '(') > 0 )
							{
								if ( substr_count($nextType, '(') == substr_count($nextType, ')') )
								{
									$nextType = 'func';
								}
							}

							break;
						}

						/* Integer */
						case ( is_numeric($nextType) ) :
						{
							$nextType = 'int';

							break;
						}

						/* Default (String) */
						default:
						{
							$nextType = 'str';
						}
					}

					/* Concatenation operator, not addition operator, in some cases */
					if ( $word == '+' )
					{
						if ( ( $lastType == 'col'  && $nextType == 'str' ) || ( $lastType == 'str' && $nextType == 'col'  ) ||
						     ( $lastType == 'str'  && $nextType == 'str' ) || ( $lastType == 'col' && $nextType == 'col'  ) ||
						     ( $lastType == 'func' && $nextType == 'str' ) || ( $lastType == 'str' && $nextType == 'func' ) )
						{
							$word = '.';
						}
					}

					/* Conver to php-equality operator */
					elseif ( $word == '=' )
					{
						$word = ' == ';
					}

					$code .= " $word ";
				}

				/* Column */
				else
				{
					/* What if the column name is primary? */
					if ( strtolower(trim($word)) == 'primary' )
					{
						/* Make sure there is a primary key */
						if ( empty($cols['primary']) )
						{
							return $this->_error(E_USER_NOTICE, 'No primary key has been assigned to this table');
						}

						$word = $cols['primary'];
					}

					/* Look for the index for the column */
					if ( ( $colPos = $this->_getColPos($word, $cols) ) === FALSE )
					{
						return $this->_error(E_USER_NOTICE, 'Column \'' . $word . '\' doesn\'t exist');
					}

					$code .= '$value[' . $colPos . ']';
				}
			}

			/* Text, numbers etc.*/
			else
			{
				/* This is a string */
				if ( !is_numeric($word) )
				{
					$word     = "'" . substr($word, 1, -1) . "'";
					$lastType = 'str';
				}

				/* This is a number */
				else
				{
					$lastType = 'int';
				}

				$code .= $word;
			}
		}

		/* Return our PHP code */
		return $code . ')';
	}

	/**
	 * To retrieve the index of the column from the columns' array
	 * @param string $colname The name of the column to be searched for
	 * @param mixed $cols The column definitions array
	 * @return int $position The index of the column in the array
	 * @access private
	 */
	function _getColPos ( $colname, $cols )
	{
		/* Make sure array is not empty, and the parameter is an array */
		if ( empty($cols) || !is_array($cols) || !array_key_exists($colname, $cols) )
		{
			return FALSE;
		}

		unset($cols['primary']);

		/* Get the index for the column */
		if ( ( $position = array_search( $colname, array_keys($cols) ) ) === FALSE )
		{
			return FALSE;
		}

		return $position;
	}

	/**
	 * Does what unique_array() does but with multidimensional arrays
	 * @param mixed $array The array that will be filtered
	 * @param string $sub_key The $key that will be examined for duplicates
	 */
	function _uniqueArray ( $array, $sub_key )
	{
		$target	                 = array();
		$existing_sub_key_values = array();

		if ( isset($array[0][$sub_key]) )
		{
			foreach ( $array as $key => $sub_array )
			{
				if ( !in_array($sub_array[$sub_key], $existing_sub_key_values) )
				{
					$existing_sub_key_values[] = $sub_array[$sub_key];
					$target[$key]              = $sub_array;
				}
			}

			return $target;
		}

		return $this->_error(E_USER_NOTICE, 'Column \'' . $sub_key . '\' is not in result set; cannot determine distinct values');
	}

	/**
	 * Checks whether a variable contains a string or not (inside single or double quotes)
	 * @return bool TRUE/FALSE
	 * @access private
	 */
	function _isTextString ( $string )
	{
		$tokens = token_get_all("<?" ."php {$string}; ?" . ">");

		if ( is_array(@$tokens[1]) )
		{
			if ( $tokens[1][0] == T_CONSTANT_ENCAPSED_STRING )
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Returns the current txtSQL version
	 * @return string $version The current version of txtSQL
	 * @access public
	 */
	function version()
	{
		return '3.0.0 Beta';
	}
}

/**
 * Defines a constant with the default value of './'
 * @return void
 * @access private
 */
function defineConstant ( $value )
{
	if ( !defined($value) )
	{
		define($value, './');
	}
}

/**
 * Returns the microtime
 * @return int The microtime calculated
 * @access private
 */
function microtimeFloat()
{
	list ( $usec, $sec ) = explode(' ', microtime());

	return ( float ) $usec + ( float ) $sec;
}
?>
