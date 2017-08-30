function e(msg) {
    console.log(msg)
}
function _(id) {
    return document.getElementById(id);
}
function $(selector) {
    return new IniteditJSLib(selector);
}
if (!Array.from) {
    Array.from = (function () {
        var toStr = Object.prototype.toString;
        var isCallable = function (fn) {
            return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
        };
        var toInteger = function (value) {
            var number = Number(value);
            if (isNaN(number)) {
                return 0;
            }
            if (number === 0 || !isFinite(number)) {
                return number;
            }
            return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
        };
        var maxSafeInteger = Math.pow(2, 53) - 1;
        var toLength = function (value) {
            var len = toInteger(value);
            return Math.min(Math.max(len, 0), maxSafeInteger);
        };

        // The length property of the from method is 1.
        return function from(arrayLike/*, mapFn, thisArg */) {
            // 1. Let C be the this value.
            var C = this;

            // 2. Let items be ToObject(arrayLike).
            var items = Object(arrayLike);

            // 3. ReturnIfAbrupt(items).
            if (arrayLike == null) {
                throw new TypeError("Array.from requires an array-like object - not null or undefined");
            }

            // 4. If mapfn is undefined, then let mapping be false.
            var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
            var T;
            if (typeof mapFn !== 'undefined') {
                // 5. else
                // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
                if (!isCallable(mapFn)) {
                    throw new TypeError('Array.from: when provided, the second argument must be a function');
                }

                // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
                if (arguments.length > 2) {
                    T = arguments[2];
                }
            }

            // 10. Let lenValue be Get(items, "length").
            // 11. Let len be ToLength(lenValue).
            var len = toLength(items.length);

            // 13. If IsConstructor(C) is true, then
            // 13. a. Let A be the result of calling the [[Construct]] internal method of C with an argument list containing the single item len.
            // 14. a. Else, Let A be ArrayCreate(len).
            var A = isCallable(C) ? Object(new C(len)) : new Array(len);

            // 16. Let k be 0.
            var k = 0;
            // 17. Repeat, while k < len… (also steps a - h)
            var kValue;
            while (k < len) {
                kValue = items[k];
                if (mapFn) {
                    A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
                } else {
                    A[k] = kValue;
                }
                k += 1;
            }
            // 18. Let putStatus be Put(A, "length", len, true).
            A.length = len;
            // 20. Return A.
            return A;
        };
    }());
}
function IniteditJSLib(selector) {

    this.element = [];

    if (typeof selector == "string") {

        this.element = document.querySelectorAll(selector);
    } else if (typeof selector == "object") {

        if (selector.hasOwnProperty("_getStyle")) {
            COST = selector;
            e(COST);
            this.element = (Array.from(selector.element)).slice();
        } else {
            this.element[0] = selector;
        }
    }
    this.length = Array.from(this.element).length;
    this.CUSTOME = function () {
    };
//Start Private Function

    this._getStyle = function (el, styleProp) {
                var value, defaultView = (document).defaultView;
//        var value, defaultView = (el.ownerDocument || document).defaultView;
        if (defaultView && defaultView.getComputedStyle) {
            styleProp = styleProp.replace(/([A-Z])/g, "-$1").toLowerCase();
            return defaultView.getComputedStyle(el, null).getPropertyValue(styleProp);
        } else if (el.currentStyle) {
            styleProp = styleProp.replace(/\-(\w)/g, function (str, letter) {
                return letter.toUpperCase();
            });
            value = el.currentStyle[styleProp];
            if (/^\d+(em|pt|%|ex)?$/i.test(value)) {
                return (function (value) {
                    var oldLeft = el.style.left, oldRsLeft = el.runtimeStyle.left;
                    el.runtimeStyle.left = el.currentStyle.left;
                    el.style.left = value || 0;
                    value = el.style.pixelLeft + "px";
                    el.style.left = oldLeft;
                    el.runtimeStyle.left = oldRsLeft;
                    return value;
                })(value);
            }
            return value;
        }
    }

//End Private Function
//Public Function
    this.elements = function () {
        return Array.from(this.element);
    }
    this.count = function () {
        return Array.from(this.element).length;
    }
    this.height = function () {
        return this.element[0].clientHeight;
    }
    this.actualHeight = function () {
        return this.element[0].offsetHeight;
    }
    this.width = function () {
        return this.element[0].clientWidth;
    }
    this.actualWidth = function () {
        return this.element[0].offsetWidth;
    }
    this.offset = function () {

        return {top: this.element[0].offsetTop, left: this.element[0].offsetLeft};
    }
    this.scrollDown = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].scrollTop = this.element[i].scrollHeight;
        }
        return this;
    }

    this.scrollTo = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].scrollTop = arguments[0];
        }
        return this;
    }

    this.isChecked = function () {
        for (var i = 0; i < this.element.length; i++) {
            if (!(this.element[i].checked)) {
                return false;
            }
        }
        return true;
    }
    this.toggleCheck = function () {
        for (var i = 0; i < this.element.length; i++) {
            if ((this.element[i].checked)) {
                this.element[i].checked = false;
            } else {
                this.element[i].checked = true;
            }
        }
        return true;
    }

    this.dump = function () {
        console.log(this.element);
    }
    this.hide = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].style.display = "none";
        }
        return this;
    }
    this.show = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].style.display = "block";
        }
        return this;
    }
    this.toggle = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            if (this._getStyle(tmp, "display") == "none") {
                tmp.style.display = "block";
            } else {
                tmp.style.display = "none";
            }
        }
        return this;
    }
    //Depricated
    this.text = function () {
        if (arguments.length == 0) {
            return this.element[0].innerHTML;
        } else {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.innerHTML = arguments[0];
            }
            return this;
        }
    }
    this.html = function () {
        if (arguments.length == 0) {
            return this.element[0].innerHTML;
        } else {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.innerHTML = arguments[0];
            }
            return this;
        }
    }
    this.replace = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.outerHTML = arguments[0];
        }
        return this;
    }
    this.remove = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.outerHTML = "";
        }
        return this;
    }
    this.append = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            //tmp.innerHTML = tmp.innerHTML + arguments[0];
            if (typeof arguments[0] == "string") {
                ch = document.createElement("div");
                ch.innerHTML = arguments[0];
                tmp.appendChild(ch.firstElementChild);

            } else {
                tmp.appendChild(arguments[0]);
            }
        }
        return this;
    }
    this.appendStart = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.innerHTML = arguments[0] + tmp.innerHTML;
        }
        return this;
    }
    this.css = function () {
        if (arguments.length === 2) {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.style.cssText += ";" + arguments[0] + ":" + arguments[1] + ";";
            }
        } else if (arguments.length === 1) {
            if (!(arguments[0].search(":") >= 0 || arguments[0].search(";") >= 0)) {
                return this._getStyle(this.element[0], arguments[0]);
            } else {
                for (var i = 0; i < this.element.length; i++) {
                    var tmp = this.element[i];
                    tmp.style.cssText += ";" + arguments[0] + ";";
                }
            }
        }
        return this;
    }

    this.toggleClass = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.classList.toggle(arguments[0]);
        }
        return this;
    }

    this.attr = function () {
        if (arguments.length === 2) {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.setAttribute(arguments[0], arguments[1])
            }
        } else if (arguments.length === 1) {

            if (this.element.length > 0) {
                var tmp = this.element[this.element.length - 1];

                return tmp.getAttribute(arguments[0]);
            } else {
                return;
            }
        }
        return this;
    }
    this.removeAttr = function () {
        if (arguments.length === 1) {

            if (this.element.length > 0) {
                var tmp = this.element[this.element.length - 1];
                tmp.removeAttribute(arguments[0]);
            } else {
                return;
            }
        }
        return this;
    }


    this.val = function () {
        if (arguments.length === 1) {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.value = arguments[0];
            }
        } else if (arguments.length === 0) {
            return this.element[0].value;
        }
        return this;
    }
    this.on = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.addEventListener(arguments[0], arguments[1].bind(tmp), true);
        }
        return this;
    }
    this.off = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.removeEventListener(arguments[0], arguments[1].bind(tmp), true);
        }
        return this;
    }
    this.click = function () {
        $(this).on("click", arguments[0]);
        return this;
    }


    this.addClass = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.classList.add(arguments[0]);
        }
        return this;
    }
    this.removeClass = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.classList.remove(arguments[0]);
        }
        return this;
    }
    this.child = function () {
        if (typeof arguments[0] === "object") {
            if (arguments[0].hasOwnProperty("CUSTOME")) {
                this.element = arguments[0].element;
            } else {
                var tmpElement = array();
                for (var i = 0; i < this.element.length; i++) {
                    var tmp = this.element[i];
                    tmpElement.push.apply(temElement, tmp.querySelectorAll(arguments[0]));
                }
                this.element = tmpElement;
            }
        } else {
            this.element = arguments[0];
        }
        return this;
    }
    this.each = function () {
        var functionname = arguments[0];
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            var anotherfun = functionname.bind(tmp);
            anotherfun();
        }
        return this;
    }
    this.focus = function () {

        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.focus();
        }
        return this;
    }
    /*    this.ajax = function() {
     var args = arguments[0];
     if (typeof args == "undefined") {
     args = {};
     }
     if (!args.hasOwnProperty("type")) {
     args.type = "get";
     }
     if (!args.hasOwnProperty("url")) {
     args.url = "/";
     }
     if (!args.hasOwnProperty("async")) {
     args.async = false;
     }
     if (!args.hasOwnProperty("args")) {
     args.args = "";
     }
     if (!args.hasOwnProperty("readystatechange")) {
     args.readystate = function() {
     
     };
     }
     
     return this;
     }*/


    return this;
}

