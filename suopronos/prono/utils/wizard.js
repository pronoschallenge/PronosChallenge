/**
 *
 * Copyright (c) 2004-2005 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 *
 * $Id: wizard.js 1096 2005-11-19 16:34:15Z alex $
 *
 * Zapatec.Wizard constructor.
 *
 * A Wizard object provides common functionality that seems to be required by
 * any wizard:
 *
 *  - tabbed display
 *  - keyboard navigation through tabs
 *  - tab navigation bar (<button>-s)
 *  - standard/advanced mode (ability to hide some elements in standard mode)
 *  - data validation code
 *
 * Call the constructor like this:
 *
 * \code
 *   var args = {
 *      tabsID    : "tabs",
 *      tabBarID  : "tab-bar"
 *   };
 *   var wizard = new Zapatec.Wizard(args);
 * \endcode
 *
 * The args object contains named arguments.  As of now, the following two are
 * required:
 *
 *  - tabsID -- must be the ID of an element that contains one DIV for each tab.
 *  - tabBarID -- must be the ID of an element where the tab bar should be inserted.
 *
 * @param args [object] contains the arguments to be used, as described above.
 * @return a new Zapatec.Wizard object.
 */

Zapatec.Wizard = function(args) {
	this.args = args;
	this.validators = Zapatec.Wizard.defaultValidators;
	this._tabsEl = document.getElementById(args.tabsID);
};

/// Static variable that implements certain common validators to be available
/// by default in any wizard.
Zapatec.Wizard.defaultValidators = {
	"numeric.int"     : function(value, range) {
		var isnum = /^-?[0-9]+$/.test(value);
		if (isnum) {
			isnum = parseInt(value, 10);
			range[0] = parseInt(range[0], 10);
			range[1] = parseInt(range[1], 10);
			if (isnum < range[0] || isnum > range[1])
				return "Must be in [" + range[0] + ", " + range[1] + "]";
			return true;
		} else
			return "Must be numeric, integer.";
	},
	"numeric.float"   : function(value, range) {
		var isfloat = strlen(value) > 0 && /^-?([0-9]*)\.?([0-9]*)$/.test(value);
		if (isfloat) {
			isfloat = parseFloat(value, 10);
			range[0] = parseFloat(range[0], 10);
			range[1] = parseFloat(range[1], 10);
			if (isfloat < range[0] || isfloat > range[1])
				return "Must be in [" + range[0] + ", " + range[1] + "]";
			return true;
		} else
			return "Must be numeric, float.";
	},
	"email"           : function(value) {
		return /^([\w.-_]+)@([\w.-_]+)\.(\w+)$/.test(value) ?
			true : "Must be an email address.";
	},
	"url"             : function(value) {
		return /^(https?|ftps?):\/\/([^\s\x22\x27(){},]+)$/i.test(value) ?
			true : "Must be an URL.";
	}
};

/**
 * Initializes the wizard.  Developers should call this function after creating
 * a wizard and assigning any event handlers and/or creating the standard
 * toolbar (with Zapatec.Wizard.setupNav()).
 */
Zapatec.Wizard.prototype.init = function() {
	this._setupTabs();
	this.onInit();
};

/**
 * Display a new tab.  This function also takes care of (re)setting the
 * visibility of buttons in the navigation bar, and of calling the appropriate
 * event hooks.  If "onBeforeTabChange()" returns false, the operation is
 * cancelled.
 *
 * @param newTab [string] ID of the new tab.
 */
Zapatec.Wizard.prototype.changeTab = function(newTab) {
	var currentTab = this.getCurrentTab(), tab;
	if (currentTab != newTab) {
		if (!currentTab || this.onBeforeTabChange(currentTab, newTab)) {
			if (currentTab) {
				tab = this.tabs[currentTab];
				tab.cont_el.style.display = "none";
				Zapatec.Utils.removeClass(tab.tab_el, "active");
			}
			tab = this.tabs[newTab];
			tab.cont_el.style.display = "block";
			Zapatec.Utils.addClass(tab.tab_el, "active");
			this.tabsArray.current = tab.index;
			window.location = tab.tab_el.href;
			this.onTabChange(currentTab, newTab);
			if (this.btnHome)
				this.btnHome.style.visibility = this.isFirstTab() ? "hidden" : "visible";
			if (this.btnPrev)
				this.btnPrev.style.visibility = this.isFirstTab() ? "hidden" : "visible";
			if (this.btnNext)
				this.btnNext.style.visibility = this.isLastTab() ? "hidden" : "visible";
			if (this.btnEnd)
				this.btnEnd.style.visibility = this.isLastTab() ? "hidden" : "visible";
			if (this.btnAdvanced)
				this._updateAdvancedButton();
			if (tab.tab_el.__msh_onclick_action) {
				var func = tab.tab_el.__msh_onclick_action;
				if (typeof func == "string")
					eval(func);
				else if (typeof func == "function")
					func();
			}
		}
	}
	return this;
};

