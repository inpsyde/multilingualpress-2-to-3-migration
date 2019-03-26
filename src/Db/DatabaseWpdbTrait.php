<?php

namespace Inpsyde\MultilingualPress2to3\Db;

use Exception;
use Throwable;
use wpdb as Wpdb;

/**
 * Functionality for running database operations using WPDB.
 *
 * @package MultilingualPress2to3
 */
trait DatabaseWpdbTrait
{

    /**
     * Inserts a record defined by provided data into a table with the specified name.
     *
     * @param string $name The name of the table to insert into.
     * @param array|object $data The data of the record to insert.
     *
     * @return int|null The inserted row's ID, if applicable; otherwise null;
     *
     * @throws Throwable If problem inserting.
     */
    protected function _insert($name, $data)
    {
        $data = (array) $data;
        $db = $this->_getDb();

        $db->insert($name, $data);
        $error = $db->last_error;
        $insertId = $db->insert_id;

        // Error while inserting
        if (empty($result) && !empty($error)) {
            throw new Exception($error);
        }

        // No new insert ID generated
        if ($insertId === 0) {
            return null;
        }

        return $insertId;
    }

    /**
     * Retrieves rows from the database matching the query conditions.
     *
     * @param string $query A query with optional `sprintf()` style placeholders.
     * @param array $values A list of values for the query placeholders.
     *
     * @return object[] A list of records.
     *
     * @throws Throwable If problem selecting.
     */
    protected function _select($query, $values = [])
    {
        $db = $this->_getDb();

        if (!empty($values)) {
            $query = $db->prepare($query, $values);
        }

        $result = $db->get_results($query, 'OBJECT');
        $error = $db->last_error;

        // Error while selecting
        if (empty($result) && !empty($error)) {
            throw new Exception($error);
        }

        return $result;
    }

    /**
     * Prefixes a table name.
     *
     * @param string $name The name to prefix.
     *
     * @return string The prefixed name.
     *
     * @throws Throwable If problem prefixing.
     */
    protected function _getPrefixedTableName($name)
    {
        $prefix = $this->_getDb()->prefix;

        return "{$prefix}$name";
    }

    /**
     * Retrieves the WPDB adapter used by this instance.
     *
     * @return Wpdb
     *
     * @throws Throwable
     */
    abstract protected function _getDb();
}
