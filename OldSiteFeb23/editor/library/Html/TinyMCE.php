<?php

class Html_TinyMCE{
	
	private static $included_load_code = false;
	
	public static function renderLoadCode(){
		return <<<EOD
	<script type="text/javascript" src="files/jscripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="files/lists/image_list.js"></script>
	<script type="text/javascript" src="files/lists/link_list.js"></script>
	<script type="text/javascript" src="files/lists/media_list.js"></script>
EOD;
	}
	
	public static function renderInitCode($overwrite_init_options){
		
		$base_href = WEB_ROOT;
		$css = CSS;
		
		if(!is_array($overwrite_init_options)){
			$overwrite_init_options = array();
		}
		
		$required_options = array("elements", "width", "height");
		$missing = array();
		foreach($required_options as $required){
			if(!isset($overwrite_init_options[$required])){
				$missing[] = $required;
			}
		}
		if(count($missing)){
			$list = implode(", ", $missing);
			trigger_error("Missing init properties: $list", E_USER_ERROR);
		}
		
		$init_options = array(
			"mode" => "exact",
			"theme" => "advanced",
			"relative_urls" => true,
			"document_base_url" => $base_href,
			"plugins" => "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			"theme_advanced_buttons1" => "newdocument,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,template,cut,copy,paste,pastetext,pasteword,search,replace,bullist,numlist,outdent,indent,blockquote",
			"theme_advanced_buttons2" => "undo,redo,link,unlink,anchor,image,cleanup,help,code,tablecontrols,hr,removeformat,visualaid,sub,sup,charmap,fullscreen",
			"theme_advanced_buttons3" => "",
			"theme_advanced_toolbar_location" => "top",
			"theme_advanced_toolbar_align" => "left",
			"theme_advanced_statusbar_location" => "bottom",
			"content_css" => $css,
			"template_external_list_url" => "$base_href/editor/files/lists/template_list.js",
			"external_link_list_url" => "$base_href/editor/files/lists/link_list.js",
			"external_image_list_url" => "$base_href/editor/files/lists/image_list.js",
			"media_external_list_url" => "$base_href/editor/files/lists/media_list.js"
		);
		
		$init_options = array_merge($init_options, $overwrite_init_options);
		
		$init_options_serialized = json_encode($init_options);
		
		if(!self::$included_load_code){
			$load_code = self::renderLoadCode();
			self::$included_load_code = true;
		}
		else{
			$load_code = "";
		}
		
		return <<<EOD
	<!-- TinyMCE -->
	$load_code
	<script type="text/javascript">
	//<![CDATA[
		tinyMCE.init($init_options_serialized);
	//]]>
	</script>
	<!-- /TinyMCE -->
EOD;
	}
}
