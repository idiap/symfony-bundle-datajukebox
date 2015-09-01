<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonProportional.php

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
 * @subpackage Operator
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Filter\Operator;

use DataJukeboxBundle\DataJukebox\Expression as Expression;

/** Comparison: proportional (~=, ~)
 *
 * @package    FilterLanguage
 * @subpackage Operator
 */
class ComparisonProportional
  extends Expression\Operator\ComparisonProportional
{

  /*
   * METHODS: Expression\ComparisonProportional
   ********************************************************************************/

  public function evaluate(Expression\Node $oLeft, Expression\Node $oRight, array $amParameters)
  {
    $mLeft = $oLeft->evaluate($amParameters);
    $mRight = $oRight->evaluate($amParameters);
    if (!is_scalar($mLeft) or !is_scalar($mRight)) {
      return 'FALSE'; // let's fail gracefully
    }
    $mRight = str_replace(array('%', '_'), array('\%', '\_'), $mRight);
    $mRight = str_replace(array('*', '?'), array('%', '_'), $mRight);
    return sprintf('%s LIKE %s', $mLeft, $mRight);
  }

}
