function showAlert(message, type, time) {
    if (time == undefined) time = 3000;
    if (type == undefined) type = 'info';
    var alert = '<div style="z-index:5000;position:fixed;top:80px;left:55%;transform: translateX(-50%);" class="alert alert-' + type + ' alert-dismissible fade show" role="alert"><button type="button" class="close" aria-label="Close" data-dismiss="alert"><span aria-hidden="true">×</span></button>' + message + '</div>';
    $('body').append(alert);
    if (time > 0) {
        setTimeout(function () {
            $('.alert .close').click();
        }, time);
    }
}

String.Format = function (b) {
    var a = arguments;
    return b.replace(/(\{\{\d\}\}|\{\d\})/g, function (b) {
        if (b.substring(0, 2) == "{{") return b;
        var c = parseInt(b.match(/\d/)[0]);
        return a[c + 1]
    })
};
var uiDatetimeFormat = 'DD.MM.YYYY HH:mm';
var dbDatetimeFormat = 'YYYY-MM-DD HH:mm:ss';
var uiDateFormat = 'DD.MM.YYYY';
var dbDateFormat = 'YYYY-MM-DD';

function parseFormData(formId) {
    let formData = new FormData($('#' + formId)[0]);

    //iterating form data
    for (var pair of formData.entries()) {
        //date
        if ($('#' + formId + ' input[name="' + pair[0] + '"]').hasClass('date')) {
            formData.set(pair[0], moment(pair[1], uiDateFormat).format(dbDateFormat));
        }
        //datetime
        if ($('#' + formId + ' input[name="' + pair[0] + '"]').hasClass('datetime')) {
            formData.set(pair[0], moment(pair[1], uiDatetimeFormat).format(dbDatetimeFormat));
        }
        //money
        if ($('#' + formId + ' input[name="' + pair[0] + '"]').hasClass('money')) {
            formData.set(pair[0], accounting.unformat(pair[1]));
        }
    }
    return formData;
}
function parseInputData(key, value, storage) {
    let element = $('#' + key);
    storage = typeof (storage) == 'undefined' ? '' : storage;
    if (element.attr('type') === 'file') {
        element.attr('required', false);
        $('#' + key + '_filename').text(value).attr('href', storage + "/" + value);

    } else if (element.hasClass('money')) {
        element.val(Number(value));

    } else if (element.hasClass('date')) {
        if (value) {
            element.val(moment(value, dbDateFormat).format(uiDateFormat));
        }

    } else if (element.hasClass('datetime')) {
        element.val(moment(value, dbDatetimeFormat).format(uiDatetimeFormat));
    } else if (typeof (value) == 'boolean') {
        element.val(value ? '1' : '0');
    } else {
        element.val(value);
    }
    // console.log(key,value);
}

function defaultAjaxFail(jqXHR, textStatus, errorThrown, callback) {
    let message = "Data not saved.";
    // let responstatusNeedReload = [0,503];
    try {
        console.log(jqXHR);
        let status = jqXHR.status;
        if (status == 401) {
            // window.location.href = 'login';
            // return;
        }
        message = jqXHR.responseJSON.message;
        if (message == "") {
            message = jqXHR.status + ' ' + jqXHR.statusText;
        }
    } catch (e) {
        if (jqXHR.readyState == 4) {
            message = jqXHR.status + ' ' + jqXHR.statusText;
        }
        else if (jqXHR.readyState == 0) {
            // Network error (i.e. connection refused, access denied due to CORS, etc.)
            message = "Network Error: Can't connect to server. Please try again or refresh your browser. (Status: " + jqXHR.status + ")";
        }
    }
    showAlert(message, "danger", 3000);
    if(typeof callback == 'function') callback();
}

var Loading = {
    Show: function (selector) {
        if (selector) {
            $(selector).LoadingOverlay("show", {
                image: "",
                fontawesome: "fa fa-spinner fa-spin",
                fontawesomeColor: '#52BA92'
            });
        } else {
            $.LoadingOverlay("show", {
                image: "",
                fontawesome: "fa fa-spinner fa-spin",
                fontawesomeColor: '#52BA92'
            });
        }
    },
    Hide: function (selector) {
        if (selector) {
            $(selector).LoadingOverlay("hide");
        } else {
            $.LoadingOverlay("hide");
        }
    }
}

Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
    var n = this,
        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
        decSeparator = decSeparator == undefined ? "." : decSeparator,
        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
        sign = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};
