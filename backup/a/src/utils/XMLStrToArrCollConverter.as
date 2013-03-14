package utils	
{
	import mx.collections.ArrayCollection;
	import mx.rpc.xml.*;
	import mx.utils.ArrayUtil;
	import flash.xml.XMLDocument;
	
	public class XMLStrToArrCollConverter{
	/*
	** Used to convert XML strings to ArrayCollections
	*/
		public static function convertXMLStrToArrayCollection(xmlString:String) :ArrayCollection {
			var xml:XMLDocument = new XMLDocument(xmlString);
			var decoder:SimpleXMLDecoder = new SimpleXMLDecoder();
			var data:Object = decoder.decodeXML(xml);
			var array:Array = ArrayUtil.toArray(data.entries.entry);
			var ac:ArrayCollection = new ArrayCollection(array);
			return ac;
		}
	}
}