/**
 * Move to the next tab.
 */
Zapatec.Wizard.prototype.nextTab = function() {
	if (this.tabsArray.current < this.tabsArray.length - 1)
		this.changeTab(this.tabsArray[this.tabsArray.current + 1].id);
	return this;
};

/**
 * Move to the previous tab.
 */
Zapatec.Wizard.prototype.prevTab = function() {
	if (this.tabsArray.current > 0)
		this.changeTab(this.tabsArray[this.tabsArray.current - 1].id);
	return this;
};

/**
 * Move to the first tab.
 */
Zapatec.Wizard.prototype.firstTab = function() {
	this.changeTab(this.tabsArray[0].id);
};

/**
 * Move to the last tab.  Usually needed by buttons like "finish wizard".
 */
Zapatec.Wizard.prototype.lastTab = function() {
	this.changeTab(this.tabsArray[this.tabsArray.length - 1].id);
};

/**
 * @return [string] the ID of the currently displayed tab.
 */
Zapatec.Wizard.prototype.getCurrentTab = function() {
	var tab = this.tabsArray[this.tabsArray.current];
	return tab ? tab.id : null;
};

/**
 * @return [boolean] true if we are at the first tab, false otherwise.
 */
Zapatec.Wizard.prototype.isFirstTab = function() {
	return this.tabsArray.current == 0;
};

/**
 * @return [boolean] true if we are at the last tab, false otherwise.
 */
Zapatec.Wizard.prototype.isLastTab = function() {
	return this.tabsArray.current == this.tabsArray.length - 1;
};

/**
 * Toggles "advanced mode" for the currently displayed tab.  This operation
 * involves displaying or hiding any fields that have the class "advanced".
 * This is actually achieved by simply adding or removing the class to those
 * elements, accordingly.
 */
Zapatec.Wizard.prototype.toggleAdvanced = function() {
	var
		tab = this.tabs[this.getCurrentTab()],
		a = tab.advanced_els,
		el,
		i = 0,
		visible = (tab.advanced =! tab.advanced);
	while (el = a[i++]) {
		Zapatec.Utils.removeClass(el, "wizard-advanced");
		if (!visible)
			Zapatec.Utils.addClass(el, "wizard-advanced");
	}
	this._updateAdvancedButton();
};

/**
 * Creates a default navigation bar for the wizard and appends it into the
 * given parent.  The default buttons are:
 *
 *  - "advanced mode" (only visible if the current tab has advanced elements)
 *  - "Begin" (moves to first tab)
 *  - "Prev." (moves to previous tab)
 *  - "Next" (moves to next tab)
 *  - "End" (moves to last tab)
 *
 * Any of these buttons is assigned a certain class name, which helps
 * customizing the look through external CSS.  The classes are (in the same
 * order as above): "btn-advanced", "btn-begin", "btn-prev", "btn-next",
 * "btn-finish".
 *
 * @param parent [HTMLElement] the parent of the tab navigation bar.
 *
 * @return [HTMLElement] a reference to the DIV containing the navigation bar.
 */
Zapatec.Wizard.prototype.setupNav = function(parent) {
	var div = Zapatec.Utils.createElement("div", parent || this._tabsEl.parentNode);
	div.className = "navigation";
	var self = this, btn;

	btn = Zapatec.Utils.createElement("button", div);
	btn.innerHTML = "Show advanced options";
	btn.className = "btn-advanced";
	btn.onclick = function() { self.toggleAdvanced(); };
	this.btnAdvanced = btn;

	btn = Zapatec.Utils.createElement("button", div);
	btn.innerHTML = "Begin";
	btn.className = "btn-begin";
	btn.onclick = function() { self.firstTab(); };
	this.btnHome = btn;

	btn = Zapatec.Utils.createElement("button", div);
	btn.innerHTML = "&laquo; <u>P</u>rev.";
	btn.accessKey = "p";
	btn.className = "btn-prev";
	btn.onclick = function() { self.prevTab(); };
	this.btnPrev = btn;

	btn = Zapatec.Utils.createElement("button", div);
	btn.innerHTML = "<u>N</u>ext &raquo;";
	btn.accessKey = "n";
	btn.className = "btn-next";
	btn.onclick = function() { self.nextTab(); };
	this.btnNext = btn;

	btn = Zapatec.Utils.createElement("button", div);
	btn.innerHTML = "Finish";
	btn.className = "btn-finish";
	btn.onclick = function() { self.lastTab(); };
	this.btnEnd = btn;

	return div;
};

