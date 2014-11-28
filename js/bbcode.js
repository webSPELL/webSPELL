var form = "post",
    textarea = "message",
    which = null,
    languageArray = [],
    wmtt = null;

function AddTag(open, close, content) {
    var textfield,
        toinsert,
        range,
        start,
        end,
        pos;

    if (typeof document.forms[form].elements[textarea] === "undefined") {
        if (which) {
            textfield = which;
        } else {
            textfield = document.forms[form].elements["message[0]"];
        }
    } else {
        textfield = document.forms[form].elements[textarea];
    }
    textfield.focus();
    if (typeof document.selection !== "undefined") {
        range = document.selection.createRange();
        if (content === "") {
            toinsert = range.text;
        } else {
            toinsert = content;
        }
        range.text = open + toinsert + close;
        range = document.selection.createRange();
        if (toinsert.length === 0) {
            range.move("character", -close.length);
        } else {
            range.moveStart("character", open.length + toinsert.length + close.length);
        }
        range.select();
    } else if (typeof textfield.selectionStart !== "undefined") {
        start = textfield.selectionStart;
        end = textfield.selectionEnd;
        if (content === "") {
            toinsert = textfield.value.substring(start, end);
        } else {
            toinsert = content;
        }
        textfield.value = textfield
            .value
            .substr(0, start) + open + toinsert + close + textfield
            .value
            .substr(end);

        if (toinsert.length === 0) {
            pos = start + open.length;
        } else {
            pos = start + open.length + toinsert.length + close.length;
        }
        textfield.selectionStart = pos;
        textfield.selectionEnd = pos;
    } else {
        if (content === "") {
            toinsert = open + close;
        } else {
            toinsert = open + content + close;
        }
        textfield.innerHTML = textfield.innerHTML + toinsert;
    }
}

// insert [img] tag
function AddImg() {
    AddTag("[IMG]", "[/IMG]", "");
}

// insert [url] or [email] tag
function AddLink(thetype) {
    AddTag("[" + thetype + "]", "[/" + thetype + "]", "");
}

// insert html list
function AddList() {
    type = prompt(languageArray.bbcode.listguide, "");
    if ((type == "a") || (type == "1")) {
        list = "[LIST=" + type + "]\n";
        listend = "[/LIST=" + type + "]";
    } else {
        list = "[LIST]\n";
        listend = "[/LIST]";
    }
    entry = "start";
    while ((entry !== "") && (entry !== null)) {
        entry = prompt(languageArray.bbcode.listpoint, "");
        if ((entry !== "") && (entry !== null)) {
            list = list + "[*]" + entry + "[/*]\n";
        }
    }
    if (list !== "[LIST]\n" && list !== "[LIST=\" + type + \"]\n") {
        addtext = list + listend;
        AddTag("", "", addtext);
    }
}

// insert code from another window
function AddCodeFromWindow(thecode) {
    var textfield,
        pos,
        re = new RegExp("^[0-9]{0,3}$"),
        start,
        end,
        range;

    if (typeof opener.document.forms[form].elements[textarea] === "undefined") {
        if (which) {
            textfield = which;
        } else {
            textfield = opener.document.forms[form].elements["message[0]"];
        }
    } else {
        textfield = opener.document.forms[form].elements[textarea];
    }
    textfield.focus();

    textfield.focus();
    if (typeof opener.document.selection != "undefined") {
        range = opener.document.selection.createRange();
        range.text = thecode;
        range = opener.document.selection.createRange();
        range.moveStart("character", thecode.length);
        range.select();
    } else if (typeof textfield.selectionStart != "undefined") {
        start = textfield.selectionStart;
        end = textfield.selectionEnd;
        textfield.value = textfield.value.substr(0, start) + thecode + textfield.value.substr(end);
        pos = start + thecode.length;
        textfield.selectionStart = pos;
        textfield.selectionEnd = pos;
    } else {
        while (!re.test(pos)) {
            pos = prompt(
                languageArray.bbcode.addcode + " (0.." + textfield.value.length + "):", "0"
            );
        }
        if (pos > textfield.value.length) {
            pos = textfield.value.length;
        }
        textfield.value = textfield.value.substr(0, pos) + thecode + textfield.value.substr(pos);
    }
}

// insert [b] tag
function AddB() {
    AddTag("[B]", "[/B]", "");
}

// insert [U] tag
function AddU() {
    AddTag("[U]", "[/U]", "");
}

// insert [I] tag
function AddI() {
    AddTag("[I]", "[/I]", "");
}

