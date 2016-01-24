<?php // $Id: html.php 2090 2012-09-19 03:42:34Z cross $ Copyright (c) Preston C. Urka. All Rights Reserved.


function swwat_createInputHidden($name, $value)
{
    echo '<input type="hidden" id="', $name, '" name="', $name, '" value="', $value, '"/>';
}

function swwat_createInputSubmit($name, $value)
{
    echo '<input type="submit" id="', $name, '"  name="', $name, '" value="', $value, '"/>';
}

function swwat_createBigInputSubmit($name, $value)
{
    echo '<input style="font-size: 28px; width: 100%; height: 48px; font-weight: bold;" id="', $name, '" name="', $name, '" type="submit" value="', $value, '" />';
}

function swwat_createMenuInputSubmit($name, $value, $isDisabled = FALSE)
{
	if ($isDisabled)
	{
    	echo '<input id="menuitem" type="submit" id="', $name, '" name="', $name, '" value="', $value, '" disabled="disabled"/>';
	}
	else
	{
    	echo '<input id="menuitem" type="submit" id="', $name, '" name="', $name, '" value="', $value, '"/>';
	}
}

function swwat_createInputNamelessSubmit($value)
{
    echo '<input type="submit" value="', $value, '"/>';
}

function swwat_createDisplayError($param)
{
    if (isset($_POST[$param]))
    {
        echo "<div class='display-error'>", $_POST[$param], "</div>";
    }
} // swwat_createDisplayError

function swwat_createInputValidate($param, $formname, $jsfunction, $isDisabledFlag = FALSE)
{
	if (isset($_POST[$param]))
	{
    	echo '<input type="text" name="', $param, '" value="', $_POST[$param], '" size="30"';
	}
	else
	{
    	echo '<input type="text" name="', $param, '" value="" size="30"';
	}
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    if (!is_null($jsfunction))
    {
        echo "       onkeyup =\"", $jsfunction, "(document.forms['", $formname, "']['", $param, "'])\"";
        echo "       onchange=\"", $jsfunction, "(document.forms['", $formname, "']['", $param, "'])\"";
    }
    echo '/>';
} // swwat_createInputValidate

function swwat_createInputValidateLength($param, $formname, $jsfunction, $len, $isDisabledFlag = FALSE)
{
	if (isset($_POST[$param]))
	{
    	echo '<input type="text" name="', $param, '" value="', $_POST[$param], '"';
	}
	else
	{
    	echo '<input type="text" name="', $param, '" value=""';
	}
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    if (!is_null($jsfunction))
    {
        echo " onkeyup =\"", $jsfunction, "(document.forms['", $formname, "']['", $param, "'], ", $len, ")\"";
        echo " onchange=\"", $jsfunction, "(document.forms['", $formname, "']['", $param, "'], ", $len, ")\"";
    }
    echo '/>';
} // swwat_createInputValidateLength

function swwat_createInputValidateInteger($param, $formname, $len, $isDisabledFlag = FALSE)
{
    swwat_createInputValidateLength($param, $formname, "swwat_ValidateInteger", $len, $isDisabledFlag);
}

function swwat_createInputValidateDouble($param, $formname, $decimalLen, $isDisabledFlag = FALSE)
{
    swwat_createInputValidateLength($param, $formname, "swwat_ValidateDouble", $len, $isDisabledFlag);
}

function swwat_createInputValidatePhone($param, $formname, $len, $isDisabledFlag = FALSE)
{
    swwat_createInputValidateLength($param, $formname, "swwat_ValidatePhone", $len, $isDisabledFlag);
}

function swwat_createInputValidateTextAreaLength($param, $formname, $len, $buttonname, $isDisabledFlag = FALSE)
{
    echo '<textarea name="', $param, '"';
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    echo "       onkeyup  =\"swwat_validateLength(document.forms['", $formname, "']['", $param, "'], ", $len, ", document.forms['", $formname, "']['", $buttonname, "'])\"";
    echo "       onchange =\"swwat_validateLength(document.forms['", $formname, "']['", $param, "'], ", $len, ", document.forms['", $formname, "']['", $buttonname, "'])\"";
    if (isset($_POST[$param]))
    {
        echo '>', $_POST[$param], '</textarea>';
    }
    else
    {
        echo '></textarea>';
    }
} // swwat_createInputValidateTextAreaLength

function swwat_createOption($option, $selected)
{
    echo '<option value="', $option[0], '"';
    if ($selected)
    {
        echo ' selected="selected"';
    }
    echo '>', $option[1], '</option>';
} // swwat_createOption

// $optionArray = 2D array {{name, value}, {name, value}, ...}
function swwat_createSelect($param, $optionArray, $defaultOption, $isDisabledFlag = FALSE)
{
    $selected = $defaultOption;
	if (isset($_POST[$param]))
	{
        $selected = $_POST[$param];
    }
    echo '<select id="', $param, '" name="', $param, '" ';
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    echo '>';
    for ($j = 0; $j < count($optionArray); $j++)
    {
        $option = $optionArray[$j];
        swwat_createOption($option, (0 == strcmp($selected, $option[0])));
    } // $j
    echo '</select>';
} // swwat_createSelect

function swwat_createRadioOption($name, $option, $type, $selected, $isDisabledFlag)
{
    echo '<input id="', $name, '" name="', $name, '" type="', $type, '" value="', $option[0], '"';
    if ($selected)
    {
        echo ' checked="checked"';
    }
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled"';
    }
    echo ' />', $option[1], "\n";
} // swwat_createRadioOption

define("SWWAT_RADIO",    "radio");
define("SWWAT_CHECKBOX", "checkbox");

// $optionArray = 2D array {{name, value}, {name, value}, ...}
// $type = radio/checkbox
function swwat_createRadioSelect($param, $optionArray, $type, $defaultOption, $isDisabledFlag = FALSE)
{
    $selected = $defaultOption;
	if (isset($_POST[$param]))
	{
        $selected = $_POST[$param];
    }
    for ($j = 0; $j < count($optionArray); $j++)
    {
        $option = $optionArray[$j];
        swwat_createRadioOption($param, $option, $type, (0 == strcmp($selected, $option[0])), $isDisabledFlag);
    } // $j
} // swwat_createRadioSelect

?>
