/*
 *
 * Copyright (c) 2004-2005 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A.
 * All rights reserved.
 *
 * Drag and Drop module
 *
 */

var ErrorTimer = 0
Zapatec.defaultSlideTime = 500;
Zapatec.defaultSlideSteps = 30;
//searches elements with className=class and makes them draggable(useful to call it on document load)
// @ param class [string] searcable element's CSSclassname.
// @ param el [HTMLElement] reference to the element.
// @ param recursive [boolean] searches in childs.
Zapatec.Utils.initDragObjects = function(className, el, recursive, attribObject){
	if (!className) return;
	el = Zapatec.Utils.idOrObject(el);
	var changeArray = Zapatec.Utils.getElementsByAttribute('className', className, el, recursive);
	for (a in changeArray){
		a = new Zapatec.Utils.Draggable(changeArray[a], attribObject);
	}
}
//Replaces the image with DIV element, and sets the image as background-image.
//this is not good for images with changed size, because you cant change the background-image size and it will tile.
//@param el [HTMLElement] reference to the element
Zapatec.Utils.img2div = function(el){
	if (el.nodeName.match(/img/i)) {
		var div = document.createElement('div');
	    // Set div width and height when image is loaded
	    var objImage = new Image();
	    objImage.onload = function() {
	      div.style.width = this.width + 'px';
	      div.style.height = this.height + 'px';
	      div.style.fontSize = '0px';
	      this.onload = null;
	    };
	    objImage.src = el.src;
	    // Replace image with the div
	    div.style.backgroundImage = 'url(' + el.src + ')';
	    div.style.backgroundColor = 'transparent';
		var id = el.id;
		el.parentNode.replaceChild(div, el);
		div.id = id;
		return div
	} else {
		return el
	}
};
//gives the mouse position
//@param ev [event]
//returns object {X:[event.pageX],Y:[event.pageY]
Zapatec.Utils.eventPosition = function(ev){
		ev || (ev = window.event);
		var ret = {};
		ret.X = ev.pageX || ev.clientX + window.document.body.scrollLeft || 0;
		ret.Y = ev.pageY || ev.clientY + window.document.body.scrollTop || 0;
		return ret
};

//verifies is the given variable object or object id and returns the object or false if either
//@param el [HTMLElement or string] reference to the element or element id
//@param errorMessage [string] message shown in statusbar if el is not correct object
//@param errorAction [function reference] called function if el is not correct object
Zapatec.Utils.idOrObject = function(el, errorMessage, errorAction){
	if (typeof(el) == 'string') {
		el = document.getElementById(el) || el;
	}
	if (typeof(el) != 'object') {
		if (errorAction) errorAction();
		return false
	} else {
		return el
	}
};
//returns element Array which has attribute 'attr' with value 'val'
//by giving 'el' you can finetune your search
//@param attr [string] attribute to search
//@param val [string OR number] searched attributes value, ignored if 0.
//@param el [HTMLElement] reference to the element.
//@param recursive [boolean] searches in childs.

Zapatec.Utils.getElementsByAttribute = function(attr, val, el, recursive){
	if (!attr) return false;
	el = Zapatec.Utils.idOrObject(el);
	el || (el = window.document.body);
	var a = el.firstChild; retArray = [];
	while (a) {
		if (a[attr]) {
			if (val) {
				if (a[attr] == val) {
					retArray = retArray.concat([a]);
				}
			} else {
				retArray = retArray.concat([a]);
			}
		};
		if (recursive && a.hasChildNodes()) {
			retArray = retArray.concat(Zapatec.Utils.getElementsByAttribute(attr, val, a, recursive));
		}
		a = a.nextSibling
	};
	return retArray
};
//makes element draggable
//@param el [HTMLElement] reference to the element.
//@param left [number] draggable area left edge in pixels according to the dragLayer or document.body.
//@param top [number] draggable area top edge -''-.
//@param right [number] draggable area right edge -''-.
//@param bottom [number] draggable area bottom edge -''-.
//@param direction [String 'horizontal'/'vertical'] enables dragging only described direction
//@param followShape [boolean] draggable area controls the object size (not only the top left position).
//@param handler [HTMLElement] reference to the handler element (fe. window titlebar).
//@param dragCSS [string] className for dragstate (will be changed back after releease)
//@param dragLayer [HTMLElement] reference to the element in which we are dragging the draggable element, default is el.parentNode.
//@param method [string] cut, copy, dummy, slide
//@param dropname [string] defines name of the droparea
//alternate description Zapatec.Utils.Draggable(el,{left:number,top:number,right:number,bottom:number,
//vertical:boolean,horizontal:boolean,followShape:boolean,handler:HTMLElement OR string,dragCSS:string,dragLayer:HTMLElement OR string})

