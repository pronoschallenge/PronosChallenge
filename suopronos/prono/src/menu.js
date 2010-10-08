/**
 * \file menu.js
 * Zapatec DHTML Menu Widget.
 * Contains two classes. The menu and the tree that it uses.
 *
 * Copyright (c) 2004-2005 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 *
 * $Id: menu.js 2579 2006-04-21 19:26:12Z alex $
 */

/* =============================================================== */
/* ===================== Tree Class ============================== */
/* =============================================================== */

/**
 * The Zapatec DHTML Tree, as used by the menu
 *
 * The Zapatec.MenuTree object constructor.  Pass to it the ID of an UL element (or
 * a reference to the element should you have it already) and an optional
 * configuration object.  This function creates and initializes the tree widget
 * according to data existent in the nested list, and applies the configuration
 * specified.
 *
 * The configuration object may contain the following options (the following
 * shows default values):
 *
 * \code
 * {
 *    hiliteSelectedNode : true,     // boolean
 *    compact            : false,    // boolean
 *    dynamic            : false,    // boolean
 *    initLevel          : false,    // false or number
 *    defaultIcons       : null      // null or string
 * }
 * \endcode
 *
 * - hiliteSelectedNode -- if \b false is passed, the tree will not highlight
 *   the currently selected node.
 * - compact -- if \b true is passed the tree will work in a "compact" mode; in
 *   this mode it automatically closes sections not relevant to the current
 *   one.
 * - dynamic -- if \b true is passed the tree will use the "dynamic initialization"
 *   technique which greatly improves generation time.  Some functionality is
 *   not available in this mode until all the tree was generated.  In "dynamic"
 *   mode the tree is initially collapsed and levels are generated "on the fly"
 *   as the end user expands them.  You can't retrieve nodes by ID (which
 *   implies you can't synchronize to certain nodes) until they have been
 *   generated.
 * - initLevel -- when this is a numeric value, it specifies the maximum
 *   "expand level" that the tree will use initially.  Therefore, if for
 *   instance you specify 1 then the tree will be initially expanded one level.
 *   Pass here 0 to have the tree fully collapsed, or leave it \b false to have
 *   the tree fully expanded.
 * - defaultIcons -- you can pass here a string.  If so, all tree items will
 *   get an additional TD element containing that string in the \b class
 *   attribute.  This helps you to include custom default icons without
 *   specifying them as IMG tags in the tree.  See our examples.
 *
 * \param el [string or HTMLElement] -- the UL element
 * \param config [Object, optional] -- the configuration options
 * \param noInit [boolean, optional] -- if true, don't configure/init tree.
 */
Zapatec.MenuTree = function(el, config, noInit) {
  if (typeof config == "undefined")
    config = {};
  this._el = el;
  this._config = config;
  if (!noInit) this.initTree();
}


/**
 * This global variable indicates that window is loaded
 */
Zapatec.MenuTree.windowLoaded = false;
Zapatec.Utils.addEvent(window, 'load', function(){Zapatec.MenuTree.windowLoaded = true});


/**
 * \internal Function that initialises a tree based on informatino stored in
 * the constructor function above. Separated so that other scripts can inherit
 * from this script and run their own postponed setup routine.
 */
Zapatec.MenuTree.prototype.initTree = function() {
    var el = this._el;
    var config = this._config;
  // Now we have the stored parameters, run the rest of the init code.
  function param_default(name, value) {
      if (typeof config[name] == "undefined") config[name] = value;
  };
  param_default('d_profile', false);
  param_default('hiliteSelectedNode', true);
  param_default('compact', false);
  param_default('dynamic', false);
  param_default('initLevel', false);
  if (config.dynamic)
    config.initLevel = 0;
  this.config = config;
  // <PROFILE>
  if (this.config.d_profile) {
    var T1 = new Date().getTime();
    profile = {
      items : 0,
      trees : 0,
      icons : 0
    };
  }
  // </PROFILE>
  if (typeof el == "string")
    el = document.getElementById(el);
  this.list = el;
  this.items = {};
  this.trees = {};
  this.selectedItem = null;

  // Check for valid ID, if none found then alert user
  this.id=null;
  if (el)
    this.id = el.id || Zapatec.Utils.generateID("tree");
  else
    alert("Can not find Menu id=\"" + this._el + "\"")
  var top = this.top_parent = Zapatec.Utils.createElement("div");
  top.style.display = 'none'; // Initially menu is hidden and will be shown on triggerEvent
  top.__zp_menu = Zapatec.Utils.createElement("div", top);
  top.__zp_menu.className = 'zpMenu';
  top.className = "zpMenuContainer zpMenu-top";
  if (this.config.vertical)
    // vertical:true, set top div container class to
    // NOTE:zpMenu-vertical-mode and zpMenu-level-1 defines the top vertical menu
    Zapatec.Utils.addClass(top, "zpMenu-vertical-mode");
  else
    Zapatec.Utils.addClass(top, "zpMenu-horizontal-mode");
  this.createTree(el, top, 0);
  if (el) {
    if (this.config.theme != '') {
      var theme = Zapatec.Utils.createElement("div");
      theme.className = 'zpMenu-' + this.config.theme;
      theme.appendChild(top);
      el.parentNode.insertBefore(theme, el);
    } else {
      el.parentNode.insertBefore(top, el);
    }
    el.parentNode.removeChild(el);
  }
  Zapatec.MenuTree.all[this.id] = this;
  // check if we have an initially selected node and sync. the tree if so
  if (this.selectedItem)
    this.sync(this.selectedItem.__zp_item);
  // <PROFILE>
  if (this.config.d_profile) {
    alert("Generated in " + (new Date().getTime() - T1) + " milliseconds\n" +
          profile.items + " total tree items\n" +
          profile.trees + " total (sub)trees\n" +
          profile.icons + " total icons");
  }
  // </PROFILE>

  // Get path from cookies
  this.path = Zapatec.Utils.getCookie(this.config.pathCookie);
  if (this.path) {
    // Remove path from cookies
    Zapatec.Utils.writeCookie(this.config.pathCookie, '');
  }

  // Show menu if triggerEvent is not set
  if (!this.config.triggerEvent) {
    if (Zapatec.MenuTree.windowLoaded) {
      this.showMenu();
    } else {
      var self = this;
      Zapatec.Utils.addEvent(window, 'load', function(){self.showMenu()});
    }
  }
};

/**
 * This global variable keeps a "hash table" (that is, a plain JavaScript
 * object) mapping ID-s to references to Zapatec.MenuTree objects.  It's helpful if
 * you want to operate on a tree but you don't want to keep a reference to it.
 * Example:
 *
 * \code
 *   // the following makes a tree for the <ul id="tree-id"> element
 *   var tree = new Zapatec.MenuTree("tree-id");
 *   // ... later
 *   var existing_tree = Zapatec.MenuTree.all("tree-id");
 *   // and now we can use \b existing_tree the same as we can use \b tree
 *   // the following displays \b true
 *   alert(existing_tree == tree);
 * \endcode
 *
 * So in short, this variable remembers values returned by "new
 * Zapatec.MenuTree(...)" in case you didn't.
 */
Zapatec.MenuTree.all = {};

/* HR methods */
/* Is the html item an hr? */
Zapatec.MenuTree.prototype.is_hr_tag= function(item) {
  return (item.tagName.toLowerCase() == 'hr');
};

/* does this item contain the zapatec class name for an hr? */
Zapatec.MenuTree.prototype.is_hr_class= function(item) {
  return (/zpMenu-item-hr/i.test(item.className));
};

/**
 * \internal Function that creates a (sub)tree.  This function walks the UL
 * element, computes and assigns CSS class names and creates HTML elements for
 * a subtree.  Each time a LI element is encountered, createItem() is called
 * which effectively creates the item.  Beware that createItem() might call
 * back this function in order to create the item's subtree. (so createTree and
 * createItem form an indirect recursion).
 *
 * \param list [HTMLElement] -- reference to the UL element
 * \param parent [HTMLElement] -- reference to the parent element that should hold the (sub)tree
 * \param level [integer] -- the level of this (sub)tree in the main tree.
 *
 * \return id -- the (sub)tree ID; might be automatically generated.
 */
Zapatec.MenuTree.prototype.createTree = function(list, parent, level) {
  if (this.config.d_profile) // PROFILE
    ++profile.trees; // PROFILE
  var id;
  var intItem=1, bFirst=true;

  if (list) id=list.id; // list can be null
  if (!id)  id=Zapatec.Utils.generateID("tree.sub");
  var
    self = this;
  function _makeIt() {
    self.creating_now = true;
    var
      last_li = null, //previous <li>
      next_li, //next <li>
      i = (list ? list.firstChild : null),
      items = parent.__zp_items = [];
    self.trees[id] = parent;
    parent.__zp_level = level;
    parent.__zp_treeid = id;
    parent.__zp_keymap = {};
    var strOddEven;
    while (i) {
      if (last_li)
        last_li.className += " zpMenu-lines-c";
      if (i.nodeType != 1)
        i = i.nextSibling;
      else {
        next_li = Zapatec.Utils.getNextSibling(i, 'li');
        if (i.tagName.toLowerCase() == 'li') {
          last_li = self.createItem(i, parent, next_li, level, intItem);
          if (last_li) { //false when webmaster creates malformed tree
            // ONLY do odd/even for NON HR items
            // If HR items had odd/even then visual odd/even themes (see zebra) look wrong
            if (!self.is_hr_class(last_li)) 
            {
              // this previously created item is NOT in the HR class, create odd/even class
              strOddEven="zpMenu-item-" + (intItem % 2==1 ? "odd" : "even");
              Zapatec.Utils.addClass(last_li, strOddEven)
              intItem++
            }

            if (bFirst)
            {
              // First li for this sub-menu
              bFirst=false;
              Zapatec.Utils.addClass(last_li, "zpMenu-item-first");
            }
            //adds it to the parent's array of items
            items[items.length] = last_li.__zp_item;
          }
        }
        i = next_li;
      }
    }

    // Last li for this sub-menu
    if (last_li) Zapatec.Utils.addClass(last_li, "zpMenu-item-last");

    i = parent.firstChild;
    if (i && !level) {
      i.className = i.className.replace(/ zpMenu-lines-./g, "");
      i.className += (i === last_li) ? " zpMenu-lines-s" : " zpMenu-lines-t";
    }
    if (last_li && (level || last_li !=  i)) {
      last_li.className = last_li.className.replace(/ zpMenu-lines-./g, "");
      last_li.className += " zpMenu-lines-b";
    }
    self.creating_now = false;
  };
  if (this.config.dynamic && level > 0)
    this.trees[id] = _makeIt;
  else
    _makeIt();
  return id;
};