// insert [S] tag
function AddS() {
    AddTag("[S]", "[/S]", "");
}

// insert [quote] tag
function AddQuote() {
    AddTag("[quote]", "[/quote]", "");
}

// insert [code] tag
function AddCodetag() {
    AddTag("[code]", "[/code]", "");
}

// insert [Toggle] tag
function AddToggle() {
    AddTag("[toggle=" + languageArray.bbcode.readMore + "]", "[/toggle]", "");
}

// toggle function - read more
function Toggle(id, multi) {
    var i;

    spanid1 = "ToggleRow_" + id;
    spanid2 = "ToggleImg_" + id;

    if (multi === true) {
        elements = document.getElementsByName(spanid1);
        val = document.getElementsByName(spanid1)[0].style.display;

        for (i = 0; i < elements.length; i++) {
            if (val === "none") {
                elements[i].style.display = "inline";
            } else {
                elements[i].style.display = "none";
            }
        }
    } else {
        if (/MSIE/.test(navigator.userAgent)) {
            images = document.getElementById(spanid1).getElementsByTagName("img");
            anz = images.length;
            for (i = 0; i < anz; i++) {
                elem = images[i];
                if (typeof elem.onload == "function") {
                    elem.onload();
                }
            }
        }
        val = document.getElementById(spanid1).style.display;
        if (val === "none") {
            document.getElementById(spanid1).style.display = "block";
            document.getElementById(spanid2).src = "images/icons/collapse.gif";
            document.getElementById(spanid2).alt = "hide";
            document.getElementById(spanid2).title = "hide";
        } else {
            document.getElementById(spanid1).style.display = "none";
            document.getElementById(spanid2).src = "images/icons/expand.gif";
            document.getElementById(spanid2).alt = "read more";
            document.getElementById(spanid2).title = "read more";
        }
    }
}

// function addRow() ** this adds a new row to the table,
// containing mapname, mapresult_home, mapresult_opponent
function addRow(action) {
    var theAction = action,
        table = document.getElementById("maplist"),
        theRows = table.rows.length,
        inkrement = theRows,
        row = table.insertRow(theRows),
        cell0 = row.insertCell(0),
        textNode = document.createTextNode("map #" + inkrement),
        ele0, ele1, ele2, ele3, ele4,
        cell1, cell2, cell3, cell4;

    if (theAction == "edit") {
        ele0 = document.createElement("input");
        ele0.setAttribute("type", "hidden");
        ele0.setAttribute("name", "map_id[]");
        ele0.setAttribute("value", inkrement);
        cell0.appendChild(ele0);
    }
    cell0.appendChild(textNode);

    // mapname
    cell1 = row.insertCell(1);
    ele1 = document.createElement("input");
    ele1.setAttribute("type", "text");
    ele1.setAttribute("name", "map_name[]");
    ele1.setAttribute("id", "map_name_" + inkrement);
    ele1.setAttribute("size", "35");
    ele1.className = "form_off";
    cell1.appendChild(ele1);

    // results: home
    cell2 = row.insertCell(2);
    ele2 = document.createElement("input");
    ele2.setAttribute("type", "text");
    ele2.setAttribute("name", "map_result_home[]");
    ele2.setAttribute("id", "map_result_home_" + inkrement);
    ele2.setAttribute("size", "3");
    ele2.className = "form_off";
    cell2.appendChild(ele2);
    // results: opponent
    cell3 = row.insertCell(3);
    ele3 = document.createElement("input");
    ele3.setAttribute("type", "text");
    ele3.setAttribute("name", "map_result_opp[]");
    ele3.setAttribute("id", "map_result_opp_" + inkrement);
    ele3.setAttribute("size", "3");
    ele3.className = "form_off";
    cell3.appendChild(ele3);
    // create delete-selection for edit-function
    if (theAction === "edit") {
        cell4 = row.insertCell(4);
        ele4 = document.createElement("input");
        ele4.setAttribute("type", "checkbox");
        ele4.setAttribute("name", "delete[\"+inkrement+\"]");
        ele4.setAttribute("value", inkrement);
        ele4.className = "form_off";
        cell4.appendChild(ele4);
    } else {
        cell4 = row.insertCell(4);
    }
}

// function removeRow() ** removes the last row of a table
function removeRow() {
    var table = document.getElementById("maplist"),
        theRows = table.rows.length;

    if (theRows != 1) {
        table.deleteRow(theRows - 1);
    }
}