Zapatec.Utils.Draggable = function(el, left, top, right, bottom, direction, followShape, handler, dragCSS, dragLayer, method, dropName){
	el = Zapatec.Utils.idOrObject(el);
	if (!el) return;
	el = Zapatec.Utils.img2div(el);
	if(typeof(left) == 'object'){
		el.Atr = left
	} else {
		var Atr = {};
		if (left) Atr.left = left;
		if (top) Atr.top = top;
		if (right) Atr.right = right;
		if (bottom) Atr.bottom = bottom;
		if (direction) Atr[direction] = true;
		if (followShape) Atr.followShape = true;
		if (handler) Atr.handler = handler;
		if (dragCSS) Atr.dragCSS = dragCSS;
		if (dragLayer) Atr.dragLayer = dragLayer;
		if (method) Atr.method = method;
		if (dropName) Atr.dropName = dropName;
		el.Atr = Atr
	};
	if (el.Atr.followShape) {
	    // IE 6 needs separate thread to determine offsetWidth correctly
	    setTimeout(function() {
	      if (el.Atr.right) {
	        el.Atr.right -= el.offsetWidth;
	      }
	      if (el.Atr.bottom) {
	        el.Atr.bottom -= el.offsetHeight;
	      }
	    }, 0);
	};
	if (el.Atr.handler) {
		el.Atr.handler = Zapatec.Utils.idOrObject(el.Atr.handler, 'cannot find the handlerobject:' + el.Atr.handler);
	}
	if (el.Atr.handler) {
		el.Atr.handler = Zapatec.Utils.img2div(el.Atr.handler);
	};
	el.Atr.dragLayer = Zapatec.Utils.idOrObject(el.Atr.dragLayer, 'cannot find the dragLayer:' + el.Atr.dragLayer) || (el.Atr.horizontal || el.Atr.vertical) ? 0 : window.document.body;

  this.draggable = el;
  this.hook = el.Atr.handler || el;
  this.dragging = false;
  var self = this;
  Zapatec.Utils.addEvent(this.hook, 'mousedown', function(objEvent) {
    return self.dragStart(objEvent);
  });
  Zapatec.Utils.addEvent(window.document, 'mousemove', function(objEvent) {
    return self.dragMove(objEvent);
  });
  Zapatec.Utils.addEvent(window.document, 'mouseup', function(objEvent) {
    return self.dragEnd(objEvent);
  });
};

/**
 * Indicator to prevent moving of several items simultaneously.
 * It is needed because when you press mouse button, then move mouse pointer
 * outside of browser window, then release mouse button and move pointer back
 * to browser window, mouseup event doesn't fire. This is normal behaviour for
 * all Windows applications. It makes possible to capture and move several
 * items.
 */
Zapatec.Utils.Draggable.dragging = false;