/**
 * Zapatec.MenuTree.tabIndex counter increased by 1 before each item added
 * Next menu item will have tabIndex property value = current value of Zapatec.MenuTree.tabIndex + 1
 *
 * Note:
 * in Opera tabIndex property value of node must be > 0, otherwise it will be ignored;
 * Mozilla starts travelling from nodes with tabIndex > 0;
 * IE starts travelling from nodes with tabIndex == 0;
 * all nodes without tabIndex set explicitly have tabIndex == 0
 */
Zapatec.MenuTree.tabIndex = 1000;

/**
 * \internal This function walks through a LI element and creates the HTML
 * elements associated with that tree item.  When it encounters an UL element
 * it calls createTree() in order to create the item's subtree.  This function
 * may also call item_addIcon() in order to add the +/- buttons or icons
 * present in the item definition as IMG tags, or item_addDefaultIcon() if the
 * tree configuration specifies "defaultIcons" and no IMG tag was present.
 *
 * \param li [HTMLElement] -- reference to the LI element
 * \param parent [HTMLElement] -- reference to the parent element where the HTML elements should be created
 * \param next_li [HTMLLiElement] -- reference to the next LI element, if this is not the last one
 * \param level [integer] -- the level of this item in the main tree
 * \param intItem [integer] -- Nth item for this sub-tree
 *
 * \return [HTMLElement] -- a reference to a DIV element holding the HTML elements of the created item
 */
Zapatec.MenuTree.prototype.createItem = function(li, parent, next_li, level, intItem) {
  if (this.config.d_profile) // PROFILE
    ++profile.items; // PROFILE
  if (!li.firstChild)
    return;
  var
    id = li.id || Zapatec.Utils.generateID("tree.item"),
    item = this.items[id] = Zapatec.Utils.createElement("div", parent.__zp_menu),
    t = Zapatec.Utils.createElement("table", item),
    tb = Zapatec.Utils.createElement("tbody", t),
    tr = Zapatec.Utils.createElement("tr", tb),
    td = Zapatec.Utils.createElement("td", tr),
    has_icon = false;

  if (!level) {
    // This will allow to have correct item offsetWidth value in Opera
    td.style.whiteSpace = 'nowrap';
  }

  t.className = "zpMenu-table";
  t.cellSpacing = 0;
  t.cellPadding = 0;
  td.className = "zpMenu-label"

  //If there's a title attribute to the LI
  var title = li.getAttribute('title');
  if (title) {
    //apply it to the menu item
    td.setAttribute('title', title);
  }

  // add the LI's classname to the
  item.className = "zpMenu-item" + (li.className ? ' ' + li.className : '');
  Zapatec.Utils.addClass(item, "zpMenu-level-" + (level+1));  // Define the Nth level of a sub-menu, 1 based
  item.__zp_item = id;
  item.__zp_tree = this.id;
  item.__zp_parent = parent.__zp_treeid;
  item.onmouseover = Zapatec.Menu.onItemMouseOver;
  item.onmouseout = Zapatec.Menu.onItemMouseOut;
  item.onclick = Zapatec.Menu.onItemClick;
  Zapatec.Utils.addClass(item, "zpMenu-item-" + (intItem % 2==1 ? "odd" : "even"));

  // Parse li
  var fc, subtree = false, accessKey = null;

  var getAccessKey = function(node) {
    var key = null;
    if (node.nodeType == 1) { // ELEMENT_NODE
      if (key = node.getAttribute('accesskey')) {
        // Remove accesskey attribute because it will cause duplicate onclick event
        node.removeAttribute('accesskey', false);
        if (/^[a-z0-9]$/i.test(key)) {
          return key;
        } else {
          key = null;
        }
      }
      var childNodes = node.childNodes;
      for (var i = 0; i < childNodes.length; i++) {
        if (key = getAccessKey(childNodes[i])) {
          break;
        }
      }
    } else if (node.nodeType == 3) { // TEXT_NODE
      var label = node.data.replace(/(^\s+|\s+$)/g, '');
      if (/_([a-z0-9])/i.test(label)) {
        label = label.replace(/_([a-z0-9])/i, '<span style="text-decoration:underline">$1</span>');
        key = RegExp.$1;
        var span = Zapatec.Utils.createElement("span");
        span.innerHTML = label;
        var parent = node.parentNode;
        parent.insertBefore(span, node);
        parent.removeChild(node);
      }
    }
    return key;
  };

  while (fc = li.firstChild) {
    if (fc.nodeType == 1 && /^[ou]l$/i.test(fc.tagName.toLowerCase())) {
      // Subtree
      if (!subtree) {
        this.item_addIcon(item, null);
        var np = Zapatec.Utils.createElement("div", parent);
        // The following to be able to position menu at the bottom right corner
        // of the screen without appearing of scrollbars
        // Also Opera zIndex requires absolute positioning
        np.style.position = 'absolute';
        if (!this.config.triggerEvent) {
          np.style.left = '-9999px';
          np.style.top = '-9999px';
        }
        if (this.config.dropShadow) {
          var ds = np.__zp_dropshadow = Zapatec.Utils.createElement('div');
          parent.insertBefore(ds, np);
          ds.style.position = 'absolute';
          if (!this.config.triggerEvent) {
            ds.style.left = '-9999px';
            ds.style.top = '-9999px';
          }
          ds.style.backgroundColor = '#000';
          if (window.opera) {
            ds.style.backgroundColor = '#666'; // opacity doesn't work in Opera
          } else {
            ds.style.filter = 'alpha(opacity=' + this._config.dropShadow + ')';
          }
          ds.style.opacity = this.config.dropShadow / 100;
        }
        np.__zp_item = id;
        np.__zp_menu = Zapatec.Utils.createElement("div", np);
        np.__zp_menu.className = 'zpMenu' + (fc.className ? ' ' + fc.className : '');
        np.className = 'zpMenuContainer';
        np.__zp_menu.onmouseover = Zapatec.Menu.onItemMouseOver;
        np.__zp_menu.onmouseout = Zapatec.Menu.onItemMouseOut;
        if (next_li) {
          np.__zp_menu.className += " zpMenu-lined";
        }
        item.__zp_subtree = this.createTree(fc, np, level+1);
        if ((this.config.initLevel !=  false && this.config.initLevel <= level) ||
            (this.config.compact && !/(^|\s)expanded(\s|$)/i.test(li.className))
            || /(^|\s)collapsed(\s|$)/i.test(li.className)) {
          item.className += " zpMenu-item-collapsed";
          this.toggleItem(id);
        } else {
          item.className += " zpMenu-item-expanded";
        }
        if (/(^|\s)selected(\s|$)/i.test(li.className)) {
          this.selectedItem = item;
        }
        subtree = true;
      }
      li.removeChild(fc);
    } else {
      // Label
      li.removeChild(fc);
      if (fc.nodeType == 3) {
        // Text
        var label = fc.data.replace(/(^\s+|\s+$)/g, '');
        if (label) {
          var strInnerHtml = label;
          if (!accessKey) {
            strInnerHtml = label.replace(/_([a-z0-9])/i,
             '<span style="text-decoration:underline">$1</span>');
            accessKey = RegExp.$1;
          }
          var span = Zapatec.Utils.createElement("span", td);
          // IE 6.0 doesn't escape correctly plain text when it is assigned to
          // innerHTML property
          if (strInnerHtml == label) {
            // Plain text
            span.appendChild(document.createTextNode(strInnerHtml));
          } else {
            // Contains <span>
            span.innerHTML = strInnerHtml;
          }
          if (title) span.setAttribute('title', title); // To make title work in Opera
        }
      } else if (fc.tagName) { // Skip comments, etc.
        if (fc.tagName.toLowerCase() == 'img') {
          // Icon
          this.item_addIcon(item, fc);
          has_icon = true;
        } else {
          // Other stuff
          if (this._menuMode && (fc.tagName.toLowerCase() == 'hr')) {
            Zapatec.Utils.addClass(item, "zpMenu-item-hr");
          } else if (fc.tagName.toLowerCase() == 'input' && fc.getAttribute('type') == 'checkbox') {
            fc.onmousedown = function(ev){
              if (this.checked) {
                this.checked = false;
              } else {
                this.checked = true;
              }
              return Zapatec.Utils.stopEvent(ev);
            };
          } else if (fc.tagName.toLowerCase() == 'input' && fc.getAttribute('type') == 'radio') {
            fc.onmousedown = function(ev){
              this.checked = true;
              return Zapatec.Utils.stopEvent(ev);
            };
          } else if (fc.tagName.toLowerCase() == 'a') {
            if (!accessKey) {
              accessKey = getAccessKey(fc);
            }
            // Tab navigation support
            fc.tabIndex = ++Zapatec.MenuTree.tabIndex;
            fc.onfocus = Zapatec.Menu.onItemMouseOver;
            fc.onblur = Zapatec.Menu.onItemMouseOut;
          }
          td.appendChild(fc);
          if (title && !fc.getAttribute('title')) fc.setAttribute('title', title); // To make title work in Opera
        }
      }
    }
  }

  if (accessKey) {
    accessKey = accessKey.toUpperCase().charCodeAt(0);
    parent.__zp_keymap[accessKey] = item;
  }

  if (!has_icon && !/zpMenu-item-hr/i.test(item.className))
    // No icons for this non-HR menu item
    if (this.config.defaultIcons)
      // Use user config setting defaultIcons className
      this.item_addDefaultIcon(item, this.config.defaultIcons);
    else
      // No icons default className
      this.item_addDefaultIcon(item, "zpMenu-noicon");

  return item;
};

/**
 * \internal This function adds a TD element having a certain class attribute
 * which helps having a tree containing icons without defining IMG tags for
 * each item.  The class name will be "tgb icon className" (where "className"
 * is the specified parameter).  Further, in order to customize the icons, one
 * should add some CSS lines like this:
 *
 * \code
 *  div.tree-item td.customIcon {
 *    background: url("themes/img/fs/document2.png") no-repeat 0 50%;
 *  }
 *  div.tree-item-expanded td.customIcon {
 *    background: url("themes/img/fs/folder-open.png") no-repeat 0 50%;
 *  }
 *  div.tree-item-collapsed td.customIcon {
 *    background: url("themes/img/fs/folder.png") no-repeat 0 50%;
 *  }
 * \endcode
 *
 * As you can see, it's very easy to customize the default icons for a normal
 * tree item (that has no subtrees) or for expanded or collapsed items.  For
 * the above example to work, one has to pass { defaultIcons: "customIcon" } in
 * the tree configuration object.
 *
 * This function does nothing if the \b className parameter has a false logical
 * value (i.e. is null).
 *
 * \param item [HTMLElement] -- reference to the DIV element holding the item
 * \param className -- a string containing the additional class name
 */
