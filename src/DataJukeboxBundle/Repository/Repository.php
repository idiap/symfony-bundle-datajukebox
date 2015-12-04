<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Repository\Repository.php

/** Data Jukebox Bundle
 *
 * <P><B>COPYRIGHT:</B></P>
 * <PRE>
 * Data Jukebox Bundle
 * Copyright (C) 2015 Idiap Research Institute <http://www.idiap.ch>
 * Author: Cedric Dufour <http://cedric.dufour.name>
 *
 * This file is part of the Data Jukebox Bundle.
 *
 * The Data Jukebox Bundle is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, Version 3.
 *
 * The Data Jukebox Bundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * </PRE>
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyExtension
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\Repository;

use DataJukeboxBundle\DataJukebox as DataJukebox;

use Doctrine\DBAL as DBAL;
use Doctrine\ORM as ORM;

/** Doctrine\ORM generic repository
 *
 * <P>This class provides a default Doctrine ORM entity repository
 * for fetching data according to their properties.</P>
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyExtension
 */
class Repository
  extends ORM\EntityRepository
  implements DataJukebox\RepositoryInterface
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Data properties
   * @var PropertiesInterface
   */
  protected $oProperties;

  /** Debug flag
   * @var boolean
   */
  protected $bDebug = false;


  /*
   * METHODS
   ********************************************************************************/

  /** Sets the debug flag
   * @param boolean $bDebug Debug flag
   */
  public function setDebug($bDebug)
  {
    $this->bDebug = (boolean)$bDebug;
  }

  /** Returns the data list (queried from the database)
   * @param BrowserInterface $oBrowser Data browser
   * @return Result Data result (data list)
   */
  public function getDataList($oBrowser=null)
  {
    if (!is_null($oBrowser) and !$oBrowser instanceof DataJukebox\BrowserInterface) {
      throw new \RuntimeException('Browser object must implement \DataJukeboxBundle\DataJukebox\BrowserInterface');
    }

    // Entity resources
    $oEntityManager = $this->getEntityManager();
    $oDbConnection = $oEntityManager->getConnection();
    $oClassMetadata = $this->getClassMetadata();
    if (!$oBrowser) $oBrowser = $this->oProperties->getBrowser();

    // Data count
    $sSQL_where = $this->sqlWhere($oBrowser);
    $iCount = $this->queryCount($sSQL_where);

    // Check/save range
    $iFrom = $oBrowser->getOffset();
    $iTo = $iFrom+$oBrowser->getLimit()-1;
    if ($iFrom >= $iCount) $iFrom = $iCount-$oBrowser->getLimit();
    if ($iTo >= $iCount) $iTo = $iCount-1;
    if ($iFrom<0) $iFrom = 0;
    if ($iTo<$iFrom) $iTo = $iFrom;
    $oBrowser->setRangeActual($iCount, $iFrom, $iTo);

    // Fields to display/query
    $asFields_display = $oBrowser->getFields();
    $asFields_valid = $this->validFields($asFields_display);

    // Query the actual data
    $aaResults = array();
    if ($iFrom < $iCount) {
      // SELECT statement
      $sSQL = sprintf(
        'SELECT %s FROM %s',
        implode(',', $oClassMetadata->getColumnNames($asFields_valid)),
        $oClassMetadata->getTableName()
      );

      // WHERE statement
      if ($sSQL_where) $sSQL = sprintf('%s WHERE %s', $sSQL, $sSQL_where);

      // ORDER BY statement
      $sSQL_order = null;
      if ($oBrowser) {
        $asFields_order = $this->sqlOrder($oBrowser->getFieldsOrder(), $asFields_valid);
        if (count($asFields_order)) {
          $sSQL_order = implode(',', $asFields_order);
        }
      }
      if ($sSQL_order) $sSQL = sprintf('%s ORDER BY %s', $sSQL, $sSQL_order);

      // LIMIT/OFFSET statement
      $sSQL = sprintf('%s LIMIT %d OFFSET %d', $sSQL, $iTo-$iFrom+1, $iFrom);

      // Query the database
      if ($this->bDebug) var_dump($sSQL); // DEBUG
      $oResultSetMapping = new ORM\Query\ResultSetMappingBuilder($oEntityManager);
      $oResultSetMapping->addRootEntityFromClassMetadata($oClassMetadata->name, 'e');
      $oQuery = $oEntityManager->createNativeQuery($sSQL, $oResultSetMapping);
      $aaResults = $oQuery->getArrayResult();
      $this->mergePrimaryKey($aaResults);
      if ($this->oProperties instanceof DataJukebox\FormatInterface)  {
        array_walk($aaResults, array($this->oProperties, 'formatFields'));
      }
    }

    // Done
    return new DataJukebox\Result(
      $this->oProperties,
      $oBrowser,
      $aaResults
    );
  }

  /** Returns the data detail (queried from the database)
   * @param array $aPK_values Primary key(s)
   * @param BrowserInterface $oBrowser Data browser
   * @return Result Data result (data detail)
   */
  public function getDataDetail($aPK_values, $oBrowser=null)
  {
    if (!is_null($oBrowser) and !$oBrowser instanceof DataJukebox\BrowserInterface) {
      throw new \RuntimeException('Browser object must implement \DataJukeboxBundle\DataJukebox\BrowserInterface');
    }

    // Entity resources
    $oEntityManager = $this->getEntityManager();
    $oDbConnection = $oEntityManager->getConnection();
    $oClassMetadata = $this->getClassMetadata();

    // Data pointer
    if (!$oBrowser) $oBrowser = $this->oProperties->getBrowser();
    $oBrowser->setRangeActual(1, 1, 1, 1);
    if (!is_array($aPK_values)) $aPK_values = explode(':', $aPK_values);
    $aPK_fields = $oClassMetadata->getIdentifierFieldNames();
    if (count($aPK_values) != count($aPK_fields)) {
      throw new ORM\ORMInvalidArgumentException('Incomplete primary key (missing values)');
    }
    $aPKs = array_combine($aPK_fields, $aPK_values);

    // Fields to display/query
    $asFields_display = $oBrowser->getFields();
    $asFields_valid = $this->validFields($asFields_display);

    // SELECT statement
    $sSQL = sprintf(
      'SELECT %s FROM %s',
      implode(',', $oClassMetadata->getColumnNames($asFields_valid)),
      $oClassMetadata->getTableName()
    );

    // WHERE statement
    $aPK_where = array();
    foreach ($aPKs as $sField => $mValue) {
      $aPK_where[] = sprintf(
        '%s=%s',
        $oClassMetadata->getColumnName($sField),
        $oDbConnection->quote($mValue ,$oClassMetadata->getTypeOfField($sField))
      );
    }
    $sSQL = sprintf('%s WHERE %s', $sSQL, implode(' AND ', $aPK_where));

    // Query the database
    if ($this->bDebug) var_dump($sSQL); // DEBUG
    $oResultSetMapping = new ORM\Query\ResultSetMappingBuilder($oEntityManager);
    $oResultSetMapping->addRootEntityFromClassMetadata($oClassMetadata->name, 'e');
    $oQuery = $oEntityManager->createNativeQuery($sSQL, $oResultSetMapping);
    $aaResults = $oQuery->getArrayResult();
    $iResults = count($aaResults);
    if (!$iResults) {
      throw new ORM\EntityNotFoundException();
    }
    if ($iResults > 1) {
      throw new ORM\NonUniqueResultException();
    }
    $this->mergePrimaryKey($aaResults);
    if ($this->oProperties instanceof DataJukebox\FormatInterface) {
      array_walk($aaResults, array($this->oProperties, 'formatFields'));
    }

    // Done
    return new DataJukebox\Result(
      $this->oProperties,
      $oBrowser,
      $aaResults
    );
  }


  /*
   * METHODS: RepositoryInterface
   ********************************************************************************/

  /*
   * SETTERS
   */

  public function setProperties(DataJukebox\PropertiesInterface &$oProperties)
  {
    $this->oProperties =& $oProperties;
  }

  /*
   * GETTERS
   */

  public function getProperties()
  {
    return $this->oProperties;
  }

  public function getDataCount($oBrowser=null)
  {
    if (!is_null($oBrowser) and !$oBrowser instanceof DataJukebox\BrowserInterface) {
      throw new \RuntimeException('Browser object must implement \DataJukeboxBundle\DataJukebox\BrowserInterface');
    }

    if (!$oBrowser) $oBrowser = $this->oProperties->getBrowser();
    $sSQL_where = $this->sqlWhere($oBrowser);
    return $this->queryCount($sSQL_where);
  }

  public function getDataResult($aPK_values=null, $oBrowser=null)
  {
    if (!is_null($oBrowser) and !$oBrowser instanceof DataJukebox\BrowserInterface) {
      throw new \RuntimeException('Browser object must implement \DataJukeboxBundle\DataJukebox\BrowserInterface');
    }

    return is_null($aPK_values)
      ? $this->getDataList($oBrowser)
      : $this->getDataDetail($aPK_values, $oBrowser);
  }

  public function getDataEntity($aPK_values)
  {
    // Entity resources
    $oEntityManager = $this->getEntityManager();
    $oClassMetadata = $this->getClassMetadata();

    // Data pointer
    if (!is_array($aPK_values)) $aPK_values = explode(':', $aPK_values);
    $aPK_fields = $oClassMetadata->getIdentifierFieldNames();
    if (count($aPK_values) != count($aPK_fields)) {
      throw new ORM\ORMInvalidArgumentException('Incomplete primary key (missing values)');
    }

    // SELECT statement
    $sDQL = sprintf(
      'SELECT e FROM %s e WHERE %s',
      $oClassMetadata->name,
      implode(
        ' AND ',
        array_map(
          function($f,$i) {
            return sprintf('e.%s = :pk%d', $f, $i);
          },
          $aPK_fields,
          array_keys($aPK_fields)
        )
      )
    );

    // Query the database
    if ($this->bDebug) var_dump($sDQL); // DEBUG
    $oQuery = $this->getEntityManager()->createQuery($sDQL);
    foreach ($aPK_values as $iIndex => $mPK_value) {
      $oQuery->setParameter(sprintf('pk%d', $iIndex), $mPK_value);
    }
    $aoResults = $oQuery->getResult();
    if (!$aoResults) {
      throw new ORM\EntityNotFoundException();
    }
    if (count($aoResults) > 1) {
      throw new ORM\NonUniqueResultException();
    }

    // Done
    return $aoResults[0];
  }


  /*
   * METHODS (cont'd)
   ********************************************************************************/

  /** Returns the validated fields list
   * @param array|string User-provided fields
   * @return array|string Valid fields (names)
   * @throws \RuntimeException
   */
  protected function validFields($asFields)
  {
    // Check for our own reserved "_PK" field
    if (in_array('_PK', $asFields)) {
      throw new \RuntimeException('Field "_PK" is reserved for internal primary key(s) handling');
    }

    // Check fields
    // NOTE: array operations are such as to preserve fields order
    $oClassMetadata = $this->getClassMetadata();
    // ... required
    $asFields_required = $this->oProperties->getFieldsRequired();
    $asFields_pk = $oClassMetadata->getIdentifierFieldNames();
    $asFields = array_merge(
      $asFields,
      array_diff(
        array_merge(
          $asFields_required,
          array_diff($asFields_pk, $asFields_required)
        ),
        $asFields
      )
    );
    // ... valid
    $asFields = array_intersect(
      $asFields,
      $oClassMetadata->getFieldNames()
    );

    return $asFields;
  }

  /** Translates fields ordering data to SQL (ORDER BY)
   * @param array|array|string $aasFields_order Fields ordering data
   * @param array|string $asFields_valid Valid fields (names)
   * @return array|string Fields ordering SQL fragments
   */
  protected function sqlOrder(array $aasFields_order, array $asFields_valid) {
    static $asOrder = array(
      'A' => 'ASC',
      'D' => 'DESC',
    );

    // Entity resources
    $oClassMetadata = $this->getClassMetadata();

    // Build ORDER BY fragments
    $asFields_entity = $oClassMetadata->getFieldNames();
    $asFields_sql = array();
    foreach ($aasFields_order as $asField_order) {
      $sField = $asField_order[0];

      // Valid field ?
      if ($sField[0] == '*') { // "SYSTEM" criteria
        $sField = substr($sField, 1);
        if (!in_array($sField, $asFields_entity)) continue;
      } elseif (!in_array($sField, $asFields_valid)) continue;

      // SQL
      $asFields_sql[] = sprintf('%s %s', $oClassMetadata->getColumnName($sField), $asOrder[$asField_order[1]] );
    }
    return $asFields_sql;
  }

  /** Translates (global) search (filter) criteria to SQL (ORDER BY)
   * @param string $sCriteria Search criteria
   * @return array|string Fields filtering SQL fragments
   */
  protected function sqlSearch($sCriteria) {
    // Entity resources
    $oEntityManager = $this->getEntityManager();
    $oDbConnection = $oEntityManager->getConnection();
    $oClassMetadata = $this->getClassMetadata();

    // Build WHERE fragments
    $asFields_searchable = $this->oProperties->getFieldsSearch();
    $asFields_sql = array();
    try {
      $aoCriteriaNodes = DataJukebox\Expression\Expression::parse($sCriteria, new DataJukebox\Filter\Filter());
      foreach ($asFields_searchable as $sField) {
        $asFields_sub = array();
        foreach ($aoCriteriaNodes as $oCriteriaNode) {
          try {
            $asFields_sub[] = sprintf(
              '(%s)',
              $oCriteriaNode->evaluate(
                array(
                  '__CONNECTION' => $oDbConnection,
                  '__COLUMN' => $oClassMetadata->getColumnName($sField),
                  '__TYPE' => $oClassMetadata->getTypeOfField($sField),
                )
              )
            );
          } catch(\Exception $e) {}
        }
        $asFields_sql[] = implode(' AND ', $asFields_sub);
      }
    } catch(\Exception $e) {
      if ($this->bDebug) throw $e; // DEBUG
      $asFields_sql = array('FALSE');
    }
    return $asFields_sql;
  }

  /** Translates field-spectific filter criteria to SQL (ORDER BY)
   * @param array|string $asCriteria Array associating field name to corresponding filter criteria
   * @param array|string $asFields_valid Valid fields (names)
   * @param array|string $asFields_display Displayed fields (names)
   * @return array|string Fields filtering SQL fragments
   */
  protected function sqlFilter($asCriteria, array $asFields_valid, $asFields_display=null) {
    // Entity resources
    $oEntityManager = $this->getEntityManager();
    $oDbConnection = $oEntityManager->getConnection();
    $oClassMetadata = $this->getClassMetadata();

    // Build WHERE fragments
    $asFields_entity = $oClassMetadata->getFieldNames();
    $asFields_filterable = array_intersect($this->oProperties->getFieldsFilter(), $asFields_valid);
    $asFields_sql = array();
    $oFilter = new DataJukebox\Filter\Filter();
    foreach ($asCriteria as $sField => $sCriteria) {
      // Valid field ?
      if ($sField[0] == '*') { // "SYSTEM" criteria
        $sField = substr($sField, 1);
        if (!in_array($sField, $asFields_entity)) continue;
      } elseif (!in_array($sField, $asFields_filterable)) continue;
      elseif (is_array($asFields_display) and !in_array($sField, $asFields_display)) continue;

      // SQL
      try {
        $aoCriteriaNodes = DataJukebox\Expression\Expression::parse($sCriteria, $oFilter);
        $asFields_sub = array();
        foreach ($aoCriteriaNodes as $oCriteriaNode) {
          $asFields_sub[] = sprintf(
            '(%s)',
            $oCriteriaNode->evaluate(
              array(
                '__CONNECTION' => $oDbConnection,
                '__COLUMN' => $oClassMetadata->getColumnName($sField),
                '__TYPE' => $oClassMetadata->getTypeOfField($sField),
              )
            )
          );
        }
        $asFields_sql[] = implode(' AND ', $asFields_sub);
      } catch(\Exception $e) {
        if ($this->bDebug) throw $e; // DEBUG
        $asFields_sql = array('FALSE');
        break;
      }
    }

    return $asFields_sql;
  }

  /** Translates the given browser to SQL (WHERE)
   * @param BrowserInterface $oBrowser Data browser
   * @return string SQL 'WHERE' clause
   */
  protected function sqlWhere(DataJukebox\BrowserInterface $oBrowser)
  {
    // Fields to display/query
    $asFields_display = $oBrowser->getFields();
    $asFields_valid = $this->validFields($asFields_display);

    // Build WHERE statement
    $sSQL = null;
    // ... search (apply same criteria across searchable fields)
    $sCriteria = trim($oBrowser->getSearch());
    $asSQL = array(); if ($sCriteria) $asSQL = $this->sqlSearch($sCriteria);
    $sSQL_search = implode(' OR ', $asSQL);

    // ... filter (apply specific criteria to filterable fields)
    $asCriteria = $oBrowser->getFieldsFilter();
    $asSQL = array(); if ($asCriteria) $asSQL = $this->sqlFilter($asCriteria, $asFields_valid, $asFields_display);
    $sSQL_filter = implode(' AND ', $asSQL);

    // ... mix the two
    $sSQL = $sSQL_search;
    if ($sSQL_filter and $sSQL) $sSQL = sprintf('( %s ) AND ', $sSQL);
    $sSQL .= $sSQL_filter;

    return $sSQL;
  }

  /** Query the database for data quantity (COUNT)
   * @param $sSQL_where SQL 'WHERE' clause
   * @return integer Data quantity
   */
  protected function queryCount($sSQL_where=null)
  {
    // Entity resources
    $oEntityManager = $this->getEntityManager();
    $oClassMetadata = $this->getClassMetadata();

    // SELECT COUNT statement
    $sSQL = sprintf(
      'SELECT COUNT(*) AS __count FROM %s',
      $oClassMetadata->getTableName()
    );

    // WHERE statement
    if ($sSQL_where) $sSQL = sprintf('%s WHERE %s', $sSQL, $sSQL_where);

    // Query the database for data count
    if ($this->bDebug) var_dump($sSQL); // DEBUG
    $oResultSetMapping = new ORM\Query\ResultSetMapping();
    $oResultSetMapping->addScalarResult('__count','__count');
    $oQuery = $oEntityManager->createNativeQuery($sSQL, $oResultSetMapping);
    $iCount = $oQuery->getSingleScalarResult();

    return $iCount;
  }

  /** Adds the internal primary key field (_PK) to the query results
   * @param array|array|mixed Data row/fields
   */
  protected function mergePrimaryKey(array &$aaResults) {
    array_walk(
      $aaResults,
      function(&$v, $k, $pk) {
        $v['_PK'] = implode(':', array_intersect_key($v, array_fill_keys($pk, null)));
      },
      $this->getClassMetadata()->getIdentifierFieldNames()
    );
  }

}
