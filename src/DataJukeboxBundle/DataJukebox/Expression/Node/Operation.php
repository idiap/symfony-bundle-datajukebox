<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Expression\Node\Operation.php

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

/** Operation
 *
 * @package    ExpressionLanguage
 * @subpackage Node
 */
class Operation
  extends Expression\Node
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Left-hand operand
   * @var Node
   */
  protected $oLeft;

  /** Right-hand operand
   * @var Node
   */
  protected $oRight;

  /** Operator
   * @var Operator
   */
  protected $oOperator;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  public function __construct(Expression\Node $oLeft, Expression\Node $oRight, Expression\Operator $oOperator)
  {
    $this->oLeft = $oLeft;
    $this->oRight = $oRight;
    $this->oOperator = $oOperator;
  }


  /*
   * METHODS
   ********************************************************************************/

  /** Returns this operation left-hand node (operand)
   * @return Expression\Node Left-hand node (operand)
   */
  public function left()
  {
    return $this->oLeft;
  }

  /** Returns this operation right-hand node (operand)
   * @return Expression\Node Right-hand node (operand)
   */
  public function right()
  {
    return $this->oRight;
  }

  /** Returns this operation operator
   * @return Expression\Operator Operator
   */
  public function operator()
  {
    return $this->oOperator;
  }


  /*
   * METHODS: Expression\Node
   ********************************************************************************/

  public function toString($bCompact=false)
  {
    return $this->oOperator->toString($this->oLeft, $this->oRight, $bCompact);
  }

  public function evaluate(array $amParameters)
  {
    return $this->oOperator->evaluate($this->oLeft, $this->oRight, $amParameters);
  }

}