Zapatec.MenuTree.prototype.item_addDefaultIcon = function(item, className) {
  if (!className)
    return;
  var last_td = item.firstChild.firstChild.firstChild.lastChild, td;
  var td = Zapatec.Utils.createElement("td");
  td.className = "tgb icon " + className;

  last_td.parentNode.insertBefore(td, last_td);
};

/**
 * \internal This function does different things, depending on whether the \b
 * img parameter is passed or not.  If the \b img is passed, then this function
 * adds it as an icon for the given item.  If not passed, this function creates
 * a "+/-" button for the given item.
 *
 * \param item [HTMLElement] -- reference to the DIV holding the item elements
 * \param img [HTMLImgElement, optional] -- reference to an IMG element; normally one found in the <LI>
 */
Zapatec.MenuTree.prototype.item_addIcon = function(item, img) {
  if (this.config.d_profile) // PROFILE
    ++profile.icons; // PROFILE
  var last_td = item.firstChild.firstChild.firstChild, td;
  last_td = img ? last_td.lastChild : last_td.firstChild;
  if (!img || !item.__zp_icon) {
    td = Zapatec.Utils.createElement("td");
    td.className = "tgb " + (img ? "icon" : "minus");
    last_td.parentNode.insertBefore(td, last_td);
  } else {
    td = item.__zp_icon;
    img.style.display = "none";
  }
  if (!img) {
    td.innerHTML = "&nbsp;";
    item.className += " zpMenu-item-more";
    item.__zp_state = true; // expanded
    item.__zp_expand = td;
  } else {
    td.appendChild(img);
    item.__zp_icon = td;
  }
};

/**
 * This function gets called from a global event handler when some item was
 * clicked.  It selects the item and toggles it if it has a subtree (expands or
 * collapses it).
 *
 * \param item_id [string] -- the item ID
 */
Zapatec.MenuTree.prototype.itemClicked = function(item_id) {
  this.selectedItem = this.toggleItem(item_id);
  if (this.config.hiliteSelectedNode && this.selectedItem) {
    Zapatec.Menu.selectItem(this.selectedItem);
  }
  this.onItemSelect(item_id);
};

/**
 * This function toggles an item if the \b state parameter is not specified.
 * If \b state is \b true then it expands the item, and if \b state is \b false
 * then it collapses the item.
 *
 * \param item_id [string] -- the item ID
 * \param state [boolean, optional] -- the desired item state
 *
 * \return a reference to the item element if found, null otherwise
 */
Zapatec.MenuTree.prototype.toggleItem = function(item_id, state) {
  if (!item_id) {
    return null;
  }
  if (this.config.hiliteSelectedNode && this.selectedItem) {
    Zapatec.Menu.unselectItem(this.selectedItem);
  }
  var item = this.items[item_id];
  if (typeof state == "undefined")
    state = !item.__zp_state;
  if (state != item.__zp_state) {
    var subtree = this._getTree(item.__zp_subtree, this.creating_now);
    if (subtree) {
      if (state) {
        // Unselect all children
        for (var i = 0; i < subtree.__zp_items.length; i++) {
          var subItemID = subtree.__zp_items[i];
          Zapatec.Menu.unselectItem(this.items[subItemID]);
          if (subtree.__zp_activeitem == subItemID) subtree.__zp_activeitem = '';
        }
      } else {
        // Recursively hide all children
        for (var i = 0; i < subtree.__zp_items.length; i++) {
          var subItemID = subtree.__zp_items[i];
          this.toggleItem(subItemID, state);
          Zapatec.Menu.unselectItem(this.items[subItemID]);
          if (subtree.__zp_activeitem == subItemID) subtree.__zp_activeitem = '';
        }
      }
      this.treeSetDisplay(subtree, state);
      Zapatec.Utils.removeClass(item, "zpMenu-item-expanded");
      Zapatec.Utils.removeClass(item, "zpMenu-item-collapsed");
      Zapatec.Utils.addClass(item, state ? "zpMenu-item-expanded" : "zpMenu-item-collapsed");
    }
    var img = item.__zp_expand;
    if (img)
      img.className = "tgb " + (state ? "minus" : "plus");
    item.__zp_state = state;
    if (this.config.compact && state) {
      var hideItems = this._getTree(item.__zp_parent).__zp_items;
      for (var i = hideItems.length; --i >= 0;) {
        if (hideItems[i] != item_id && hideItems[i].__zp_state) {
          this.toggleItem(hideItems[i], false);
        }
      }
    }
  }
  return item;
};

/**
 * Call this function to collapse all items in the tree.
 */
Zapatec.MenuTree.prototype.collapseAll = function() {
  for (var i in this.trees)
    this.toggleItem(this._getTree(i).__zp_item, false);
};

/**
 * Call this function to expand all items in the tree.
 */
Zapatec.MenuTree.prototype.expandAll = function() {
  for (var i in this.trees)
    this.toggleItem(this._getTree(i).__zp_item, true);
};

/**
 * Call this function to toggle all items in the tree.
 */
Zapatec.MenuTree.prototype.toggleAll = function() {
  for (var i in this.trees)
    this.toggleItem(this._getTree(i).__zp_item);
};

/**
 * Call this function to synchronize the tree to a given item.  This means that
 * all items will be collapsed, except that item and the full path to it.
 *
 * \param item_id [string] -- the ID of the item to sync to.
 */
Zapatec.MenuTree.prototype.sync = function(item_id) {
  var item = this.items[item_id];
  if (item) {
    this.collapseAll();
    this.selectedItem = item;
    var path = [];
    while (item.__zp_parent) {
      path[path.length] = item;
      var parentItem = this._getTree(item.__zp_parent);
      if (parentItem.__zp_item) {
        item = this.items[parentItem.__zp_item];
      } else {
        break;
      }
    }
    for (var ii = path.length; --ii >= 0;) {
      var item = path[ii];
      var item_id = item.__zp_item;
      this.itemShow(item_id);
      var menu = this._getTree(item.__zp_parent);
      menu.__zp_activeitem = item_id;
      Zapatec.Menu.selectItem(item);
    }
  }
};

/**
 * Highlight specified item and all higher items.
 *
 * \param item_id [string] -- the ID of the item.
 */
Zapatec.MenuTree.prototype.highlightPath = function(item_id) {
  // Put this menu on top
  if (this.putOnTop) {
    this.putOnTop();
  }
  // Highlight path
  var item = this.items[item_id];
  if (item) {
    var a = [];
    while (item.__zp_parent) {
      a[a.length] = item;
      var pt = this._getTree(item.__zp_parent);
      if (pt.__zp_item)
        item = this.items[pt.__zp_item];
      else
        break;
    }
    for (var i = a.length; --i >= 0;) {
      Zapatec.Utils.addClass(a[i], 'zpMenuPath');
    }
  }
};

/**
 * Destroys the tree.  Removes all elements.  Does not destroy the Zapatec.MenuTree
 * object itself (actually there's no proper way in JavaScript to do that).
 */
Zapatec.MenuTree.prototype.destroy = function() {
  var p = this.top_parent;
  p.parentNode.removeChild(p);
};

/**
 * \internal This function is used when "dynamic initialization" is on.  It
 * retrieves a reference to a subtree if already created, or creates it if it
 * wasn't yet and \b dont_call is \b false (returns null in that case).
 *
 * \param tree_id [string] the ID of the subtree
 * \param dont_call [boolean] pass true here if you don't want the subtree to be created
 *
 * \return reference to the tree if it was found or created, null otherwise.
 */
Zapatec.MenuTree.prototype._getTree = function(tree_id, dont_call) {
  var tree = this.trees[tree_id];
  if (typeof tree == "function") {
    if (dont_call)
      tree = null;
    else {
      tree();
      tree = this.trees[tree_id];
    }
  }
  return tree;
};

// CUSTOMIZABLE EVENT HANDLERS; default action is "do nothing"

/**
 * Third party code can override this member in order to add an event handler
 * that gets called each time a tree item is selected.  It receives a single
 * string parameter containing the item ID.
 */
Zapatec.MenuTree.prototype.onItemSelect = function() {};

// GLOBAL EVENT HANDLERS (to workaround the stupid Microsoft memory leak)

/**
 * \internal This is a global event handler that gets called when a tree item
 * is clicked.  Don't override! ;-)
 */
Zapatec.MenuTree.onItemToggle = function() {
  var item = this;
  var body = document.body;
  while (item && item !=  body && !/zpMenu-item/.test(item.className))
    item = item.parentNode;
  Zapatec.MenuTree.all[item.__zp_tree].itemClicked(item.__zp_item);
};

/* =============================================================== */
/* ==================== Menu Class =============================== */
/* =============================================================== */

/**
 * Conctructor.
 * This inherits from Zapatec.MenuTree, and accepts the same parameters.
 * The main differences are that the tree's "compact" mode is always on,
 * and additional config options are available.
 *
 * \param el [string or object] UL element the menu is built on.
 * \param objUserConfig [object, optional] configuration options.
 */
Zapatec.Menu = function(el, objUserConfig) {
  // Arguments are made optional here to be able to inherit from this class
  if (arguments.length > 0) {
    this.init(el, objUserConfig);
  }
};

Zapatec.Menu.prototype = new Zapatec.MenuTree('', null, true);

