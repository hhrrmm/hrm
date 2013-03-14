package calcs
{
	public class Freq
	{
		public function Freq()
		{
		}
		
		public var indiNamesAnnual:Array = new Array(
			'annualInd10', 'annualInd2', 'annualInd3'
			, 'annualInd4',  'annualInd5', 'annualInd7'
		);		
		
		public var indiNamesOther:Array = new Array(
		    'ind1', 'ind11', 'ind12', 'ind13', 'ind14', 'ind15'
		  , 'ind6', 'ind8', 'ind9'
		);
		
		public var indiNames:Array = new Array(
			'ind10', 'ind2', 'ind3', 'ind4', 'ind5', 'ind7'
		);
		
		
		public function convert(baseData:Array, newData:Array) :Array {
			var rez:Array;
			
			var currPoint:Object = newData[0];
			var currYear:String = currPoint['annualPeriod'];
		
			if (currYear) {
				rez = this.ann_2_month(baseData, newData);
			} else {
				rez = this.month_2_month(baseData, newData);
			};
				
			return rez;
		}
		
		//if newData dont have annualPeriod, then just push the newValues to the rez array
		public function month_2_month(baseData:Array, newData:Array):Array {
			var rez:Array = new Array();
			var hh:uint = rez.length;			
			//0.append last 12 points, if needed
			var currPoint:Object = newData[0];
			
			//1.have to check where new and base data inetrsect
			var currYear:String = currPoint['period'];
			//rez.push(baseData[0]);
			for (var u:uint = 0; u < baseData.length; u++) {
				if (baseData[u]['period'] == currYear) {	
					break;
				} else {
					rez.push(baseData[u]);
				};
			};			
			hh = u;
			
			for (var i:uint = hh; i < baseData.length; i++) {
				var tempRez:Array = new Array();
				tempRez['period'] = baseData[i]['period'];
				
				if (baseData[i]['annualPeriod']) {
					tempRez['annualPeriod'] = baseData[i]['annualPeriod'];
				};		
				tempRez['type'] = 'New';
				
				for each (var nm:String in indiNames) {
					// here we leave the same data
					tempRez[nm] = baseData[i][nm];
				};
				
				for each (nm in indiNamesAnnual) {
					// here we leave the same data
					tempRez[nm] = baseData[i][nm];
				};
				
				for each (nm in indiNamesOther) {
					// here we put new data!!
					//tempRez[nm] = baseData[i][nm];
					if ((i - hh) < newData.length){ 
						tempRez[nm] = (Number)(newData[i - hh][nm]);
					} else {
						tempRez[nm] = baseData[i][nm];
					};
					// new data!!!
				};
				
				rez.push(tempRez);
			};
			
			return rez;
			
		}
		
		//annual freq conversion to monthly data
		public function ann_2_month(baseData:Array, newData:Array):Array {			
			var rez:Array = new Array();			
			var ratios:Array = new Array();			
			var currPoint:Object = newData[0];
			//0. get the starting position
			var currYear:String = currPoint['annualPeriod'];
			rez.push(baseData[0]);
			for (var u:uint = 0; u < baseData.length; u++) {
				if (baseData[u]['annualPeriod'] == currYear) {	
					break;
				} else {
					if (u > 11) {
						rez.push(baseData[u - 11]);
					};
				};
			};			
			var minIndex:uint = u;
			
			for (var f:uint = 0; f < newData.length; f++) {
				//1. get first year of newData
				currPoint = newData[f];				
				var basePoint:Object = new Object();
				//2.find according year in baseData
				currYear = currPoint['annualPeriod'];				
				for (u = minIndex; u < baseData.length; u++) {
					if (baseData[u]['annualPeriod'] == currYear) {	break;};
				};
				basePoint = baseData[u];			
				//3.get ratio array for all annual indicators
				for each (var name:String in indiNames) {
					var annName:String = "annualI" + name.substr(1);
					ratios[name] = currPoint[annName]/basePoint[annName];
				};			
				//4. iterate through 12 months, by multiplying baseData monthly by ratio[indiX]			
				for (var i:uint = 1; i <= 12; i++) {
					var tempRez:Array = new Array();
					tempRez['period'] = baseData[u - 12 + i]['period'];					
					tempRez['type'] = 'New';
					for each (name in indiNames) {
						tempRez[name] = baseData[u - 12 + i][name] * ratios[name];
					};					
					if (i == 12) {
						tempRez['annualPeriod'] = currYear;
						for each (name in indiNamesAnnual) {
							tempRez[name] = newData[f][name];
						};
					};
					for each (name in indiNamesOther) {
						tempRez[name] = baseData[u - 12 + i][name];
					};
					rez.push(tempRez);
				};
				//5. push new object into rez array								
			};
			var hh:uint = rez.length;			
			//6.append last 12 points, if needed
			for (i = hh; i < baseData.length; i++) {
				tempRez = new Array();
				tempRez['period'] = baseData[i]['period'];
				tempRez['annualPeriod'] = currYear;		
				tempRez['type'] = 'New';
				for each (name in indiNames) {
					tempRez[name] = baseData[i][name];
				};					
				for each (name in indiNamesAnnual) {
					tempRez[name] = baseData[i][name];
				};
				for each (name in indiNamesOther) {
					tempRez[name] = baseData[i][name];
				};	
				rez.push(tempRez);
			};
			
			return rez;
		}
		
	}
}