/**
 * The Zapatec DHTML Calendar
 *
 * Copyright (c) 2004-2005 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 *
 *
 * Tree Widget
 */
// if (_zapatec_tree_url)
// 	_zapatec_tree_url = _zapatec_tree_url.replace(/\/*$/, '/');

/**
 * The Zapatec.BasicTree object constructor.  Pass to it the ID of an UL element (or
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
 * @param el [string or HTMLElement] -- the UL element
 * @param config [Object, optional] -- the configuration options
 *
 * @return
 */
Zapatec.BasicTree = function(el, config) {
	if (arguments.length > 0) {
		this.init(el, config);
	}
}

Zapatec.BasicTree.prototype.init = function(el, config) {
	if (typeof config == "undefined")
		config = {};
	function param_default(name, value) { if (typeof config[name] == "undefined") config[name] = value; };
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
	this.id = el.id || Zapatec.Utils.generateID("tree");
	var top = this.top_parent = Zapatec.Utils.createElement("div");
	top.className = "tree tree-top";
	this.createTree(el, top, 0);
	el.parentNode.insertBefore(top, el);
	el.parentNode.removeChild(el);
	Zapatec.BasicTree.all[this.id] = this;
	// check if we have an initially selected node and sync. the tree if so
	if (this.selectedItem)
		this.sync(this.selectedItem.__msh_item);
	// <PROFILE>
	if (this.config.d_profile) {
		alert("Generated in " + (new Date().getTime() - T1) + " milliseconds\n" +
		      profile.items + " total tree items\n" +
		      profile.trees + " total (sub)trees\n" +
		      profile.icons + " total icons");
	}
	// </PROFILE>
};

/**
 * This global variable keeps a "hash table" (that is, a plain JavaScript
 * object) mapping ID-s to references to Zapatec.BasicTree objects.  It's helpful if
 * you want to operate on a tree but you don't want to keep a reference to it.
 * Example:
 *
 * \code
 *   // the following makes a tree for the <ul id="tree-id"> element
 *   var tree = new Zapatec.BasicTree("tree-id");
 *   // ... later
 *   var existing_tree = Zapatec.BasicTree.all("tree-id");
 *   // and now we can use \b existing_tree the same as we can use \b tree
 *   // the following displays \b true
 *   alert(existing_tree == tree);
 * \endcode
 *
 * So in short, this variable remembers values returned by "new
 * Zapatec.BasicTree(...)" in case you didn't.
 */
Zapatec.BasicTree.all = {};

/**
 * \internal Function that creates a (sub)tree.  This function walks the UL
 * element, computes and assigns CSS class names and creates HTML elements for
 * a subtree.  Each time a LI element is encountered, createItem() is called
 * which effectively creates the item.  Beware that createItem() might call
 * back this function in order to create the item's subtree. (so createTree and
 * createItem form an indirect recursion).
 *
 * @param list [HTMLElement] -- reference to the UL element
 * @param parent [HTMLElement] -- reference to the parent element that should hold the (sub)tree
 * @param level [integer] -- the level of this (sub)tree in the main tree.
 *
 * @return id -- the (sub)tree ID; might be automatically generated.
 */
