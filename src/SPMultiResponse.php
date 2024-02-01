<?php

namespace laravel_hrdk\LaravelSPMultiResponse;

use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

/**
 * Class for calling MSSQL stored procedures with multiple data sets in response
 * @package RodionARR
 */
class SPMultiResponse
{
    /**
     * @var \Illuminate\Database\ConnectionInterface|object
     */
    private $connection;

    /**
     * MSSQLPDOService constructor.
     * @param string $connection
     */
    public function __construct(string $connection = 'sqlsrv')
    {
        $this->connection = DB::connection($connection);
    }

    /**
     * Calls a stored procedure and returns multiple result sets
     *
     * @param string $storedProcedureName Name of the stored procedure
     * @param array $parameters Parameters for the stored procedure
     * @return array An array of result sets
     * @throws RuntimeException If the stored procedure does not exist
     */
    public function callStoredProcedure(string $storedProcedureName, array $parameters = [])
    {
        if ($this->_checkStoredProcedure($storedProcedureName) == 0) {
            throw new RuntimeException($storedProcedureName.' - Stored Procedure does not exist');
        }

        /**
         * @var PDO $pdo
         */
        $pdo = $this->connection->getPdo();

        $parametersString = implode(',', array_fill(0, count($parameters), '?'));

        $callString = "EXEC $storedProcedureName $parametersString;";
        $statement = $pdo->prepare($callString);

        foreach ($parameters as $index => $paramValue) {
            // Note: Parameter index in bindValue is 1-based
            $statement->bindValue($index + 1, $paramValue, $this->_PDODataType($paramValue));
        }

        $statement->execute();
        $pdoDataResults = [];
        do {
            try {
                $rowSet = $statement->fetchAll(PDO::FETCH_ASSOC);
                if ($rowSet) {
                    $pdoDataResults[] = $rowSet;
                }
            } catch (\Exception $e) {
                // Catch exception if no more result sets are available
                break;
            }
        } while ($statement->nextRowset());

        return $pdoDataResults;
    }

    /**
     * Checks the existence of a stored procedure
     *
     * @param string $procedureName The name of the stored procedure
     * @return int Returns 1 if exists, otherwise 0
     */
    private function _checkStoredProcedure($procedureName)
    {
        $check = $this->connection
            ->table("information_schema.routines")
            ->where("ROUTINE_TYPE", "PROCEDURE")
            ->where("ROUTINE_SCHEMA", "dbo") // Adjust schema name as necessary
            ->where("ROUTINE_NAME", "=", $procedureName)
            ->select("ROUTINE_NAME")
            ->first();

        return count((array)$check);
    }

    /**
     * Determines the PDO data type for a given value
     *
     * @param mixed $value The value whose PDO data type is to be determined
     * @return int The PDO data type
     */
    private function _PDODataType($value)
    {
        if (is_null($value)) {
            return PDO::PARAM_NULL;
        }

        if (is_bool($value)) {
            return PDO::PARAM_BOOL;
        }

        if (is_int($value)) {
            return PDO::PARAM_INT;
        }

        if (is_resource($value)) {
            return PDO::PARAM_LOB;
        }

        return PDO::PARAM_STR;
    }
}