/// Call this function given the value and the validator to match it against.
/// WARNING, this function throws an exception if the validator is not defined.
/// You do not normally need to call this function manually, as all the
/// validation checks are being done automatically when the field requiring
/// validation loses focus.
///
/// @param value the string to test
/// @param validator the validator name
/// @return [boolean] true if it validates, false otherwise
Zapatec.Wizard.prototype.validate = function(value, validator, args) {
	var f = this.validators[validator];
	if (f)
		return f(value, args, validator);
	else
		throw "Validator “" + validator + "” is NOT defined.";
};

/**
 * Create a custom validator.  You need to specify the ID of the validator, and
 * a function that does the validation checks.  The function specification is
 * simple:
 *
 * \code
 *    function validate(value, args, validator);
 * \endcode
 *
 * The 3 arguments are:
 *
 * - value -- the value that we should check validation against; usually the
 *            \em value attribute of the input field.
 * - args -- any arguments that might be passed to the validator in the class
 *           name.  Note that this might be null.
 * - validator -- the ID of the validator, useful if you wish to use the same
 *                handler function for multiple validators.
 *
 * @param name
 * @param func
 *
 * @return
 */
Zapatec.Wizard.prototype.addCustomValidator = function(name, func) {
	if (!/^[a-z0-9.]+$/i.test(name)) {
		throw "Illegal validator ID: '" + name +
			"'.  Accepted values can only contain letters, digits and the dot sign.";
	} else
		this.validators[name] = func;
	return this;
};

/// Add a simple validator.  Pass the regexp that should match and the error
/// message that should be displayed if it doesn't.
Zapatec.Wizard.prototype.addValidator = function(name, regexp, error) {
	this.addCustomValidator(name, function(value) {
		return regexp.test(value) ? true : error;
	});
};

/** \defgroup IntFunctions Internal Functions
 *
 * These functions should not be of interest for outside scripts.
 */
//@{

/**
 * \internal Initializes the wizard tabs and internal data.
 */