function SelectAll() {
    var x, y;

    for (x = 0; x < document.form.elements.length; x++) {
        y = document.form.elements[x];
        if (y.name != "ALL") {
            y.checked = document.form.ALL.checked;
        }
    }
}

function checkSize(name, xmax, ymax) {
    var xsize,
        ysize;

    xsize = document.getElementById("ws_image_" + name).width;
    ysize = document.getElementById("ws_image_" + name).height;

    if (ysize > ymax) {
        document.getElementById("ws_image_" + name).height = ymax;
        document.getElementById("ws_imagediv_" + name).style.display = "block";
    }

    if (xsize > xmax) {
        document.getElementById("ws_image_" + name).width = xmax;
        document.getElementById("ws_imagediv_" + name).style.display = "block";
    }
}

function AddText(addtext) {
    AddTag("", "", addtext);
}

function AddCode(code) {
    AddText(code);
}

/* tooltip */
function updateWMTT(e) {
    var x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX,
        y = (document.all) ? window.event.y + document.body.scrollTop : e.pageY;

    if (wmtt) {
        wmtt.style.left = (x + 20) + "px";
        wmtt.style.top = (y + 20) + "px";
    }
}

function showWMTT(id) {
    document.onmousemove = updateWMTT;
    wmtt = document.getElementById(id);
    wmtt.style.display = "block";
}

function hideWMTT() {
    wmtt.style.display = "none";
    document.onmousemove = "none";
}

//ajax functions
function postRequest(strURL, id, action) {
    var xmlHttp;

    if (window.XMLHttpRequest) {
        xmlHttp = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlHttp.open("POST", strURL, true);
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState === 4) {
            updatepage(xmlHttp.responseText, id, action);
        }
    };
    xmlHttp.send(strURL);
}

//update target
function updatepage(str, id, action) {
    if (action === "add") {
        document.getElementById(id).innerHTML += str;
    } else if (action === "replace") {
        document.getElementById(id).innerHTML = str;
    } else if (action === "formfield") {
        document.getElementById(id).value = trim(str);
    } else if (action === "return") {
        return str;
    }
    //else if (action === "execute") {
    //	eval(str);
    //}
}

//fetch data for onclick/onchange events
function eventfetch(url, id, action) {
    postRequest(url, id, action);
}
//fetch data for timebased events
function timefetch(url, id, action, milliseconds) {
    eventfetch(url, id, action);
    setTimeout(
        function() {
            timefetch(url, id, action, milliseconds);
        }, milliseconds
    );
}
//generic fetch function, accepts 5 parameters (first 4 mandatory).
//url = script to access on the server
//id = html id (for example of a div, a form field etc.., works with all tags which accept an id)
//action = add, replace, return or formfield, add adds up content at the end of the original
// content in the id element, replace replaces the complete content in the id element,
// formfield replaces a form field value, return simply returns the fetched data
//base = time or event, time based means script will autoexecute itself every amount of
// milliseconds specified via the 5th parameter, event based means you are calling the
// funtion with something like onclick, onchange, onmouseover or directly in a script
//milliseconds = time in milliseconds till the script should autoexecute itself again
// (only needed when base==time)
function fetch(url, id, action, base, milliseconds) {
    if (base === "event") {
        eventfetch(url, id, action);
    } else if (base === "time") {
        timefetch(url, id, action, milliseconds);
    }
}

//search & overlay functions
function search(table, column, identifier, searchqry, searchtemp, id, action, exact, searchtype) {
    exact = typeof(exact) !== "undefined" ? exact : 0;
    searchtype = typeof(searchtype) !== "undefined" ? searchtype : 0;
    searchrequest = "../asearch.php?table=" + table +
    "&column=" + column +
    "&identifier=" + identifier +
    "&search=" + searchqry +
    "&searchtemp=" + searchtemp +
    "&div=" + id +
    "&exact=" + exact +
    "&searchtype=" + searchtype;

    eventfetch(searchrequest, id, action);
}

function getposOffset(overlay, offsettype) {
    var totaloffset = (offsettype === "left") ? overlay.offsetLeft : overlay.offsetTop,
        parentEl = overlay.offsetParent;
    while (parentEl !== null) {
        totaloffset = (offsettype === "left") ?
        totaloffset + parentEl.offsetLeft : totaloffset + parentEl.offsetTop;
        parentEl = parentEl.offsetParent;
    }
    return totaloffset;
}
function overlay(curobj, subobjstr, optPosition) {
    if (document.getElementById) {
        var subobj = document.getElementById(subobjstr),
            xpos = getposOffset(curobj, "left") + ((typeof optPosition !== "undefined" &&
                optPosition.indexOf("right") !== -1) ?
                    -(subobj.offsetWidth - curobj.offsetWidth) : 0),
            ypos = getposOffset(curobj, "top") + ((typeof optPosition !== "undefined" &&
                optPosition.indexOf("bottom") !== -1) ?
                    curobj.offsetHeight : 0);

        subobj.style.display = "block";
        subobj.style.left = xpos + "px";
        subobj.style.top = (ypos + 15) + "px";

        return false;
    } else {
        return true;
    }
}