//drag start event
Zapatec.Utils.Draggable.prototype.dragStart = function(ev) {
  // Don't do anything if already dragging
  if (Zapatec.Utils.Draggable.dragging) {
    return;
  }
  // Get event
  ev || (ev = window.event);
  // Check mouse button
  var iButton = ev.button || ev.which;
  if (iButton > 1) {
    return;
  }
  // Check if hook element was clicked
  var objTarget = ev.srcElement || ev.target;
  if (objTarget != this.hook) {
    return;
  }
  // Set flag
  Zapatec.Utils.Draggable.dragging = true;
  this.dragging = true;

  var el = this.draggable;
  el.mouseStart = Zapatec.Utils.eventPosition(ev);

  el.Atr.Start = {};
  el.Atr.Start.X = el.offsetLeft;
  el.Atr.Start.Y = el.offsetTop;
  if (el.Atr.dragLayer) {
    el.Atr.Start = Zapatec.Utils.getRelativePos(el, el.Atr.dragLayer);
    //el.Atr.dragLayer.appendChild(el);
  }
  if (el.Atr.handler) {
	el.Atr.handler.style.cursor = "move";
  }
  el.Atr.beforeDrag = Zapatec.Utils.changeAttributes(el, {
    className : el.Atr.dragCSS || '',
    parentNode : el.Atr.dragLayer,
    nextSibling : null,
    offsetLeft : el.Atr.Start.X,
    offsetTop : el.Atr.Start.Y,
    style : {
      display : 'block', 
      position : 'absolute'
    }
  });
  var initObjects = Zapatec.Utils.getElementsByAttribute('onDragInit', 0, el.Atr.dragLayer, true);
  if (initObjects.length) {
    for(a in initObjects) {
      if (initObjects[a] != el) initObjects[a].onDragInit(ev);
    }
  }

  // Stop event
  //Zapatec.Utils.stopEvent(ev);
};

Zapatec.Utils.Draggable.prototype.dragMove = function(ev){
  // Must initialize dragging first
  if (!this.dragging) {
    return;
  }
  var el = this.draggable;

//drag event
  var mouse = Zapatec.Utils.eventPosition(ev);
  if (!el.Atr.vertical) {
    var X = el.Atr.Start.X + mouse.X - el.mouseStart.X;
    if (X < el.Atr.left) {
      X=el.Atr.left;
    } else {
      if (el.Atr.right && X > el.Atr.right) {
        X = el.Atr.right;
      } else {
        if (el.Atr.right === 0) {
          X = el.Atr.right;
        }
      }
    }
    el.style.left = X + 'px';
  }
  if (!el.Atr.horizontal) {
    var Y = el.Atr.Start.Y + mouse.Y - el.mouseStart.Y;
    if (Y < el.Atr.top) {
      Y = el.Atr.top
    } else {
      if (el.Atr.bottom && Y > el.Atr.bottom) {
        Y = el.Atr.bottom;
      } else {
        if (el.Atr.bottom === 0) {
          Y = el.Atr.bottom;
        }
      }
    }
    el.style.top = Y + 'px';
  }
  var overel = '';
  if ((typeof Zapatec.Utils.DropArea.areas == "object") && Zapatec.Utils.DropArea.areas.length) {
    for (i = 0; i < Zapatec.Utils.DropArea.areas.length; i++) {
      var area = Zapatec.Utils.DropArea.areas[i];
      var pos = Zapatec.Utils.getAbsolutePos(area);
      if (area != el) {
        if ((pos.x < mouse.X) && ((pos.x + area.offsetWidth) > mouse.X) &&
          (pos.y < mouse.Y) && ((pos.y + area.offsetHeight) > mouse.Y)) {
          if ((overel == '') || (everel.style.zInex < area.style.zIndex)) {
            var overel = area;
          }
        }
      }
    }
  }
  // document.title=el.Atr.dragLayer+':'+overel.id;
  if (overel != el.Atr.overEl) {
    if (el.Atr.overEl && el.Atr.overEl.onDragOut) el.Atr.overEl.onDragOut(el);
    el.Atr.overEl = overel;
    if (overel.onDragOver) overel.onDragOver(el)
  }

  // Stop event
  //Zapatec.Utils.stopEvent(ev);
};

