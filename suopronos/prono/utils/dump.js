/*
 * Dumpe the dom, for an item, often the body.
 * 
 * Use with something like 
 * <a href='#' onclick='new dump(document.getElementsByTagName("body")[0], true);' >Dump DOM </a>
 * $Id: dump.js 854 2005-09-29 22:25:07Z ken $
 * 
 * Heavily borrowed from htmlarea. 
 */

dump = function (domElement, bWindow) {
  // Use destination window to print the tree or create a new
  // window, then print the tree into that window
  var strDump=dump.getHTML(domElement, false, 0)
  if (!bWindow) return strDump

  var outputWindow;
  outputWindow=window.open("","dumpdom");
  outputWindow.focus();

  outputWindow.document.open("text/html", "replace");
  outputWindow.document.write("<HTML><HEAD><TITLE>DOM</TITLE></HEAD><BODY>\n");
  outputWindow.document.write("<textarea rows=20 cols=60>")
  outputWindow.document.write(strDump)
  outputWindow.document.write("</textarea>")
  outputWindow.document.write("</BODY></HTML>\n");
  outputWindow.document.close();
}

dump.agt = navigator.userAgent.toLowerCase();
dump.is_ie     = ((dump.agt.indexOf("msie") != -1) && (dump.agt.indexOf("opera") == -1));
dump.RE_tagName = /(<\/|<)\s*([^ \t\n>]+)/ig;

dump.getHTML = function(root, outputRoot, intDeep) {

	var i2;
	var html = "";
	for (i2=0; i2 <= intDeep; i2++) html+="  "
	switch (root.nodeType) {
		case 1: // Node.ELEMENT_NODE
		case 11: // Node.DOCUMENT_FRAGMENT_NODE
			var closed;
			var i;
			var root_tag = (root.nodeType == 1) ? root.tagName.toLowerCase() : '';
			if (dump.is_ie && root_tag == "head") {
				if (outputRoot)
					html += "\n<head>\n";
				// lowercasize
				var save_multiline = RegExp.multiline;
				RegExp.multiline = true;
				var txt = root.innerHTML.replace(dump.RE_tagName, function(str, p1, p2) {
						return p1 + p2.toLowerCase();
						});
				RegExp.multiline = save_multiline;
				html += '\n' + txt;
				if (outputRoot)
					html += "\n</head>\n";
				break;
			} else if (outputRoot) {
				closed = (!(root.hasChildNodes() || dump.needsClosingTag(root)));
				html += "<" + root.tagName.toLowerCase();
				var attrs = root.attributes;
				for (i = 0; i < attrs.length; ++i) {
					var a = attrs.item(i);
					if (!a.specified) {
						continue;
					}
					var name = a.nodeName.toLowerCase();
					if (/_moz|contenteditable|_msh/.test(name)) {
						// avoid certain attributes
						continue;
					}
					var value;
					if (name != "style") {
						// IE5.5 reports 25 when cellSpacing is
						// 1; other values might be doomed too.
						// For this reason we extract the
						// values directly from the root node.
						// I'm starting to HATE JavaScript
						// development.  Browser differences
						// suck.
						//
						// Using Gecko the values of href and src are converted to absolute links
						// unless we get them using nodeValue()
						if (typeof root[a.nodeName] != "undefined" && name != "href" && name != "src") {
							value = root[a.nodeName];
						} else {
							value = a.nodeValue;
							// IE seems not willing to return the original values - it converts to absolute
							// links using a.nodeValue, a.value, a.stringValue, root.getAttribute("href")
							// So we have to strip the baseurl manually -/
							if (value && (dump.is_ie && (name == "href" || name == "src"))) {
								value = this.stripBaseURL(value);
							}
						}
					} else { // IE fails to put style in attributes list
						// FIXME: cssText reported by IE is UPPERCASE
						value = root.style.cssText;
					}
					if (/(_moz|^$)/.test(value)) {
						// Mozilla reports some special tags
						// here; we don't need them.
						continue;
					}
					html += " " + name + '="' + value + '"';
				}
				html += closed ? " />\n" : ">\n";
			}


			for (i = root.firstChild; i; i = i.nextSibling) {
				html += this.getHTML(i, true, intDeep+1)
			}

			if (outputRoot && !closed) {
				for (i2=0; i2 <= intDeep; i2++) html+="  "
				html += "</" + root.tagName.toLowerCase() + ">\n";
			}
			break;
		case 3: // Node.TEXT_NODE
			// If a text node is alone in an element and all spaces, replace it with an non breaking one
			// This partially undoes the damage done by moz, which translates '&nbsp;'s into spaces in the data element
			if ( !root.previousSibling && !root.nextSibling && root.data.match(/^\s*$/i) ) html += '&nbsp;';
			else 
			{
				html += this.htmlEncode(root.data) + '\n';
			}
			break;
		case 8: // Node.COMMENT_NODE
			html += "<!--" + root.data + "-->";
			break;		// skip comments, for now.
	}

	return html;
};

dump.stripBaseURL = function(string) {
	var baseurl = string;

	// strip to last directory in case baseurl points to a file
	baseurl = baseurl.replace(/[^\/]+$/, '');
	var basere = new RegExp(baseurl);
	string = string.replace(basere, "");

	// strip host-part of URL which is added by MSIE to links relative to server root
	baseurl = baseurl.replace(/^(https?:\/\/[^\/]+)(.*)$/, '$1');
	basere = new RegExp(baseurl);
	return string.replace(basere, "");
};

// performs HTML encoding of some given string
dump.htmlEncode = function(str) {
	// we don't need regexp for that, but.. so be it for now.
	str = str.replace(/&/ig, "&amp;");
	str = str.replace(/</ig, "&lt;");
	str = str.replace(/>/ig, "&gt;");
	str = str.replace(/\x22/ig, "&quot;");
	// \x22 means '"' -- we use hex reprezentation so that we don't disturb
	// JS compressors (well, at least mine fails.. ;)
	return str;
};

dump.needsClosingTag = function(el) {
	var closingTags = " head script style div span tr td tbody table em strong font a title ";
	return (closingTags.indexOf(" " + el.tagName.toLowerCase() + " ") != -1);
};


