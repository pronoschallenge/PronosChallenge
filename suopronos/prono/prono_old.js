var RCArray = new Array();

function nb_aleatoire(nb)
{
nombre= Math.floor(Math.random() * nb)+1;
} 


function Radio_CheckBox( inIMAGES, inDATA) {
 this.uID = document.uniqueID;
 this.imgPath = inIMAGES[0];
 this.img = new RC_Images(inIMAGES, this);
 this.data = new RC_Read(inDATA, this);
 RCArray[this.uID] = this;
 this.SwapImage = function( inDIV, inIMG, inOBJ)
 {
  if (document.images) {
   if (document.layers && inDIV != null) {
    eval("document." + inDIV + ".document.images['" + inIMG + "'].src = " + inOBJ + ".src;");
   } else {
    document.images[inIMG].src  = eval(inOBJ + ".src");
   }
  }
 };
 this.ToggleCheckBox = function( inINDX )
 {
  var oCB, iObj;
  oCB = this.data.aRead[inINDX];
  if (oCB.state == 1) {
   oCB.state = 0;
   eval("document." + oCB.oForm + "." + oCB.oInput + ".value = '" + oCB.off + "';");
   iObj = this.img.aImg[1][0];
  } else {
   oCB.state = 1;
   eval("document." + oCB.oForm + "." + oCB.oInput + ".value = '" + oCB.on + "';");
   iObj = this.img.aImg[1][1];
  }
  this.SwapImage(oCB.oDiv, "chkb_" + this.uID + "_" + inINDX, "this.img." + iObj);
 };
 this.ToggleRadio = function( inINDX, inRDO, inVALUE)
 {
  var oRdo, i;
  oRdo = this.data.aRead[inINDX];
  eval("document." + oRdo.oForm + "." + oRdo.oInput + ".value = '" + inVALUE + "';");
  for (i = 0; i < oRdo.nb; i++) {
   this.SwapImage(oRdo.oDiv, "rdo_" + this.uID + "_" + inINDX + "_" + i, "this.img." + this.img.aImg[0][0]);
  };
  this.SwapImage(oRdo.oDiv, "rdo_" + this.uID + "_" + inINDX + "_" + inRDO, "this.img." + this.img.aImg[0][1]);
 };
 this.Init = function()
 {
  var i = 0, iDiv;
  for (i = 0; i < this.data.aRead.length; i++) {
   iDiv = document.getElementById(this.data.aRead[i].oDiv);
   if (iDiv.innerHTML != "") {
    iDiv.insertAdjacentHTML("beforeend", "<br />" + this.data.aRead[i].str);
   } else {
    iDiv.innerHTML = this.data.aRead[i].str;
   }
  };
 };
 this.Init();
};

function RC_Images( inIMAGES, inOBJ ) {
 this.rc = inOBJ;
 this.aImg = new Array();
 this.Preload = function()
 {
  var i = 0;
  if (document.images) {
   for (i = 1; i < inIMAGES.length; i++) {
    eval("this." + (inIMAGES[i][0].substring(0, inIMAGES[i][0].indexOf('.'))) + " = new Image();");
    eval("this." + (inIMAGES[i][1].substring(0, inIMAGES[i][1].indexOf('.'))) + " = new Image();");
    eval("this." + (inIMAGES[i][0].substring(0, inIMAGES[i][0].indexOf('.'))) + ".src = '" + this.rc.imgPath + inIMAGES[i][0] + "';");
    eval("this." + (inIMAGES[i][1].substring(0, inIMAGES[i][1].indexOf('.'))) + ".src = '" + this.rc.imgPath + inIMAGES[i][1] + "';");
    this.aImg[this.aImg.length] = new Array((inIMAGES[i][0].substring(0, inIMAGES[i][0].indexOf('.'))), (inIMAGES[i][1].substring(0, inIMAGES[i][1].indexOf('.'))));
   };
  }
 };
 this.Preload();
};

