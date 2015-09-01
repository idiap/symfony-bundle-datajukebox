<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Expression\Node.php

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
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Expression;

/** Node (place-holder)
 *
 * @package    ExpressionLanguage
 */
abstract class Node
{

  /*
   * METHODS
   ********************************************************************************/

  /** Returns a string representation of the node content
   * @param boolean $bCompact Prefer a compact string representation
   * @return string String representation of the node content
   */
  abstract public function toString($bCompact=false);

  /** Returns the evaluated value of the node content
   * @param array|mixed $amParameters Array associating parameters name and their value
   * @return mixed Evaluated value of the node content
   */
  abstract public function evaluate(array $amParameters);

  /*
   * METHODS: PHP magic
   ********************************************************************************/

  public function __toString()
  {
    return $this->toString(true);
  }

}
