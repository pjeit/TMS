// script uang 2
function setCaretPosition(elemId, caretPos) {
    var elem = document.getElementById(elemId);

    if (elem != null) {
        if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.move("character", caretPos);
            range.select();
        } else {
            if (elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            } else elem.focus();
        }
    }
}
function getSelectionStart(o) {
    if (o.createTextRange) {
        var r = document.selection.createRange().duplicate();
        r.moveEnd("character", o.value.length);
        if (r.text == "") return o.value.length;
        return o.value.lastIndexOf(r.text);
    } else return o.selectionStart;
}
function removePeriod(nStr, remove) {
    if (nStr != "") {
        tamp = nStr.split(remove);
        nStr = "";
        for (var kembali = 0; kembali < tamp.length; kembali++) {
            nStr += tamp[kembali];
        }
    }
    return nStr;
}
function addPeriodType(nStr, add) {
    nStr += "";
    nStr = removePeriod(nStr, add);
    nStr += "";
    x = nStr.split(add);
    x1 = x[0];
    x2 = x.length > 1 ? add + x[1] : "";
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, "$1" + add + "$2");
    }
    return x1 + x2;
}
function myFunctionduit() {
    var add = ",";
    $(document).on("keyup", ".uang", function (e) {
        if (e.keyCode < 37 || e.keyCode > 40) {
            if (e.keyCode != 9) {
                var id = $(this).attr("id");
                var locationMouse = getSelectionStart(
                    document.getElementById(id)
                );
                var input = document.getElementById(id).value;
                var output = addPeriodType(input, add);
                var posAwal = input.length;
                var posAkhir = output.length;
                if (posAwal - posAkhir == 1) {
                    locationMouse--;
                } else if (posAkhir - posAwal == 1) {
                    locationMouse++;
                }
                document.getElementById(id).value = output;
                setCaretPosition(id, locationMouse);
            }
        }
    });
}

$(document).ready(function () {
    myFunctionduit();
});