function RC_Read( inDATA, inOBJ) {
 this.rc = inOBJ;
 this.aRead = new Array();
 this.Read = function()
 {
  var i = 0;
  for (i = 0; i < inDATA.length; i++) {
   switch (inDATA[i][0].toUpperCase()) {
    //case "CHECKBOX": this.aRead[this.aRead.length] = new RC_CheckBox(inDATA[i], this.rc, this.aRead.length);break;
    case "RADIO":  this.aRead[this.aRead.length] = new RC_Radio(inDATA[i], this.rc, this.aRead.length);break;
    //case "BUTTON": this.aRead[this.aRead.length] = new RC_Button(inDATA[i], this.rc, this.aRead.length);break;
    //case "FILE": this.aRead[this.aRead.length] = new RC_File(inDATA[i], this.rc, this.aRead.length);break;
   }
  };
 };
 this.Read();
};

//function RC_CheckBox(inDATA, inOBJ, inINDX) {
// this.rc = inOBJ;
// this.indx = inINDX;
// this.oForm = inDATA[1];
// this.oInput = inDATA[2];
// this.oDiv = inDATA[3];
// this.str = "";
// this.state = -1;
// this.on = inDATA[5][0][1];
// this.off = inDATA[5][0][2];
// this.Init = function()
// {
//  var h_value, i_value;
//  if (inDATA[5][0][3].toUpperCase() == "TRUE") {
//   h_value = inDATA[5][0][1];
//   i_value = this.rc.img.aImg[1][1];
//   this.state = 1;
//  } else {
//   h_value = inDATA[5][0][2];
//   i_value = this.rc.img.aImg[1][0];
//   this.state = 0;
//  }
//  ths.str += "<a href=\"javascript:RCArray['" + this.rc.uID + "'].ToggleCheckBox(" + this.indx + ")\"";
//  this.str += " border=\"0\"";
//  if (inDATA[4] != "") {
//   this.str += " class=\"" + inDATA[4] + "\"";
//  }
//  this.str += " onMouseOver=\"window.status=' ';return true;\"";
//  this.str += " onMouseOut=\"window.status=' ';return true;\">";
//  this.str += "<img alt= \"\" name=\"chkb_" + this.rc.uID + "_" + this.indx + "\" src=\"" + eval("this.rc.img." + i_value + ".src") + "\" border=\"0\" align=\"absmiddle\">";
//  this.str += "&nbsp;" + inDATA[5][0][0];
//  this.str += "</a>";
//  this.str += "<input type=\"hidden\" name=\"" + this.oInput + "\" value=\"" + h_value + "\">";
// };
// this.Init();
//};

function RC_Radio(inDATA, inOBJ, inINDX) {
 this.rc = inOBJ;
 this.indx = inINDX;
 this.oForm = inDATA[1];
 this.oInput = inDATA[2];
 this.oDiv = inDATA[3];
 this.nb = inDATA[5].length;
 this.str = ""; 
 this.Init = function() 
 {
  var i = 0, i_value, h_value, isSelected = 0;
  for (i = 0; i < inDATA[5].length; i++) {
   if (inDATA[5][i][2].toUpperCase() == "SELECTED") {
    if (isSelected == 0) {
     i_value = this.rc.img.aImg[0][1];
     h_value = inDATA[5][i][1];
     isSelected = 1;
    } else {
     i_value = this.rc.img.aImg[0][0];
    }
   } else {
    i_value = this.rc.img.aImg[0][0];
   }
   this.str += "<td width=\"12\" height=\"16\" valign=\"bottom\" align=\"center\"><a href=\"javascript:RCArray['" + this.rc.uID + "'].ToggleRadio(" + this.indx + ", " + i + ", '" + inDATA[5][i][1] + "');\"";
   this.str += " border=\"0\"";
   if (inDATA[4] != "") {
    this.str += " class=\"" + inDATA[4] + "\"";
   }
   this.str += " onMouseOver=\"window.status=' ';return true;\"";
   this.str += " onMouseOut=\"window.status=' ';return true;\">";   
   this.str += "<img alt= \"\" name=\"rdo_" + this.rc.uID + "_" + this.indx + "_" + i + "\" src=\"" + eval("this.rc.img." + i_value + ".src") + "\" border=\"0\" align=\"absmiddle\"  width=\"12\" height=\"16\">";
   this.str += "" + inDATA[5][i][0];
   this.str += "</a></td><img src=\"u_2.gif\" alt=\"\"><td width=\"3\"></td>";

  };
  this.str += "<input type=\"hidden\" name=\"" + this.oInput + "\" value=\"" + h_value + "\">";
 };
 this.Init();
};