Zapatec.Menu.prototype.init = function(el, objUserConfig) {
  this._el = el;
  this._config = {};

  /**
   * showDelay config option [number].
   * The delay before a submenu is shown, in milliseconds.
   */
  this._config.showDelay = 0;

  /**
   * hideDelay config option [number].
   * The delay before a submenus is hidden, in milliseconds.
   */
  this._config.hideDelay = 500;

  /**
   * onClick config option [boolean].
   * Top menu drops on click not on hover.
   */
  this._config.onClick = false;

  /**
   * vertical config option [boolean].
   * Make it a vertical menu.
   */
  this._config.vertical = false;

  this._config.scrollWithWindow = false;
  this._config.dropShadow = 0;
  this._config.drag = false;
  this._config.slide = false;
  this._config.glide = false;
  this._config.fade = false;
  this._config.wipe = false;
  this._config.unfurl = false;
  this._config.animSpeed = 10; // percentage animation per frame.
  this._config.compact = true; //always true in a menu
  this._config.initLevel = 0; //always 0 in a menu
  this._config.defaultIcons = null;

  /**
   * zIndex config option [number].
   * Can be used for two menus on the same page.
   * Use higher value for menu which must be in front of other menus.
   */
  this._config.zIndex = 0;

  /**
   * theme config option [string].
   * If set, corresponding CSS file will be loaded automatically.
   * Otherwise CSS file must be included in the HTML.
   *
   * Important: Don't use this option if CSS file is included in the HTML,
   * results will be unpredictable.
   */
  this._config.theme = '';

  /**
   * rememberPath config option [boolean].
   * Used to keep track of previous menu location.
   *
   * Possible values:
   * true - keep track,
   * false - do not keep track,
   * 'expand' - the menu will open expanded to this previously location.
   *
   * Optional if pathCookie flag value differs from '__zp_item'.
   */
  this._config.rememberPath = false;

  /**
   * pathCookie config option [string].
   * Used to keep track of previous menu location.
   * Use this option with or instead of rememberPath when you need to specify
   * which cookie will contain path information. This is needed e.g. when you
   * have several menus on a page.
   *
   * If rememberPath option is not false and pathCookie option is not set,
   * cookie name __zp_item will be used by default.
   */
  this._config.pathCookie = '__zp_item';

  /**
   * triggerEvent config option [string].
   * Event that will trigger showing of the menu.
   *
   * Possible values for mouse click: 'mousedown' or 'mouseup' or 'click'
   * (no matter which, all values treated the same).
   *
   * Possible values for keyboard: 'keydown' or 'keyup' or 'keypress'
   * (no matter which, all values treated the same).
   */
  this._config.triggerEvent = null;

  /**
   * triggerKey config option [number or string].
   * Decimal keyboard scan code or mouse button: 'left' or 'both'.
   *
   * Required for keyboard event.
   * Optional for mouse click (right mouse button by default).
   *
   * Requires triggerEvent to be set.
   *
   * See keyboard scan codes at:
   * http://techwww.in.tu-clausthal.de/Dokumentation/Standards_Bussysteme/ASCII-Tabelle/
   * http://www.nthelp.com/charts.htm
   */
  this._config.triggerKey = null;

  /**
   * triggerObject config option [string or object].
   * Element id or HTMLElement object associated with the menu.
   * E.g. div inside which you should click to show menu.
   *
   * Also can be following array:
   * [
   *   element id [string] ||
   *   HTMLElement object [object] ||
   *   {
   *     triggerObject: element id [string] || HTMLElement object [object],
   *     triggerArgs: any args that should be available to external scripts [any]
   *   },
   *   ...
   * ]
   *
   * In this case trigger is set on all the elements.
   *
   * When trigger menu is shown, its triggerObject property contains reference
   * to trigger object that last invoked the menu, triggerArgs property contains
   * corresponding arguments. External scripts can access those properties.
   *
   * This gives ability to attach menu to several objects and to pass through
   * some piece of data from those objects to external scripts.
   * E.g. to determine, which cell of the grid was clicked, etc.
   *
   * If array is empty (triggerObject: []), trigger objects are not set
   * initially and can be set later using setTriggerObject method.
   *
   * Optional (window.document by default).
   *
   * Requires triggerEvent to be set.
   */
  this._config.triggerObject = null;

  /**
   * top, right, bottom, left config options [string].
   * If top, right, bottom or left options are set, top_parent div will be
   * absolute positioned. Their values will be assigned to corresponding
   * CSS properties of top_parent div.
   *
   * You should set either top or bottom and right or left options for
   * drag and scroll menus instead of putting it inside absolute positioned div.
   * Otherwise menu can be wrong positioned.
   */
  this._config.top = null;    // menu initial top offset
  this._config.right = null;  // menu initial right offset
  this._config.bottom = null; // menu initial bottom offset
  this._config.left = null;   // menu initial left offset

  // User Option overrides
  if (typeof objUserConfig != "undefined") {
    for (var strConfigOption in objUserConfig) {
      //defaults are defined above
      if (typeof this._config[strConfigOption] == "undefined") {
        //unknown config parameter. Issue an error.
        alert("Error:Menu " + this._el + " has invalid parameter --" +
         strConfigOption + ":" + objUserConfig[strConfigOption]);
      } else {
        this._config[strConfigOption] = objUserConfig[strConfigOption];
      }
    }
  }

  // Apply theme
  if (this._config.theme != '') {
    var link = document.createElement('link');
    link.setAttribute('rel', 'stylesheet');
    link.setAttribute('type', 'text/css');
    link.setAttribute('href', '../themes/' + this._config.theme + '_2menus.css');
    document.getElementsByTagName('head')[0].appendChild(link);
  }

  /*
   * Current trigger object that launched menu (menu can be attached to several
   * objects).
   */
  this.triggerObject = null;
  /*
   * Arguments received from current trigger object. Those arguments can be
   * accessed from external script, e.g. to determine, which cell of the grid
   * was clicked, etc.
   */
  this.triggerArgs = null;

  this.animations = [];
  this._menuMode = true;
  this.initTree();
  this.openMenus = [];
  this.clickDone = false;

  // Setup triggers
  if (this.config.triggerEvent) {
    this.setTriggerObject(this.config.triggerObject || window.document);
    var self = this;
    // Hide menu on click
    Zapatec.Utils.addEvent(window.document, 'mouseup',
      function(ev) {
        setTimeout(function() {self.hideMenu()}, 50);
        return Zapatec.Utils.stopEvent(ev);
      }
    );
    // Prevent hiding on click inside menu
    Zapatec.Utils.addEvent(this.top_parent, 'mouseup',
      function(ev) {
        return Zapatec.Utils.stopEvent(ev);
      }
    );
    // Hide menu on ESC
    Zapatec.Utils.addEvent(window.document, 'keypress',
      function(ev) {
        ev || (ev = window.event);
        if (ev.keyCode == 27) {
          for (var i = 0; i < Zapatec.Menu.selectedItemsStack.length; i++) {
            if (Zapatec.MenuTree.all[Zapatec.Menu.selectedItemsStack[i].__zp_tree] == self) {
              return;
            }
          }
          // No more selected items in this menu
          self.hideMenu();
        }
      }
    );
  } else {
    // Dragging and scrolling can't work correctly together with triggers
    if (this.config.scrollWithWindow) {
      Zapatec.ScrollWithWindow.register(this.trees[this._el]);
    }
    if (this.config.drag) {
      var self = this;
      self.dragging = false;
      Zapatec.Utils.addEvent(window.document, "mousedown",
        function(ev) { return Zapatec.Menu.dragStart(ev, self) });
      Zapatec.Utils.addEvent(window.document, "mousemove",
        function(ev) { return Zapatec.Menu.dragMove(ev, self) });
      Zapatec.Utils.addEvent(window.document, "mouseup",
        function(ev) { return Zapatec.Menu.dragEnd(ev, self) });
    }
  }

  // Enforce animation mixing rules: fade + any 1 other.
  if (this._config.fade)
    this.addAnimation('fade');

  if (this._config.slide)
    this.addAnimation('slide');
  else if (this._config.glide)
    this.addAnimation('glide');
  else if (this._config.wipe)
    this.addAnimation('wipe');
  else if (this._config.unfurl)
    this.addAnimation('unfurl');
};

/**
 * \internal Holds reference to menu object that is currently on top. When menu
 * is mouseovered, its top_parent zIndex is changed to max to put it over the
 * rest of elements. This variable is needed to be able to restore zIndex of
 * previous top menu.
 */
Zapatec.Menu.onTop = null;

/**
 * \internal Restores zIndex of this menu.
 */
Zapatec.Menu.prototype.restoreZIndex = function() {
  this.top_parent.style.zIndex = this.config.zIndex;
  Zapatec.Menu.onTop = null;
};

/**
 * \internal Puts this menu on top.
 */
Zapatec.Menu.prototype.putOnTop = function() {
  // Restore zIndex of previous top menu
  var objOnTop = Zapatec.Menu.onTop;
  if (objOnTop) {
    objOnTop.restoreZIndex();
  }
  // Put this menu over the rest of elements
  // Max zIndex in IE and FF: 10737418239, in Opera: 2147483583
  this.top_parent.style.zIndex = 2147483583;
  Zapatec.Menu.onTop = this;
};

/**
 * Sets additional trigger object or several trigger objects at once.
 *
 * \param triggerObject [string or object]
 * element id [string] ||
 * HTMLElement object [object] ||
 * [
 *   element id [string] ||
 *   HTMLElement object [object] ||
 *   {
 *     triggerObject: element id [string] || HTMLElement object [object],
 *     triggerArgs: any args that should be available to external scripts [any]
 *   },
 *   ...
 * ]
 */