Zapatec.Wizard.prototype._setupTabs = function() {
	var self = this;
	var tabs = this._tabsEl;
	Zapatec.Utils.addClass(tabs, "tabs");
	var bar = document.getElementById(this.args.tabBarID);
	Zapatec.Utils.addClass(bar, "tab-bar");
	this.tabs = {};		// maintain by ID
	this.tabsArray = [];	// maintain by index
	for (var i = tabs.firstChild; i; i = i.nextSibling) {
		if (i.nodeType != 1)
			continue;
		var tab = {
			tab_el       : Zapatec.Utils.createElement("a", bar),
			cont_el      : i,
			id           : i.id,
			index        : this.tabsArray.length,
			advanced     : false,
			advanced_els : []
		};
		tab.tab_el.href = "#" + i.id;
		var tmp = Zapatec.Utils.getFirstChild(i, "label");
		if (tmp)
			while (tmp.firstChild)
				tab.tab_el.appendChild(tmp.firstChild);
		if (tmp.accessKey) {
			tab.tab_el.accessKey = tmp.accessKey;
			tmp.accessKey = "";
		}
		tab.tab_el.title = tmp.title;
		tab.tab_el.__msh_onclick_action = tmp.onclick;
		tmp.parentNode.removeChild(tmp);
		tab.tab_el.__msh_info = tab;
		tab.tab_el.onclick = function() {
			self.changeTab(this.__msh_info.id);
			if (typeof this.blur == "function")
				this.blur();
			return false;
		};
		if (Zapatec.is_ie)
			tab.tab_el.onfocus = tab.tab_el.onclick;
		this.tabsArray[this.tabsArray.length] = tab;
		this.tabs[tab.id] = tab;
		i.style.display = "none";
		this._populateLists(tab);
	}
	this.tabsArray.current = -1;

	var currentTab = this.tabsArray[0].id;
	if (/#([^\/]+)$/.test(document.URL) && this.tabs[RegExp.$1])
		currentTab = RegExp.$1;
	this.changeTab(currentTab);
};

/**
 * \internal Populates some internal arrays in the given tab object, analyzing
 * all elements in the tab.  At this time, this function initializes:
 *
 * - the list of advanced elements (those that should be visible only if
 *   the tab is in "show advanced options" mode).
 * - the list of fields that require validation.  Also, all these fields get
 *   installed an "onblur" handler that checks validation.
 *
 * @param tab reference to the internally defined tab object.
 */
Zapatec.Wizard.prototype._populateLists = function(tab) {
	var a = tab.cont_el.getElementsByTagName("*"), i = 0, el, c, self = this;
	while (el = a[i++]) {
		var c = el.className;
		if (/(^|\s)wizard-advanced(\s|$)/i.test(c))
			tab.advanced_els[tab.advanced_els.length] = el;
		if (/(^|\s)validate-([^\s-]+)(-[^\s]+)?/i.test(c)) {
			el.__msh_validator = RegExp.$2;
			el.__msh_validator_args = RegExp.$3;
			el.onblur = function(ev) {
				ev || (ev = window.event);
				return self._validateField(
					this, this.__msh_validator,
					this.__msh_validator_args, ev);
			};
		}
	}
};

/**
 * \internal Called by the "onblur" handler for any fields that might required
 * validation, this function parses arguments, calls the appropriate validating
 * code and outputs the error message if it's the case.
 *
 * @param field [HTMLElement] a reference to a HTMLInputElement (usually) to be validated.
 * @param validator [string] the ID of a validator.
 * @param args [string] the validator arguments, as specified in the class name.
 * @param event [Event] the Event object, useful if we want to stop propagation.
 */
Zapatec.Wizard.prototype._validateField = function(field, validator, args, event) {
	var value, tag = field.tagName.toLowerCase(), div, message;
	if (typeof args != "undefined") {
		args = args.replace(/^-/, '');
		args = args.split(/-/);
	} else
		args = null;
	try {
		if (tag == "input" || tag == "select" || tag == "textarea") {
			message = this.validate(field.value, validator, args);
			if (typeof message == "boolean" && !message)
				// No message provided, let's think of one..
				message = "This field must validate by “" + validator + "”";
			if (typeof message == "string") {
				div = field.__msh_message;
				if (!div) {
					// create the message area
					div = field.__msh_message = Zapatec.Utils.createElement("div");
					div.className = "validation-error";
					field.parentNode.insertBefore(div, field.nextSibling);
				}
				div.innerHTML = message;
				// FIXME: this doesn't work, for some reason
				// field.focus();
				// field.select();
				Zapatec.Utils.addClass(field, "field-error");
				Zapatec.Utils.stopEvent(event);
				return false;
			} else {
				div = field.__msh_message;
				if (div) {
					div.parentNode.removeChild(div);
					field.__msh_message = null;
				}
				Zapatec.Utils.removeClass(field, "field-error");
			}
		} else
			// FIXME: what should we do here?
			throw "I don't know how to validate <" + tag + "> elements.";
	} catch(e) {
		alert("Error: " + e); // FIXME: what should we do here?
	}
};

/**
 * \internal Updates the state of the "advanced" button.
 *
 * @param tab [optional, Object] a reference to the tab to check the state
 * against.  If not passed, the current tab is assumed.
 */
Zapatec.Wizard.prototype._updateAdvancedButton = function(tab) {
	if (this.btnAdvanced) {
		if (!tab)
			tab = this.tabs[this.getCurrentTab()];
		this.btnAdvanced.innerHTML = tab.advanced ? "Hide advanced options" : "Show advanced options";
		this.btnAdvanced.style.visibility = tab.advanced_els.length == 0 ? "hidden" : "visible";
	}
};
//@}

/** \defgroup Hooks Customizable event hooks
 *
 * Event hooks provide a way for the developer to insert special code that is
 * executed when wizard events occur.  For instance, one could write the
 * following:
 *
 * \code
 *    function myOnInit() {
 *        this.changeTab("tab-3");
 *        alert("Look, we changed to the third tab! >8-]");
 *    };
 *    wizard.onInit = myOnInit;
 * \endcode
 */
//@{

/// A "do nothing" handler used for default event hooks.
Zapatec.Wizard._doNothing = function() { return true; };

/// Called when the wizard is created.  Users can perform
/// problem-specific initializations at this stage.  No
/// arguments.
Zapatec.Wizard.prototype.onInit = Zapatec.Wizard._doNothing;

/// Called _after_ the tab was changed.
///    @param oldTab ID of the old tab
///    @param newTab ID of the new tab.
Zapatec.Wizard.prototype.onTabChange = Zapatec.Wizard._doNothing;

/// Called when the tab is about to be changed, just _before_.
///    @param oldTab ID of the old (current) tab
///    @param newTab ID of the new tab
///    @return false if the tab should not change.
Zapatec.Wizard.prototype.onBeforeTabChange = Zapatec.Wizard._doNothing;

///
//@}
