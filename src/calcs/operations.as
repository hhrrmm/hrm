package calcs
{
	import flash.utils.ByteArray;
	
	import mx.charts.AreaChart;
	import mx.collections.ArrayCollection;
	
	import org.osmf.layout.AbsoluteLayoutFacet;
	
	//	import valueObjects.Indicator;
	
	public class operations
	{
		public function operations()
		{
		}
		
		/*		public function clone_deep(source:ArrayCollection):ArrayCollection {
		var rez:ArrayCollection = new ArrayCollection();
		for each (var indi:Indicator in source) {
		var indi_rez:Indicator = new Indicator();
		indi_rez.data = new ArrayCollection();
		for each(var point:Object in indi.data) {
		indi_rez.data.addItem(clone(point));
		};
		rez.addItem(indi_rez);				
		}
		return rez;
		}*/
		
		public function clone(source:Object):*
		{
			var myBA:ByteArray = new ByteArray();
			myBA.writeObject(source);
			myBA.position = 0;
			return(myBA.readObject());
		}
		
		public function joinSeries(a:ArrayCollection, b:ArrayCollection):ArrayCollection {
			var rez:ArrayCollection = new ArrayCollection();
			
			var temp:Object = new Object()
			var temp2:Object = new Object();
			
			for (var i:uint = 0; ((i < a.length)&&(i < b.length)); i++) {
				temp = a.getItemAt(i);
				temp2 = b.getItemAt(i);
				
				rez.addItem(
					{'timeID' :temp.timeID,
						'timeName':temp.timeName,
						'val1'	  :temp.val,
						'val2'	  :temp2.val
					});
			};			 
			
			return rez;
		}
		
		public function calc_gr_rates(source:ArrayCollection, type:uint, ind2Type:Number):Array {
			
			var rez:Array = new Array();			
			var temp:Object = new Object();
			var temp2:Object = new Object();
			
			var diff_hist:Object = null;
			var diff_base:Object = null;
			var diff_newScn:Object = null;
			var diff_base2:Object = null;
			var diff_newScn2:Object = null;
			var diff_hist2:Object = null;
			var start_index:int = 1;
			
			var last_index:int = 0;
			var last_index2:int = 0;
			for (var jj:int = 0; jj < source.length; jj++) { 
				var temp_divv:Object = (source.getItemAt(jj).newScn);
				if (temp_divv) {
					last_index = jj;									
					break;
				};
			};
			
			for (var j1:int = 0; j1 < source.length; j1++) { 
				var temp_div1:Object = (source.getItemAt(j1).newScn2);
				if (temp_div1) {
					last_index2 = j1;									
					break;
				};
			};
			
			if (ind2Type == 0) { //refers wheter to use growth rate or difference
				if (type == 0) {
					return source.source;
				} else {
					if (type == 1) {
						start_index = 1;
					} else if(type == 2) {
						start_index = 4;
					} else {
						start_index = 12;
					};
					
					for (var i:uint = start_index; i < source.length; i++) {
						temp = source.getItemAt(i);
						var temp_div:Object = (source.getItemAt(i - start_index).base);
						//to check whether we have zero or null
						if (temp_div){
							//if zero - do not divide
							//base
							if (temp_div != 0) {
								diff_base = 100*(source.getItemAt(i).base - source.getItemAt(i-start_index).base)/(Number)(temp_div);
							} else {
								diff_base = 0;						
							}
						} else {
							diff_base = null;						
						};
						
						//base2
						var temp_div2:Object = (source.getItemAt(i - start_index).base2);
						if ((temp_div2)&&(source.getItemAt(i).base2)) {
							if (temp_div2 != 0) {
								diff_base2 = 100*(source.getItemAt(i).base2 - source.getItemAt(i-start_index).base2)/(Number)(temp_div2);
							} else {
								diff_base2 = 0;						
							}
						}
						else {
							diff_base2 = null;						
						};
						
						//newScn
						var temp_div_n:Object = (source.getItemAt(i-start_index).newScn);					
						if (temp_div_n){
							if (temp_div_n != 0) {
								diff_newScn = 100*(source.getItemAt(i).newScn - source.getItemAt(i-start_index).newScn)/(Number)(temp_div_n);
							} else {
								diff_newScn = 0;						
							}
						} else if ((temp_div)&&(i >= last_index)) {
							//diff_newScn  = diff_base;						
							diff_newScn = 100*(source.getItemAt(i).newScn - source.getItemAt(i-start_index).base)/(Number)(temp_div);
						} else {
							diff_newScn = null;						
						};
						
						//newScn2
						/*if (i > 330) {
							var a:uint = 1+1; 
						} */
						var temp_div_n2:Object = (source.getItemAt(i-start_index).newScn2);					
						if ((temp_div_n2)&&(source.getItemAt(i).newScn2)) {
							if (temp_div_n2 != 0) {
								diff_newScn2 = 100*(source.getItemAt(i).newScn2 - source.getItemAt(i-start_index).newScn2)/(Number)(temp_div_n2);
							} else {
								diff_newScn2 = 0;						
							}
						} else if ((temp_div2)&&(source.getItemAt(i).newScn2)&&(i >= last_index2)) {
							//diff_newScn2  = diff_base2;
							diff_newScn2 = 100*(source.getItemAt(i).newScn2 - source.getItemAt(i-start_index).base2)/(Number)(temp_div2);
						} else {
							diff_newScn2 = null;						
						};
						
						//history
						var temp_div_h:Object = (source.getItemAt(i - start_index).history);
						if (temp_div_h){
							//if zero - do not divide
							if (temp_div_h != 0) {
								diff_hist = 100*(source.getItemAt(i).history - source.getItemAt(i-start_index).history)/(Number)(temp_div_h);
							} else {
								diff_hist = 0;						
							}
						} else {
							diff_hist = null;						
						};
						
						//history2
						var temp_div_h2:Object = (source.getItemAt(i - start_index).history2);
						if (temp_div_h2){
							if (temp_div_h2 != 0) {
								diff_hist2 = 100*(source.getItemAt(i).history2 - source.getItemAt(i-start_index).history2)/(Number)(temp_div_h2);
							} else {
								diff_hist2 = 0;						
							}
						} else {
							diff_hist2 = null;						
						};
						
						rez.push({
							'period'    :temp.period,
							'base'      :diff_base,
							'base2' 	:diff_base2,
							'history'   :diff_hist,
							'history2' 	:diff_hist2,
							'newScn'    :diff_newScn,
							'newScn2'	:diff_newScn2
						});
					};			 						
				};//if type <> 0
				
			}// if ind2Type == 0
				//refers wheter to use growth rate or difference
			else if (ind2Type == 1) {
				if (type == 0) {
					return source.source;
				} else {
					if (type == 1) {
						start_index = 1;
					} else if(type == 2) {
						start_index = 3;
					} else {
						start_index = 12;
					};
					
					for (var j:uint = start_index; j < source.length; j++) {
						temp = source.getItemAt(j);
						//base
						temp_div = source.getItemAt(j-start_index).base;
						if (temp_div == null) { 
							diff_base = null; 
						} else {
							//diff_base = source.getItemAt(j).base - source.getItemAt(j-start_index).base;
							if (temp_div != 0) {
								diff_base = 100*(source.getItemAt(j).base - source.getItemAt(j-start_index).base)/(Number)(temp_div);
							} else {
								diff_base = 0;						
							};
						};
						
						//base2
						temp_div2 = source.getItemAt(j-start_index).base2;
						if ((temp_div2)&&(source.getItemAt(j).base2)) {
							diff_base2 = source.getItemAt(j).base2 - (Number)(temp_div2);
						} else {
							diff_base2 = null; 
						};
						
						//newScn
						temp_div_n = source.getItemAt(j-start_index).newScn;		
						//if (source.getItemAt(j-start_index).newScn == null) { diff_newScn = null; }
						if (temp_div_n) {
							if (temp_div_n != 0) {
								diff_newScn = 100*(source.getItemAt(j).newScn - source.getItemAt(j-start_index).newScn)/(Number)(temp_div_n);
							} else {
								diff_newScn = 0;						
							}
						} else if ((temp_div)&&(i >= last_index)) {
							//diff_newScn = diff_base;
							diff_newScn = 100*(source.getItemAt(j).newScn - source.getItemAt(j-start_index).base)/(Number)(temp_div);							
						} else {
							diff_newScn = null;
						};
						
						//newScn2
						if ((source.getItemAt(j-start_index).newScn2)&&(source.getItemAt(j).newScn2)) {
							diff_newScn2 = source.getItemAt(j).newScn2 - source.getItemAt(j-start_index).newScn2;
						} else if ((temp_div2)&&(i >= last_index)) {
							diff_newScn2 = 100*(source.getItemAt(j).newScn2 - source.getItemAt(j-start_index).base2);
						}
						else { 
							diff_newScn2 = null; 
						};
						
						//history
						if (source.getItemAt(j-start_index).hist == null) { diff_hist = null; }	
						else {
							diff_hist = source.getItemAt(j).hist - source.getItemAt(j-start_index).hist;
						}
						
						//history2
						if (source.getItemAt(j-start_index).hist2 == null) { diff_hist2 = null; }	
						else {
							if (temp_div_n != 0) {
								diff_hist2 = 100*(source.getItemAt(j).hist2 - source.getItemAt(j-start_index).hist2)/(Number)(source.getItemAt(j-start_index).hist2);
							} else {
								diff_hist2 = 0;						
							}
							//diff_hist2 = source.getItemAt(j).hist2 - source.getItemAt(j-start_index).hist2;
						}
						
						rez.push({
							'period'    :temp.period,
							'base'      :diff_base,
							'base2' 	:diff_base2,
							'history'   :diff_hist,
							'history2' 	:diff_hist2,
							'newScn'    :diff_newScn,
							'newScn2'	:diff_newScn2
						});
					};			 						
				};//if type <> 0
			} // if ind2Type == 1
			
			return rez;
		}
		
		public function calc_gr_rates_2(source:ArrayCollection, type:uint, ind2Type:Number):Array {
			
			var rez:Array = new Array();			
			var temp:Object = new Object();
			var temp2:Object = new Object();
			
			var diff_hist:Object = null;
			var diff_base:Object = null;
			var diff_newScn:Object = null;
			var diff_base2:Object = null;
			var diff_newScn2:Object = null;
			var diff_hist2:Object = null;
			var start_index:int = 1;
			
			var last_index:int = 0;
			for (var jj:int = 0; jj < source.length; jj++) { 
				var temp_divv:Object = (source.getItemAt(jj).newScn);
				if (temp_divv) {
					last_index = jj;									
					break;
				};
			};
			
			if (ind2Type == 0) { //refers wheter to use growth rate or difference
				if (type == 0) {
					return source.source;
				} else {
					if (type == 1) {
						start_index = 1;
					} else if(type == 2) {
						start_index = 4;
					} else {
						start_index = 12;
					};
					
					for (var i:uint = start_index; i < source.length; i++) {
						temp = source.getItemAt(i);
						var temp_div:Object = (source.getItemAt(i - start_index).base);
						//to check whether we have zero or null
						if (temp_div){
							//if zero - do not divide
							//base
							if (temp_div != 0) {
								diff_base = 100*(source.getItemAt(i).base - source.getItemAt(i-start_index).base)/(Number)(temp_div);
							} else {
								diff_base = 0;						
							}
						} else {
							diff_base = null;						
						};
						
						//base2
						var temp_div2:Object = (source.getItemAt(i - start_index).base2);
						if (temp_div2){
							if (temp_div2 != 0) {
								diff_base2 = 100*(source.getItemAt(i).base2 - source.getItemAt(i-start_index).base2)/(Number)(temp_div2);
							} else {
								diff_base2 = 0;						
							}
						}
						else {
							diff_base2 = null;						
						};
						
						if (i > 370) {
							var s:int = 0;
						}
						
						//newScn
						var temp_div_n:Object = (source.getItemAt(i-start_index).newScn);					
						if (temp_div_n){
							if (temp_div_n != 0) {
								diff_newScn = 100*(source.getItemAt(i).newScn - source.getItemAt(i-start_index).newScn)/(Number)(temp_div_n);
							} else {
								diff_newScn = 0;						
							}
						} else if ((temp_div)&&(i >= last_index)) {
							//diff_newScn  = diff_base;						
							diff_newScn = 100*(source.getItemAt(i).newScn - source.getItemAt(i-start_index).base)/(Number)(temp_div);
						} else {
							diff_newScn = null;						
						};
						
						//newScn2
						var temp_div_n2:Object = (source.getItemAt(i-start_index).newScn2);					
						if (temp_div_n2){
							if (temp_div_n2 != 0) {
								diff_newScn2 = 100*(source.getItemAt(i).newScn2 - source.getItemAt(i-start_index).newScn2)/(Number)(temp_div_n2);
							} else {
								diff_newScn2 = 0;						
							}
						} else if ((temp_div2)&&(i >= last_index)) {
							//diff_newScn2  = diff_base2;
							diff_newScn2 = 100*(source.getItemAt(i).newScn2 - source.getItemAt(i-start_index).base2)/(Number)(temp_div2);
						} else {
							diff_newScn2 = null;						
						};
						
						//history
						var temp_div_h:Object = (source.getItemAt(i - start_index).history);
						if (temp_div_h){
							//if zero - do not divide
							if (temp_div_h != 0) {
								diff_hist = 100*(source.getItemAt(i).history - source.getItemAt(i-start_index).history)/(Number)(temp_div_h);
							} else {
								diff_hist = 0;						
							}
						} else {
							diff_hist = null;						
						};
						
						//history2
						var temp_div_h2:Object = (source.getItemAt(i - start_index).history2);
						if (temp_div_h2){
							if (temp_div_h2 != 0) {
								diff_hist2 = 100*(source.getItemAt(i).history2 - source.getItemAt(i-start_index).history2)/(Number)(temp_div_h2);
							} else {
								diff_hist2 = 0;						
							}
						} else {
							diff_hist2 = null;						
						};
						
						rez.push({
							'period'    :temp.period,
							'base'      :diff_base,
							'base2' 	:diff_base2,
							'history'   :diff_hist,
							'history2' 	:diff_hist2,
							'newScn'    :diff_newScn,
							'newScn2'	:diff_newScn2
						});
					};			 						
				};//if type <> 0
				
			}// if ind2Type == 0
				//refers wheter to use growth rate or difference
			else if (ind2Type == 1) {
				if (type == 0) {
					return source.source;
				} else {
					if (type == 1) {
						start_index = 1;
					} else if(type == 2) {
						start_index = 3;
					} else {
						start_index = 12;
					};
					
					for (var j:uint = start_index; j < source.length; j++) {
						temp = source.getItemAt(j);
						//base
						temp_div = source.getItemAt(j-start_index).base;
						if (temp_div == null) { diff_base = null; }	
						else {
							//diff_base = source.getItemAt(j).base - source.getItemAt(j-start_index).base;
							if (temp_div != 0) {
								diff_base = (source.getItemAt(j).base - source.getItemAt(j-start_index).base);
							} else {
								diff_base = 0;						
							}
						}
						
						//base2
						if (source.getItemAt(j-start_index).base2 == null) { diff_base2 = null; }	
						else {
							diff_base2 = source.getItemAt(j).base2 - source.getItemAt(j-start_index).base2;
						}
						
						//newScn
						temp_div_n = (source.getItemAt(j-start_index).newScn);		
						//if (source.getItemAt(j-start_index).newScn == null) { diff_newScn = null; }	
						if (temp_div_n == null) { diff_newScn = null; }
						else {
							if (temp_div_n != 0) {
								diff_newScn = (source.getItemAt(j).newScn - source.getItemAt(j-start_index).newScn);
							} else {
								diff_newScn = 0;						
							}
							//diff_newScn = source.getItemAt(j).newScn - source.getItemAt(j-start_index).newScn;
						}
						
						//newScn2
						if (source.getItemAt(j-start_index).newScn2 == null) { diff_newScn2 = null; }	
						else {
							diff_newScn2 = source.getItemAt(j).newScn2 - source.getItemAt(j-start_index).newScn2;
						}
						//history
						if (source.getItemAt(j-start_index).hist == null) { diff_hist = null; }	
						else {
							diff_hist = source.getItemAt(j).hist - source.getItemAt(j-start_index).hist;
						}
						
						//history2
						if (source.getItemAt(j-start_index).hist2 == null) { diff_hist2 = null; }	
						else {
							if (temp_div_n != 0) {
								diff_hist2 = (source.getItemAt(j).hist2 - source.getItemAt(j-start_index).hist2);
							} else {
								diff_hist2 = 0;						
							}
							//diff_hist2 = source.getItemAt(j).hist2 - source.getItemAt(j-start_index).hist2;
						}
						
						rez.push({
							'period'    :temp.period,
							'base'      :diff_base,
							'base2' 	:diff_base2,
							'history'   :diff_hist,
							'history2' 	:diff_hist2,
							'newScn'    :diff_newScn,
							'newScn2'	:diff_newScn2
						});
					};			 						
				};//if type <> 0
			} // if ind2Type == 1
			
			return rez;
		}
		
		
		public function calc_gr_rates_complex(source:ArrayCollection, type:uint):Array {
			
			var rez:Array = new Array();			
			var temp:Object = new Object();
			var temp2:Object = new Object();
			
			var diff_base:Object = null;			
			var diff_newScn:Object = null;			
			
			var start_index:int = 1;
			
			var base_nam:Array = new Array();
			var newScn_nam:Array = new Array();
			
			for (var i:uint = 1; i < 12; i++) {
				var a:String = new String("base".concat(i));
				base_nam.push(a);
				var b:String = new String("newScn".concat(i));
				newScn_nam.push(b);
			};
			
			if (type == 0) {
				return source.source;
			} else {
				if (type == 1) {
					start_index = 1;				
				} else if (type == 2) {
					start_index = 3;
				} else {
					start_index = 12;
				}
				
				for (i = start_index; i < source.length; i++) {
					temp = source.getItemAt(i);
					temp2 = source.getItemAt(i - start_index);
					
					rez.push({
						'period'    :temp.period});						
					
					//iterate through names array
					for (var j:uint = 0; j < base_nam.length; j++) {					
						var temp_div:Object	  =  temp[base_nam[j]];
						var temp_div_2:Object = temp2[base_nam[j]];						
						var s:Object = null;
						var div_rez:Object = 0;
						s = (((temp_div)||(temp_div_2 == 0))? 1 : null ); 
						if (s) {
							div_rez = -(100*( 1 - (Number)(temp_div)/(Number)(temp_div_2)));
						} else {
							div_rez = null;
						};						
						rez[rez.length - 1][base_nam[j]] = div_rez;
					};				
					
					for (j = 0; j < newScn_nam.length; j++) {					
						temp_div	 =  temp[newScn_nam[j]];
						temp_div_2	 = temp2[newScn_nam[j]];						
						s			 = null;
						div_rez 	 = 0;
						s = (((temp_div)||(temp_div_2 == 0))? 1 : null ); 
						if (s) {
							div_rez = -(100*( 1 - (Number)(temp_div)/(Number)(temp_div_2)));
						} else {
							div_rez = null;
						};						
						rez[rez.length - 1][newScn_nam[j]] = div_rez;
					};
					
				};			 					
				return rez;
			};
			
		}
		
		public function transform_data_one(source:timeSeries):timeSeries {
			var rez:timeSeries = new timeSeries();			
			var temp:Object = new Object();
			//var temp2:Array =  new Array();
			
			for (var i:uint = 1; i < source.data.length; i++) {
				temp = source.data.getItemAt(i);
				
				var diff:Number = 100*(source.data.getItemAt(i).val - source.data.getItemAt(i-1).val)/(source.data.getItemAt(i-1).val);
				rez.data.addItem({'timeID':temp.timeID,
					'timeName':temp.timeName,
					'val':diff});
			};			 
			
			return rez;
		}		
		
		public function change_freq(source:Array, init_freq:Number, rez_freq:Number, agg_type:Number, HRunits:Array = null, isSales:Boolean = false):Array {
			var a:Array = new Array();
			//if ((init_freq == 1)&&(rez_freq == 3)) {
			if (init_freq == 1) {
				switch (agg_type) {
					case 1: //sum agg_type == 1
						a = aggregate(source, agg_type, rez_freq, HRunits, isSales);
						break;
					case 2: //average agg_type == 2						
						a = aggregate(source, agg_type, rez_freq, HRunits, isSales);
						break;
					case 3: //CPI  agg_type == 3
						var b:Array = new Array();
						b = aggregate(source, agg_type, rez_freq, HRunits, isSales);
						a = mult(b, 100); 
						break;
					case 4: //prices
						a = aggregate(source, agg_type, rez_freq, HRunits, isSales);
						break;
					default:
						break;
				};
			};
			
			return a;
		}
		
		public function mult(src:Array, factor:Number):Array {
			var rez:Array = new Array();
			for (var u:uint = 0; u < src.length; u++) {
				var temp:Object = src[u];
				
				if (temp['base']) {
					temp['base'] = temp['base']*100;
				} else {
					temp['base'] = null;
				};				
				if (temp['newScn']) {
					temp['newScn'] = temp['newScn']*100;
				} else {
					temp['newScn'] = null;
				};
				
				rez.push(temp);
			};		
			return rez;
		}
		
		public function change_freq_2(source:Array, init_freq:Number, rez_freq:Number, agg_type:Number, agg_type_right:Number,  HRunits:Array = null):Array {
			var a:Array = new Array();
			var a2:Array = new Array();
			var rr:Array = new Array();
			//if ((init_freq == 1)&&(rez_freq == 3)) {
			if (init_freq == 1) {			
				//array a for sales volume/value
				a = aggregate(source, agg_type, rez_freq, HRunits, true);
				//extract second indicator from source
				//second array for input indicators
				var b0:Array = splitArray(source);
				//aggregate 2nd indicator
				//add if's - to chech if we need aggregation!!!!
				if (agg_type_right > 0) {
					if (agg_type_right == 3) {
						// here we need to distinguish between sales volume 
						// for volume - use fiscal year when aggregating, or function aggregate						
						
						var b:Array = new Array();
						//b = aggregate(b0, agg_type_right, rez_freq, HRunits, isVolume);
						b = aggregate(b0, agg_type_right, rez_freq, HRunits, false);
						a2 = mult(b, 100);
					} else {
						var isVolume:Boolean = false;
						// comm: 04-03
						//isVolume = (source[0].base == source[0].base2);
						
						a2 = aggregate(b0, agg_type_right, rez_freq, HRunits, isVolume);
					};
				} else {
					//labels????
					//just skip every 12 item of the array
					a2 = annualizeArray(b0);
					//a2 = b0;
				};
				//merge both indicators
				rr = mergeArrays(a, a2);
			};
			return rr;
		}
		
		public function annualizeArray(b:Array) : Array {
			var rez:Array = new Array();			
			for (var i:uint = 0; i < b.length; i++) {
				var s:Number = (Number)((String)(b[i]['period']).substr(0, 4));
				var s1:Number = (Number)((String)(b[i]['period']).substr(5, 2));
				if ((s == 1986)&&(s1 == 12)) {break;};
			};			
			
			for (i; i < b.length; i = i + 12) {
				rez.push({
					'period':(String)(b[i]['period']).substr(0, 4),
					'base'  :b[i]['base'],
					'newScn':b[i]['newScn']
				});
			};			
			return rez;
		}
		
		public function splitArray(source:Array):Array {
			var rez:Array = new Array();
			var newScn:Object = 0;
			var base:Object = 0;
			var per:String = "";
			
			for (var i:uint = 0; i < source.length; i++) {
				var temp:Object = source[i];
				per = temp['period'];
				base = temp['base2'];
				newScn = temp['newScn2'];
				
				rez.push({
					'period':per
					,'base'	 :(base ? Number(base) : null)
					,'newScn':(newScn ? Number(newScn) : null)
				});
			};						
			return rez;
		}
		
		public function mergeArrays(a:Array, b:Array):Array {
			var rez:Array = new Array();
			
			var newScn:Object = 0;
			var base:Object = 0;
			var newScn2:Object = 0;
			var base2:Object = 0;
			
			var per:String = "";
			
			for (var i:uint = 0; i < a.length; i++) {
				var temp:Object = a[i];
				per = temp['period'];
				base = temp['base'];
				newScn = temp['newScn'];
				
				var temp2:Object = b[i];	
				if (temp2) {
					base2 = temp2['base'];
					newScn2 = temp2['newScn'];
				} else {
					base2 = null;
					newScn2 = null;
				};
				
				
				rez.push({
					'period':per
					,'base'	 :base
					,'newScn':newScn
					,'base2' :base2
					,'newScn2':newScn2
				});
			};						
			return rez;
		}
		
		public function aggregate(source:Array, agg_type:uint, rez_freq:uint, HRunits:Array = null, sales:Boolean = false):Array { 
			var indConst:int = 1;
			var const_divider:Number = 1.14542924249534; // base - year - 1983
			//1.24015774665188 - for quarter
			//var initial_level:Number = 1.24204921247618; //level of CPI at 1985 for initial values, 
			var initial_level:Number = 1;
			//since time series do not start at same time
			var initial_start:Number;			
			var sales_index:Number = 0;
			
			if (rez_freq == 3) { //annual
				indConst = 12;				
				//const_divider = 1.209381942; // base - year - 1983
				const_divider = 1.09550058667832;
				//initial_level = 1.30677259178366;
				initial_level = 1;
				
				if ((source[12]['period']).toString().substr(0, 4) == '1982') {
					initial_level = 1;
				};
				//adjustment for annual data when we have sales value
				if (sales) {
					sales_index = 9;
				} else {
					sales_index = 12;
				};
				initial_start = indConst + sales_index;
				
			} else { //quarterly
				indConst = 3;				
				const_divider = 1.24015774665188; // base - year - 1983
				initial_level = 1.34476831516851;
				
				if ((source[3]['period']).toString().substr(0, 4) == '1980') {
					initial_level = 1;
				};
				initial_start = indConst;
			};	
			//agg_type - 1 - average, 0 - sum			
			var rez:Array = new Array();
			var ann:Number = 0;
			var ann_new:Number = 0;
			var isNull:Boolean = true;
			var isNull_new:Boolean = true;			
			var yr:String = "1980";
			var mm:Number = 1;			
			var base_val:Object = 0;
			var newScn_val:Object = 0;			
			var base_ratio:Object = 1;
			var newScn_ratio:Object = 1;
			var base_avrg_ratio:Object = 0;
			var newScn_avrg_ratio:Object = 0;			
			var base_prod:Number = 1;
			var newScn_prod:Number = 1;			
			var base_value:Object = 0;
			var newScn_value:Object = 0;			
			var base_price:Object = 0;
			var newScn_price:Object = 0;			
			var base_units:Object = 0;
			var newScn_units:Object = 0;			
			
			rez.push({
				//for usual way
				//'period' : ((rez_freq == 2) ? yr + 'Q' + (mm/indConst) : yr)
				//adjusted for fiscal year - real Q1 = fiscal Q2
				'period' : ((source[initial_start - indConst]['period']).toString().substr(0, 4))
				,'base'  : (null)
				,'newScn': (null)
			});
			
			var delta:int = 0;
			var f:uint = initial_start;
			var mo:String = (source[f]['period']).toString().substr(4);
			mm = (Number)(mo.substring(1));			
			//if (((mm + 3) >= 12)&&(rez_freq == 3)&&(sales)) delta = 3 else delta= 0;			
			
			for (f = initial_start; f < (source.length - delta); f++) {
				
				yr = (source[f]['period']).toString().substr(0, 4);				
				mo = (source[f]['period']).toString().substr(4);				
				mm = (Number)(mo.substring(1));
				if ((rez_freq == 3)&&(sales)) { 
					mm = (mm + 3)%12; 
				} else {
					mm = (Number)(mo.substring(1));
				};
				
				base_val = source[f]['base'];
				newScn_val = source[f]['newScn'] ;
				
				if (source[f-indConst]['base']) {
					base_ratio = source[f]['base']/source[f-indConst]['base'];
				} else {
					base_ratio = null;
				};
				if (source[f-indConst]['newScn']) {
					newScn_ratio = source[f]['newScn']/source[f-indConst]['newScn'];
				} else if (base_ratio){
					newScn_ratio = source[f]['base']/source[f-indConst]['base'];
				} else {
					newScn_ratio = null;
				};
				
				if (agg_type == 4) {
					base_value =   HRunits[f]['base']*source[f]['base'];
					newScn_value = HRunits[f]['newScn']*source[f]['newScn'];
				};
				
				/*var s:int = mm - 3 * (mm / 3);
				var s2:int = (mm % 3);
				var s1:int = (mm % 12);*/
				if ((mm % indConst) > 0 ) {
					//if (mm < 12) {					
					if (base_val != null) {
						ann = ann + (Number)(base_val);
						base_avrg_ratio = base_avrg_ratio + base_ratio;
						base_price = base_price + (Number)(base_value);
						base_units = (HRunits ? base_units + HRunits[f]['base'] : null); 
						isNull = false;
					} else {
						isNull = true;
					};
					
					if (newScn_val != null) {
						ann_new = ann_new + (Number)(newScn_val);	
						newScn_avrg_ratio = newScn_avrg_ratio + newScn_ratio; 
						newScn_price = newScn_price + newScn_value;
						newScn_units = (HRunits ? newScn_units + HRunits[f]['newScn'] : null); 
						isNull_new = false;
					} else if (!isNull)	{
						ann_new = ann_new + (Number)(base_val);
						newScn_avrg_ratio = newScn_avrg_ratio + base_ratio;
						newScn_price = newScn_price + base_value;
						newScn_units = (HRunits ? newScn_units + HRunits[f]['base'] : null);
						isNull_new = true;
					};	
					
				} else {					
					if (base_val) {
						ann = ann + (Number)(base_val);		
						base_avrg_ratio = base_avrg_ratio + base_ratio;
						base_price = base_price + base_value;
						base_units = (HRunits ? base_units + HRunits[f]['base'] : null);;
						isNull = false;
					} else {
						isNull = true;
					};
					
					if (newScn_val) {
						ann_new = ann_new + (Number)(newScn_val);
						newScn_avrg_ratio = newScn_avrg_ratio + newScn_ratio;
						newScn_price = newScn_price + newScn_value;
						newScn_units = (HRunits ? newScn_units + HRunits[f]['newScn'] : null); 
						isNull_new = false;
					} else if (!isNull)	{
						ann_new = ann_new + (Number)(base_val);
						newScn_avrg_ratio = newScn_avrg_ratio + base_ratio;
						newScn_price = newScn_price + base_value;
						newScn_units = (HRunits ? newScn_units + HRunits[f]['base'] : null); 
						isNull_new = true;
					} else {
						isNull_new = true;
					};
					
					if (agg_type == 2) { // average
						ann = (ann / indConst);
						ann_new = (ann_new / indConst);
					} else if (agg_type == 1) { // sum
						ann = ann;
						ann_new = ann_new;
					} else if (agg_type == 3) { // CPI
						base_avrg_ratio = ((Number)(base_avrg_ratio) / indConst);
						newScn_avrg_ratio = ((Number)(newScn_avrg_ratio) / indConst);
						
						base_prod = base_prod * (Number)(base_avrg_ratio);
						newScn_prod = newScn_prod * (Number)(newScn_avrg_ratio);
						
						ann = initial_level * base_prod/const_divider;
						ann_new = initial_level * newScn_prod/const_divider;
					} else if (agg_type == 4) {
						base_price = (Number)(base_price) / (Number)(base_units);
						newScn_price = (Number)(newScn_price) / (Number)(newScn_units);
						
						ann = (Number)(base_price);
						ann_new = (Number)(newScn_price);
					};
					
					if (rez_freq == 3) {
						if ((f + 12) < source.length) {
							if ((!source[f]['newScn'])&&(source[f + 12]['newScn'])) {
								isNull_new = false;
							};
						}
					} else if (rez_freq == 2) {
						if ((f + 3) < source.length) {
							if ((!source[f]['newScn'])&&(source[f + 3]['newScn'])) {
								isNull_new = false;
							};
						};
					};
					
					if (rez_freq == 2) {
						var realQ:Number = mm/indConst;
						if (realQ < 4) {
							realQ = realQ + 1;
						} else {
							realQ = 1;
							yr = (Number)(yr) + 1;
						};
						var fiscalQ:String = yr + 'Q' + realQ;
					};
					
					var fiscYr:String;
					if ((rez_freq == 3)&&(sales)) {						
						//if (mm >= 10) {
						fiscYr = ((Number)(yr) ).toString();
						/*} else {
						fiscYr = yr;
						}*/
					} else {
						fiscYr = ((Number)(yr)).toString();
					};
					
					rez.push({
						//for usual way
						//'period' : ((rez_freq == 2) ? yr + 'Q' + (mm/indConst) : yr)
						//adjusted for fiscal year - real Q1 = fiscal Q2
						'period' : ((rez_freq == 2) ? fiscalQ : fiscYr)
						,'base'  : (isNull? null : ann)
						,'newScn': (isNull_new? null : ann_new)
					});
					
					ann = 0;
					ann_new = 0;
					base_avrg_ratio = 0;
					newScn_avrg_ratio = 0;
					base_price = 0;
					newScn_price = 0;
					base_units = 0;
					newScn_units = 0;
				};
			};
			//add remove statement for sales and when fiscal year (normal mo + 3 mo.) exceeds real year			
			return rez;
		}
	}
}