<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Filter\Node\Value.php

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
 * @package    FilterLanguage
 * @subpackage Node
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Filter\Node;

use DataJukeboxBundle\DataJukebox\Expression as Expression;
use DataJukeboxBundle\DataJukebox\Filter as Filter;

/** Value value
 *
 * @package    FilterLanguage
 * @subpackage Node
 */
class Value
  extends Expression\Node\Value
{

  /*
   * METHODS: Expression\Value
   ********************************************************************************/

  public function evaluate(array $amParameters)
  {
    if (!array_key_exists('__CONNECTION', $amParameters)) {
      throw new Filter\Exception('Missing database connection');
    }
    if (!array_key_exists('__TYPE', $amParameters)) {
      throw new Filter\Exception('Missing column type');
    }
    $oConnection = $amParameters['__CONNECTION'];
    if (!$oConnection instanceof \Doctrine\DBAL\Connection) {
      throw new Filter\Exception('Database connection must be a \Doctrine\DBAL\Connection object');
    }
    $sType = $amParameters['__TYPE'];

    if (is_null($this->mValue)) return 'NULL';

    switch ($sType) {
    case 'string':
    case 'text':
      return sprintf('LOWER(%s)', $oConnection->quote((string)$this->mValue, $sType));
      break;

    case 'integer':
    case 'smallint':
      return $oConnection->quote((integer)$this->mValue, $sType);
      break;

    case 'bigint':
      return $oConnection->quote((string)$this->mValue, $sType); //bigint is reprensented as string in PHP
      break;

    case 'decimal':
    case 'float':
      return $oConnection->quote((float)$this->mValue, $sType);
      break;

    case 'boolean':
      $bValue = filter_var($this->mValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
      if (is_null($bValue)) return 'NULL';
      return $bValue ? 'TRUE' : 'FALSE';
      break;

    case 'date':
    case 'datetime':
    case 'datetimetz':
    case 'time':
      return $oConnection->quote((string)$this->mValue, 'string');
      break;

    default:
      return 'NULL';
    }
  }

}
