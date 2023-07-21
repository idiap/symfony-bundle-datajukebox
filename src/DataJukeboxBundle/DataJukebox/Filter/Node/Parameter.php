<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Filter\Node\Parameter.php

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

/** Parameter
 *
 * @package    FilterLanguage
 * @subpackage Node
 */
class Parameter
  extends Expression\Node\Parameter
{

  /*
   * METHODS: Expression\Parameter
   ********************************************************************************/

  public function evaluate(array $amParameters)
  {
    if (!array_key_exists('__CONNECTION', $amParameters)) {
      throw new Filter\Exception('Missing database connection');
    }
    if (!array_key_exists('__COLUMN', $amParameters)) {
      throw new Filter\Exception('Missing column (name)');
    }
    if (!array_key_exists('__TYPE', $amParameters)) {
      throw new Filter\Exception('Missing column type');
    }
    $oConnection = $amParameters['__CONNECTION'];
    if (!$oConnection instanceof \Doctrine\DBAL\Connection) {
      throw new Filter\Exception('Database connection must be a \Doctrine\DBAL\Connection object');
    }
    $sColumn = $amParameters['__COLUMN'];
    $sType = $amParameters['__TYPE'];

    switch ($sType) {
    case 'string':
    case 'text':
      return sprintf('LOWER(%s)', $sColumn);
      break;

    case 'date':
      switch($oConnection->getParams()['driver']) {

      case 'pdo_mysql':
      case 'drizzle_pdo_mysql':
      case 'mysqli':
      case 'pdo_sqlsrv':
      case 'sqlsrv':
        return sprintf('DATE_FORMAT(%s,\'%%Y-%%m-%%d\')', $sColumn);
        break;

      case 'pdo_pgsql':
      case 'pdo_oci':
      case 'oci8':
        return sprintf('TO_CHAR(%s,\'YYYY-MM-DD\')', $sColumn);
        break;

      default:
        return 'NULL';

      }
      break;

    case 'datetime':
    case 'datetimetz':
      switch($oConnection->getParams()['driver']) {

      case 'pdo_mysql':
      case 'drizzle_pdo_mysql':
      case 'mysqli':
      case 'pdo_sqlsrv':
      case 'sqlsrv':
        return sprintf('DATE_FORMAT(%s,\'%%Y-%%m-%%d %%H:%%i:%%s\')', $sColumn);
        break;

      case 'pdo_pgsql':
      case 'pdo_oci':
      case 'oci8':
        return sprintf('TO_CHAR(%s,\'YYYY-MM-DD HH24:MI:SS\')', $sColumn);
        break;

      default:
        return 'NULL';

      }
      break;

    case 'time':
      switch($oConnection->getParams()['driver']) {

      case 'pdo_mysql':
      case 'drizzle_pdo_mysql':
      case 'mysqli':
      case 'pdo_sqlsrv':
      case 'sqlsrv':
        return sprintf('DATE_FORMAT(%s,\'%%H:%%i:%%s\')', $sColumn);
        break;

      case 'pdo_pgsql':
      case 'pdo_oci':
      case 'oci8':
        return sprintf('TO_CHAR(%s,\'HH24:MI:SS\')', $sColumn);
        break;

      default:
        return 'NULL';

      }
      break;

    default:
      return $sColumn;
    }
  }

}
