<?php
declare(strict_types=1);

namespace Inpsyde\MultilingualPress2to3;

use Throwable;
use wpdb as Wpdb;

/**
 * The command responsible for migrating relationships.
 *
 * @package MultilingualPress2to3
 */
class MigrateRelationshipsCliCommand
{
    use DatabaseWpdbTrait;

    protected $migrator;
    protected $db;

    /**
     * @param ContentRelationshipMigrator $migrator The relationship migrator to be used by this instance.
     *
     * @param Wpdb $db The database driver for this instance.
     */
    public function __construct(
        ContentRelationshipMigrator $migrator,
        Wpdb $db
    ) {

        $this->migrator = $migrator;
        $this->db = $db;
    }

    /**
     * Executes the command.
     *
     * @throws Throwable If problem executing.
     */
    public function __invoke()
    {
        $relationships = $this->_getRelationshipsToMigrate();

        foreach ($relationships as $relationship) {
            $this->migrator->migrate($relationship);
        }
    }

    /**
     * Retrieves MLP2 links to migrate to MLP3.
     *
     * @return object[] A list of objects, each representing an MLP2 relationship.
     * A relationship corresponds to a record of the `multilingual_linked` table.
     *
     * @throws Throwable If problem retrieving relationships.
     */
    protected function _getRelationshipsToMigrate()
    {
        $table = $this->_getTableName('multilingual_linked');
        return $this->_select("SELECT * FROM {$table}");
    }

    protected function _getMigrator()
    {
        return $this->migrator;
    }

    /**
     * Retrieves the database driver associated with this instance.
     *
     * @return Wpdb The database driver.
     *
     * @throws Throwable If problem retrieving driver.
     */
    protected function _getDb()
    {
        return $this->db;
    }

    /**
     * Retrieves the table name corresponding to the given identifier.
     *
     * @param string $name The table identifier.
     * @return string The table name.
     *
     * @throws Throwable If problem retrieving table name.
     */
    protected function _getTableName(string $name)
    {
        return $this->_getPrefixedTableName($name);
    }
}
