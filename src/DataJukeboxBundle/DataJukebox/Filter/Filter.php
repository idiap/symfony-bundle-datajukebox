<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Filter\Filter.php

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
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Filter;

use DataJukeboxBundle\DataJukebox\Expression as Expression;


/** Data filter parser implementation
 *
 * @package    FilterLanguage
 */
class Filter
  implements Expression\ParserInterface
{

  /*
   * CONSTANTS
   ********************************************************************************/

  const PRECEDENCE_LEFT = 0;
  const PRECEDENCE_RIGHT = 1;

  const OPERATOR_UNARY = 1;
  const OPERATOR_ASSOCIATIVE = 2;
  const OPERATOR_OPEN = 4;
  const OPERATOR_CLOSE = 8;
  const OPERATOR_COMPARISON = 16;

  const OPERATORS_UNARY = array(
    // symbol => type, left precedence, handler, right precedence / close match, context define, contexts allowed
    '!' => array( 1, 17, '\DataJukeboxBundle\DataJukebox\Filter\Operator\LogicalNot', null, 'logical', array('default', 'parenthesis')),
    'not' => array( 1, 17, '\DataJukeboxBundle\DataJukebox\Filter\Operator\LogicalNot', null, 'logical', array('default', 'parenthesis')),
    '=' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '==' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '!=' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonNotEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '<>' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonNotEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '<' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonSmaller', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '<=' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonSmallerOrEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '>' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonBigger', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '>=' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonBiggerOrEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '~' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonProportional', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '~=' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonProportional', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '><' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonIncluded', 50, 'comparison_in', array('default', 'logical', 'parenthesis')),
    'in' => array( 17, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonIncluded', 50, 'comparison_in', array('default', 'logical', 'parenthesis')),
    '\'' => array( 5, 1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\QuoteSingle', '\'', 'quote', null),
    '"' => array( 5, 1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\QuoteDouble', '"', 'quote', null),
    '(' => array( 5, 1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\GroupParenthesis', ')', 'parenthesis', array('default', 'logical', 'parenthesis')),
    '[' => array( 5, 90, '\DataJukeboxBundle\DataJukebox\Filter\Operator\GroupBracket', ']', 'bracket', array('comparison_in')),
  );

  const OPERATORS_ASSOCIATIVE = array(
    // symbol => type, left precedence, handler, right precedence / close match, context define, contexts allowed
    '&&' => array( 2, 6, '\DataJukeboxBundle\DataJukebox\Filter\Operator\LogicalAnd', null, 'logical', array('default', 'parenthesis')),
    'and' => array( 2, 6, '\DataJukeboxBundle\DataJukebox\Filter\Operator\LogicalAnd', null, 'logical', array('default', 'parenthesis')),
    '||' => array( 2, 4, '\DataJukeboxBundle\DataJukebox\Filter\Operator\LogicalOr', null, 'logical', array('default', 'parenthesis')),
    'or' => array( 2, 4, '\DataJukeboxBundle\DataJukebox\Filter\Operator\LogicalOr', null, 'logical', array('default', 'parenthesis')),
    '=' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '==' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '!' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonNotEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '!=' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonNotEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    'not' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonNotEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '<>' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonNotEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '<' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonSmaller', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '<=' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonSmallerOrEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '>' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonBigger', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '>=' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonBiggerOrEqual', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '~' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonProportional', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '~=' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonProportional', 50, 'comparison', array('default', 'logical', 'parenthesis')),
    '><' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonIncluded', 50, 'comparison_in', array('default', 'logical', 'parenthesis')),
    'in' => array( 18, -1, '\DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonIncluded', 50, 'comparison_in', array('default', 'logical', 'parenthesis')),
    '[' => array( 6, 90, '\DataJukeboxBundle\DataJukebox\Filter\Operator\GroupBracket', ']', 'bracket', array('comparison_in')),
    ',' => array( 2, 95, '\DataJukeboxBundle\DataJukebox\Filter\Operator\CollectionSeparator', null, null, array('bracket') ),
    '\'' => array( 10, -1, null, '\'', null, null ),
    '"' => array( 10, -1, null, '"', null, null ),
    '(' => array( 10, -1, null, ')', null, null ),
    ')' => array( 10, -1, null, ')', null, null ),
    ']' => array( 10, -1, null, ']', null, null ),
    '__END' => array( 10, -1, null, null, null, null ),
  );


  /*
   * PROPERTIES
   ********************************************************************************/

  /** Expression tokens
   * @var array|array
   */
  private $aaTokens;


  /*
   * METHODS
   ********************************************************************************/

  /** Returns the operator (description) for the given token and operator type
   *
   * <P>This method looks for the actual operator matching the given token and operator type
   * and returns an array associating:</P>
   * <UL>
   * <LI><B>type</B>: the actual operator type</LI>
   * <LI><B>precedence_left</B>: the operator left precendence value (priority)</LI>
   * <LI><B>precedence_right</B>: the operator right precendence value (priority)</LI>
   * <LI><B>handler</B>: the corresponding operator class (Operator)</LI>
   * <LI><B>match</B>: the matching (closing) symbol for open/close operators</LI>
   * <LI><B>context</B>: the context defined by this operator</LI>
   * <LI><B>contexts_allowed</B>: the contexts (array) in which this operator is allowed</LI>
   * </UL>
   *
   * @param array $aToken Token array
   * @param integer $iOperatorType Operator (primary) type
   * @return array Operator (description) array
   */
  protected function getOperator($aToken,$iOperatorType)
  {
    $sOperator = $aToken['value'];

    if ($iOperatorType & self::OPERATOR_UNARY) {
      if (!array_key_exists($sOperator, self::OPERATORS_UNARY)) {
        throw new Exception(sprintf('[%s] Invalid operator; expecting unary operator, got "%s"', $aToken['position'], $sOperator));
      }
      $aOperator = self::OPERATORS_UNARY[$sOperator];
    } elseif ($iOperatorType & self::OPERATOR_ASSOCIATIVE) {
      if (!array_key_exists($sOperator, self::OPERATORS_ASSOCIATIVE)) {
        throw new Exception(sprintf('[%s] Invalid operator; expecting associative operator, got "%s"', $aToken['position'], $sOperator));
      }
      $aOperator = self::OPERATORS_ASSOCIATIVE[$sOperator];
    }

    return array(
      'type' => $aOperator[0],
      'precedence_left' => $aOperator[1],
      'precedence_right' => !($aOperator[0] & (self::OPERATOR_OPEN|self::OPERATOR_CLOSE)) && $aOperator[3] ? $aOperator[3] : $aOperator[1],
      'handler' => $aOperator[2],
      'match' => $aOperator[0] & (self::OPERATOR_OPEN|self::OPERATOR_CLOSE) ? $aOperator[3] : null,
      'context' => $aOperator[4],
      'contexts_allowed' => $aOperator[5],
    );
  }

  /** Returns the precedence for the given token (operator)
   *
   * <P>This method looks for the actual operator matching the given operator type
   * and returns the requested left-hand or right-hand precedence.</P>
   *
   * @param integer $iToken Token index
   * @param integer $iPrecedenceSide Precedence side (left or right)
   * @param integer $iOperatorType Operator (primary) type
   * @return integer Operator precedence
   */
  protected function getPrecedence($iToken, $iPrecedenceSide, $iOperatorType)
  {
    $iPrecedence = 0;
    $aToken = $this->aaTokens[$iToken];
    if ($aToken['type'] == Expression\Expression::TOKEN_OPERATOR) {
      $aOperator = $this->getOperator($aToken, $iOperatorType);
      switch ($iPrecedenceSide) {
      case self::PRECEDENCE_LEFT: $iPrecedence = $aOperator['precedence_left']; break;
      case self::PRECEDENCE_RIGHT: $iPrecedence = $aOperator['precedence_right']; break;
      }
    }
    return $iPrecedence;
  }

  /** Returns the node for the given token and expected operator type (along the current context)
   * @param integer $iToken Token index
   * @param integer $iOperatorType Operator (primary) type
   * @param string $sContext Current context
   * @param Node $oLeft Left-hand node (operand), if applicable
   * @return Node Expression node
   */
  protected function getNode(&$iToken, $iOperatorType, $sContext, $oLeft=null)
  {
    $aToken = $this->aaTokens[$iToken];
    switch ($aToken['type']) {

    case Expression\Expression::TOKEN_VALUE:
      if ($iOperatorType & self::OPERATOR_UNARY) {
        $iToken++;
        $oNode = new Node\Value($aToken['value']);
        if (in_array($sContext, array('default', 'parenthesis', 'logical'))) {
          $oNode = new Node\Operation(new Node\Parameter('__DATA'), $oNode, new \DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonEqual());
        }
        return $oNode;
      } else {
        throw new Exception(sprintf('[%s] Unexpected operand: "%s" ', $aToken['position'], $aToken['value']));
      }
      break;

    case Expression\Expression::TOKEN_PARAMETER:
      throw new Exception(sprintf('[%s] Unexpected token (parameter): "%s" ', $aToken['position'], $aToken['value']));
      break;

    case Expression\Expression::TOKEN_OPERATOR:
      $aOperator = $this->getOperator($aToken, $iOperatorType);
      $sHandler = $aOperator['handler'];
      $asContexts = $aOperator['contexts_allowed'];
      if ($asContexts and !in_array($sContext, $asContexts)) {
        throw new Exception(sprintf('[%s] Invalid operator: "%s" not allowed in current context', $aToken['position'], $aToken['value']));
      }
      $iToken++;
      if ($aOperator['type'] & self::OPERATOR_COMPARISON) {
        $oLeft = new Node\Parameter('__DATA');
      } elseif ($aOperator['type'] & self::OPERATOR_UNARY) {
        $oLeft = new Node\Blank();
      }
      $oNode = new Node\Operation(
        $oLeft,
        $this->parseTokens(
          $iToken,
          $aOperator['precedence_right'],
          $aOperator['context'] ? $aOperator['context'] : $sContext
        ),
        new $sHandler()
      );
      if ($aOperator['type'] & self::OPERATOR_OPEN) {
        $sMatch_open = $aOperator['match'];
        $aToken_close = $this->aaTokens[$iToken]; $iToken++;
        if ($aToken_close['type'] != Expression\Expression::TOKEN_OPERATOR) {
          throw new Exception(sprintf('[%s] Unexpected operand: "%s"', $aToken_close['position'], $aToken_close['value']));
        }
        $aOperator_close = $this->getOperator($aToken, self::OPERATOR_ASSOCIATIVE);
        $sMatch_close = $aOperator_close['match'];
        if ($sMatch_open != $sMatch_close) {
          throw new Exception(sprintf('[%s] Missing operator; expecting "%s"', $aToken['position'], $sMatch_open));
        }
      }
      if ($aOperator['context'] == 'quote' and in_array($sContext, array('default', 'parenthesis', 'logical'))) {
        $oNode = new Node\Operation(new Node\Parameter('__DATA'), $oNode, new \DataJukeboxBundle\DataJukebox\Filter\Operator\ComparisonEqual());
      }
      return $oNode;
      break;
    }
  }

  /** Parse the given token and returns the corresponding nodes
   * @param integer $iToken Token index
   * @param integer $iPrecedence Current precedence
   * @param string $sContext Current context
   * @return Node Expression node
   */
  protected function parseTokens(&$iToken, $iPrecedence=0, $sContext='default')
  {
    $oLeft = $this->getNode($iToken, self::OPERATOR_UNARY, $sContext);
    while( $this->getPrecedence($iToken, self::PRECEDENCE_LEFT, self::OPERATOR_ASSOCIATIVE) > $iPrecedence ) {
      $oLeft = $this->getNode($iToken, self::OPERATOR_ASSOCIATIVE, $sContext, $oLeft);
    }
    return $oLeft;
  }


  /*
   * METHODS: ParserInterface
   ********************************************************************************/

  public function symbols()
  {
    return array(
      'keywords' => array(
        'and', 'in', 'not', 'or',
      ),
      'singletons' => array(
        '\\', '\'', '"',
        '=', '<', '>', '~', '!',
        '(', ')', '[', ']',
        ',',
      ),
      'doublets' => array(
        '&&', '||',
        '<<', '<=', '<>', '>>', '>=', '><', '~=', '!=',
      ),
      'triplets' => array(),
    );
  }

  public function parse(array $aaTokens)
  {
    // Save tokens
    $this->aaTokens = $aaTokens;
    $iTokens = count($this->aaTokens);
    $this->aaTokens[] = Expression\Expression::makeToken(Expression\Expression::TOKEN_OPERATOR, '__END', $iTokens);

    // Parse
    $iToken = 0;
    $aoNodes = array();
    while ($iToken<$iTokens) {
      $aoNodes[] = $this->parseTokens($iToken);
    }
    return $aoNodes;
  }

}
