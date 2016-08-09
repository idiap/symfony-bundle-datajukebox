<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\DataJukebox\Properties\StatusProperties.php

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
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle\DataJukebox\Properties;
use DataJukeboxBundle\DataJukebox as DataJukebox;

/** Generic data status properties implementation
 *
 * <P>This abstract class provides the default properties for the data status entity.</P>
 * @see \DataJukeboxBundle\Entity\StatusEntity
 *
 * @package    DataJukeboxBundle
 */
abstract class StatusProperties
  extends DataJukebox\Properties
{

  /*
   * METHODS
   ********************************************************************************/

  public function myFields()
  {
    return array(
      'InsertedAt',
      'InsertedBy',
      'UpdatedAt',
      'UpdatedBy',
      'Disabled',
    );
  }


  /*
   * METHODS: Properties
   ********************************************************************************/

  public function getLabels()
  {
    return array_merge(
      parent::getLabels(),
      $this->getMeta('label', 'Status')
    );
  }

  public function getTooltips()
  {
    return array_merge(
      parent::getTooltips(),
      $this->getMeta('tooltip', 'Status')
    );
  }

  public function getFields()
  {
    return $this->myFields();
  }

  public function getFieldsHidden()
  {
    switch ($this->getAction()) {
    case 'insert':
    case 'update':
      return array_merge(
        array('InsertedAt', 'InsertedBy', 'UpdateAllowed', 'UpdatedAt', 'UpdatedBy', 'DisableAllowed', 'DeleteAllowed'),
        parent::getFieldsHidden()
      );
    }
    return array_merge(
      array('UpdateAllowed', 'DisableAllowed', 'DeleteAllowed'),
      parent::getFieldsHidden()
    );
  }

  public function getFieldsRequired()
  {
    return array_merge(
      parent::getFieldsRequired(),
      array('UpdateAllowed', 'DisableAllowed', 'DeleteAllowed')
    );
  }

  public function getFieldsReadonly()
  {
    return array_merge(
      parent::getFieldsReadonly(),
      array('InsertedAt', 'InsertedBy', 'UpdateAllowed', 'UpdatedAt', 'UpdatedBy', 'DisableAllowed', 'DeleteAllowed')
    );
  }

  public function getFieldsOrder()
  {
    return array('InsertedAt', 'UpdatedAt');
  }

  public function getFieldsFilter()
  {
    return array('InsertedAt', 'InsertedBy', 'UpdatedAt', 'UpdatedBy', 'Disabled');
  }

}