function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g, "");
}

function formcheckOnsend(id) {
    valuestring = document.getElementById(id).value;
    if (trim(valuestring) === "") {
        return false;
    } else {
        return true;
    }
}
//bbcode checker
function validbbcode(txt) {
    var searchregexp = /\[(B|\/B|U|\/U|I|\/I|S|\/S|code|\/code|LIST|LIST[a1=]*|\/LIST[a1=]*|\*|\/\*|\/LIST|EMAIL[a-zA-Z0-9=#@\._-]*|\/EMAIL|URL[a-zA-Z0-9=#,;+@&?%:\/\._-]*|\/URL|IMG|\/IMG|QUOTE[^\]]*|\/QUOTE|TOGGLE[^\]]*|\/TOGGLE|SIZE=[1-5]{1}|\/SIZE|COLOR[^\]]*|\/COLOR|ALIGN[^\]]*|\/ALIGN|FONT[^\]]*|\/FONT)\]/ig,
        resulttemp = txt.match(searchregexp),
        result = [],
        ocode = 0,
        putincounter = 0,
        c;

    if (resulttemp) {
        resulttemp = [];
    }

    for (c = 0; c < resulttemp.length; c++) {
        if (
            (resulttemp[c] === "[code]") ||
            (resulttemp[c] === "[CODE]") ||
            (resulttemp[c] === "[/code]") ||
            (resulttemp[c] === "[/CODE]")
        ) {
            if ((resulttemp[c] === "[code]") || (resulttemp[c] === "[CODE]")) {
                ocode++;
                if (ocode === 1) {
                    result[putincounter] = resulttemp[c];
                    putincounter++;
                }
            } else {
                ocode--;
                if (ocode === 0) {
                    result[putincounter] = resulttemp[c];
                    putincounter++;
                }
            }
        } else {
            if (ocode < 1) {
                result[putincounter] = resulttemp[c];
                putincounter++;
            }
            continue;
        }
    }
    if (result) {
        return true;
    }
    arraylength = result.length;
    if (arraylength > 0) {
        starttest = result[0].split("=");
        if (arraylength % 2) {
            alert(languageArray.bbcode.unevenAmount);

            return false;
        } else {
            if (starttest[0].indexOf("/") === -1) {
                openingtagcounter = 0;
                closingtagcounter = 0;
                for (i = 0; i < arraylength; i++) {
                    temp = result[i].split("[");
                    temp = temp[1].split("]");
                    temp = temp[0].split("=");
                    if (temp[0].indexOf("/") === -1) {
                        openingtagcounter++;
                    } else {
                        closingtagcounter++;
                    }
                }
                if (openingtagcounter === closingtagcounter) {
                    openingtags = [];
                    closingtags = [];

                    for (i = 0; i < arraylength; i++) {
                        temp = result[i].split("[");
                        temp = temp[1].split("]");
                        temp = temp[0].split("=");
                        if (temp[0].indexOf("/") === -1) {
                            openingtags.push(temp[0]);
                        } else {
                            tmpstring = openingtags.pop();
                            if (temp[0].toLowerCase() !== ("/" + tmpstring).toLowerCase()) {
                                window.alert(languageArray.bbcode.wrongNesting);

                                return false;
                            }
                        }
                    }

                    return true;
                } else {
                    window.alert(languageArray.bbcode.notSameAmount);
                    return false;
                }
            } else {
                window.alert(languageArray.bbcode.firstTagClosingTag);
                return false;
            }
        }
    } else {
        return true;
    }
}

//initialize javascript language array
languageArray.bbcode = [];
if (typeof calledfrom === "undefined") {
    fetch("getlang.php?modul=bbcode&mode=array", "none", "execute", "event");
} else if (calledfrom === "admin") {
    fetch("../getlang.php?modul=bbcode&mode=array", "none", "execute", "event");
}
//test for valid url
function url(string) {
    regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    result = regexp.test(string);

    return result;
}
