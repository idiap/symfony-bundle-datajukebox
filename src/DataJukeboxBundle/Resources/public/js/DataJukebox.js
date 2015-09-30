// -*- mode:javascript; js-indent-level:2; -*- ex: set tabstop=2 expandtab:
// DataJukeboxBundle\Resources\public\js\DataJukebox.js

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
 * @subpackage Javascript
 * @copyright  2015 Idiap Research Institute <http://www.idiap.ch>
 * @author     Cedric Dufour <http://cedric.dufour.name>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    %{VERSION}
 * @link       https://github.com/idiap/DataJukeboxBundle
 */

var DataJukebox_repository = [];

function DataJukebox_getRepository(sID)
{
  var i;

  // Look for a matching repository
  for (i=0; i<DataJukebox_repository.length; i++) {
    if (DataJukebox_repository[i].sID == sID) {
      return DataJukebox_repository[i];
    }
  }

  // None were found; create a new one
  DataJukebox_repository[i] = {
    sID: sID,                                // data block ID
    oTimer: null,                            // (double-)click timer
    oUrl: { sDelete:null, sSelect:null },    // data deletion/selection URLs
    oConfirm: { sDelete:null, sSelect:null } // data deletion/selection confirmation alerts
  };

  return DataJukebox_repository[i];
}

function DataJukebox_getForm(sID)
{
  return document.forms['DataJukebox_'+sID+'_form'];
}

function DataJukebox_init(sID, sUrlDelete, sUrlSelect, sConfirmDelete, sConfirmSelect)
{
  var oRepository = DataJukebox_getRepository(sID);

  oRepository.oUrl.sDelete = sUrlDelete;
  oRepository.oUrl.sSelect = sUrlSelect;
  oRepository.oConfirm.sDelete = sConfirmDelete;
  oRepository.oConfirm.sSelect = sConfirmSelect;
}

function DataJukebox_onSubmit(sID)
{
  var oForm = DataJukebox_getForm(sID);
  
  // If form is submitted "normally", we're dealing with a display preferences
  // (GET) request. Primary keys must thus not appear in the query string.
  aoInputs = oForm.elements['_pk[]'];
  if (typeof aoInputs != 'undefined') {
    oForm.elements['_pkToggle'].disabled = true;
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      aoInputs[i].disabled = true;
    }
  }

  
  // A field that corresponds to defaults has its value suffixed with a trailing
  // whitespace, which is trimmed-off as soon as the user modifies it.
  // Let's not clutter the (GET) request with such defaults.
  // ... displayed fields
  aoInputs = oForm.elements['_fd[]'];
  if (typeof aoInputs != 'undefined') {
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    bAnyEnabled = false;
    for (var i=1; i<aoInputs.length; i++) {
      if (aoInputs[i].value.length && aoInputs[i].value[aoInputs[i].value.length-1] == ' ') aoInputs[i].disabled = true;
      if (!aoInputs[i].disabled) bAnyEnabled = true;
    }
    if (bAnyEnabled) {
      aoInputs[0].disabled = false; // place-holder to trigger fields display processing on server-side
      for (var i=1; i<aoInputs.length; i++) {
        if (aoInputs[i].checked) {
          aoInputs[i].value = aoInputs[i].value.trim();
          aoInputs[i].disabled = false;
        }
      }
    }
  }
  // ... data limit (quantity of rows)
  oInput = oForm.elements['_lt'];
  if (typeof oInput != 'undefined') {
    if (oInput.value.length && oInput.value[oInput.value.length-1] == ' ') oInput.disabled = true;
  }

  // Hide empty fields from query
  for (var i=0; i<oForm.elements.length; i++) {
    if (!oForm.elements[i].value) {
      oForm.elements[i].disabled = true;
    }
  }

  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return false;

  return true;
}

function DataJukebox_submitOnEnter(sID,oEvent)
{
  if( oEvent.keyCode!=13 ) return true;

  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return true;

  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_pkToggle(sID)
{
  var oRepository = DataJukebox_getRepository(sID);

  /*
   * single-click: toggle selected items globally
   * double-click: toggle selected items individually
   */
  if (oRepository.oTimer === null) {
    oRepository.oTimer = setTimeout(function() { DataJukebox_pkToggleGlobal(sID); }, 350);
  } else {
    clearTimeout(oRepository.oTimer);
    DataJukebox_pkToggleIndividual(sID);
  }
}

function DataJukebox_pkToggleIndividual(sID)
{
  var oRepository = DataJukebox_getRepository(sID);
  var oForm = DataJukebox_getForm(sID);

  oRepository.oTimer = null;

  bChecked = false;
  aoInputs = oForm.elements['_pk[]'];
  if (typeof aoInputs != 'undefined') {
    oForm.elements['_pkToggle'].checked = !oForm.elements['_pkToggle'].checked;
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      bChecked |= ( aoInputs[i].checked = !aoInputs[i].checked );
    }
    oForm.elements['_pkToggle'].checked = bChecked;
  }
}

function DataJukebox_pkToggleGlobal(sID)
{
  var oRepository = DataJukebox_getRepository(sID);
  var oForm = DataJukebox_getForm(sID);

  oRepository.oTimer = null;

  aoInputs = oForm.elements['_pk[]'];
  if (typeof aoInputs != 'undefined') {
    bChecked = oForm.elements['_pkToggle'].checked;
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      aoInputs[i].checked = bChecked;
    }
  }
}