Zapatec.Menu.prototype.setTriggerObject = function(triggerObject) {
  if (!this.config.triggerEvent) {
    // This method is applicable only to trigger menus
    return;
  }
  var strTriggerEvent = this.config.triggerEvent;
  var strTriggerKey = this.config.triggerKey;
  // Get trigger objects
  var objTriggerElements = [];
  if (triggerObject) {
    if (typeof triggerObject == 'string') {
      // Element id
      var objElement = document.getElementById(triggerObject);
      if (objElement) {
        objTriggerElements.push({
          triggerObject: objElement,
          triggerArgs: null
        });
      }
    } else if (typeof triggerObject == 'object') {
      if (triggerObject == window.document ||
       typeof triggerObject.length == 'undefined') {
        // HTMLElement object
        objTriggerElements.push({
          triggerObject: triggerObject,
          triggerArgs: null
        });
      } else {
        // Array
        for (var iObj = 0; iObj < triggerObject.length; iObj++) {
          var triggerElement = triggerObject[iObj];
          if (triggerElement) {
            if (typeof triggerElement == 'string') {
              // Element id
              var objElement = document.getElementById(triggerElement);
              if (objElement) {
                objTriggerElements.push({
                  triggerObject: objElement,
                  triggerArgs: null
                });
              }
            } else if (typeof triggerElement == 'object') {
              if (typeof triggerElement.triggerObject != 'undefined' &&
               typeof triggerElement.triggerArgs != 'undefined') {
                // Arguments passed
                if (typeof triggerElement.triggerObject == 'string') {
                  // Element id
                  var objElement =
                   document.getElementById(triggerElement.triggerObject);
                  if (objElement) {
                    objTriggerElements.push({
                      triggerObject: objElement,
                      triggerArgs: triggerElement.triggerArgs
                    });
                  }
                } else if (typeof triggerElement.triggerObject == 'object') {
                  // HTMLElement object
                  objTriggerElements.push(triggerElement);
                }
              } else {
                // HTMLElement object
                objTriggerElements.push({
                  triggerObject: triggerElement,
                  triggerArgs: null
                });
              }
            }
          }
        }
      }
    }
  }
  if (objTriggerElements.length == 0) {
    // Nothing to set up
    return;
  }
  // Set up trigger objects
  var self = this;
  if (strTriggerEvent == 'mousedown' || strTriggerEvent == 'mouseup' ||
   strTriggerEvent == 'click') {
    // Mouse trigger
    // Need this function to be able to set current trigger object and arguments
    var funcSetupTriggerEvent = function(objTriggerElement) {
      Zapatec.Utils.addEvent(objTriggerElement.triggerObject, 'mouseup',
        function(objEvent) {
          objEvent || (objEvent = window.event);
          // Get mouse position
          var posX = objEvent.pageX ||
           objEvent.clientX + window.document.body.scrollLeft || 0;
          var posY = objEvent.pageY ||
           objEvent.clientY + window.document.body.scrollTop || 0;
          // Get mouse button
          var button;
          if (objEvent.button) {
            button = objEvent.button;
          } else {
            button = objEvent.which;
          }
          if (window.opera) {
            // Button 1 is used for both showing and hiding menu in Opera
            // because Opera doesn't allow to disable context menu
            if (button == 1 && self.top_parent.style.display == 'none') {
              setTimeout(function() {
                // Set current trigger object
                self.triggerObject = objTriggerElement.triggerObject;
                // Set arguments received from current trigger object
                self.triggerArgs = objTriggerElement.triggerArgs;
                // Show menu
                self.popupMenu(posX, posY);
              }, 100);
              return Zapatec.Utils.stopEvent(objEvent);
            }
          } else {
            if (strTriggerKey == 'both' ||
             (strTriggerKey == 'left' && button == 1) ||
             ((!strTriggerKey || strTriggerKey == 'right') && button > 1)) {
              setTimeout(function() {
                // Set current trigger object
                self.triggerObject = objTriggerElement.triggerObject;
                // Set arguments received from current trigger object
                self.triggerArgs = objTriggerElement.triggerArgs;
                // Show menu
                self.popupMenu(posX, posY);
              }, 100);
              return Zapatec.Utils.stopEvent(objEvent);
            }
          }
        }
      );
    };
    for (var iEl = 0; iEl < objTriggerElements.length; iEl++) {
      funcSetupTriggerEvent(objTriggerElements[iEl])
    }
    // Disable context menu
    window.document.oncontextmenu = function() {return false};
  } else if (strTriggerEvent == 'keydown' || strTriggerEvent == 'keyup' ||
   strTriggerEvent == 'keypress') {
    // Keyboard trigger
    // Need this function to be able to set current trigger object and arguments
    var funcSetupTriggerEvent = function(objTriggerElement) {
      Zapatec.Utils.addEvent(objTriggerElement.triggerObject, 'keydown',
        function(objEvent) {
          objEvent || (objEvent = window.event);
          if (objEvent.keyCode == strTriggerKey) {
            // Set current trigger object
            self.triggerObject = objTriggerElement.triggerObject;
            // Set arguments received from current trigger object
            self.triggerArgs = objTriggerElement.triggerArgs;
            // Show menu
            self.popupMenu();
            return Zapatec.Utils.stopEvent(objEvent);
          }
        }
      );
    };
    for (var iEl = 0; iEl < objTriggerElements.length; iEl++) {
      funcSetupTriggerEvent(objTriggerElements[iEl])
    }
  }
};

//Constants
Zapatec.Menu.MOUSEOUT = 0;
Zapatec.Menu.MOUSEOVER = 1;
Zapatec.Menu.CLICK = 2;

/**
 * Collection of animations (function references).
 * These are called to progressively style the DOM elements as menus show
 * and hide. They do not have to set item visibility, but may want to set DOM
 * properties like clipping, opacity and position to create custom effects.
 *
 * \param ref [object] HTMLElement object that contains the menu items.
 * \param counter [number] an animation progress value, from 0 (start) to
 * 100 (end).
 */
Zapatec.Menu.animations = {};

Zapatec.Menu.animations.fade = function(ref, counter) {
  var f = ref.filters, done = (counter==100);
  if (f) {
    if (!done && ref.style.filter.indexOf("alpha") == -1) {
      ref.style.filter += ' alpha(opacity=' + counter + ')';
    }
    else if (f.length && f.alpha) with (f.alpha) {
      if (done) enabled = false;
      else { opacity = counter; enabled=true }
    }
  }
  else {
    ref.style.opacity = ref.style.MozOpacity = counter/100.1;
  }
};

Zapatec.Menu.animations.slide = function(ref, counter) {
  var cP = Math.pow(Math.sin(Math.PI*counter/200),0.75);
  var noClip = ((window.opera || navigator.userAgent.indexOf('KHTML') > -1) ?
    '' : 'rect(auto, auto, auto, auto)');
  if (typeof ref.__zp_origmargintop == 'undefined') {
    ref.__zp_origmargintop = ref.style.marginTop;
  }
  ref.style.marginTop = (counter==100) ?
    ref.__zp_origmargintop : '-' + (ref.offsetHeight*(1-cP)) + 'px';
  ref.style.clip = (counter==100) ? noClip :
    'rect(' + (ref.offsetHeight*(1-cP)) + 'px, ' + ref.offsetWidth +
    'px, ' + ref.offsetHeight + 'px, 0)';
};

Zapatec.Menu.animations.glide = function(ref, counter) {
  var cP = Math.pow(Math.sin(Math.PI*counter/200),0.75);
  var noClip = ((window.opera || navigator.userAgent.indexOf('KHTML') > -1) ?
    '' : 'rect(auto, auto, auto, auto)');
  ref.style.clip = (counter==100) ? noClip :
    'rect(0, ' + ref.offsetWidth + 'px, ' + (ref.offsetHeight*cP) + 'px, 0)';
};

Zapatec.Menu.animations.wipe = function(ref, counter) {
  var noClip = ((window.opera || navigator.userAgent.indexOf('KHTML') > -1) ?
    '' : 'rect(auto, auto, auto, auto)');
  ref.style.clip = (counter==100) ? noClip :
    'rect(0, ' + (ref.offsetWidth*(counter/100)) + 'px, ' +
    (ref.offsetHeight*(counter/100)) + 'px, 0)';
};

Zapatec.Menu.animations.unfurl = function(ref, counter) {
  var noClip = ((window.opera || navigator.userAgent.indexOf('KHTML') > -1) ?
    '' : 'rect(auto, auto, auto, auto)');
  if (counter <= 50) {
    ref.style.clip = 'rect(0, ' + (ref.offsetWidth*(counter/50)) +
      'px, 10px, 0)';
  }
  else if (counter < 100) {
    ref.style.clip =  'rect(0, ' + ref.offsetWidth + 'px, ' +
      (ref.offsetHeight*((counter-50)/50)) + 'px, 0)';

  }
  else {
    ref.style.clip = noClip;
  }
};

/**
 * Called with the name of an animation (in the Zapatec.Menu.animations[] array)
 * to apply that animation to this menu object.
 *
 * \param animation [string] the name of the animation.
 */
Zapatec.Menu.prototype.addAnimation = function(animation) {
 this.animations[this.animations.length] = Zapatec.Menu.animations[animation];
};

/**
 * \internal Sets the display/visibility of a specified menu, calling
 * defined animation functions and repeatedly calling itself.
 *
 * \param menu [object] HTMLElement object.
 * \param show [boolean] true shows, false hides.
 */
Zapatec.Menu.prototype.treeSetDisplay = function(menu, show) {
  // First pass on menu creation: just hide.
  if (!menu.__zp_initialised) {
    menu.style.visibility = 'hidden';
    if (menu.__zp_dropshadow) {
      menu.__zp_dropshadow.style.visibility = 'hidden';
    }
    menu.__zp_initialised = true;
    return;
  }

  var treeId = menu.__zp_tree || menu.__zp_menu.firstChild.__zp_tree;
  var tree;
  if (treeId) {
    tree = Zapatec.MenuTree.all[treeId];
  }
  if (!tree) {
    return;
  }
  if (tree.animations.length == 0) {
    if (show) {
      menu.style.visibility = 'inherit';
      if (menu.__zp_dropshadow) {
        menu.__zp_dropshadow.style.visibility = 'inherit';
      }
    } else {
      menu.style.visibility = 'hidden';
      if (menu.__zp_dropshadow) {
        menu.__zp_dropshadow.style.visibility = 'hidden';
      }
    }
    return;
  }

  // Otherwise animate.
  menu.__zp_anim_timer |= 0;
  clearTimeout(menu.__zp_anim_timer);
  menu.__zp_anim_counter |= 0;

  if (show && !menu.__zp_anim_counter) {
    menu.style.visibility = 'inherit';
    if (menu.__zp_dropshadow) {
      menu.__zp_dropshadow.style.visibility = 'inherit';
    }
  }

  for (var ii = 0; ii < tree.animations.length; ii++) {
    tree.animations[ii](menu, menu.__zp_anim_counter);
    if (menu.__zp_dropshadow
     && tree.animations[ii] != Zapatec.Menu.animations.fade) {
      tree.animations[ii](menu.__zp_dropshadow, menu.__zp_anim_counter);
    }
  }

  // Iterate
  if (!(show && menu.__zp_anim_counter == 100)) { // Prevent infinite loop
    menu.__zp_anim_counter += tree.config.animSpeed * (show ? 1 : -1);
    if (menu.__zp_anim_counter > 100) {
      // Correction to show menu properly
      menu.__zp_anim_counter = 100;
      menu.__zp_anim_timer = setTimeout(function() {
        tree.treeSetDisplay(menu, show);
      }, 50);
    } else if (menu.__zp_anim_counter <= 0) {
      // Hide menu
      menu.__zp_anim_counter = 0;
      menu.style.visibility = 'hidden';
      if (menu.__zp_dropshadow) {
        menu.__zp_dropshadow.style.visibility = 'hidden';
      }
    } else {
      // Next iteration
      menu.__zp_anim_timer = setTimeout(function() {
        tree.treeSetDisplay(menu, show);
      }, 50);
    }
  }
};

// GLOBAL EVENT HANDLERS (to workaround the stupid Microsoft memory leak)

/**
 * \internal Global event handler that gets called when a tree item is
 * moused over.
 */
Zapatec.Menu.onItemMouseOver = function() {
  // Loop up the DOM, dispatch event to correct source item.
  var item = this, tree = null;
  while (item && item != document.body) {
    var t_id = item.__zp_tree || item.firstChild.__zp_tree;
    if (t_id) tree = Zapatec.MenuTree.all[t_id];
    var itemClassName = item.className;
    if (/zpMenu-item/.test(itemClassName) && !/zpMenu-item-hr/.test(itemClassName)) {
      tree.itemMouseHandler(item.__zp_item, Zapatec.Menu.MOUSEOVER);
    }
    item = tree && item.__zp_treeid ?
      tree.items[item.__zp_item] : item.parentNode;
  }
  return true; // To make tooltips work in Opera
};

/**
 * \internal Global event handler that gets called when a tree item is
 * moused out.
 */
