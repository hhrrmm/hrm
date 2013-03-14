package calcs {
	import mx.collections.ArrayCollection;
	import flash.utils.ByteArray;
	
	public class timeSeries extends ArrayCollection {
		
		[Bindable]
		public var data:ArrayCollection = new ArrayCollection();
		
		[Bindable]			
		public var baseData:ArrayCollection = new ArrayCollection();
			
		public function timeSeries()	{
		}
		
		public function set dataList(source:ArrayCollection):void {
			this.data = source;
			this.baseData  = this.clone(source);
		}	
		
		private function clone(source:Object):*
		{
			var myBA:ByteArray = new ByteArray();
			myBA.writeObject(source);
			myBA.position = 0;
			return(myBA.readObject());
		}
	}
}