Zapatec.BasicTree.prototype.createTree = function(list, parent, level) {
	if (this.config.d_profile) // PROFILE
		++profile.trees; // PROFILE
	var id = list.id || Zapatec.Utils.generateID("tree.sub"),
		self = this;
	function _makeIt() {
		self.creating_now = true;
		var
			last_li = null,
			next_li,
			i = list.firstChild,
			items = parent.__msh_items = [];
		self.trees[id] = parent;
		parent.__msh_level = level;
		parent.__msh_treeid = id;
		while (i) {
			if (last_li)
				last_li.className += " tree-lines-c";
			if (i.nodeType != 1)
				i = i.nextSibling;
			else {
				next_li = Zapatec.Utils.getNextSibling(i, 'li');
				if (i.tagName.toLowerCase() == 'li') {
					last_li = self.createItem(i, parent, next_li, level);
					if (last_li) { //false when webmaster creates malformed tree 
						items[items.length] = last_li.__msh_item;
					}
				}
				i = next_li;
			}
		}
		i = parent.firstChild;
		if (i && !level) {
			i.className = i.className.replace(/ tree-lines-./g, "");
			i.className += (i == last_li) ? " tree-lines-s" : " tree-lines-t";
		}
		if (last_li && (level || last_li !== i)) {
			last_li.className = last_li.className.replace(/ tree-lines-./g, "");
			last_li.className += " tree-lines-b";
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
 * \internal This function walks through a LI element and creates the HTML
 * elements associated with that tree item.  When it encounters an UL element
 * it calls createTree() in order to create the item's subtree.  This function
 * may also call item_addIcon() in order to add the +/- buttons or icons
 * present in the item definition as IMG tags, or item_addDefaultIcon() if the
 * tree configuration specifies "defaultIcons" and no IMG tag was present.
 *
 * @param li [HTMLElement] -- reference to the LI element
 * @param parent [HTMLElement] -- reference to the parent element where the HTML elements should be created
 * @param next_li [HTMLLiElement] -- reference to the next LI element, if this is not the last one
 * @param level [integer] -- the level of this item in the main tree
 * @param atStart [HTMLElement optional] -- reference to the element DIV with a TABLE object that represents the child following the new child added or inserted 
 *
 * @return [HTMLElement] -- a reference to a DIV element holding the HTML elements of the created item
 */
Zapatec.BasicTree.prototype.createItem = function(li, parent, next_li, level, atStart) {
	if (this.config.d_profile) // PROFILE
		++profile.items; // PROFILE
	if (!li.firstChild)
		return;
	var afterNode = null;
	if (atStart) { //Optional parameter after the fourth parameter to allow the new created node to be inserted before it, instead of appending to the parent
	   afterNode = atStart;
  	}
	var
		id = li.id || Zapatec.Utils.generateID("tree.item"),
		item = this.items[id] = ((afterNode == null) ? Zapatec.Utils.createElement("div", parent) : Zapatec.Utils.createElement("div")), //Do not append the new div element to the parent, the new node in the div will be inserted before the 'afterNode'.
		t = Zapatec.Utils.createElement("table", item),
		tb = Zapatec.Utils.createElement("tbody", t),
		tr = Zapatec.Utils.createElement("tr", tb),
		td = Zapatec.Utils.createElement("td", tr),
		is_list,
		tmp,
		i = li.firstChild,
		has_icon = false;
	t.className = "tree-table";
	t.cellSpacing = 0;
	t.cellPadding = 0;
	td.className = "label";
	item.className = "tree-item";
	item.__msh_item = id;
	item.__msh_tree = this.id;
	item.__msh_parent = parent.__msh_treeid;
	if (afterNode) { //A child node from the same parent is sent in to let the new child node inserted before it.
	   parent.insertBefore(item, afterNode); //New item inserted before the 'afterNode'
	}
	while (i) {
		is_list = i.nodeType == 1 && /^[ou]l$/i.test(i.tagName);
		if (i.nodeType != 1 || !is_list) {
			if (i.nodeType == 3) {
				// remove whitespace, it seems to cause layout trouble
				tmp = i.data.replace(/^\s+/, '');
				tmp = tmp.replace(/\s+$/, '');
				li.removeChild(i);
				if (tmp) {
					i = Zapatec.Utils.createElement("span");
					i.className = "label";
					i.innerHTML = tmp;
					i.onclick = Zapatec.BasicTree.onItemToggle;
					td.appendChild(i);
				}
			} else if (i.tagName.toLowerCase() == 'img') {
				this.item_addIcon(item, i);
				has_icon = true;
			} else {
				i.onclick = Zapatec.BasicTree.onItemToggle;
				td.appendChild(i);
			}
			i = li.firstChild;
			continue;
		}
		if (is_list) {
			this.item_addIcon(item, null);
			var np;
			if (afterNode != null) {
			   np = Zapatec.Utils.createElement("div");
		 	   parent.insertBefore(np, afterNode); //New item inserted before the 'afterNode'
			} else {
				np = Zapatec.Utils.createElement("div", item.parentNode);
			}
			np.__msh_item = id;
			np.className = "tree";
			if (next_li)
			   np.className += " tree-lined";
			item.__msh_subtree = this.createTree(i, np, level+1);
			if ((this.config.initLevel !== false && this.config.initLevel <= level) ||
			    (this.config.compact && !/(^|\s)expanded(\s|$)/i.test(li.className))
			    || /(^|\s)collapsed(\s|$)/i.test(li.className)) {
				item.className += " tree-item-collapsed";
				this.toggleItem(id);
			} else
				item.className += " tree-item-expanded";
			if (/(^|\s)selected(\s|$)/i.test(li.className))
				this.selectedItem = item;
			break;
		}
	}
	if (!has_icon)
		this.item_addDefaultIcon(item, this.config.defaultIcons);
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
 * @param item [HTMLElement] -- reference to the DIV element holding the item
 * @param className -- a string containing the additional class name
 */
Zapatec.BasicTree.prototype.item_addDefaultIcon = function(item, className) {
	if (!className)
		return;
	var last_td = item.firstChild.firstChild.firstChild.lastChild, td;
	var td = Zapatec.Utils.createElement("td");
	td.className = "tgb icon " + className;
	td.onclick = Zapatec.BasicTree.onItemToggle;
	last_td.parentNode.insertBefore(td, last_td);
};

/** 
 * \internal This function does different things, depending on whether the \b
 * img parameter is passed or not.  If the \b img is passed, then this function
 * adds it as an icon for the given item.  If not passed, this function creates
 * a "+/-" button for the given item.
 * 
 * @param item [HTMLElement] -- reference to the DIV holding the item elements
 * @param img [HTMLImgElement, optional] -- reference to an IMG element; normally one found in the <LI>
 */
Zapatec.BasicTree.prototype.item_addIcon = function(item, img) {
	if (this.config.d_profile) // PROFILE
		++profile.icons; // PROFILE
	var last_td = item.firstChild.firstChild.firstChild, td;
	last_td = img ? last_td.lastChild : last_td.firstChild;
	if (!img || !item.__msh_icon) {
		td = Zapatec.Utils.createElement("td");
		td.className = "tgb " + (img ? "icon" : "minus");
		last_td.parentNode.insertBefore(td, last_td);
		td.onclick = Zapatec.BasicTree.onItemToggle;
	} else {
		td = item.__msh_icon;
		img.style.display = "none";
	}
	if (!img) {
		td.innerHTML = "&nbsp;";
		item.className += " tree-item-more";
		item.__msh_state = true; // expanded
		item.__msh_expand = td;
	} else {
		td.appendChild(img);
		item.__msh_icon = td;
	}
};

/** 
 * This function gets called from a global event handler when some item was
 * clicked.  It selects the item and toggles it if it has a subtree (expands or
 * collapses it).
 * 
 * @param item_id [string] -- the item ID
 */
Zapatec.BasicTree.prototype.itemClicked = function(item_id) {
	this.selectedItem = this.toggleItem(item_id);
	if (this.config.hiliteSelectedNode && this.selectedItem)
		Zapatec.Utils.addClass(this.selectedItem, "tree-item-selected");
	this.onItemSelect(item_id);
};

/** 
 * This function toggles an item if the \b state parameter is not specified.
 * If \b state is \b true then it expands the item, and if \b state is \b false
 * then it collapses the item.
 * 
 * @param item_id [string] -- the item ID
 * @param state [boolean, optional] -- the desired item state
 * 
 * @return a reference to the item element if found, null otherwise
 */
Zapatec.BasicTree.prototype.toggleItem = function(item_id, state) {
	if (item_id) {
		if (this.config.hiliteSelectedNode && this.selectedItem)
			Zapatec.Utils.removeClass(this.selectedItem, "tree-item-selected");
		var item = this.items[item_id];
		if (typeof state == "undefined")
			state = !item.__msh_state;
		if (state != item.__msh_state) {
			var subtree = this._getTree(item.__msh_subtree, this.creating_now);
			if (subtree) {
				subtree.style.display = state ? "block" : "none";
				Zapatec.Utils.removeClass(item, "tree-item-expanded");
				Zapatec.Utils.removeClass(item, "tree-item-collapsed");
				Zapatec.Utils.addClass(item, state ? "tree-item-expanded" : "tree-item-collapsed");
			}
			var img = item.__msh_expand;
			if (img)
				img.className = "tgb " + (state ? "minus" : "plus");
			item.__msh_state = state;
			img = item.__msh_icon;
			if (img) {
				img.firstChild.style.display = "none";
				img.appendChild(img.firstChild);
				img.firstChild.style.display = "block";
			}
			if (this.config.compact && state) {
				var a = this._getTree(item.__msh_parent).__msh_items;
				for (var i = a.length; --i >= 0;)
					if (a[i] != item_id)
						this.toggleItem(a[i], false);
			}
		}
		return item;
	}
	return null;
};

/** 
 * Call this function to collapse all items in the tree.
 */
Zapatec.BasicTree.prototype.collapseAll = function() {
	for (var i in this.trees)
		this.toggleItem(this._getTree(i).__msh_item, false);
};

/**
 * Call this function to expand all items in the tree.
 */
Zapatec.BasicTree.prototype.expandAll = function() {
	for (var i in this.trees)
		this.toggleItem(this._getTree(i).__msh_item, true);
};

/**
 * Call this function to create a html element with the optional element type specified.
 * By default, it is a <LI> element.
 * @param html [string, optional] -- html of the node; may include <UL>, <LI> elements; user is responsible for the content of the html
 * @param type [string, optional] -- type of the node to be created 
 */
Zapatec.BasicTree.prototype.makeNode = function(html, type) {
	if (!type) {
	   type = "li"; //Make it a <LI> node if the type is not specified.	
	}
	var node = Zapatec.Utils.createElement(type);
	if (html) { 
		node.innerHTML = html; //Assign the inner html of the node if it is specified.
	}
	return node;
}

/**
 * Call to get the parent to append, insert or remove a child either at start or end position or in between two nodes of the (sub-)tree.
 *
 * For insert, if the user selects a tree-item that represents the root node of a subtree, then the new child will be inserted outside the subtree, 
 * i.e. exactly before the root node of the subtree. However, if the user selects the node that represents one of the children in the subtree,
 * then the new child will be inserted in the subtree under the root.
 * For remove, if the user selects the root node of a subtree, the root node including its children will be removed. If the user selects a node that is not
 * a root node of a subtree, then only that node will be deleted.
 * In the implementation, the p will first retrieve the subtree from Tree.trees. But this is just the object that encapsulates the subtree, not the root of the
 * entire subtree. For insert and remove, 
 * @param id 	 [String] -- id of the parent the new child will be added to or inserted before/at.
 * @param mode   [String optional] -- "I" is for inserting a child node. "R" is for removing a child node. 
 */
Zapatec.BasicTree.prototype.getParent = function(id, mode) {
	var parent = null;
	for (var i in this.trees) {
	    //id sent in may be name of the top tree or one of the tree item's or subtree parent's name 
	    if ( (this.trees[i].__msh_treeid == id) || (this.trees[i].__msh_item == id) ) {
	       parent = this.trees[i]; //Get the body of a subtree
	       break;
	    }
	}
	//For inserting a new child before the referece child, the reference child should be the tree item, 
	//not the subtree under it (in case it has tree nodes under it). This is because inserting a new child 
	//should be 'before' the subtree (if the tree item has tree nodes under it), not inside or become part of the subtree.
	if ( (mode != null) && ((mode.toUpperCase() == "I") || (mode.toUpperCase() == "R")) ) {  //At this point, if p not null, then it must be body of the subtree. So, get the tree item (root node of the subtree) instead. 
	   //Otherwise id doesn't refer to a subtree. In this case, get the tree item instead.
	   if (parent != null) { //A subtree (not include its root) has been retrieved. That means the insertion or removal operation got to be performed before or at the root of the subtree. This warrants the retrival of the root of the subtree.
	      if (parent.className != this.top_parent.className) //As long as p is not the top parent, get the previous sibling or node of p. The previous sibling will be the root of the subtree.
	   	 parent = parent.previousSibling;
	   } else parent = this.items[id.toLowerCase()]; //If no subtree is retrieved, then it must an item node, then get it from array this.items.
	}
	if (!parent) { //If the node matching the id still cannot be found, then look into each item under a subtree 
	   parent = this.items[id.toLowerCase()];
	}
	return parent;	
}

/**
 * Append a child to the start or end of the given parent.
 *
 * @param parent   [HTMLElement] -- reference to a tree node (either as a node at top level or at a subtree level) the new child going to be appended to in the tree.
 * @param newChild [HTMLElement] -- reference to an HTML element created of type LI, to which futher HTML elements such UL and LI can be included to generate subtrees.
 * @param atStart  [boolean, optional] -- true if the child going to be added at the start of the parent. 
 */
Zapatec.BasicTree.prototype.appendChild = function(parent, newChild, atStart) {
	atStart = (atStart != null && atStart == true);
	
	if (parent == null || newChild == null || (typeof parent == "undefined") || (typeof newChild == "undefined") || this.items[newChild.id]) return; //Abort operation when either parent/child is empty or the child node is already added to the tree.
	
	var item = null;
	if (atStart) { //Append new child before first child of the parent
  	   item = this.createItem(newChild, parent, parent.firstChild.nextSibling, parent.__msh_level, parent.firstChild);
  	} else { //Append new child after last child of the parent
  	   item = this.createItem(newChild, parent, null, parent.__msh_level);
	}
	
	//After adding a child, re-draw tree lines that connect the new child to the tree.
	//This is necessary because the tree structure has been changed due to addition or insertion of a new child node.
	if (item) { //Child added has no subtree
	   var this_node = null;
	   var next_node = null;
	   var prev_node = null;
	   var subtree   = false;
		
	   if (atStart) { //Child appended at start of the tree
	      this_node = parent.childNodes[0]; //Always the first node regardless of whether has a subtree under it
	      if (item.__msh_subtree==null) { //New child appended has no subtree
	         next_node = parent.childNodes[1]; //Next node will be the second node - the one after the new child node.
	      } else { //New child appended has a subtree
	         next_node = parent.childNodes[2]; //Next node will be the third node - two nodes after the new child node
	      }
	  } else { //Child appended at end of the tree
	  	   //Get the appropriate tree node the new node going to attach to
	  	   if (!item.__msh_subtree) { //Child appended has no subtree
				 this_node = parent.childNodes[parent.childNodes.length-1]; //Last node
				
			   prev_node = parent.childNodes[parent.childNodes.length-2];
			   subtree = (prev_node.className != null && prev_node.className == "tree");
			   if (subtree) { //prev_node is a not a tree item, it has a subtree
			      prev_node.className += " tree-lined"; //Add vertical tree line to the tree
			      prev_node = parent.childNodes[parent.childNodes.length-3]; //Get its parent instead 
			   }
		   } else {
					 this_node = parent.childNodes[parent.childNodes.length-2]; //Second last node
					 
				   prev_node = parent.childNodes[parent.childNodes.length-3];
				   subtree = (prev_node.className != null && prev_node.className == "tree");
				   if (subtree) { //prev_node is a not a tree item, it has a subtree
				   	  prev_node.className += " tree-lined"; //Add vertical tree line to the tree
				      prev_node = parent.childNodes[parent.childNodes.length-4]; //Get its parent instead
				   }
		   }
	  }
		
		//Draw tree lines between the child and the parent
		if (this_node) { //Make sure the child is in the parent (sub-)tree
		   this_node.className = this_node.className.replace(/ tree-lines-./g, "");
		   if (atStart) {
		      this_node.className += " tree-lines-t";
					if (next_node) {
						 next_node.className = next_node.className.replace(/ tree-lines-./g, "");
					   next_node.className += " tree-lines-c";
				  }
		   } else {
			    this_node.className += " tree-lines-b";
			    if (prev_node) {
				prev_node.className = prev_node.className.replace(/ tree-lines-./g, "");
					 
				prev_node.className += " tree-lines-c";
		    		if (subtree) {
		    	 	   prev_node.className += " tree-lines-c";
		    		}
			    }
		  }
		}
	}
}; 

/**
 * A new child can be inserted between two nodes/children or before one node /children at the same tree level. 
 * Inserting the new child before the first node of a tree is allowed but not allowed 
 * after the last node of the tree, i.e. end of the tree, except before the last node.
 * @param newChild [HTMLElement] -- New child node to be inserted into the tree
 * @param refChild [HTMLElement] -- Reference to the child node which the new child node will be inserted before
 */
Zapatec.BasicTree.prototype.insertBefore = function(newChild, refChild) {
	if (newChild == null || refChild == null || (typeof newChild == "undefined") || (typeof refChild == "undefined") || this.items[newChild.id]) return; //Abort operation when either newChild/refChild is empty or the child node is already added to the tree..	
	
	var parent = refChild.parentNode;
	var item = this.createItem(newChild, parent, parent.firstChild.nextSibling, parent.__msh_level, refChild);
	
	var nodeBefore = false, nodeAfter = false;
	var next_node  = null;
	
	if (item.previousSibling) nodeBefore = true;
	if (item.nextSibling)	  nodeAfter  = true;
	
	item.className = item.className.replace(/ tree-lines-./g, "");
	if (nodeBefore && nodeAfter)
	   item.className += " tree-lines-c";
	else if (nodeBefore) item.className += " tree-lines-b";
	else if (nodeAfter) { //Insert new child at the start of the tree.
	   item.className += " tree-lines-t";
	   //Since the node now after the new child was the first node before, the tree line from it is not connected to the new child.
	   //So it needs to redraw the tree line for the second node (formerly the first node in the tree).
	   if (item.className.indexOf("tree-item-more tree-item")>-1) { //New child has a subtree under it.
	      next_node = item.nextSibling.nextSibling; //Get the next node at the 'same' level as the new child, skip the new child's subtree.
	   } else {
	      next_node = item.nextSibling; //Get the next node at the 'same' level.
	   }
	   next_node.className = next_node.className.replace(/ tree-lines-./g, "");
	   //To find out whether next node has next node after it.
	   if (next_node.className.indexOf("tree-item-more tree-item")>-1) { //Next node has a subtree
	   	  if (next_node.nextSibling.nextSibling != null)
	   	      next_node.className += " tree-lines-c"; 
	   	  else
	   	      next_node.className += " tree-lines-b";
	   } else { //Next node is an tree item.
			  if (next_node.nextSibling != null)
		   	   next_node.className += " tree-lines-c"; 
	   	  else
	   	     next_node.className += " tree-lines-b";
	   }
	}
};

/**
 * An old child node in the tree can be removed at any level.
 * If the old child node happens to be the root of a subtree, 
 * then the entire subtree including child node(s) under the root node will be removed.
 * If the old child node is just an item node without any child node under it as children, 
 * then the only node removed is the old child node.
 * Once an item node or a subtree is removed, the node/subtree before and/or after the old 
 * node will be joined together, sometimes with tree lines redrawn.
 * The only limitations are that the root node of the top tree is not permitted for removal;
 * the first child node found will be removed if there are more than one tree node with the same id.
 * @param oldChild [HTMLElement] -- Old child node to be removed from the tree where it can 
 * be an item node without children or a subtree including the old child node as the root.
 */
Zapatec.BasicTree.prototype.removeChild = function(oldChild) {
	if (oldChild == null || (typeof oldChild == "undefined") ) return; //No child to remove
	else if (oldChild.className == this.top_parent.className) { //Top root node is not allowed
		alert("Removing root node not allowed.")
		return;	
	}
	
	var p = oldChild.parentNode; //Get the child node's parent node
	
	//Remove node(s) - check if the old child represents a root node of a subtree. If it does, remove the child nodes in the subtree as well as the root node.
	//If it does not represents a root node of a subtree, just remove it from the parent it attached to.
	//Clean up - remove the subtree's id from this.trees or the item node's id from this.items.
	if (oldChild.__msh_item && oldChild.__msh_tree && oldChild.__msh_parent) { //Make sure all common attributes of root node (of a subtree) or item node are there.
	   var prev_node	 = oldChild.previousSibling;
	   var next_node	 = oldChild.nextSibling;
	   var hasPrevNode = false;
	   var hasNextNode = false;
	   
	   //Find out if there is a node before the old node at the same tree level.
     if (prev_node) {
     	  //If the node before the old child is the entire subtree at next level, get the root node of that subtree.
     	  //The root node will be at the same level as the old child. Otherwise, the node before the old child is an item node.
     	  if (prev_node.__msh_treeid) { //The node before the old child is a tree consists of all tree nodes in a subtree.
     	     prev_node = prev_node.previousSibling; //Get the root node of that subtree. It should have the same level as the old child.
     	  }
   	  hasPrevNode = true;
     } else { //No child before the old child at the same level.
        //Check if the old child has a parent node such as root of a subtree and the parent node is not the top of the tree
        if (oldChild.parentNode && oldChild.parentNode.className != this.top_parent.className) {        	
        	 hasPrevNode = true; //This is needed in order to 'not' change any tree line for the child node(s) under a subtree. 
        }
     }
     
     //Find out if there is a node after the old node at the same tree level.
     if (next_node) {
     	 if (oldChild.__msh_subtree) { //Old child is a subtree. So old node is the root of a subtree. Get the node after the subtree then.
     	 	  next_node = next_node.nextSibling; //Get the node after the old node at the same level.
     	 	  if (next_node)
     	 	     hasNextNode = true; //There is a next node at same level as the old node.
     	 } else { //Old child is an item node. So old node is an item node, not a subtree. Old node and next node are at the same level.
     		  hasNextNode = true; //There is a next node at same level as the old node.
     	 }
     } //else no next node
	   
	   if (oldChild.__msh_subtree) { //Root node of a subtree; has child node(s) under it.
	      var subtreeNode = oldChild.nextSibling; //Try to get the whole subtree of the old child node
	      
	      if (subtreeNode && oldChild.__msh_subtree == subtreeNode.__msh_treeid) { //Subtree node exists
	      	 for (var i = 0; i < subtreeNode.childNodes.length; i++) { //Loop through each children of the subtree 
	      	 	   if (subtreeNode.childNodes[i])
	      	        delete this.items[subtreeNode.childNodes[i].__msh_item]; //and delete the corresponding object in this.items
	      	 }
	      	 delete this.items[subtreeNode.__msh_item]; //Remove the corresponding item in this.items for the root of the subtree
	      	 p.removeChild(subtreeNode); //Remove the whole subtree
	      }
 			  delete this.trees[oldChild.__msh_subtree]; //Remove the corresponding subtree in this.trees
	      p.removeChild(oldChild); //Remove the child node from its parent
	   } else { //An item node, not a subtree
	      delete this.items[oldChild.__msh_item]; //Remove the corresponding item in this.items for the item node
	      p.removeChild(oldChild); //Remove the child node from its parent
	   }
	   //Re-draw tree lines nodes/subtrees between removed node if necessary
	   //If there is a previous node and next node between the old node, there is no need to redraw thr tree lines
	   //as the previous and next node will become joined together after the old node is removed.
	   //If 'before' removal of the old node, there is a next node after the old node but no previous node before 
	   //the old node, then it needs to redraw the tree line of the next node. The next node thus become the first node of the tree.
	   if (!hasPrevNode && hasNextNode) {
	      if (next_node) {
	    	 next_node.className = next_node.className.replace(/ tree-lines-./g, "");
	 	 next_node.className += " tree-lines-t";
	      }
	   } else if (hasPrevNode && !hasNextNode) {
	      if (prev_node) {
	         if (prev_node.__msh_subtree) {
		    prev_node.nextSibling.className = prev_node.nextSibling.className.replace(/ tree-lined/g, "");
		 } //else not a subtree 
		 prev_node.className = prev_node.className.replace(/ tree-lines-./g, "");
		 prev_node.className += " tree-lines-b";
	      }
	   }  
	}
};

/** 
 * Call this function to toggle all items in the tree.
 */
Zapatec.BasicTree.prototype.toggleAll = function() {
	for (var i in this.trees)
		this.toggleItem(this._getTree(i).__msh_item);
};

/** 
 * Call this function to synchronize the tree to a given item.  This means that
 * all items will be collapsed, except that item and the full path to it.
 * 
 * @param item_id [string] -- the ID of the item to sync to.
 */
Zapatec.BasicTree.prototype.sync = function(item_id) {
	var item = this.items[item_id];
	if (item) {
		this.collapseAll();
		this.selectedItem = item;
		var a = [];
		while (item.__msh_parent) {
			a[a.length] = item;
			var pt = this._getTree(item.__msh_parent);
			if (pt.__msh_item)
				item = this.items[pt.__msh_item];
			else
				break;
		}
		for (var i = a.length; --i >= 0;)
			this.toggleItem(a[i].__msh_item, true);
		Zapatec.Utils.addClass(this.selectedItem, "tree-item-selected");
	}
};

/** 
 * Destroys the tree.  Removes all elements.  Does not destroy the Zapatec.BasicTree
 * object itself (actually there's no proper way in JavaScript to do that).
 */
Zapatec.BasicTree.prototype.destroy = function() {
	var p = this.top_parent;
	p.parentNode.removeChild(p);
};

/** 
 * \internal This function is used when "dynamic initialization" is on.  It
 * retrieves a reference to a subtree if already created, or creates it if it
 * wasn't yet and \b dont_call is \b false (returns null in that case).
 * 
 * @param tree_id [string] the ID of the subtree
 * @param dont_call [boolean] pass true here if you don't want the subtree to be created
 * 
 * @return reference to the tree if it was found or created, null otherwise.
 */
Zapatec.BasicTree.prototype._getTree = function(tree_id, dont_call) {
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
Zapatec.BasicTree.prototype.onItemSelect = function() {};

// GLOBAL EVENT HANDLERS (to workaround the stupid Microsoft memory leak)

/** 
 * \internal This is a global event handler that gets called when a tree item
 * is clicked.  Don't override! ;-)
 */
Zapatec.BasicTree.onItemToggle = function() {
	var item = this;
	var body = document.body;
	while (item && item !== body && !/tree-item/.test(item.className))
		item = item.parentNode;
	Zapatec.BasicTree.all[item.__msh_tree].itemClicked(item.__msh_item);
};