Zapatec.Menu.onItemMouseOut = function() {
  var item = this, tree = null;
  while (item && item != document.body) {
    var t_id = item.__zp_tree || item.firstChild.__zp_tree;
    if (t_id) tree = Zapatec.MenuTree.all[t_id];
    var itemClassName = item.className;
    if (
     /zpMenu-item/.test(itemClassName) && !/zpMenu-item-hr/.test(itemClassName) &&
     // Top item was not unselected with Esc button
     !(/zpMenu-level-1/.test(itemClassName) && !/zpMenu-item-selected/.test(itemClassName))
    ) {
      tree.itemMouseHandler(item.__zp_item, Zapatec.Menu.MOUSEOUT);
    }
    item = tree && item.__zp_treeid ?
      tree.items[item.__zp_item] : item.parentNode;
  }
  return false;
};

/**
 * \internal Global event handler that gets called when a tree item is clicked,
 * to make the whole item clickable.
 */
Zapatec.Menu.onItemClick = function(ev) {
  var item = this;
  if (!/zpMenuDisabled/.test(item.className)) {
    while (item && item != document.body) {
      if (item.nodeName && item.nodeName.toLowerCase() == 'a') {
        return true;
      }
      if (/zpMenu-item/.test(item.className)) {
        var self = Zapatec.MenuTree.all[item.__zp_tree];
        // Show-on-click mode
        if (self.config.onClick && item.__zp_subtree &&
          (/zpMenu-top/.test(self.trees[item.__zp_parent].className))) {
            self.itemMouseHandler(item.__zp_item, Zapatec.Menu.CLICK);
            return Zapatec.Utils.stopEvent(ev);
        }
        // Otherwise navigate the page
        var itemLink = item.getElementsByTagName('a');
        var itemInput = item.getElementsByTagName('input');
        var itemSelect = item.getElementsByTagName('select');
        if (itemLink && itemLink.item(0)
         && itemLink.item(0).getAttribute('href')
         && itemLink.item(0).getAttribute('href') != '#'
         && itemLink.item(0).getAttribute('href') != window.document.location.href + '#'
         && itemLink.item(0).getAttribute('href') != 'javascript:void(0)') {
          var href = itemLink.item(0).getAttribute('href');
          var target = itemLink.item(0).getAttribute('target');
          if (self.config.rememberPath || self.config.pathCookie != '__zp_item') {
            // Save path in cookies
            Zapatec.Utils.writeCookie(self.config.pathCookie, item.__zp_item);
          }
          try {
            if (target) {
              window.open(href, target);
            } else {
              window.location.href = href; // may raise exception in Mozilla
            }
          } catch(e) {};
          if (self.config.triggerEvent) {
            self.hideMenu();
          }
        } else if (itemInput && itemInput.item(0)) {
          var inp = itemInput.item(0);
          var type = inp.getAttribute('type');
          if (type == 'checkbox') {
            if (inp.checked) {
              inp.checked = false;
            } else {
              inp.checked = true;
            }
          } else if (type == 'radio') {
            inp.checked = true;
          }
        } else if (itemSelect && itemSelect.item(0)) {
          return true; // Pass through
        } else if (item.__zp_subtree) {
          self.itemMouseHandler(item.__zp_item, Zapatec.Menu.CLICK);
        } else if (self.config.triggerEvent) {
          self.hideMenu();
        }
        return Zapatec.Utils.stopEvent(ev);
      }
      item = item.parentNode;
    }
  }
  return false;
};

/**
 * \internal Called from the mouse over/out event handlers to process the
 * mouse event and correctly manage timers.
 *
 * \param item_id [string] the item ID.
 * \param type [number] 0 = mouseout, 1 = mouseover, 2 = click.
 */
Zapatec.Menu.prototype.itemMouseHandler = function(item_id, type) {
  if (type) {
    // Mouseover or click
    // Put this menu on top
    this.putOnTop();
  } else {
    // Mouseout
    // Restore zIndex
    this.restoreZIndex();
  }

  var item = this.items[item_id];
  if (!item) return;
  var menu = this._getTree(item.__zp_parent);

  // Record an item as lit/shown, and dim/hide any previously lit items.
  if (menu && menu.__zp_activeitem != item_id) {
    if (menu.__zp_activeitem) {
      var lastItem = this.items[menu.__zp_activeitem];
      clearTimeout(lastItem.__zp_dimtimer);
      clearTimeout(lastItem.__zp_mousetimer);
      Zapatec.Menu.unselectItem(lastItem);
      // Threading bugfix for some menus remaining visible.
      if (lastItem.__zp_state) this.toggleItem(lastItem.__zp_item, false);
    }
    menu.__zp_activeitem = item_id;
    Zapatec.Menu.selectItem(item);
  }

  // Set a timer to dim this item when the whole menu hides.
  clearTimeout(item.__zp_dimtimer);
  if (type == Zapatec.Menu.MOUSEOUT) {
    item.__zp_dimtimer = setTimeout(function() {
      Zapatec.Menu.unselectItem(item);
      if (menu.__zp_activeitem == item_id) menu.__zp_activeitem = '';
    }, this.config.hideDelay);
  }

  // Stop any pending show/hide action.
  clearTimeout(item.__zp_mousetimer);
  // Check if this is a click on a first-level menu item.
  if (this.config.onClick && !this.clickDone) {
    if (/zpMenu-top/.test(this.trees[item.__zp_parent].className) &&
      (type == Zapatec.Menu.MOUSEOVER)) return;
    // Set the flag that enables further onmouseover activity.
    if (type == Zapatec.Menu.CLICK) this.clickDone = true;
  }

  // Setup show/hide timers.
  if (!item.__zp_state && type)
  {
    item.__zp_mousetimer = setTimeout('Zapatec.MenuTree.all["' +
      item.__zp_tree + '"].itemShow("' + item.__zp_item + '")',
      (this.config.showDelay || 1));
  }
  else if (item.__zp_state && !type)
  {
    item.__zp_mousetimer = setTimeout('Zapatec.MenuTree.all["' +
      item.__zp_tree + '"].itemHide("' + item.__zp_item + '")',
      (this.config.hideDelay || 1));
  }
};

/**
 * \internal Called from the itemMouseHandler() after a timeout;
 * positions and shows a designated item's branch of the tree.
 *
 * \param item_id [string] item ID to show.
 */
