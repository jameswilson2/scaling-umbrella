<?php

/*
	How to use:
	
		echo TransferListControl::getJsClassCode(); // Call once for the whole web page
		
		while(...){
			$related_list[] = array('ID'=>$id, 'Name'=>$display_name, 'Selected'=>$_related_id);
			
			// Items with non-empty Selected values will appear in the assigned list
		}
		
		$list_control = new TransferListControl();
		$list_control->setName('Related products');
		$list_control->setFieldName('product_related'); // The name used for the input hidden element in the html form
		$list_control->setList($related_list); // Assign list items from an array
		echo $list_control->build(); // Render html
*/

class TransferListControl{

	function setName($name){
		$this->name = $name;
	}
	
	function setFieldName($name){
		$this->field_name = $name;
	}
	
	function setList($list){
		$items = array();
		
		foreach($list AS $item){
			$id = $item['ID'];
			$name = $item['Name'];
			$selected = $item['Selected'];
			if($selected!=''){
				$selected = ', included:true';
			}

			$items[] = "{id:$id, text:\"$name\" $selected}";
		}
		
		$this->items = implode(', ', $items);
	}
	
	static function getJsClassCode(){
		return <<< EOD
<script type="text/javascript">// <![CDATA[
function TransferListControl(items,options){var self=this;this.m_items=items;this.m_itemIndex={};this.m_options=options;this.m_containerDiv=document.createElement("div");this.m_containerDiv.className="transfer_list";this.m_unassignedSelect=createSelect("Unassigned","transfer_list_unassigned","transfer_list_unassigned_container");var buttonContainer=document.createElement("span");buttonContainer.className="transfer_list_button_container";this.m_containerDiv.appendChild(buttonContainer);var assignButton=document.createElement("a");assignButton.href="#";assignButton.className="transfer_list_assign";assignButton.style.display="none";assignButton.innerHTML="<span>&gt;</span>";assignButton.onclick=function(){moveSelectedOptions(self.m_unassignedSelect,self.m_assignedSelect,true);return false;}
buttonContainer.appendChild(assignButton);var unassignButton=document.createElement("a");unassignButton.href="#";unassignButton.className="transfer_list_unassign";unassignButton.style.display="none";unassignButton.innerHTML="<span>&lt;</span>";unassignButton.onclick=function(){moveSelectedOptions(self.m_assignedSelect,self.m_unassignedSelect,false);return false;}
buttonContainer.appendChild(unassignButton);this.m_assignedSelect=createSelect("Assigned","transfer_list_assigned","transfer_list_assigned_container");bindSelectEventHandlers(this.m_unassignedSelect,this.m_assignedSelect,false);bindSelectEventHandlers(this.m_assignedSelect,this.m_unassignedSelect,true);build();function build(){for(var i=0;i<items.length;i++){var item=items[i];item.order=i;self.m_itemIndex[item.id]=item;var option=document.createElement("option");option.value=item.id;option.appendChild(document.createTextNode(item.text));(item.included?self.m_assignedSelect:self.m_unassignedSelect).appendChild(option);}}
function createSelect(title,className,containerClassName){var container=document.createElement("div");container.className=containerClassName;self.m_containerDiv.appendChild(container);var label=document.createElement("div");label.appendChild(document.createTextNode(title+":"));container.appendChild(label);var select=document.createElement("select");container.appendChild(select);select.multiple="multiple";select.size=20;select.className=className;return select;}
function moveSelectedOptions(src,dst,includedValue){for(var i=0;i<src.options.length;i++){if(src.options[i].selected){self.m_itemIndex[src.options[i].value].included=includedValue;insertOption(src.options[i],dst);i--;}}
dst.focus();function insertOption(option,select){var itemOrder=self.m_itemIndex[option.value].order;for(var i=0;i<select.options.length;i++){if(itemOrder<self.m_itemIndex[select.options[i].value].order){select.insertBefore(option,select.options[i]);return;}}
select.appendChild(option);}}
function bindSelectEventHandlers(selectElement,otherSelectElement,includedValue){selectElement.onfocus=function(){unselectAll(otherSelectElement);if(selectElement.className=="transfer_list_unassigned"){assignButton.style.display="block";unassignButton.style.display="none";}
else{unassignButton.style.display="block";assignButton.style.display="none";}}
selectElement.onkeydown=function(event){if(!event){event=window.event;}
switch(event.keyCode){case 37:if(selectElement.className=="transfer_list_assigned"){otherSelectElement.focus();}
break;case 39:if(selectElement.className=="transfer_list_unassigned"){otherSelectElement.focus();}
break;case 13:moveSelectedOptions(selectElement,otherSelectElement,includedValue);break;}}
selectElement.ondblclick=function(){selectAll(selectElement);}}}
TransferListControl.prototype.getContainerElement=function(){return this.m_containerDiv;}
function unselectAll(selectElement){for(var i=0;i<selectElement.options.length;i++){selectElement.options[i].selected=false;}}
function selectAll(selectElement){for(var i=0;i<selectElement.options.length;i++){selectElement.options[i].selected=true;}}
// ]]></script>
EOD;
	}
	
	public function build(){
	
		$html = <<< EOD
		<input type="hidden" name="$this->field_name" />
		<fieldset>
			<legend>$this->name</legend>
			<div id="{$this->field_name}_container">&nbsp;</div>
		</fieldset>
EOD;
	
		$script = <<< EOD
<noscript>Error! You need to enable Javascript in your web browser to see all of this web page.</noscript>
<script type="text/javascript">// <![CDATA[
(function(){
	var items = [$this->items];
	var options = {};
	var list = new TransferListControl(items, options);
	document.getElementById("{$this->field_name}_container").appendChild(list.getContainerElement());
	$("form").submit(function() {
		for(var i = 0; i < items.length; i++){
			if(items[i].included){
				var input = document.createElement("input");
				input.type = "hidden";
				input.name = "$this->field_name[]";
				input.value = items[i].id
				this.appendChild(input);
			}
		}
	});
})();
// ]]></script>
EOD;
		
		$this->content = $html . $script;
		
		return $this->content;
	}
}

?>