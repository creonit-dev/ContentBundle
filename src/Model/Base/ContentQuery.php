<?php

namespace Creonit\ContentBundle\Model\Base;

use \Exception;
use \PDO;
use Creonit\ContentBundle\Model\Content as ChildContent;
use Creonit\ContentBundle\Model\ContentQuery as ChildContentQuery;
use Creonit\ContentBundle\Model\Map\ContentTableMap;
use Creonit\PageBundle\Model\Page;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'content' table.
 *
 *
 *
 * @method     ChildContentQuery orderByText($order = Criteria::ASC) Order by the text column
 * @method     ChildContentQuery orderByCompleted($order = Criteria::ASC) Order by the completed column
 * @method     ChildContentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildContentQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildContentQuery orderById($order = Criteria::ASC) Order by the id column
 *
 * @method     ChildContentQuery groupByText() Group by the text column
 * @method     ChildContentQuery groupByCompleted() Group by the completed column
 * @method     ChildContentQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildContentQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildContentQuery groupById() Group by the id column
 *
 * @method     ChildContentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildContentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildContentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildContentQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildContentQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildContentQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildContentQuery leftJoinPage($relationAlias = null) Adds a LEFT JOIN clause to the query using the Page relation
 * @method     ChildContentQuery rightJoinPage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Page relation
 * @method     ChildContentQuery innerJoinPage($relationAlias = null) Adds a INNER JOIN clause to the query using the Page relation
 *
 * @method     ChildContentQuery joinWithPage($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Page relation
 *
 * @method     ChildContentQuery leftJoinWithPage() Adds a LEFT JOIN clause and with to the query using the Page relation
 * @method     ChildContentQuery rightJoinWithPage() Adds a RIGHT JOIN clause and with to the query using the Page relation
 * @method     ChildContentQuery innerJoinWithPage() Adds a INNER JOIN clause and with to the query using the Page relation
 *
 * @method     \Creonit\PageBundle\Model\PageQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildContent findOne(ConnectionInterface $con = null) Return the first ChildContent matching the query
 * @method     ChildContent findOneOrCreate(ConnectionInterface $con = null) Return the first ChildContent matching the query, or a new ChildContent object populated from the query conditions when no match is found
 *
 * @method     ChildContent findOneByText(string $text) Return the first ChildContent filtered by the text column
 * @method     ChildContent findOneByCompleted(boolean $completed) Return the first ChildContent filtered by the completed column
 * @method     ChildContent findOneByCreatedAt(string $created_at) Return the first ChildContent filtered by the created_at column
 * @method     ChildContent findOneByUpdatedAt(string $updated_at) Return the first ChildContent filtered by the updated_at column
 * @method     ChildContent findOneById(int $id) Return the first ChildContent filtered by the id column *

 * @method     ChildContent requirePk($key, ConnectionInterface $con = null) Return the ChildContent by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildContent requireOne(ConnectionInterface $con = null) Return the first ChildContent matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildContent requireOneByText(string $text) Return the first ChildContent filtered by the text column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildContent requireOneByCompleted(boolean $completed) Return the first ChildContent filtered by the completed column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildContent requireOneByCreatedAt(string $created_at) Return the first ChildContent filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildContent requireOneByUpdatedAt(string $updated_at) Return the first ChildContent filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildContent requireOneById(int $id) Return the first ChildContent filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildContent[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildContent objects based on current ModelCriteria
 * @method     ChildContent[]|ObjectCollection findByText(string $text) Return ChildContent objects filtered by the text column
 * @method     ChildContent[]|ObjectCollection findByCompleted(boolean $completed) Return ChildContent objects filtered by the completed column
 * @method     ChildContent[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildContent objects filtered by the created_at column
 * @method     ChildContent[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildContent objects filtered by the updated_at column
 * @method     ChildContent[]|ObjectCollection findById(int $id) Return ChildContent objects filtered by the id column
 * @method     ChildContent[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ContentQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Creonit\ContentBundle\Model\Base\ContentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Creonit\\ContentBundle\\Model\\Content', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildContentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildContentQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildContentQuery) {
            return $criteria;
        }
        $query = new ChildContentQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildContent|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ContentTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ContentTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildContent A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT `text`, `completed`, `created_at`, `updated_at`, `id` FROM `content` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildContent $obj */
            $obj = new ChildContent();
            $obj->hydrate($row);
            ContentTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildContent|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ContentTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ContentTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the text column
     *
     * Example usage:
     * <code>
     * $query->filterByText('fooValue');   // WHERE text = 'fooValue'
     * $query->filterByText('%fooValue%'); // WHERE text LIKE '%fooValue%'
     * </code>
     *
     * @param     string $text The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterByText($text = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($text)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentTableMap::COL_TEXT, $text, $comparison);
    }

    /**
     * Filter the query on the completed column
     *
     * Example usage:
     * <code>
     * $query->filterByCompleted(true); // WHERE completed = true
     * $query->filterByCompleted('yes'); // WHERE completed = true
     * </code>
     *
     * @param     boolean|string $completed The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterByCompleted($completed = null, $comparison = null)
    {
        if (is_string($completed)) {
            $completed = in_array(strtolower($completed), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ContentTableMap::COL_COMPLETED, $completed, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ContentTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ContentTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ContentTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ContentTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ContentTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ContentTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ContentTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query by a related \Creonit\PageBundle\Model\Page object
     *
     * @param \Creonit\PageBundle\Model\Page|ObjectCollection $page the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildContentQuery The current query, for fluid interface
     */
    public function filterByPage($page, $comparison = null)
    {
        if ($page instanceof \Creonit\PageBundle\Model\Page) {
            return $this
                ->addUsingAlias(ContentTableMap::COL_ID, $page->getContentId(), $comparison);
        } elseif ($page instanceof ObjectCollection) {
            return $this
                ->usePageQuery()
                ->filterByPrimaryKeys($page->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPage() only accepts arguments of type \Creonit\PageBundle\Model\Page or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Page relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function joinPage($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Page');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Page');
        }

        return $this;
    }

    /**
     * Use the Page relation Page object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Creonit\PageBundle\Model\PageQuery A secondary query class using the current class as primary query
     */
    public function usePageQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinPage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Page', '\Creonit\PageBundle\Model\PageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildContent $content Object to remove from the list of results
     *
     * @return $this|ChildContentQuery The current query, for fluid interface
     */
    public function prune($content = null)
    {
        if ($content) {
            $this->addUsingAlias(ContentTableMap::COL_ID, $content->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the content table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContentTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ContentTableMap::clearInstancePool();
            ContentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ContentTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ContentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ContentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildContentQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(ContentTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildContentQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(ContentTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildContentQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(ContentTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildContentQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(ContentTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildContentQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(ContentTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildContentQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(ContentTableMap::COL_CREATED_AT);
    }

} // ContentQuery
