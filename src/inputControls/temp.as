package inputControls
{
	public class temp
	{
		public function temp()
		{
		}
		
		public function updateGrid():void{			
			var ind_freq_type:Number = 0;
			// type 1 - monthly, 3 - annual
			ind_freq_type = Number(this.indicatorList.selectedItem['IndicatorType']);
			
			if ((ind_freq_type == 1)&&(freq_type == 2)) {
				//update dataGrid provider gridAnnualDataProvider with new sales data - annual
				if ((sel_indi_freq == 1) && (freq_type == 2)) {						
					gridQuatDataProvider = new ArrayCollection(
						addDataQuat(gridDataProvider.source, indValChartDataProvider.source, sel_indi_left, 'newScn')
					);
					//gridAnnualDataProvider = new ArrayCollection(updateBlock(gridAnnualDataProvider.source, salesValueChartData.source, sel_indi_right));
					valueCol.dataField = 'quatI' + sel_indi_left.substr(1, sel_indi_left.length);
					periodCol.dataField = 'quatPeriod';
					
					inputDataGrid.dataProvider = this.gridQuatDataProvider;
				} else {
					valueCol.dataField = this.sel_indi_left;
					selectedIndicator = this.sel_indi_left;
					periodCol.dataField = 'period';
				};
			} else if ((ind_freq_type == 1)&&(freq_type == 3)) {
				//update dataGrid provider gridAnnualDataProvider with new sales data - annual
				if ((sel_indi_freq == 1) && (freq_type == 3)) {						
					gridAnnualDataProvider = new ArrayCollection(
						addData(gridAnnualDataProvider.source, indValChartDataProvider.source, sel_indi_left, 'newScn')
					);
					//gridAnnualDataProvider = new ArrayCollection(updateBlock(gridAnnualDataProvider.source, salesValueChartData.source, sel_indi_right));
					valueCol.dataField = 'annualI' + sel_indi_left.substr(1, sel_indi_left.length);
					selectedIndicator = 'annualI' + sel_indi_left.substr(1, sel_indi_left.length);
					periodCol.dataField = 'annualPeriod';
					//periodType = 'annualPeriod';
					inputDataGrid.dataProvider = this.gridAnnualDataProvider;
				} else {
					valueCol.dataField = this.sel_indi_left;
					selectedIndicator = this.sel_indi_left;
					periodCol.dataField = 'period';
				};
			} else if ((sel_indi_freq == 3)) {
				var gridAnnualData:Array = new Array();
				var hEnd  :String = sel_indi_period;
				
				if (sel_indi_freq == 3) { 
					hEnd = sel_indi_period + "M12"; 
				};
				
				for (var j:int = 0; j < baselineScnData.length; j++) {
					var object:Object;
					var per   :String;
					
					if ((getTimeID(newScnData[j]['period']) ) > getTimeID(hEnd) && 
						Number(String(newScnData[j]['period']).substr(0,4)) < 2016 ) {
						
						object = newScnData[j];
						
						per = newScnData[j]['period'];						
						
						if ((per.substr(5, per.length-5) == "12") 
							&&( Number(newScnData[j]['annualPeriod']) > 1981) )
							//&&( Number(newScnData[j]['annualPeriod']) > Number(sel_indi_period.substr(0, 4))))
						{gridAnnualData.push(object);}
					}
				}
				gridAnnualDataProvider = new ArrayCollection(gridAnnualData);
				
				inputDataGrid.dataProvider = this.gridAnnualDataProvider;
				valueCol.dataField = this.sel_indi_left;
				selectedIndicator = this.sel_indi_left;
				periodCol.dataField = 'annualPeriod';
				freq_type = 3;					
				
			} else {
				inputDataGrid.dataProvider = this.gridDataProvider;				
				valueCol.dataField = this.sel_indi_left;
				selectedIndicator = this.sel_indi_left;
				periodCol.dataField = 'period';
			};
			
		}
		
		
	}
}