Zapatec.Menu.prototype.itemShow = function(item_id) {
  var item = this.items[item_id];
  if (/zpMenuDisabled/.test(item.className)) {
    return;
  }
  var subMenu = this._getTree(item.__zp_subtree);
  if (!subMenu) {
    return;
  }
  var parMenu = this._getTree(item.__zp_parent);
  // Setting visible here works around MSIE bug where
  // offsetWidth/Height are initially zero.
  if (!subMenu.offsetHeight) {
    subMenu.style.visibility = 'visible';
  }

  // In Opera z-index is not inherited by default
  if (subMenu.style.zIndex === '') {
    subMenu.style.zIndex = 'inherit';
  }

  var subMenuBorderLeft, subMenuBorderTop;
  if (typeof subMenu.clientLeft != 'undefined') { // IE & Opera
    subMenuBorderLeft = subMenu.clientLeft;
    subMenuBorderTop = subMenu.clientTop;
  } else { // Mozilla
    subMenuBorderLeft = (subMenu.offsetWidth - subMenu.clientWidth) / 2;
    subMenuBorderTop = (subMenu.offsetHeight - subMenu.clientHeight) / 2;
  }

  var fc = subMenu.firstChild;
  var subMenuMarginLeft = fc.offsetLeft;
  var subMenuMarginTop = fc.offsetTop;

  // Acquire browser dimensions
  var scrollX = window.pageXOffset || document.body.scrollLeft ||
    document.documentElement.scrollLeft || 0;
  var scrollY = window.pageYOffset || document.body.scrollTop ||
    document.documentElement.scrollTop || 0;
  var objWindowSize = Zapatec.Utils.getWindowSize();
  var winW = objWindowSize.width;
  var winH = objWindowSize.height;

  // Adjust sub-menu width and height
  if (!subMenu.style.width || !subMenu.style.height) {
    var maxHeight = winH - 7;
    if (subMenu.offsetHeight > maxHeight) {
      // Need scrolling
      fc.__zp_first = fc.firstChild;
      fc.__zp_last = fc.lastChild;
      var objUp = Zapatec.Utils.createElement("div");
      objUp.__zp_tree = fc.firstChild.__zp_tree;
      objUp.className = 'zpMenuScrollUpInactive';
      objUp.__zp_mouseover = false;
      objUp.__zp_timer = null;
      // Up arrow handler
      var funcMoveUp = function() {
        var objContainer = objUp.parentNode;
        var iContainerHeight = objContainer.parentNode.clientHeight;
        var objUpArrow = objContainer.firstChild;
        var objDownArrow = objContainer.lastChild;
        // Check if we can move up
        if (objContainer.__zp_first.previousSibling != objUpArrow) {
          // Show first item
          if (objContainer.__zp_first.style.height) {
            // Partly hidden
            objContainer.__zp_first.style.height = '';
            objContainer.__zp_first.style.overflow = '';
          } else {
            // Completely hidden
            objContainer.__zp_first = objContainer.__zp_first.previousSibling;
            objContainer.__zp_first.style.display = 'block';
          }
          var iNewHeight = objContainer.offsetHeight;
          // Hide last item
          while (iNewHeight > iContainerHeight) {
            objContainer.__zp_last.style.display = 'none';
            if (objContainer.__zp_last.style.height) {
              objContainer.__zp_last.style.height = '';
              objContainer.__zp_last.style.overflow = '';
            }
            objContainer.__zp_last = objContainer.__zp_last.previousSibling;
            iNewHeight = objContainer.offsetHeight;
          }
          // Correct height
          var iSpace = iContainerHeight - iNewHeight;
          if (iSpace > 0) {
            // Return last item back and cut it off
            objContainer.__zp_last = objContainer.__zp_last.nextSibling;
            objContainer.__zp_last.style.display = 'block';
            var iItemHeight = iSpace - (objContainer.__zp_last.offsetHeight -
             objContainer.__zp_last.clientHeight);
            if (iItemHeight >= 0) {
              objContainer.__zp_last.style.display = 'none';
              objContainer.__zp_last.style.height = iItemHeight + 'px';
              objContainer.__zp_last.style.overflow = 'hidden';
              objContainer.__zp_last.style.display = 'block';
              iNewHeight = objContainer.offsetHeight;
              // Check height
              if (iNewHeight != iContainerHeight) {
                // May be non-standards-compliant mode
                iItemHeight -= iNewHeight - iContainerHeight;
                if (iItemHeight > 0) {
                  objContainer.__zp_last.style.height = iItemHeight + 'px';
                } else {
                  objContainer.__zp_last.style.display = 'none';
                  objContainer.__zp_last.style.height = '';
                  objContainer.__zp_last.style.overflow = '';
                  objContainer.__zp_last = objContainer.__zp_last.previousSibling;
                }
              }
            } else {
              objContainer.__zp_last.style.display = 'none';
              objContainer.__zp_last = objContainer.__zp_last.previousSibling;
            }
          }
          // Show down arrow
          objDownArrow.className = 'zpMenuScrollDownActive';
          // Hide up arrow if needed
          if (objContainer.__zp_first.previousSibling == objUpArrow) {
            objUpArrow.className = 'zpMenuScrollUpInactive';
          }
          // Continue scrolling
          if (objUp.__zp_timer) clearTimeout(objUp.__zp_timer);
          if (objUp.__zp_mouseover) {
            objUp.__zp_timer = setTimeout(funcMoveUp, 50);
          }
        }
        return true;
      };
      objUp.onmouseover = function() {
        objUp.__zp_mouseover = true;
        return funcMoveUp();
      }
      objUp.onmouseout = function() {
        objUp.__zp_mouseover = false;
        if (objUp.__zp_timer) {
          clearTimeout(objUp.__zp_timer);
          objUp.__zp_timer = null;
        }
      };
      fc.insertBefore(objUp, fc.firstChild);
      var objDown = Zapatec.Utils.createElement("div");
      objDown.__zp_tree = fc.firstChild.__zp_tree;
      objDown.className = 'zpMenuScrollDownActive';
      objDown.__zp_mouseover = false;
      objDown.__zp_timer = null;
      // Down arrow handler
      var funcMoveDown = function() {
        var objContainer = objDown.parentNode;
        var iContainerHeight = objContainer.parentNode.clientHeight;
        var objUpArrow = objContainer.firstChild;
        var objDownArrow = objContainer.lastChild;
        // Check if we can move down
        if (objContainer.__zp_last.nextSibling != objDownArrow) {
          // Show last item
          if (objContainer.__zp_last.style.height) {
            // Partly hidden
            objContainer.__zp_last.style.height = '';
            objContainer.__zp_last.style.overflow = '';
          } else {
            // Completely hidden
            objContainer.__zp_last = objContainer.__zp_last.nextSibling;
            objContainer.__zp_last.style.display = 'block';
          }
          var iNewHeight = objContainer.offsetHeight;
          // Hide first item
          while (iNewHeight > iContainerHeight) {
            objContainer.__zp_first.style.display = 'none';
            if (objContainer.__zp_first.style.height) {
              objContainer.__zp_first.style.height = '';
              objContainer.__zp_first.style.overflow = '';
            }
            objContainer.__zp_first = objContainer.__zp_first.nextSibling;
            iNewHeight = objContainer.offsetHeight;
          }
          // Correct height
          var iSpace = iContainerHeight - iNewHeight;
          if (iSpace > 0) {
            // Return first item back and cut it off
            objContainer.__zp_first = objContainer.__zp_first.previousSibling;
            objContainer.__zp_first.style.display = 'block';
            var iItemHeight = iSpace - (objContainer.__zp_first.offsetHeight -
             objContainer.__zp_first.clientHeight);
            if (iItemHeight > 0) {
              objContainer.__zp_first.style.display = 'none';
              objContainer.__zp_first.style.height = iItemHeight + 'px';
              objContainer.__zp_first.style.overflow = 'hidden';
              objContainer.__zp_first.style.display = 'block';
              iNewHeight = objContainer.offsetHeight;
              // Check height
              if (iNewHeight != iContainerHeight) {
                // May be non-standards-compliant mode
                iItemHeight -= iNewHeight - iContainerHeight;
                if (iItemHeight > 0) {
                  objContainer.__zp_first.style.height = iItemHeight + 'px';
                } else {
                  objContainer.__zp_first.style.display = 'none';
                  objContainer.__zp_first.style.height = '';
                  objContainer.__zp_first.style.overflow = '';
                  objContainer.__zp_first = objContainer.__zp_first.nextSibling;
                }
              }
            } else {
              objContainer.__zp_first.style.display = 'none';
              objContainer.__zp_first = objContainer.__zp_first.nextSibling;
            }
          }
          // Show up arrow
          objUpArrow.className = 'zpMenuScrollUpActive';
          // Hide down arrow if needed
          if (objContainer.__zp_last.nextSibling == objDownArrow) {
            objDownArrow.className = 'zpMenuScrollDownInactive';
          }
          // Continue scrolling
          if (objDown.__zp_timer) clearTimeout(objDown.__zp_timer);
          if (objDown.__zp_mouseover) {
            objDown.__zp_timer = setTimeout(funcMoveDown, 50);
          }
        }
        return true;
      };
      objDown.onmouseover = function() {
        objDown.__zp_mouseover = true;
        return funcMoveDown();
      }
      objDown.onmouseout = function() {
        objDown.__zp_mouseover = false;
        if (objDown.__zp_timer) {
          clearTimeout(objDown.__zp_timer);
          objDown.__zp_timer = null;
        }
      };
      fc.appendChild(objDown);
      var lc = fc.__zp_last;
      while (subMenu.offsetHeight > maxHeight) {
        lc.style.display = 'none';
        lc = lc.previousSibling;
        fc.__zp_last = lc;
      }
    }
    var width = shadowWidth = fc.offsetWidth;
    if (typeof subMenu.clientLeft != 'undefined') { // IE & Opera
      width += subMenuBorderLeft * 2 + subMenuMarginLeft * 2;
      shadowWidth = width;
    } else if (subMenu.__zp_dropshadow) { // Mozilla
      shadowWidth += subMenuBorderLeft * 2 + subMenuMarginLeft * 2;
    }
    var height = shadowHeight = fc.offsetHeight;
    if (typeof subMenu.clientTop != 'undefined') { // IE & Opera
      height += subMenuBorderTop * 2 + subMenuMarginTop * 2;
      shadowHeight = height;
    } else if (subMenu.__zp_dropshadow) { // Mozilla
      shadowHeight += subMenuBorderTop * 2 + subMenuMarginTop * 2;
    }
    subMenu.style.width = width + 'px';
    subMenu.style.height = height + 'px';
    if (subMenu.__zp_dropshadow) {
      subMenu.__zp_dropshadow.style.width = shadowWidth + 'px';
      subMenu.__zp_dropshadow.style.height = shadowHeight + 'px';
    }
    fc.style.position = 'absolute';
    fc.style.left = fc.offsetLeft + 'px';
    fc.style.top = fc.offsetTop + 'px';
    fc.style.visibility = 'inherit';
  }

  // Calculate new menu position & check document boundaries.
  var newLeft = 0, newTop = 0;
  var menuPos = Zapatec.Utils.getAbsolutePos(parMenu);
  if ((/zpMenu-top/.test(this.trees[item.__zp_parent].className)) && (!(this.config.vertical))) {
    // Drop Down menus
    newLeft = item.offsetLeft;
    newTop = item.offsetHeight;
    // Adjust menu direction if it will display outside visible area
    if (menuPos.x + newLeft + subMenu.offsetWidth + subMenuMarginLeft + 7 > scrollX + winW) {
      newLeft += item.offsetWidth - subMenu.offsetWidth - subMenuMarginLeft;
      if (subMenu.__zp_dropshadow) newLeft -= 6;
    } else {
      newLeft -= subMenuBorderLeft;
    }
    if (menuPos.y + newTop + subMenu.offsetHeight + subMenuMarginTop + 7 > scrollY + winH) {
      newTop = -subMenu.offsetHeight;
      if (subMenu.__zp_dropshadow) newTop -= 5;
    }
  } else {
    // Vertical menus
    newLeft = item.offsetWidth;
    newTop = item.offsetTop;
    // Adjust menu direction if it will display outside visible area
    if (menuPos.x + newLeft + subMenu.offsetWidth + subMenuMarginLeft + 7 > scrollX + winW) {
      newLeft = -subMenu.offsetWidth;
      if (subMenu.__zp_dropshadow) newLeft -= 5;
    }
    if (menuPos.y + newTop + subMenu.offsetHeight + subMenuMarginTop + 7 > scrollY + winH) {
      newTop -= subMenu.offsetHeight - item.offsetHeight;
      if (subMenu.__zp_dropshadow) newTop -= 5;
    } else {
      newTop -= subMenuBorderTop;
    }
  }
  if (menuPos.x + newLeft < 0) {
    newLeft = 0 - menuPos.x;
  }
  if (menuPos.y + newTop < 0) {
    newTop = 0 - menuPos.y;
  }
  subMenu.style.left = newLeft + 'px';
  subMenu.style.top = newTop + 'px';
  if (subMenu.__zp_dropshadow) {
    subMenu.__zp_dropshadow.style.left = (newLeft + 5) + 'px';
    subMenu.__zp_dropshadow.style.top = (newTop + 5) + 'px';
  }

  // Apply MSIE 5.5+ Select Box fix last, so it corrects the dropshadow.
  if (Zapatec.is_ie && !Zapatec.is_ie5) {
    if (!subMenu.__zp_wch) {
      subMenu.__zp_wch = Zapatec.Utils.createWCH(subMenu);
    }
    subMenu.__zp_wch.style.zIndex = -1;
    if (this._config.dropShadow) {
      Zapatec.Utils.setupWCH(subMenu.__zp_wch, -subMenuBorderLeft, -subMenuBorderTop, subMenu.offsetWidth + 6, subMenu.offsetHeight + 5);
    } else {
      Zapatec.Utils.setupWCH(subMenu.__zp_wch, -subMenuBorderLeft, -subMenuBorderTop, subMenu.offsetWidth, subMenu.offsetHeight);
    }
  }

  this.toggleItem(item_id, true);
};

/**
 * \internal Called from the itemMouseHandler() after a timeout;
 * hides a designated item's branch of the tree.
 *
 * \param item_id [string] item ID to hide.
 */
Zapatec.Menu.prototype.itemHide = function(item_id) {
  var item = this.items[item_id];
  var subMenu = this._getTree(item.__zp_subtree);
  var parMenu = this._getTree(item.__zp_parent);
  if (subMenu) {
    this.toggleItem(item_id, false);
    parMenu.__zp_activeitem = '';
    subMenu.__zp_activeitem = '';
    // Go no further if some items are still expanded.
    for (var i in this.items) {
      if (this.items[i].__zp_state) return;
    }
    // Another click is necessary to activate menu again.
    this.clickDone = false;
  }
};

/**
 * \defgroup dndmove Drag'n'drop (move menu) functions
 *
 * Contains some functions that implement menu "drag'n'drop" facility which
 * allows one to move the menu around the browser's view.
 *
 */
