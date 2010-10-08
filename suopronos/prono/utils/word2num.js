Zapatec.word2num=function() {
this.init()
}

// For given money sentence convert to float
// example: 12 thousand 3 hundred 10 dollar 50 cent --> 12310.50
Zapatec.word2num.prototype.val2num=function(strVal, delim, arrWord2Num)
{
	var p=new Zapatec.Parse(strVal, delim)
	if (p.arr.length==0)
		return 0

	var num, num2, numFinal=0
	for (i=0; i<p.arr.length; i+=2)
	{
		num=parseFloat(p.arr[i])
		word=p.arr[i+1].toLowerCase()
		if (typeof arrWord2Num[word] != 'undefined')
			num *= arrWord2Num[word]
		else // check if plural cents-->cent
		if (word.charAt(word.length-1)=='s')
		{
			word=word.substr(0,word.length-1)
			if (typeof arrWord2Num[word] != 'undefined')
				num *= arrWord2Num[word]
			else
				alert('new nf word:' + word)
		}

		numFinal+=num
	}

	return numFinal;
}

// Populate Arrays
Zapatec.word2num.prototype.init=function() {
this.arrWord2Num_money=[]
this.arrWord2Num_money['trillion'] = 1000000000000
this.arrWord2Num_money['billion']  = 1000000000
this.arrWord2Num_money['million']  = 1000000
this.arrWord2Num_money['thousand'] = 1000
this.arrWord2Num_money['hundred']  = 100
this.arrWord2Num_money['dollar']   = 1
this.arrWord2Num_money['cent']     = .01

var kb=1024
this.arrWord2Num_computer=[]
this.arrWord2Num_computer['gb'] = kb * 1000000000
this.arrWord2Num_computer['mb'] = kb * 1000000
this.arrWord2Num_computer['kb'] = kb 

this.arrWord2Num_hour=[]
this.arrWord2Num_hour['week']   = 7*24
this.arrWord2Num_hour['day']    = 24
this.arrWord2Num_hour['hour']   = 1
}