//function RC_Button(inDATA, inOBJ, inINDX) {
// this.rc = inOBJ;
// this.indx = inINDX;
// this.oInput = inDATA[1];
// this.oDiv = inDATA[2];
// this.onclick = inDATA[3];
// this.str = "";
// this.Init = function()
// {
//  this.str += " <a href=\"javascript:" + this.onclick + "\"";
//  this.str += " border=\"0\"";
//  this.str += " onMouseUp=\"RCArray['" + this.rc.uID + "'].SwapImage('" + this.oDiv + "', 'but_" + this.rc.uID + "_" + this.indx + "', 'this.img." + this.rc.img.aImg[2][0] + "');window.status=' ';return true;\"";
//  this.str += " onMouseDown=\"RCArray['" + this.rc.uID + "'].SwapImage('" + this.oDiv + "', 'but_" + this.rc.uID + "_" + this.indx + "', 'this.img." + this.rc.img.aImg[2][1] + "');window.status=' ';return true;\"";
//  this.str += " onMouseOver=\"window.status=' ';return true;\"";
//  this.str += " onMouseOut=\"RCArray['" + this.rc.uID + "'].SwapImage('" + this.oDiv + "', 'but_" + this.rc.uID + "_" + this.indx + "', 'this.img." + this.rc.img.aImg[2][0] + "');window.status=' ';return true;\">";
//  this.str += "<img alt=\"\" src=\"" + eval("this.rc.img." + this.rc.img.aImg[2][0] + ".src") + "\" name=\"but_" + this.rc.uID + "_" + this.indx + "\" border=\"0\">";
//  this.str += "</a>";
// };
// this.Init();
//};

//function RC_File(inDATA, inOBJ, inINDX) {
// this.rc = inOBJ;
// this.indx = inINDX;
// this.oInput = inDATA[1];
// this.oDiv = inDATA[2];
// this.str = "";
// this.Init = function()
// {
//  this.str += "<input type=\"file\" name=\"" + this.oInput + "\" id=\"" + this.oInput + "\" style=\"display:none;\" onChange=\"document.getElmentById['file_" + this.rc.uID + "_" + this.indx + "'].value=document.getElmentById['" + this.oInput + "'].value;\">";
//  this.str += "<input type=\"text\" class=\"" + inDATA[3] + "\" name=\"file_" + this.rc.uID + "_" + this.indx + "\" id=\"file_" + this.rc.uID + "_" + this.indx + "\" readonly>";
//  this.str += "&nbsp;<a href=\"javascript:document.getElmentById['" + this.oInput + "'].click();\"";
//  this.str += " border=\"0\"";
//  this.str += " onMouseUp=\"RCArray['" + this.rc.uID + "'].SwapImage('" + this.oDiv + "', 'ifl_" + this.rc.uID + "_" + this.indx + "', 'this.img." + this.rc.img.aImg[3][0] + "');window.status=' ';return true;\"";
//  this.str += " onMouseDown=\"RCArray['" + this.rc.uID + "'].SwapImage('" + this.oDiv + "', 'ifl_" + this.rc.uID + "_" + this.indx + "', 'this.img." + this.rc.img.aImg[3][1] + "');window.status=' ';return true;\"";
//  this.str += " onMouseOver=\"window.status=' ';return true;\"";
//  this.str += " onMouseOut=\"RCArray['" + this.rc.uID + "'].SwapImage('" + this.oDiv + "', 'ifl_" + this.rc.uID + "_" + this.indx + "', 'this.img." + this.rc.img.aImg[3][0] + "');window.status=' ';return true;\">";
//  this.str += "<img alt=\"\" src=\"" + eval("this.rc.img." + this.rc.img.aImg[3][0] + ".src") + "\" name=\"ifl_" + this.rc.uID + "_" + this.indx + "\" border=\"0\" align=\"absmiddle\">";
//  this.str += "</a>";
//  this.str += "";
// };
// this.Init();
//};