$.get = function () {
    console.log("A");
}

$.ajax = function () {
    var args = arguments[0];
    if (typeof args == "undefined") {
        args = {};
    }
    if (!args.hasOwnProperty("type")) {
        args.type = "get";
    }
    if (!args.hasOwnProperty("url")) {
        args.url = "/";
    }
    if (!args.hasOwnProperty("async")) {
        args.async = false;
    }
    if (!args.hasOwnProperty("data")) {
        args.data = {};
    }
    if (!args.hasOwnProperty("readystatechange")) {
        args.readystate = function () {

        };
    }
    if (!args.hasOwnProperty("success")) {
        args.success = function () {

        };
    }
    if (!args.hasOwnProperty("failure")) {
        args.failure = function () {

        };
    }
    if (!args.hasOwnProperty("start")) {
        args.start = function () {

        };
    }
    if (!args.hasOwnProperty("end")) {
        args.end = function () {

        };
    }

    if (!args.hasOwnProperty("dataType")) {
        args.dataType = "text";
    }

    if (!args.hasOwnProperty("contentType")) {
        args.contentType = "application/x-www-form-urlencoded";
    }


    args.type = args.type.toUpperCase();
    args.dataType = args.dataType.toUpperCase();
    if (args.data instanceof FormData) {

    } else {
        url = Object.keys(args.data).map(function (k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(args.data[k])
        }).join('&');
        args.data = url;
    }

    var xml = window.XMLHttpRequest ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP");

    xml.open(args.type, args.url, args.async);
    if (!(args.data instanceof FormData)) {
        xml.setRequestHeader("Content-type", args.contentType);
    }


    xml.onreadystatechange = function () {
        args.readystate(xml);
        if (xml.readyState == 4 && xml.status == 200) {
            try {
                if (args.dataType == "TEXT") {
                    args.success(xml.responseText);
                } else if (args.dataType == "HTML") {
                    args.success(xml.responseXML);
                } else if (args.dataType == "JSON") {
                    args.success(JSON.parse(xml.responseText));
                }
            } catch (exce) {
                args.failure();
                e(exce);
                e(xml);
            }
        }

        if (xml.readyState == 4 && xml.status != 200) {
            args.failure();
        }

        if (xml.readyState == 4) {
            args.end();
        }
    }
    args.start();
    e(args.data);
    xml.send(args.data);
    return xml;
}


function get(e) {
    var t = new XMLHttpRequest;
    return t = window.XMLHttpRequest ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP"), t.open("GET", e, !1), t.send(), t.responseText
}
function post(e, t, n, r) {
    return ("false" == typeof r || "undefined" == typeof r) && (r = function () {
    }), "false" == typeof n | "undefined" == typeof n && (n = !1), xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP"), xmlhttp.open("POST", e, n), xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"), xmlhttp.onreadystatechange = r.bind(xmlhttp), xmlhttp.send(t), xmlhttp
}
function trim(e, t) {
    return "false" == typeof t && (t = 5), e = e.substr(0, t)
}
function changeAddr(e) {
    window.history.pushState("", "", e);

}

;

