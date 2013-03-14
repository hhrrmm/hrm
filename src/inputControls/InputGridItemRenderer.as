package inputControls
{
	import mx.controls.Label;
	import mx.controls.dataGridClasses.DataGridListData;
	import mx.controls.listClasses.*;
	import mx.formatters.NumberFormatter;

	public class InputGridItemRenderer extends Label{
		override public function set data(value:Object):void {
			super.data = value;
			
			var nF:NumberFormatter = new NumberFormatter;
			
			nF.decimalSeparatorTo   = ".";
			nF.thousandsSeparatorTo = ",";
			nF.precision            = "2";
			nF.rounding             = "nearest";
			
			if (value != null)
			{
				var s:String = String(value[DataGridListData(listData).dataField]);
				var n:Number = Number(s);
				
				if (n < 0.005 && n > -0.005 && n != 0) {
					nF.rounding  = "none";
					nF.precision = "-1";
					/* converting to scientific notation would be preffered*/
				}
				else if (n < 0.05 && n > -0.05 && n != 0) {
					nF.rounding  = "none";
					nF.precision = "3";
				}
				
				var out:String = nF.format(Number(n));
				
				text = out;
			}
			else
			{
				text= "";
			}
			
			super.invalidateDisplayList();
		}

	}
}