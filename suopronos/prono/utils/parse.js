Zapatec.Parse=function(str2parse, delim)
{
this.init()
this.arr=[]
if (arguments.length > 1)
{
	this.str2parse=this.trim(str2parse)
	this.delim=delim
	this.parse(this.str2parse, this.delim)
}

}

Zapatec.Parse.prototype.init=function() {
}

Zapatec.Parse.prototype.arr_clear=function() {
	this.arr=[]
}

Zapatec.Parse.prototype.parse=function(str2parse, delim) 
{
var i
this.arr=str2parse.split(this.delim)
for (i=0; i<this.arr.length; i++)
	this.arr[i]=this.trim(this.arr[i])
}

// return number fields
Zapatec.Parse.prototype.NF=function(s) { return this.arr.length }

// check if field number is in range, 0 to N-1
Zapatec.Parse.prototype.validField=function(iFld) { return iFld >=0 && iFld <=this.NF()-1 }

// get the field based on the field number
Zapatec.Parse.prototype.getField=function(iFld) { 
if (!this.validField(iFld)) return undefined
return this.arr[iFld]
}

// set the field based on the field number, field number must be valid
Zapatec.Parse.prototype.setField=function(iFld, value) { 
if (!validField(iFld)) return undefined
this.arr[iFld]=value
}

// push field to end of list
Zapatec.Parse.prototype.pushField=function(value) { this.arr.push(value) }

// pop field off array
Zapatec.Parse.prototype.popField=function(value) { return this.arr.pop() }

// Add field into list a,b,d,e addField(2,0,c) --> a,b,c,d,e
Zapatec.Parse.prototype.insertField=function(iFld, value) { this.arr.splice(iFld,0,value) }


// trim Leading white spaces
Zapatec.Parse.prototype.pretrim=function(s) { return s.replace(/^\s?/, '') }

// trim Trailing white spaces
Zapatec.Parse.prototype.posttrim=function(s) { return s.replace(/\s?$/, '') }

// trim Leading and Trailing white spaces
Zapatec.Parse.prototype.trim=function(s)
{
	s=this.pretrim(s)
	s=this.posttrim(s)
	return s
}


// For given array, flatfile with delim as separator
Zapatec.Parse.prototype.flatfile=function(delimOut) 
{
var strFld, i, strFlat=''
for (i=0; i<this.NF(); i++)
	{
	strFld=this.getField(i) || ''
	strFld=strFld.replace(/"/, "\"")
	if (strFld.indexOf(delimOut) != -1) 
		// if delim in field then enclose in quotes
		strFld='"' + strFld + '"'
	strFlat+=strFld
	if (i<this.NF()-1)	
		// last field?, no delim
		strFlat += delimOut
	}

	return strFlat
}