String.prototype.fileName = function () {
    return this != null && this != '' ? this.replace(/^.*[\\\/]/, '') : '';
};
function isNumberKey(keyCode) {
    var c = String.fromCharCode(keyCode);
    // var isWordCharacter = c.match(/\w/);
    var isBackspaceOrDelete = (keyCode == 8 || keyCode == 46);
    // console.log(isNaN(c));
    // console.log(isBackspaceOrDelete);
    return !isNaN(c) && !isBackspaceOrDelete;
}
function formatQty(value, currency) {
    // if (currency && currency.toUpperCase() === "IDR")
    //     return parseFloat(value || 0).formatMoney(0, ".", ",");
    // else
    //     return parseFloat(value || 0).formatMoney(3, ".", ",");
    return parseFloat(value || 0).formatMoney(3, ".", ",");
}
function formatCurrency(value, currency) {
    if (currency && currency.toUpperCase() === "IDR")
        return currency + ' ' + parseFloat(value || 0).formatMoney(0, ".", ",");
    else
        return currency + ' ' + parseFloat(value || 0).formatMoney(2, ".", ",");
}
function formatDecimal(value, currency) {
    if (currency && currency.toUpperCase() === "IDR")
        return parseFloat(value || 0).formatMoney(0, ".", ",");
    else
        return parseFloat(value || 0).formatMoney(2, ".", ",");
}

function formatAmmount(value, currency) {
    if (currency && currency.toUpperCase() === "IDR")
        return parseFloat(value || 0).formatMoney(0, ".", ",");
    else
        return parseFloat(value || 0).formatMoney(2, ".", ",");
}

function formatPercentage(value) {
    return parseFloat(value || 0).formatMoney(2, ".", ",");
}

function formatScore(value) {
    return parseFloat(value || 0).formatMoney(0, ".", ",");
}

function formatNumberByCurrency(value, currency) {
    if (currency.toUpperCase() === "IDR")
        return parseFloat(value || 0).formatMoney(0, ".", ",");
    else
        return parseFloat(value || 0).formatMoney(2, ".", ",");
}

function inputItemTextLength(text, maxLength) {
    if (!maxLength) maxLength = 132;
    var lines = text.split(/(\r\n|\n|\r)/gm);
    var linesNew = [];
    let ij = 0;
    for (var i = 0; i < lines.length; i++) {
        if (lines[i].length > (maxLength)) {
            let startAt = 0;
            while (startAt < (lines[i].length - 1)) {
                console.log(startAt + '<' + lines[i].length);
                linesNew[ij] = lines[i].substring(startAt, startAt + maxLength) + '\n';
                startAt = startAt + maxLength;
                ij++;
            }
            // linesNew[ij] = lines[i].substring(0, maxLength) + '\n';
            // ij++;
        } else {
            linesNew[ij] = lines[i];
            ij++;
        }
    }
    return linesNew;
}