//@{

/**
 * \internal Starts dragging the element.
 *
 * \param ev [object] Event object.
 * \param menu [object] Zapatec.Menu object.
 * \return [boolean] always true.
 */
Zapatec.Menu.dragStart = function (ev, menu) {
  ev || (ev = window.event);
  if (menu.dragging) {
    return true;
  }
  var rootMenu = menu.trees[menu._el];
  if (!(/(absolute|fixed)/).test(rootMenu.style.position)) {
    rootMenu.style.position = 'absolute';
    var pos = Zapatec.Utils.getAbsolutePos(rootMenu);
    rootMenu.style.left = pos.x + 'px';
    rootMenu.style.top = pos.y + 'px';
  }
  var testElm = ev.srcElement || ev.target;
  while (1) {
    if (testElm == rootMenu) break;
    else testElm = testElm.parentNode;
    if (!testElm) return true;
  }
  menu.dragging = true;
  var posX = ev.pageX || ev.clientX + window.document.body.scrollLeft || 0;
  var posY = ev.pageY || ev.clientY + window.document.body.scrollTop || 0;
  var L = parseInt(rootMenu.style.left) || 0;
  var T = parseInt(rootMenu.style.top) || 0;
  menu.xOffs = (posX - L);
  menu.yOffs = (posY - T);
  // Unregister from scroll
  if (menu.config.scrollWithWindow) {
    Zapatec.ScrollWithWindow.unregister(menu.trees[menu._el]);
  }
};

/**
 * \internal Called at mouseover and/or mousemove on document, this function
 * repositions the menu according to the current mouse position.
 *
 * \param ev [object] Event object.
 * \param menu [object] Zapatec.Menu object.
 * \return [boolean] always false.
 */
Zapatec.Menu.dragMove = function (ev, menu) {
  ev || (ev = window.event);
  var rootMenu = menu.trees[menu._el];
  if (!(menu && menu.dragging)) {
    return false;
  }
  var posX = ev.pageX || ev.clientX + window.document.body.scrollLeft || 0;
  var posY = ev.pageY || ev.clientY + window.document.body.scrollTop || 0;
  var st = rootMenu.style, L = posX - menu.xOffs, T = posY - menu.yOffs;
  st.left = L + "px";
  st.top = T + "px";
  //Zapatec.Utils.setupWCH(cal.WCH, L, T);
  return Zapatec.Utils.stopEvent(ev);
};

/**
 * \internal Gets called when the drag and drop operation is finished; thus, at
 * "onmouseup".
 *
 * \param ev [object] Event object.
 * \param menu [object] Zapatec.Menu object.
 */
Zapatec.Menu.dragEnd = function (ev, menu) {
  if (!menu) {
    return false;
  }
  if (menu.dragging) {
    menu.dragging = false;
    // Adjust menu position if it will display outside visible area
    var rootMenu = menu.trees[menu._el];
    var st = rootMenu.style, L = parseInt(st.left), T = parseInt(st.top);
    var scrollX = window.pageXOffset || document.body.scrollLeft ||
      document.documentElement.scrollLeft || 0;
    var scrollY = window.pageYOffset || document.body.scrollTop ||
      document.documentElement.scrollTop || 0;
    var objWindowSize = Zapatec.Utils.getWindowSize();
    var winW = objWindowSize.width;
    var winH = objWindowSize.height;
    if (L < 0) {
      st.left = '0px';
    } else if (L + rootMenu.offsetWidth > scrollX + winW) {
      st.left = scrollX + winW - rootMenu.offsetWidth + 'px';
    }
    if (T < 0) {
      st.top = '0px';
    } else if (T + rootMenu.offsetHeight > scrollY + winH) {
      st.top = scrollY + winH - rootMenu.offsetHeight + 'px';
    }
    // Restore to scroll
    if (menu.config.scrollWithWindow) {
      Zapatec.ScrollWithWindow.register(rootMenu);
    }
  }
};

//@}

/**
 * Disables item from an external script.
 *
 * Example:
 * \code
 * <ul id="myMenu">
 *  <li id="itemToDisable">Menu Item</li>
 * </ul>
 * <script type="text/javascript">
 *  var menu = new Zapatec.Menu('myMenu', {});
 *  menu.itemDisable('itemToDisable');
 * </script>
 * \endcode
 *
 * \param item_id [string] item ID to disable.
 */
Zapatec.Menu.prototype.itemDisable = function(item_id) {
  var item = this.items[item_id];
  if (item) {
    // item_id exists
    Zapatec.Utils.addClass(item, "zpMenuDisabled");
  }
};

/**
 * Enables previously disabled item from an external script.
 *
 * \param item_id [string] item ID to enable.
 */
Zapatec.Menu.prototype.itemEnable = function(item_id) {
  var item = this.items[item_id];
  if (item) {
    // item_id exists
    Zapatec.Utils.removeClass(item, "zpMenuDisabled");
  }
};

/**
 * \internal Hides previously open trigger menu and shows trigger menu.
 * Called from trigger event handler.
 *
 * \param 0 [number] left position of trigger menu (optional).
 * \param 1 [number] top position of trigger menu (optional).
 */
Zapatec.Menu.prototype.popupMenu = function() {
  // Hide previously open trigger menu
  for (var menuId in Zapatec.MenuTree.all) {
    var menu = Zapatec.MenuTree.all[menuId];
    if (menu.config.triggerEvent) { // Is trigger menu
      menu.hideMenu();
    }
  }
  // Show trigger menu
  if (arguments.length > 1) {
    this.showMenu(arguments[0], arguments[1]);
  } else {
    this.showMenu();
  }
};

/**
 * \internal Shows menu.
 *
 * \param 0 [number] left position of trigger menu (optional).
 * \param 1 [number] top position of trigger menu (optional).
 */
Zapatec.Menu.prototype.showMenu = function() {
  var top = this.top_parent;
  var menu = top.__zp_menu;

  // Set position
  if (arguments.length > 1) {
    top.style.position = 'absolute';
    top.style.left = arguments[0] + 'px';
    top.style.top = arguments[1] + 'px';
  }

  // Show menu
  top.style.display = 'block';

  // Prevent showing of horizontal menu in several lines
  // and fix different items width in vertical menu Mozilla issue
  if (!menu.style.width) {
    if (menu.childNodes) {
      // Calculate menu width
      var menuWidth = 0;
      var itemMargin = 0;
      for (var i = 0; i < menu.childNodes.length; i++) {
        var item = menu.childNodes[i];
        if (i == 0) {
          // Assume margin-right is 0 because we can't determine it
          itemMargin = item.offsetLeft;
        }
        if (this.config.vertical) {
          if (item.offsetWidth > menuWidth) {
            menuWidth = item.offsetWidth + itemMargin;
          }
        } else {
          menuWidth += item.offsetWidth + itemMargin;
        }
      }
      // + menu border
      if (typeof menu.clientLeft != 'undefined') { // IE & Opera
        menuWidth += menu.clientLeft * 2;
      } else { // Mozilla
        menuWidth += menu.offsetWidth - menu.clientWidth;
      }
      // Set menu width
      if (menu.clientWidth > menuWidth) {
        menu.style.width = menu.clientWidth + 'px';
      } else {
        menu.style.width = menuWidth + 'px';
      }
    }
  }

  // Adjust position
  if (arguments.length <= 1) {
    if (this.config.top || this.config.right || this.config.bottom || this.config.left) {
      var objWindowSize = Zapatec.Utils.getWindowSize();
      var winW = objWindowSize.width;
      var winH = objWindowSize.height;
      top.style.position = 'absolute';
      if (this.config.top) {
        top.style.top = parseInt(this.config.top) + 'px';
      } else if (this.config.bottom) {
        top.style.top = (winH - parseInt(this.config.bottom) - menu.offsetHeight - (top.offsetHeight - top.clientHeight)) + 'px';
      }
      if (this.config.left) {
        top.style.left = parseInt(this.config.left) + 'px';
      } else if (this.config.right) {
        top.style.left = (winW - parseInt(this.config.right) - menu.offsetWidth - (top.offsetWidth - top.clientWidth)) + 'px';
      }
    } else if (window.opera && (this.config.drag || this.config.scrollWithWindow)) {
      top.style.position = 'absolute';
      var pos = Zapatec.Utils.getAbsolutePos(top);
      top.style.left = pos.x + 'px';
      top.style.top = pos.y + 'px';
    }
  }

  // Set z-index
  top.style.zIndex = this.config.zIndex;

  // Highlight path
  if ((this.config.rememberPath || this.config.pathCookie != '__zp_item') && this.path) {
    this.highlightPath(this.path);
    if (this.config.rememberPath == 'expand') {
      this.sync(this.path);
    }
  }
};

/**
 * \internal Hides trigger menu.
 */
Zapatec.Menu.prototype.hideMenu = function() {
  this.collapseAll();
  this.top_parent.style.display = 'none';
};

/**
 * \internal Array that keeps mouseovered items of all menus on the page.
 * It is used to determine which menu and which item is active now to be able
 * to use keyboard arrows, Enter, Esc buttons for menu navigation.
 * Used in keyboard navigation module.
 */
Zapatec.Menu.selectedItemsStack = [];

/**
 * \internal Adds item to zpMenu-item-selected class and selectedItemsStack.
 *
 * \param item [HTMLElement] reference to the DIV element holding the item.
 */
Zapatec.Menu.selectItem = function(item) {
  Zapatec.Utils.addClass(item, "zpMenu-item-selected");
  if (/zpMenu-item-collapsed/i.test(item.className)) {
    Zapatec.Utils.addClass(item, "zpMenu-item-selected-collapsed");
  }
  // Remove item from stack
  for (var i = Zapatec.Menu.selectedItemsStack.length - 1; i >= 0; i--) {
    if (Zapatec.Menu.selectedItemsStack[i] == item) {
      Zapatec.Menu.selectedItemsStack.splice(i, 1);
    }
  }
  // Add item to stack
  Zapatec.Menu.selectedItemsStack.push(item);
};

/**
 * \internal Removes item from zpMenu-item-selected class and
 * selectedItemsStack.
 *
 * \param item [HTMLElement] reference to the DIV element holding the item.
 */
Zapatec.Menu.unselectItem = function(item) {
  Zapatec.Utils.removeClass(item, "zpMenu-item-selected");
  Zapatec.Utils.removeClass(item, "zpMenu-item-selected-collapsed");
  // Remove item from stack
  for (var i = Zapatec.Menu.selectedItemsStack.length - 1; i >= 0; i--) {
    if (Zapatec.Menu.selectedItemsStack[i] == item) {
      Zapatec.Menu.selectedItemsStack.splice(i, 1);
    }
  }
};
