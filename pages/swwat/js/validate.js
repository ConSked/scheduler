// $Id: validate.js 557 2012-06-05 20:17:10Z preston $ Copyright (c) Preston C. Urka. All Rights Reserved.
"use strict";

// names will permit ,-'. i.e. O'Brien Smyth-Jones, the 3rd, M.D.
// names will NOT permit characters within PUNCTUATION
// this is done vs. inclusion in alpha-num due to internationalization
var PUNCTUATION = "`!@#$%^&*()_+=[]{}\"\\;:<>?/|";
function swwat_ValidateNoPunct(docEl)
{
    var str = docEl.value;
    for (var j = 0; j < PUNCTUATION.length; j++)
    {
        var ch = PUNCTUATION.charAt(j);
        // string.search(regexp) might be faster, but would still need to know which character
        if (-1 != str.indexOf(ch)) // always input postive
        {
            docEl.value = str.substr(0, str.length - 1);
            return false;
        }
    } // var j
    return true;
} // swwat_ValidateNoPunct

function swwat_ValidateLength(docEl, len, docButton)
{
    var str = docEl.value;
    if (str.length > len)
    {
        docEl.value = str.substr(0, len);
        return false;
    }
    return true;
} // swwat_ValidateLength

// len=12 for 111-222-3333
function swwat_ValidatePhone(docEl, len)
{
    var str = docEl.value;
    if (str.length > len)
    {
        docEl.value = str.substr(0, len);
        return false;
    }
    for (var j = 0; j < str.length; j++)
    {
        var ch = str.charAt(j);
        if (((3 == j) || (7 == j)) && ('-' == ch))  {  continue;  }
        var num = new Number(ch);
        if (""+Number.NaN == ""+num)
        {
            docEl.value = str.substr(0, j);
            return false;
        }
        // ok, is a digit and proper length; modify if not a dash
        if ((3 == j) || (7 == j)) // change to dash
        {
            docEl.value = str.substr(0, j) + '-' + ch;
        }
    } // var j
    return true;
} // swwat_ValidatePhone

function swwat_ValidateDouble(docEl, len)
{
    var str = docEl.value;
    if (-1 != str.indexOf('-')) // always input postive
    {
        docEl.value = str.substr(0, str.length - 1);
        return false;
    }
    if (str.length > len)
    {
        docEl.value = str.substr(0, len);
        return false;
    }
    var num = new Number(str);
    if (""+Number.NaN == ""+num)
    {
        docEl.value = str.substr(0, str.length - 1);
        return false;
    }
    return true;
} // swwat_ValidateDouble

function swwat_ValidateInteger(docEl, len)
{
    var str = docEl.value;
    if (-1 != str.indexOf('.'))
    {
        docEl.value = str.substr(0, str.length - 1);
        return false;
    }
    return swwat_ValidateDouble(docEl, len);
} // swwat_ValidateInteger