function DataJukebox_displaySubmit(sID)
{
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_displayReset(sID)
{
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  aoInputs = oForm.elements['_fd[]'];
  if (typeof aoInputs != 'undefined') {
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      aoInputs[i].disabled = true;
    }
  }
  aoInputs = oForm.elements['_or[]'];
  if (typeof aoInputs != 'undefined') {
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      aoInputs[i].disabled = true;
    }
  }
  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_order(sID, sField, sDirection)
{
  var oRepository = DataJukebox_getRepository(sID);

  /*
   * single-click: set new order field/direction
   * double-click: add/replace order field/direction
   */
  if (oRepository.oTimer === null) {
    oRepository.oTimer = setTimeout(function() { DataJukebox_orderSet(sID, sField, sDirection); }, 350);
  } else {
    clearTimeout(oRepository.oTimer);
    DataJukebox_orderAdd(sID, sField, sDirection);
  }
}

function DataJukebox_orderSet(sID, sField, sDirection)
{
  var oRepository = DataJukebox_getRepository(sID);
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  oRepository.oTimer = null;

  aoInputs = oForm.elements['_or[]'];
  if (typeof aoInputs != 'undefined') {
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      if (!i) {
        aoInputs[i].disabled = false;
        aoInputs[i].value = sField+'_'+sDirection;
      } else {
        aoInputs[i].disabled = true;
      }
    }
  }
  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_goto(sID, iOffset)
{
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  oInput = oForm.elements['_of'];
  if (typeof oInput != 'undefined') {
    oInput.disabled = false;
    oInput.value = iOffset;
  }
  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_orderAdd(sID, sField, sDirection)
{
  var oRepository = DataJukebox_getRepository(sID);
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  oRepository.oTimer = null;

  aoInputs = oForm.elements['_or[]'];
  if (typeof aoInputs != 'undefined') {
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      if (aoInputs[i].disabled) {
        aoInputs[i].disabled = false;
        aoInputs[i].value = sField+'_'+sDirection;
        break;
      } else if (aoInputs[i].value.substr(0, sField.length+1) == sField+'_') {
        aoInputs[i].value = sField+'_'+sDirection;
        break;
      }
    }
  }
  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_searchSubmit(sID)
{
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_searchReset(sID)
{
  var oForm = DataJukebox_getForm(sID);
  if (typeof oForm.checkValidity != 'undefined' && !oForm.checkValidity()) return;

  oInput = oForm.elements['_sh'];
  if (typeof oInput != 'undefined') {
    oInput.disabled = true;
  }
  aoInputs = oForm.elements;
  if (typeof aoInputs != 'undefined') {
    if (typeof aoInputs.length == 'undefined') aoInputs = [aoInputs];
    for (var i=0; i<aoInputs.length; i++) {
      if (aoInputs[i].id[0] != '_') aoInputs[i].disabled = true;
    }
  }
  oForm.onsubmit();
  oForm.submit();
}

function DataJukebox_confirmDelete(sID)
{
  var oRepository = DataJukebox_getRepository(sID);

  if (oRepository.oConfirm.sDelete) {
    return window.confirm(oRepository.oConfirm.sDelete);
  } else {
    return true;
  }
}

function DataJukebox_delete(sID)
{
  var oRepository = DataJukebox_getRepository(sID);
  var oForm = DataJukebox_getForm(sID);

  if (oRepository.oUrl.sDelete) {
    oForm.action = oRepository.oUrl.sDelete;
    oForm.method = 'POST';
    oForm.submit();
  }
}

function DataJukebox_confirmSelect(sID)
{
  var oRepository = DataJukebox_getRepository(sID);

  if (oRepository.oConfirm.sSelect) {
    return window.confirm(oRepository.oConfirm.sSelect);
  } else {
    return true;
  }
}

function DataJukebox_select(sID)
{
  var oRepository = DataJukebox_getRepository(sID);
  var oForm = DataJukebox_getForm(sID);

  if (oRepository.oUrl.sSelect) {
    oForm.action = oRepository.oUrl.sSelect;
    oForm.method = 'POST';
    oForm.submit();
  }
}

function DataJukebox_displayPopupOverlay(sURL)
{
  oContainer = document.getElementById('DataJukebox_popupContainer');
  oOverlay = document.getElementById('DataJukebox_popupOverlay');
  if (typeof oContainer == 'undefined' || typeof oOverlay == 'undefined') return;

  if (oContainer.style.display == 'block' && oOverlay.value == sURL) {
    oContainer.style.display = 'none';
  } else if (oContainer.style.display == 'none' && oOverlay.value == sURL) {
    oContainer.style.top = window.pageYOffset+'px';
    oContainer.style.display = 'block';
  } else {
    oOverlay.innerHTML = '<DIV ID="DataJukebox_popupSpinner"></DIV>';
    oOverlay.value = '';
    oContainer.style.top = window.pageYOffset+'px';
    oContainer.style.display = 'block';
    oXMLHttpRequest = new XMLHttpRequest();
    oXMLHttpRequest.onreadystatechange=function() {
      if (oXMLHttpRequest.readyState==4 && oXMLHttpRequest.status==200)
      {
        oOverlay.innerHTML = oXMLHttpRequest.responseText;
        oOverlay.value = sURL
      }
    }
    oXMLHttpRequest.open('GET', sURL, true);
    oXMLHttpRequest.send();
  }
}
