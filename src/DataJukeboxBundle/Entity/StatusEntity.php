<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Entity\StatusEntity.php

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

namespace DataJukeboxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** Generic data status
 *
 * <P>This abstract class provisions the fields required to provide basic permissions
 * and accountability for INSERT, UPDATE and DELETE operations.</P>
 * <P>It ought to go along the following table columns:</P>
 * <UL>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN ts_InsertedAt timestamp NOT NULL; -- set by trigger</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN id_InsertedBy varchar(50) NULL;</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN b_UpdateAllowed boolean NOT NULL DEFAULT( true ); -- permission</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN ts_UpdatedAt timestamp NOT NULL; -- set by trigger</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN id_UpdatedBy varchar(50) NULL;</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN b_DisableAllowed boolean NOT NULL DEFAULT( true ); -- permission</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN b_Disabled boolean NOT NULL DEFAULT( false ); -- status</SAMP></LI>
 * <LI><SAMP>ALTER TABLE ... ADD COLUMN b_DeleteAllowed boolean NOT NULL DEFAULT( true ); -- permission</SAMP></LI>
 * </UL>
 *
 * @ORM\MappedSuperclass
 *
 * @package    DataJukeboxBundle
 */
abstract class StatusEntity
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Insertion timestamp
   * @var \DateTime
   * @ORM\Column(name="ts_InsertedAt", type="datetime")
   */
  protected $InsertedAt;

  /** Insertion principal
   * @var string
   * @ORM\Column(name="id_InsertedBy", type="string", length=50)
   */
  protected $InsertedBy;

  /** Update permission flag
   * @var boolean
   * @ORM\Column(name="b_UpdateAllowed", type="boolean")
   */
  protected $UpdateAllowed = true;

  /** Last update timestamp
   * @var \DateTime
   * @ORM\Column(name="ts_UpdatedAt", type="datetime")
   */
  protected $UpdatedAt;

  /** Last update principal
   * @var string
   * @ORM\Column(name="id_UpdatedBy", type="string", length=50)
   */
  protected $UpdatedBy;

  /** Disablement permission flag
   * @var boolean
   * @ORM\Column(name="b_DisableAllowed", type="boolean")
   */
  protected $DisableAllowed = true;

  /** Disablement status flag
   * @var boolean
   * @ORM\Column(name="b_Disabled", type="boolean")
   */
  protected $Disabled = false;

  /** Deletion permission flag
   * @var boolean
   * @ORM\Column(name="b_DeleteAllowed", type="boolean")
   */
  protected $DeleteAllowed = true;


  /*
   * METHODS
   ********************************************************************************/

  /*
   * GETTERS
   */

  /** Get insertion timestamp
   * @return \DateTime
   */
  public function getInsertedAt()
  {
    return $this->InsertedAt;
  }

  /** Get insertion principal
   * @return string
   */
  public function getInsertedBy()
  {
    return $this->InsertedBy;
  }

  /** Get allow update flag
   * @return boolean
   */
  public function isUpdateAllowed()
  {
    return $this->UpdateAllowed;
  }

  /** Get last update timestamp
   * @return \DateTime
   */
  public function getUpdatedAt()
  {
    return $this->UpdatedAt;
  }

  /** Get last update principal
   * @return string
   */
  public function getUpdatedBy()
  {
    return $this->UpdatedBy;
  }

  /** Get disablement permission flag
   * @return boolean
   */
  public function isDisableAllowed()
  {
    return $this->DisableAllowed;
  }

  /** Get disablement status flag
   * @return boolean
   */
  public function isDisabled()
  {
    return $this->Disabled;
  }

  /** Get allow deletion flag
   *
   * @return boolean
   */
  public function isDeleteAllowed()
  {
    return $this->DeleteAllowed;
  }


  /*
   * SETTERS
   */

  /** Set insertion principal
   * @param string $InsertedBy Insertion principal
   * @return this
   */
  public function setInsertedBy($InsertedBy)
  {
    $this->InsertedBy = $InsertedBy;
    return $this;
  }

  /** Set update principal
   * @param string $UpdatedBy Update principal
   * @return this
   */
  public function setUpdatedBy($UpdatedBy)
  {
    $this->UpdatedBy = $UpdatedBy;
    return $this;
  }

  /** Set disablement status flag
   * @param boolean $Disabled Disablement status flag
   * @return this
   */
  public function setDisabled($Disabled)
  {
    $this->Disabled = $Disabled;
    return $this;
  }

}