Zapatec.Utils.Draggable.prototype.dragEnd = function(ev){
  // Must initialize dragging first
  if (!this.dragging) {
    return;
  }

  var el = this.draggable;

  if (el.Atr.overEl && el.Atr.overEl.onDrop) {
    var returnToOldPos = !(el.Atr.overEl.onDrop(el));
    if (returnToOldPos) {
      Zapatec.Utils.Slide(el, el.Atr.Start.X, el.Atr.Start.Y);
      //Zapatec.Utils.changeAttributes(el,el.Atr.beforeDrag)
    }
  } else {
    el.className = el.Atr.beforeDrag.className;
  }
  var uninit = Zapatec.Utils.getElementsByAttribute('onDragEnd', 0, 0, 1);
  if (uninit.length) {
    for(a in uninit) {
      uninit[a].onDragEnd(el, ev);
    }
  }
  if (el.Atr.handler) {
	el.Atr.handler.style.cursor = "";
  }

  // Remove flag
  this.dragging = false;
  Zapatec.Utils.Draggable.dragging = false;
  // Stop event
  //Zapatec.Utils.stopEvent(ev);
};

Zapatec.Utils.Arrange = function(el){
	
}

Zapatec.Utils.Slide = function(el, toX, toY, time, steps) {
	time ||	(time = Zapatec.defaultSlideTime);
	steps || (steps = Zapatec.defaultSlideSteps);
	Zapatec.Glide = {ob : el, X : toX, Y : toY};
	Zapatec.Utils.Slide.step = function(el, x, y){
		var eX = el.offsetLeft || parseInt(el.style.left) || 0;
		var eY = el.offsetTop || parseInt(el.style.top) || 0;
		var cX = el.offsetLeft + (x - eX) / el.Atr.slideSteps;
		var cY = el.offsetTop + (y - eY) / el.Atr.slideSteps--;
		if (!el.Atr.slideSteps) {
			delete(el.Atr.slideSteps);
			cX = x;
			cX = y;
			clearInterval(el.Atr.sliding);
			Zapatec.Utils.changeAttributes(el, el.Atr.beforeDrag);
			el.Atr.beforeDrag = '';
			delete(el.Atr.beforeDrag);
			return
		}
		el.style.left = cX + 'px';
		el.style.top = cY + 'px'
	}
	el.Atr.slideSteps = steps;
	el.Atr.sliding = setInterval('Zapatec.Utils.Slide.step(Zapatec.Glide.ob, Zapatec.Glide.X, Zapatec.Glide.Y)', Math.round(time / steps));
}

//defines the droparea element
// @param (el) [HTML element]
// @param (dropname) [string] id for simple dnd
// @param (ondraginit) [function]
// @param (ondragover) [function]
// @param (odragout) [function]
// @param (ondragend) [function] onmouseup
Zapatec.Utils.DropArea = function(el, dropname, ondrop, ondraginit, ondragover, ondragout, ondragend) {
	el = Zapatec.Utils.idOrObject(el);
	if (!Zapatec.Utils.DropArea.areas) {
	    Zapatec.Utils.DropArea.areas = new Array();
	}
	Zapatec.Utils.DropArea.areas[Zapatec.Utils.DropArea.areas.length] = el;
	if (!el) return;
	if (!el.Atr) el.Atr = {};
	if (dropname) el.dropName = dropname;
	if (ondrop) el.onDrop = ondrop;
	if (ondraginit) el.onDragInit = ondraginit;
	if (ondragout) el.onDragOut = ondragout;
	if (ondragover) el.onDragOver = ondragover;
	if (ondragend) el.onDragEnd = ondragend
};

