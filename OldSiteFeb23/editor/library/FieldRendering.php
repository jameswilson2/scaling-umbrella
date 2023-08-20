<?php
require_once 'library/FormValidationFunctions.php';
require_once 'library/html/HtmlElement.class.php';
require_once 'library/html/HtmlTextNode.class.php';
require_once 'library/captcha.class.php';

/*
	To prevent strange quirks:
		* If a field definition provides a validate function remember to set a error_message string
*/

function renderTextField($field, $data){
	
	$field_classes = array("field");
	
	$type = $field['type'];
	$label = @$field['label'];
	$name = $field['name'];
	$size= @$field['size'];
	$id = "_$name";
	$value = $data->getField($name);
	
	$error_message = $data->getFieldError($name);
	if($error_message){
		$error = "<div class=\"field-error-message\">$error_message</div>";
		$field_classes[] = "field-error";
	}
	
	$input_element = "<input type=\"text\" class=\"input-text\" name=\"$name\" id=\"$id\" value=\"$value\" />";
	
	if(@$field['multiline']){
		$input_element = "<textarea name=\"$name\" id=\"$id\">$value</textarea>";
	}
	
	$field_classes = implode(" ", $field_classes);
	
	return <<<EOD

<div class="$field_classes">
$error
	<div class="field-label">
		<label for="$id">$label</label>
	</div>
	<div class="field-input">
		$input_element
	</div>
</div>
EOD;
}

function renderHiddenField($field, $data){
	
	$name = $field['name'];
	$id = "_$name";
	$value = $data->getField($name);
	
	if($value === NULL){
		$value = $field['value'];
	}
	
	return <<<EOD
	<input type="hidden" name="$name" id="$id" value="$value" />
EOD;
}

function renderSelectField($field, $data){
	
	$name = $field['name'];
	$label = $field['label'];
	$id = "_$name";
	
	$value = $data->getField($name);
	
	$get_options = $field['options'];
	$options = call_user_func($get_options);
	
	$options_html = "";
	foreach($options as $option_info){
	
		$option_label = $option_info['label'];
		$option_value = $option_info['value'];
	
		$option_element = new HtmlElement("option");
		$option_element->setAttribute("value", $option_value);
		$option_element->addChild(new HtmlTextNode($option_label));
		
		if($option_value == $value){
			$option_element->setAttribute("selected", "selected");
		}
		
		$options_html .= $option_element->toString();
	}
	
	if($data->getFieldError($name)){
		$beginErrorSpan = "<span class=\"error\">";
		$endErrorSpan = "</span>";
	}
	
	return <<<EOD
<div class="field">
	<div class="field-label"><label for="$id">$label</label></div>
	<div class="field-input">$beginErrorSpan<select name="$name" id="$id">$options_html</select>$endErrorSpan</div>
</div>
EOD;
}

function renderNotice($field){
	return "<p>$field[message]</p>";
}

function getCaptchaField($field, $data){
	$field_name = $field["name"];
	$captcha = $data->getField($field_name);
	if(!$captcha){
		$captcha = Captcha::generateArithmeticProblem();
		$captcha = array("question" => $captcha->getPresentation(), "answer" => $captcha->getAnswer());
		$data->setField($field_name, $captcha);
		$data->save();
	}
	return $captcha;
}

function renderCaptcha($field, $data){

	$name = $field["name"];
	$id = "_$name";
	$label = $field["label"];
	
	$captcha = getCaptchaField($field, $data);
	
	$question = $captcha["question"];
	
	if(isset($captcha["answered"])){
		if($captcha["answered"] == $captcha["answer"]){
			return "";
		}
		else{
			$error_message = $data->getFieldError($name);
			$error = "<div class=\"field-error-message\">$error_message</div>";
		}
	}
	
	return <<<EOD
<div class="field">
	$error
	<div class="field-label"><label for="$id">$label</label></div>
	<div class="field-input">$question <input type="text" class="input-text input-text-inline" name="$name" id="$id" size="2" /></div>
</div>
EOD;
}

function renderField($field, $data){

	if(@$field['no_render']){
		return "";
	}
	 
	switch($field['type']){
		case "text":
			return renderTextField($field, $data);
		case "hidden":
			return renderHiddenField($field, $data);
		case "select":
			return renderSelectField($field, $data);
		case "notice":
			return renderNotice($field);
		case "captcha":
			return renderCaptcha($field, $data);
	}
	
	return "";
}

function renderFieldsArray($fields, $data){
	$html = "";
	foreach($fields as $field){
		$html .= renderField($field, $data);
	}
	return $html;
}

function saveCaptchaFieldFromPost($field, $form_data){
	
	$name = $field["name"];
	
	$captcha = $form_data->getField($name);
	if(!$captcha){
		$form_data->setField($name, getCaptchaField($field, $form_data));
		$form_data->setFieldError($name, "Please answer this captcha correctly");
		return;
	}
	
	if(isset($captcha["answered"]) && $captcha["answered"] == $captcha["answer"]){
		return;
	}
	
	$post_value = $_POST[$name];
	
	$captcha["answered"] = $post_value;
	$form_data->setField($name, $captcha);
	
	if($captcha["answered"] != $captcha["answer"]){
		$form_data->setFieldError($name, "Please answer this captcha correctly");
	}
}

function saveFieldFromPost($field, $form_data){
	
	$field_name = $field['name'];
	
	switch($field["type"]){
		case "captcha":
			saveCaptchaFieldFromPost($field, $form_data);
			return;
		case "hidden":
			if(isset($field["no_render"]) && isset($field["value"])){
				$_POST[$field_name] = $field["value"];
			}
		case "post_date":
			$_POST[$field_name] = date("Y-m-d H:i:s");
	}
	
	$post_value = $_POST[$field_name];
	
	$modifier = @$field['modifier'];
	if($modifier){
		$post_value = call_user_func($modifier, $post_value);
	}
	
	$form_data->setField($field_name, $post_value);
	
	if($field['required'] && strlen($post_value) == 0){
		$form_data->setFieldError($field_name, $field['error_message'], "This field is required");
	}
	
	$validate = @$field['validate'];
	if($validate){
		if(!call_user_func($validate, $post_value)){
			$form_data->setFieldError($field_name, $field['error_message']);
		}
	}
}

function saveFieldsFromPost($fields, $form_data){
	foreach($fields as $field){
		saveFieldFromPost($field, $form_data);
	}
}
