/**
 * \file menu-kbd.js
 * Zapatec DHTML Menu Widget.
 * Keyboard navigation module.
 *
 * Copyright (c) 2004-2005 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.

 * $Id: menu.js 931 2005-10-15 23:43:28Z alex $
 */

/**
 * \internal Provides keyboard navigation and shortcuts.
 * Sould be assigned to window.document keydown event.
 *
 * \param ev [Event] Event object.
 */
Zapatec.Menu.onDocumentKeyDown = function(ev) {
  ev || (ev = window.event);
  // Skip Shift, Ctrl and Alt buttons because they are used only as modifiers
  if (ev.keyCode == 16 || ev.keyCode == 17 || ev.keyCode == 18) {
    return true;
  }
  var item = Zapatec.Menu.selectedItemsStack.pop();
  var self = null, subtree = null;
  if (item) {
    self = Zapatec.MenuTree.all[item.__zp_tree];
    subtree = self._getTree(item.__zp_parent);
  }

  var buttonEsc = function(item) {
    if (!item) {
      return true; // Pass through
    }
    // Hide subtree
    if (item.__zp_subtree && item.__zp_state) {
      // Hide current item child subtree
      self.itemHide(item.__zp_item);
      Zapatec.Menu.selectedItemsStack.push(item);
      subtree.__zp_activeitem = item.__zp_item;
    } else {
      // Unselect current item
      Zapatec.Utils.removeClass(item, "zpMenu-item-selected");
      Zapatec.Utils.removeClass(item, "zpMenu-item-selected-collapsed");
      if (subtree.__zp_activeitem == item.__zp_item) subtree.__zp_activeitem = '';
      // Hide current subtree
      if (subtree.__zp_item) {
        var parentItem = self.items[subtree.__zp_item];
        if (parentItem.__zp_state) {
          self.itemHide(parentItem.__zp_item);
        }
      }
    }
    // Hide menu
    if (self.config.triggerEvent) {
      for (var i = 0; i < Zapatec.Menu.selectedItemsStack.length; i++) {
        if (Zapatec.MenuTree.all[Zapatec.Menu.selectedItemsStack[i].__zp_tree] == self) {
          return Zapatec.Utils.stopEvent(ev);
        }
      }
      // No more selected items in this menu
      self.hideMenu();
    }
    return Zapatec.Utils.stopEvent(ev);
  };

  var buttonEnter = function(item) {
    if (!item) {
      return true; // Pass through
    }
    Zapatec.Menu.selectedItemsStack.push(item);
    item.onclick();
    return Zapatec.Utils.stopEvent(ev);
  };

  var buttonLeftArrow = function(item) {
    if (!item) {
      return true; // Pass through
    }
    if (!self.config.vertical && /zpMenu-level-1/.test(item.className)) {
      // Horizontal top menu item
      var prevItem = item.previousSibling;
      if (!prevItem) {
        prevItem = item.parentNode.lastChild;
      }
      if (prevItem) {
        if (item.__zp_state) {
          item.onmouseout();
          prevItem.onmouseover();
        } else {
          Zapatec.Menu.unselectItem(item);
          Zapatec.Menu.selectItem(prevItem);
          subtree.__zp_activeitem = prevItem.__zp_item;
        }
      } else {
        Zapatec.Menu.selectedItemsStack.push(item);
      }
    } else {
      // Vertical menu item
      if (!/zpMenu-level-1/.test(item.className)) {
        // Hide subtree
        return buttonEsc(item);
      } else {
        Zapatec.Menu.selectedItemsStack.push(item);
      }
    }
    return Zapatec.Utils.stopEvent(ev);
  };

  var buttonRightArrow = function(item) {
    if (!item) {
      return true; // Pass through
    }
    if (!self.config.vertical && /zpMenu-level-1/.test(item.className)) {
      // Horizontal top menu item
      var nextItem = item.nextSibling;
      if (!nextItem) {
        nextItem = item.parentNode.firstChild;
      }
      if (nextItem) {
        if (item.__zp_state) {
          item.onmouseout();
          nextItem.onmouseover();
        } else {
          Zapatec.Menu.unselectItem(item);
          Zapatec.Menu.selectItem(nextItem);
          subtree.__zp_activeitem = nextItem.__zp_item;
        }
      } else {
        Zapatec.Menu.selectedItemsStack.push(item);
      }
    } else {
      // Vertical menu item
      if (item.__zp_subtree) {
        // Show subtree
        Zapatec.Menu.selectedItemsStack.push(item);
        self.itemShow(item.__zp_item);
        var subMenu = self._getTree(item.__zp_subtree);
        if (subMenu.__zp_items.length > 0) {
          if (subMenu.__zp_activeitem) {
            Zapatec.Menu.unselectItem(self.items[subMenu.__zp_activeitem]);
          }
          subMenu.__zp_activeitem = subMenu.__zp_items[0];
          Zapatec.Menu.selectItem(self.items[subMenu.__zp_activeitem]);
        }
      } else if (!self.config.vertical && !/zpMenu-level-1/.test(item.className)) {
        // Go to next top menu item
        item.onmouseout();
        return buttonRightArrow(self.items[self._getTree(item.__zp_tree).__zp_activeitem]);
      } else {
        Zapatec.Menu.selectedItemsStack.push(item);
      }
    }
    return Zapatec.Utils.stopEvent(ev);
  };

  var buttonUpArrow = function(item) {
    if (!item) {
      return true; // Pass through
    }
    if (!self.config.vertical && /zpMenu-level-1/.test(item.className)) {
      // Horizontal top menu item
      // Show subtree
      return buttonDownArrow(item);
    } else {
      // Vertical menu item
      var prevItem = item.previousSibling;
      while (prevItem && (!/zpMenu-item/i.test(prevItem.className) || /zpMenu-item-hr/i.test(prevItem.className))) {
        prevItem = prevItem.previousSibling;
      }
      if (!prevItem) {
        prevItem = item.parentNode.lastChild;
      }
      if (prevItem) {
        if (item.__zp_state) {
          item.onmouseout();
          prevItem.onmouseover();
        } else {
          Zapatec.Menu.unselectItem(item);
          Zapatec.Menu.selectItem(prevItem);
          subtree.__zp_activeitem = prevItem.__zp_item;
        }
      } else {
        Zapatec.Menu.selectedItemsStack.push(item);
      }
    }
    return Zapatec.Utils.stopEvent(ev);
  };

  var buttonDownArrow = function(item) {
    if (!item) {
      return true; // Pass through
    }
    if (!self.config.vertical && /zpMenu-level-1/.test(item.className)) {
      // Horizontal top menu item
      Zapatec.Menu.selectedItemsStack.push(item);
      if (item.__zp_subtree) {
        // Show subtree
        self.itemShow(item.__zp_item);
        var subMenu = self._getTree(item.__zp_subtree);
        if (subMenu.__zp_items.length > 0) {
          if (subMenu.__zp_activeitem) {
            Zapatec.Menu.unselectItem(self.items[subMenu.__zp_activeitem]);
          }
          subMenu.__zp_activeitem = subMenu.__zp_items[0];
          Zapatec.Menu.selectItem(self.items[subMenu.__zp_activeitem]);
        }
      }
    } else {
      // Vertical menu item
      var nextItem = item.nextSibling;
      while (nextItem && (!/zpMenu-item/i.test(nextItem.className) || /zpMenu-item-hr/i.test(nextItem.className))) {
        nextItem = nextItem.nextSibling;
      }
      if (!nextItem) {
        nextItem = item.parentNode.firstChild;
      }
      if (nextItem) {
        if (item.__zp_state) {
          item.onmouseout();
          nextItem.onmouseover();
        } else {
          Zapatec.Menu.unselectItem(item);
          Zapatec.Menu.selectItem(nextItem);
          subtree.__zp_activeitem = nextItem.__zp_item;
        }
      } else {
        Zapatec.Menu.selectedItemsStack.push(item);
      }
    }
    return Zapatec.Utils.stopEvent(ev);
  };

  var altShortcut = function() {
    if (subtree && subtree.__zp_keymap) {
      var keymapItem = subtree.__zp_keymap[ev.keyCode];
      if (keymapItem) {
        // Unselect current item
        if (item && !item.__zp_state) {
          Zapatec.Utils.removeClass(item, "zpMenu-item-selected");
          Zapatec.Utils.removeClass(item, "zpMenu-item-selected-collapsed");
          if (subtree.__zp_activeitem == item.__zp_item) subtree.__zp_activeitem = '';
        }
        // As onItemClick shows submenu with delay, we have to
        // show submenu immediately to be able to set active item
        self.itemShow(keymapItem.__zp_item);
        // Simulate click
        keymapItem.onclick();
        // Set active item in submenu
        if (keymapItem.__zp_subtree) {
          var subMenu = self._getTree(keymapItem.__zp_subtree);
          if (subMenu.__zp_items.length > 0) {
            if (subMenu.__zp_activeitem) {
              Zapatec.Menu.unselectItem(self.items[subMenu.__zp_activeitem]);
            }
            subMenu.__zp_activeitem = subMenu.__zp_items[0];
            Zapatec.Menu.selectItem(self.items[subMenu.__zp_activeitem]);
          }
        }
        return Zapatec.Utils.stopEvent(ev);
      }
    }
    return true; // Pass through
  };

  var shortcut = function() {
    if (subtree) { // Alt modifier is not required if there is active item
      if (item.__zp_state) {
        Zapatec.Menu.selectedItemsStack.push(item);
        subtree = self._getTree(item.__zp_subtree);
      }
      var ret = altShortcut();
      if (!ret) return ret;
    } else if (ev.altKey) { // Alt modifier is required
      for (var i in Zapatec.MenuTree.all) {
        self = Zapatec.MenuTree.all[i];
        subtree = self.top_parent;
        var ret = altShortcut();
        if (!ret) return ret;
      }
    }
    Zapatec.Menu.selectedItemsStack.push(item);
    return true; // Pass through
  };

  switch (ev.keyCode) {
    case 27: // Esc
      return buttonEsc(item);
      break;
    case 13: // Enter
    case 32: // Space
      return buttonEnter(item);
      break;
    case 37: // Left arrow
      return buttonLeftArrow(item);
      break;
    case 39: // Right arrow
      return buttonRightArrow(item);
      break;
    case 38: // Up arrow
      return buttonUpArrow(item);
      break;
    case 40: // Down arrow
      return buttonDownArrow(item);
      break;
    default:
      return shortcut();
  }
};

// Setup keyboard navigation and shortcuts
Zapatec.Utils.addEvent(window.document, 'keydown', Zapatec.Menu.onDocumentKeyDown);

// Disable scrolling with arrows
Zapatec.Utils.addEvent(window.document, 'keypress',
  function(ev) {
    ev || (ev = window.event);
    switch (ev.keyCode) {
      case 37: // Left arrow
      case 39: // Right arrow
      case 38: // Up arrow
      case 40: // Down arrow
        return Zapatec.Utils.stopEvent(ev);
        break;
    }
  }
);
