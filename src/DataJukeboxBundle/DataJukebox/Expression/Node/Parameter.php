<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Expression\Node\Parameter.php

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
 * @package    ExpressionLanguage
 * @subpackage Node
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Expression\Node;

use DataJukeboxBundle\DataJukebox\Expression as Expression;

/** Parameter
 *
 * @package    ExpressionLanguage
 * @subpackage Node
 */
class Parameter
  extends Expression\Node
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Parameter key (name)
   * @var mixed
   */
  protected $mKey;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  public function __construct($mKey)
  {
    if (!is_scalar($mKey)) {
      throw new Expression\Exception('Parameter key must be scalar');
    }
    $this->mKey = $mKey;
  }


  /*
   * METHODS
   ********************************************************************************/

  /** Returns the parameter key (name)
   * @return mixed Parameter key (name)
   */
  public function key()
  {
    return $this->mKey;
  }


  /*
   * METHODS: Expression\Node
   ********************************************************************************/

  public function toString($bCompact=false)
  {
    return sprintf('${%s}', $this->mKey);
  }

  public function evaluate(array $amParameters)
  {
    if (!array_key_exists($this->mKey, $amParameters) ) {
      throw new Expression\Exception(sprintf('No value for parameter (%s)', $this->mKey));
    }
    $mValue = $amParameters[$this->mKey];
    if (!is_scalar($mValue) and !is_array($mValue)) {
      throw new Expression\Exception(sprintf('Parameter value must be either scalar or an array (%s)', $this->mKey));
    }
    return $mValue;
  }

}