function initInputDecimal(currency) {
    $('input[name="est_unit_price"]').attr("type", "text");
    $('input[name="overall_limit"]').attr("type", "text");
    $('.form-area input[name="value"]').attr("type", "text");

    $('input[name="est_unit_price"]').autoNumeric('destroy');
    $('input[name="overall_limit"]').autoNumeric('destroy');
    $('.form-area input[name="value"]').autoNumeric('destroy');

    if ((currency || "").trim().toUpperCase() === "IDR") {
        $('input[name="est_unit_price"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 0, vMax: '999999999999999', vMin: '0' });
        $('input[name="overall_limit"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 0, vMax: '999999999999999', vMin: '0' });
        $('.form-area input[name="value"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 0, vMax: '999999999999999', vMin: '0' });
    } else {
        $('input[name="est_unit_price"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '999999999999999.99', vMin: '0' });
        $('input[name="overall_limit"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '999999999999999.99', vMin: '0' });
        $('.form-area input[name="value"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '999999999999999.99', vMin: '0' });
    }
}

function initInputQty() {
    $('.input-qty').attr("type", "text");
    $('input[name="qty"]').attr("type", "text");
    let currency = $('input[name="qty"]').data("currency");
    let decimalPlace = 3; // (currency && currency.toUpperCase() === "IDR") ? 0 : 3;
    $('.input-qty').autoNumeric('destroy');
    $('input[name="qty"]').autoNumeric('destroy');
    $('.input-qty').autoNumeric('init', { aSep: ".", aDec: ",", mDec: decimalPlace, vMax: '9999999999999.999', vMin: '0' });
    $('input[name="qty"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: decimalPlace, vMax: '9999999999999.999', vMin: '0' });
}

function initInputPercentage() {
    $('.input-percentage').attr("type", "text");
    $('input[name="percentage"]').attr("type", "text");
    $('input[name="tkdn_percentage"]').attr("type", "text");

    $('.input-percentage').autoNumeric('destroy');
    $('input[name="percentage"]').autoNumeric('destroy');
    $('input[name="tkdn_percentage"]').autoNumeric('destroy');

    $('.input-percentage').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '100', vMin: '0' });
    $('input[name="percentage"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '100', vMin: '0' });
    $('input[name="tkdn_percentage"]').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '100', vMin: '0' });
}

function initInputScore() {
    $('.input-score').attr("type", "text");

    $('.input-score').autoNumeric('destroy');

    $('.input-score').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 0, vMax: '100', vMin: '0' });
}

function getCurrencyCode() {
    try {
        var currency_code = $("#currency_code").text();
        if (!currency_code || (currency_code || "").trim() == "") {
            currency_code = $('select[name="currency_code"]').val();
            if (!currency_code || (currency_code || "").trim() == "") {
                currency_code = $("#currency_code_header").val();
                if (!currency_code || (currency_code || "").trim() == "") {
                    currency_code = "IDR";
                }
            }
        }
        return currency_code;
    } catch (e) { return "IDR" }
}

function onKeypressNumberOnly(event, callback) {
    var key = event.keyCode || event.charCode;
    var charcodestring = String.fromCharCode(event.which);
    var regex = new RegExp('^[0-9]+$');
    // 8 = backspace 46 = Del 13 = Enter 39 = Left 37 = right Tab = 9
    if (key == 8 || key == 46 || key == 13 || key == 37 || key == 39 || key == 9) {
        return true;
    }
    // pressed key have to be a number
    if (!regex.test(charcodestring)) {
        event.preventDefault();
        return false;
    }
    return (typeof callback == 'function') ? callback() : true;
}

function specialKey(key) {
    // 8 = backspace 46 = Del 13 = Enter 39 = Left 37 = right Tab = 9
    if (key == 8 || key == 46 || key == 13 || key == 37 || key == 39 || key == 9 || key == 188 || key == 190) {
        return true;
    }
}
// require(['jquery'], function ($) {
//     $.fn.extend({
//         numberOnly: function (selector) {
//             return this.on('keypress', selector, function (event) {
//                 return onKeypressNumberOnly(event);
//             });
//         },
//         decimalQty: function (digit, place, selector) {
//             let _oldValue = $(this).val();

//             this.on('keydown', selector, function (event) {
//                 var key = event.keyCode || event.charCode;
//                 if (specialKey(key)) {
//                     return true;
//                 }
//                 return onKeypressNumberOnly(event);
//             });
//             this.on('keyup', selector, function (event) {
//                 let _value = $(event.target).val();
//                 let qtyLength = _value.split(".");
//                 if (typeof qtyLength == 'string' && qtyLength.length <= 1) {
//                     qtyLength = qtyLength.split(",");
//                 }
//                 if (qtyLength[0] != undefined && qtyLength[0].length <= (digit)) {
//                     _oldValue = $(event.target).val();
//                 } else {
//                     $(event.target).val(_oldValue);
//                 }
//                 if (qtyLength[1] != undefined && qtyLength[1].length > (place)) {
//                     $(event.target).val(parseFloat(_value).toFixed(3));
//                 }
//                 return true;
//             });
//         }
//     });
// });

function initLableQty(currency) {
    let decimalPlace = 3; // (currency && currency.toUpperCase() === "IDR") ? 0 : 3;
    if ($("td#qty").text() && !$("td#qty").text().includes(",")) {
        $("td#qty").text(parseFloat($("td#qty").text()).formatMoney(decimalPlace, ".", ","));
    }
    if ($("td#qty_ordered").text() && !$("td#qty_ordered").text().includes(",")) {
        $("td#qty_ordered").text(parseFloat($("td#qty_ordered").text()).formatMoney(decimalPlace, ".", ","));
    }
}

function initLableAmount(currency_code) {
    let decimalPlace = (currency_code && currency_code.toUpperCase() === "IDR") ? 0 : 3;
    if ($("td#est_unit_price").text() && !$("td#est_unit_price").text().includes(",")) {
        if (currency_code != "IDR") {
            $("td#est_unit_price").text(parseFloat($("td#est_unit_price").text()).formatMoney(2, ".", ","));
        } else {
            $("td#est_unit_price").text(parseFloat($("td#est_unit_price").text()).formatMoney(0, ".", ","));
        }
    }
    if ($("td#price_unit").text() && !$("td#price_unit").text().includes(",")) {
        $("td#price_unit").text(parseFloat($("td#price_unit").text()).formatMoney(decimalPlace, ".", ","));
    }
    // if ($("td#price_unit").text() && !$("td#price_unit").text().includes(",")) {
    //     if (currency_code != "IDR") {
    //         $("td#price_unit").text(parseFloat($("td#price_unit").text()).formatMoney(2, ".", ","));
    //     } else {
    //         $("td#price_unit").text(parseFloat($("td#price_unit").text()).formatMoney(0, ".", ","));
    //     }
    // }
    if ($("td#subtotal").text() && !$("td#subtotal").text().includes(",")) {
        if (currency_code != "IDR") {
            $("td#subtotal").text(parseFloat($("td#subtotal").text()).formatMoney(2, ".", ","));
        } else {
            $("td#subtotal").text(parseFloat($("td#subtotal").text()).formatMoney(0, ".", ","));
        }
    }

    if ($("td#overall_limit").text() && !$("td#overall_limit").text().includes(",")) {
        if (currency_code != "IDR") {
            $("td#overall_limit").text(parseFloat($("td#overall_limit").text()).formatMoney(2, ".", ","));
        } else {
            $("td#overall_limit").text(parseFloat($("td#overall_limit").text()).formatMoney(0, ".", ","));
        }
    }
    if ($("td#expected_limit").text() && !$("td#expected_limit").text().includes(",")) {
        if (currency_code != "IDR") {
            $("td#expected_limit").text(parseFloat($("td#expected_limit").text()).formatMoney(2, ".", ","));
        } else {
            $("td#expected_limit").text(parseFloat($("td#expected_limit").text()).formatMoney(0, ".", ","));
        }
    }
}

function getAutonumricValue(e) { //e => elemen jQuery
    try {
        return e.autoNumeric('get');
    } catch (ex) {
        return e.val();
    }
}

window.onload = function (e) {
    try {
        $(window).on('shown.bs.modal', function () {
            try {
                initLableQty(getCurrencyCode());
                initLableAmount(getCurrencyCode());
            } catch (e) { }
        });
    } catch (e) { }
}


CustomDTtOptions = {
    searchTimeout : undefined,
    _filterColumns : null,
    FilterColumn : function(_this, id,el){
        var th = $('<th class="th-filter-column"></th>');
        var title = $(_this).text();
        if (id != 0) {
            let field = $(el).attr('id');
            $(document.createElement("input"))
                .addClass('form-control form-control-sm')
                .appendTo(th)
                .on("keyup", function () {
                    let SELF = this;
                    if(CustomDTtOptions.searchTimeout != undefined) {
                        clearTimeout(CustomDTtOptions.searchTimeout);
                    }
                    CustomDTtOptions.searchTimeout = setTimeout(function() {
                        CustomDTtOptions.searchTimeout = undefined;
                        table.column(id).search(SELF.value).draw();
                    }, 500);
                }
            );
        }
        return th;
    },
    InitComplete : function() {
        var tr = document.createElement("tr");
        var api = this.api();
        // init filter
        $('#datatable_serverside thead th').each(function (id, el) {
            let th = CustomDTtOptions.FilterColumn(this, id, el);
            $(th).appendTo($(tr));
        });
        $(tr).appendTo($('#datatable_serverside thead'));
    },
};
