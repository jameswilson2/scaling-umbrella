<?php

/*
	A form field class with model and view operations.
*/
interface Form_Field{
	
	/*
		Return the field name string
	*/
	function getName();
	
	/*
		Return the label text as a string
	*/
	function getLabel();
	
	/*
		Return an error message string or return null if no error has occured 
		on loading.
	*/
	function getErrorMessage();
	
	/*
		Return an array of keys that are expected to be used in the storage 
		values array. The return value is constant.
	*/
	function getStorageKeys();
	
	/*
		Return the storage values array. The storage values array is a 
		representation of the current field state
	*/
	function getStorageValues();
	
	/*
		Returns a constant boolean value, indicating whether or not the field 
		handles large/file data. If method returns true then the object user
		must change the form encoding type to "multipart/form-data".
	*/
	function isLargeData();
	
	/*
		Load the field state from a user-submitted values array. This method
		returns true if the values array passes validation. If validation fails
		the method returns false and an error message is set. To get the
		validation error message call the getErrorMessage method.
	*/
	function loadFromSubmit($assoc_array);
	
	/*
		Load the field state from a storage values array. Validation is not
		performed on storage values.
	*/
	function loadFromStorage($assoc_array);
	
	/*
		This is the root create element method that calls all the other
		create element methods to build and return a complete field element
		for html rendering. The object user only needs to call this method, the
		other create elements methods are for derived classes to overload parts
		of the field element rendering. The return value type is a class derived
		from the Html_Element class.
	*/
	function createFieldElement();
	
	/*
		The outermost container element. The return value type is a class derived 
		from the Html_Element class.
	*/
	function createContainerElement();
	
	/*
		Give status information to the form user. If the field object is in an 
		error state then this method should return an element containing the 
		error message. The return value type is a class derived from the 
		Html_Element class.
	*/
	function createStatusElement();
	
	/*
		Some text to help the user know what kind of values and what format is
		valid. Most field classes will return null for this method as the valid
		value set should be obvious to the form user just by reading the label 
		text. The return value type is a class derived from the Html_Element 
		class.
	*/
	function createInfoElement();
	
	/*
		The label text for the user to identify the field. The return value
		type is a class derived from the Html_Element class.
	*/
	function createLabelElement();
	
	/*
		The user input control. The return value type is a class derived from
		the Html_Element class.
	*/	
	function createWidgetElement();
}
