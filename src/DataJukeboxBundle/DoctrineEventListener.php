<?php // -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Data\DoctrineEventListener.php

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
 * @subpackage SymfonyIntegration
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

namespace DataJukeboxBundle;

use Symfony\Component\DependencyInjection as DependencyInjection;
use Doctrine\ORM\Event\OnFlushEventArgs;

/** Doctrine event listener
 *
 * <P>This event listener will catch all classes that inherit the <SAMP>Status</SAMP>
 * class and update the insert/update author according to Symfony logged-in user.</P>
 * @see \DataJukeboxBundle\Entity\Status
 *
 * @package    DataJukeboxBundle
 * @subpackage SymfonyIntegration
 */
class DoctrineEventListener
{

  /*
   * PROPERTIES
   ********************************************************************************/

  /** Symfony security token storage
   * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
   */
  protected $oSecurityTokenStorage;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  /** Constructor
   * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface Symfony security token storage
   */
  public function __construct(\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $oSecurityTokenStorage)
  {
    $this->oSecurityTokenStorage = $oSecurityTokenStorage;
  }


  /*
   * METHODS: Doctrine
   ********************************************************************************/

  public function onFlush(OnFlushEventArgs $oEventArgs)
  {
    $oEntityManager = $oEventArgs->getEntityManager();
    $oUnitOfWork = $oEntityManager->getUnitOfWork();
    $oToken = $this->oSecurityTokenStorage->getToken();
    $sUser = $oToken ? $oToken->getUser() : null;

    foreach ($oUnitOfWork->getScheduledEntityInsertions() as $oEntity) {
      if ($oEntity instanceof \DataJukeboxBundle\Entity\StatusEntity) {
        $oEntity->setInsertedBy($sUser);
        $oUnitOfWork->recomputeSingleEntityChangeSet($oEntityManager->getClassMetadata(get_class($oEntity)), $oEntity);
      }
    }

    foreach ($oUnitOfWork->getScheduledEntityUpdates() as $oEntity) {
      if ($oEntity instanceof \DataJukeboxBundle\Entity\StatusEntity) {
        $oEntity->setUpdatedBy($sUser);
        $oUnitOfWork->recomputeSingleEntityChangeSet($oEntityManager->getClassMetadata(get_class($oEntity)), $oEntity);
      }
    }

  }

}