//gets the top element from given position
// @param (con) [HTML element] container element
// @param (X) [number] horizontal position
// @param (Y) [nunmber] vertical position
// @param (ignoreElement) [HTML element] elment not included to the comparision(excludes the dragobject on dragging)
// @param (recursive) [boolean] if true looks in child's else only in container
Zapatec.Utils.getTopElementByPos = function(con, X, Y, ignoreElement, recursive, stopAttr, stopAttrValue) {
	if (!(con = Zapatec.Utils.idOrObject(con))) return;
	var a = con.firstChild, ret = false;
	while (a) {
		if ((a.offsetLeft < X) && (a.offsetTop < Y) && (a.offsetLeft + a.offsetWidth) > X && (a.offsetTop + a.offsetHeight) > Y && a.style.display != 'none' && a != ignoreElement) {
			if(!ret || a.style.zIndex >= ret.style.zIndex) ret = a;
		}
		a = a.nextSibling
	}
	if (recursive && ret && ret.hasChildNodes() && !ret[stopAttr]) {
		ret = Zapatec.Utils.getTopElementByPos(ret, X-ret.offsetLeft, Y-ret.offsetTop, ignoreElement, recursive, stopAttr) || ret;
	}
	return ret
}
//gives element relative position in container object
//@param (con) [Html Elemnt (id or refernce)] container object
//@param (el) [Html Elemnt (id or refernce)] element
Zapatec.Utils.getRelativePos = function(el, con) {
	if (!(el = Zapatec.Utils.idOrObject(el, 'relativePos - no object:' + el))) return;
	con = Zapatec.Utils.idOrObject(con, 'relativePos - no container:' + con);
	var SL = 0, ST = 0;
	if (el.scrollLeft) SL = el.scrollLeft;
	if (el.scrollTop) ST = el.scrollTop;
	var r = {X : el.offsetLeft - SL, Y : el.offsetTop - ST};
	if (el.offsetParent && el.offsetParent != con) {
		var tmp = Zapatec.Utils.getRelativePos(el.offsetParent, con);
		r.X += tmp.X;
		r.Y += tmp.Y;
	}
	return r;
};

//replaces attributevalues with new values and returns the object with elements old values
//usage recordState=Zapatec.Utils.changeAttr(element,{variable1:value1,variable2:value2,variable3:{variable3_1:value3_!}}
//for reading out object placement on the parentObject it's better to use offsetLeft,offsetTop,offsetWidth and offsetHeight attributes
// @param (el) [HTML object]
// @param (newWalues) [object] with new values
// @param (dontSet) [boolean] if true, only returns the object's current state
Zapatec.Utils.changeAttributes = function(el, newValues, dontSet){
	if (!(el = Zapatec.Utils.idOrObject(el)) || !newValues) return;
	var ret = {};
	for(a in newValues){
		var b = newValues[a];
		if (b && typeof(b) == 'object' && !b.nodeName) {
			ret[a] = Zapatec.Utils.changeAttributes(el[a], b, false)
		} else {
			ret[a] = el[a] || '';
			if (a == 'nextSibling') {
				if (ret[a] == null) ret.parentNode = el.parentNode;
			}
		}
	}
	if (!dontSet) {
		if (newValues.parentNode && newValues.nextSibling) {
			delete(newValues.parentNode);
		}
		for(a in newValues){
			var b = newValues[a];
			if (b && typeof(b) == 'object' && !b.nodeName) {
				if (!el[a]) el[a] = {};
				ret[a] = Zapatec.Utils.changeAttributes(el[a], b);
			} else {
				//ret[a]=el[a]||'';
				if (/^offset/.test(a)) {
					el.style[a.replace(/^offset(.*)$/,'$1').toLowerCase()] = (b || 0) + 'px'
				} else if (a == 'parentNode') {
					if (b && b.nodeType == 1) b.appendChild(el)
				} else if (a == 'nextSibling') {
					if (b) b.parentNode.insertBefore(el, b);
				} else {
					try {
						el[a]=b;
					} catch(e) {}
				}
			}
		}
	};
	return ret;
}

//Zapatec.Utils.removeAttributes
