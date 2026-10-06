/*!
 * imagesLoaded PACKAGED v4.1.4
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */
!(function (e, t) {
    "function" == typeof define && define.amd ? define("ev-emitter/ev-emitter", t) : "object" == typeof module && module.exports ? (module.exports = t()) : (e.EvEmitter = t());
})("undefined" != typeof window ? window : this, function () {
    function e() {}
    var t = e.prototype;
    return (
        (t.on = function (e, t) {
            if (e && t) {
                var i = (this._events = this._events || {}),
                    n = (i[e] = i[e] || []);
                return n.indexOf(t) == -1 && n.push(t), this;
            }
        }),
        (t.once = function (e, t) {
            if (e && t) {
                this.on(e, t);
                var i = (this._onceEvents = this._onceEvents || {}),
                    n = (i[e] = i[e] || {});
                return (n[t] = !0), this;
            }
        }),
        (t.off = function (e, t) {
            var i = this._events && this._events[e];
            if (i && i.length) {
                var n = i.indexOf(t);
                return n != -1 && i.splice(n, 1), this;
            }
        }),
        (t.emitEvent = function (e, t) {
            var i = this._events && this._events[e];
            if (i && i.length) {
                (i = i.slice(0)), (t = t || []);
                for (var n = this._onceEvents && this._onceEvents[e], o = 0; o < i.length; o++) {
                    var r = i[o],
                        s = n && n[r];
                    s && (this.off(e, r), delete n[r]), r.apply(this, t);
                }
                return this;
            }
        }),
        (t.allOff = function () {
            delete this._events, delete this._onceEvents;
        }),
        e
    );
}),
    (function (e, t) {
        "use strict";
        "function" == typeof define && define.amd
            ? define(["ev-emitter/ev-emitter"], function (i) {
                  return t(e, i);
              })
            : "object" == typeof module && module.exports
            ? (module.exports = t(e, require("ev-emitter")))
            : (e.imagesLoaded = t(e, e.EvEmitter));
    })("undefined" != typeof window ? window : this, function (e, t) {
        function i(e, t) {
            for (var i in t) e[i] = t[i];
            return e;
        }
        function n(e) {
            if (Array.isArray(e)) return e;
            var t = "object" == typeof e && "number" == typeof e.length;
            return t ? d.call(e) : [e];
        }
        function o(e, t, r) {
            if (!(this instanceof o)) return new o(e, t, r);
            var s = e;
            return (
                "string" == typeof e && (s = document.querySelectorAll(e)),
                s
                    ? ((this.elements = n(s)),
                      (this.options = i({}, this.options)),
                      "function" == typeof t ? (r = t) : i(this.options, t),
                      r && this.on("always", r),
                      this.getImages(),
                      h && (this.jqDeferred = new h.Deferred()),
                      void setTimeout(this.check.bind(this)))
                    : void a.error("Bad element for imagesLoaded " + (s || e))
            );
        }
        function r(e) {
            this.img = e;
        }
        function s(e, t) {
            (this.url = e), (this.element = t), (this.img = new Image());
        }
        var h = e.jQuery,
            a = e.console,
            d = Array.prototype.slice;
        (o.prototype = Object.create(t.prototype)),
            (o.prototype.options = {}),
            (o.prototype.getImages = function () {
                (this.images = []), this.elements.forEach(this.addElementImages, this);
            }),
            (o.prototype.addElementImages = function (e) {
                "IMG" == e.nodeName && this.addImage(e), this.options.background === !0 && this.addElementBackgroundImages(e);
                var t = e.nodeType;
                if (t && u[t]) {
                    for (var i = e.querySelectorAll("img"), n = 0; n < i.length; n++) {
                        var o = i[n];
                        this.addImage(o);
                    }
                    if ("string" == typeof this.options.background) {
                        var r = e.querySelectorAll(this.options.background);
                        for (n = 0; n < r.length; n++) {
                            var s = r[n];
                            this.addElementBackgroundImages(s);
                        }
                    }
                }
            });
        var u = { 1: !0, 9: !0, 11: !0 };
        return (
            (o.prototype.addElementBackgroundImages = function (e) {
                var t = getComputedStyle(e);
                if (t)
                    for (var i = /url\((['"])?(.*?)\1\)/gi, n = i.exec(t.backgroundImage); null !== n; ) {
                        var o = n && n[2];
                        o && this.addBackground(o, e), (n = i.exec(t.backgroundImage));
                    }
            }),
            (o.prototype.addImage = function (e) {
                var t = new r(e);
                this.images.push(t);
            }),
            (o.prototype.addBackground = function (e, t) {
                var i = new s(e, t);
                this.images.push(i);
            }),
            (o.prototype.check = function () {
                function e(e, i, n) {
                    setTimeout(function () {
                        t.progress(e, i, n);
                    });
                }
                var t = this;
                return (
                    (this.progressedCount = 0),
                    (this.hasAnyBroken = !1),
                    this.images.length
                        ? void this.images.forEach(function (t) {
                              t.once("progress", e), t.check();
                          })
                        : void this.complete()
                );
            }),
            (o.prototype.progress = function (e, t, i) {
                this.progressedCount++,
                    (this.hasAnyBroken = this.hasAnyBroken || !e.isLoaded),
                    this.emitEvent("progress", [this, e, t]),
                    this.jqDeferred && this.jqDeferred.notify && this.jqDeferred.notify(this, e),
                    this.progressedCount == this.images.length && this.complete(),
                    this.options.debug && a && a.log("progress: " + i, e, t);
            }),
            (o.prototype.complete = function () {
                var e = this.hasAnyBroken ? "fail" : "done";
                if (((this.isComplete = !0), this.emitEvent(e, [this]), this.emitEvent("always", [this]), this.jqDeferred)) {
                    var t = this.hasAnyBroken ? "reject" : "resolve";
                    this.jqDeferred[t](this);
                }
            }),
            (r.prototype = Object.create(t.prototype)),
            (r.prototype.check = function () {
                var e = this.getIsImageComplete();
                return e
                    ? void this.confirm(0 !== this.img.naturalWidth, "naturalWidth")
                    : ((this.proxyImage = new Image()),
                      this.proxyImage.addEventListener("load", this),
                      this.proxyImage.addEventListener("error", this),
                      this.img.addEventListener("load", this),
                      this.img.addEventListener("error", this),
                      void (this.proxyImage.src = this.img.src));
            }),
            (r.prototype.getIsImageComplete = function () {
                return this.img.complete && this.img.naturalWidth;
            }),
            (r.prototype.confirm = function (e, t) {
                (this.isLoaded = e), this.emitEvent("progress", [this, this.img, t]);
            }),
            (r.prototype.handleEvent = function (e) {
                var t = "on" + e.type;
                this[t] && this[t](e);
            }),
            (r.prototype.onload = function () {
                this.confirm(!0, "onload"), this.unbindEvents();
            }),
            (r.prototype.onerror = function () {
                this.confirm(!1, "onerror"), this.unbindEvents();
            }),
            (r.prototype.unbindEvents = function () {
                this.proxyImage.removeEventListener("load", this), this.proxyImage.removeEventListener("error", this), this.img.removeEventListener("load", this), this.img.removeEventListener("error", this);
            }),
            (s.prototype = Object.create(r.prototype)),
            (s.prototype.check = function () {
                this.img.addEventListener("load", this), this.img.addEventListener("error", this), (this.img.src = this.url);
                var e = this.getIsImageComplete();
                e && (this.confirm(0 !== this.img.naturalWidth, "naturalWidth"), this.unbindEvents());
            }),
            (s.prototype.unbindEvents = function () {
                this.img.removeEventListener("load", this), this.img.removeEventListener("error", this);
            }),
            (s.prototype.confirm = function (e, t) {
                (this.isLoaded = e), this.emitEvent("progress", [this, this.element, t]);
            }),
            (o.makeJQueryPlugin = function (t) {
                (t = t || e.jQuery),
                    t &&
                        ((h = t),
                        (h.fn.imagesLoaded = function (e, t) {
                            var i = new o(this, e, t);
                            return i.jqDeferred.promise(h(this));
                        }));
            }),
            o.makeJQueryPlugin(),
            o
        );
    });

/*! modernizr 3.5.0 (Custom Build) | MIT *
 * https://modernizr.com/download/?-csspointerevents-mediaqueries-touchevents-setclasses !*/
!(function (e, n, t) {
    function o(e, n) {
        return typeof e === n;
    }
    function s() {
        var e, n, t, s, a, i, r;
        for (var l in u)
            if (u.hasOwnProperty(l)) {
                if (((e = []), (n = u[l]), n.name && (e.push(n.name.toLowerCase()), n.options && n.options.aliases && n.options.aliases.length))) for (t = 0; t < n.options.aliases.length; t++) e.push(n.options.aliases[t].toLowerCase());
                for (s = o(n.fn, "function") ? n.fn() : n.fn, a = 0; a < e.length; a++)
                    (i = e[a]),
                        (r = i.split(".")),
                        1 === r.length ? (Modernizr[r[0]] = s) : (!Modernizr[r[0]] || Modernizr[r[0]] instanceof Boolean || (Modernizr[r[0]] = new Boolean(Modernizr[r[0]])), (Modernizr[r[0]][r[1]] = s)),
                        f.push((s ? "" : "no-") + r.join("-"));
            }
    }
    function a(e) {
        var n = d.className,
            t = Modernizr._config.classPrefix || "";
        if ((p && (n = n.baseVal), Modernizr._config.enableJSClass)) {
            var o = new RegExp("(^|\\s)" + t + "no-js(\\s|$)");
            n = n.replace(o, "$1" + t + "js$2");
        }
        Modernizr._config.enableClasses && ((n += " " + t + e.join(" " + t)), p ? (d.className.baseVal = n) : (d.className = n));
    }
    function i() {
        return "function" != typeof n.createElement ? n.createElement(arguments[0]) : p ? n.createElementNS.call(n, "http://www.w3.org/2000/svg", arguments[0]) : n.createElement.apply(n, arguments);
    }
    function r() {
        var e = n.body;
        return e || ((e = i(p ? "svg" : "body")), (e.fake = !0)), e;
    }
    function l(e, t, o, s) {
        var a,
            l,
            f,
            u,
            c = "modernizr",
            p = i("div"),
            m = r();
        if (parseInt(o, 10)) for (; o--; ) (f = i("div")), (f.id = s ? s[o] : c + (o + 1)), p.appendChild(f);
        return (
            (a = i("style")),
            (a.type = "text/css"),
            (a.id = "s" + c),
            (m.fake ? m : p).appendChild(a),
            m.appendChild(p),
            a.styleSheet ? (a.styleSheet.cssText = e) : a.appendChild(n.createTextNode(e)),
            (p.id = c),
            m.fake && ((m.style.background = ""), (m.style.overflow = "hidden"), (u = d.style.overflow), (d.style.overflow = "hidden"), d.appendChild(m)),
            (l = t(p, e)),
            m.fake ? (m.parentNode.removeChild(m), (d.style.overflow = u), d.offsetHeight) : p.parentNode.removeChild(p),
            !!l
        );
    }
    var f = [],
        u = [],
        c = {
            _version: "3.5.0",
            _config: { classPrefix: "", enableClasses: !0, enableJSClass: !0, usePrefixes: !0 },
            _q: [],
            on: function (e, n) {
                var t = this;
                setTimeout(function () {
                    n(t[e]);
                }, 0);
            },
            addTest: function (e, n, t) {
                u.push({ name: e, fn: n, options: t });
            },
            addAsyncTest: function (e) {
                u.push({ name: null, fn: e });
            },
        },
        Modernizr = function () {};
    (Modernizr.prototype = c), (Modernizr = new Modernizr());
    var d = n.documentElement,
        p = "svg" === d.nodeName.toLowerCase(),
        m = c._config.usePrefixes ? " -webkit- -moz- -o- -ms- ".split(" ") : ["", ""];
    (c._prefixes = m),
        Modernizr.addTest("csspointerevents", function () {
            var e = i("a").style;
            return (e.cssText = "pointer-events:auto"), "auto" === e.pointerEvents;
        });
    var h = (c.testStyles = l);
    Modernizr.addTest("touchevents", function () {
        var t;
        if ("ontouchstart" in e || (e.DocumentTouch && n instanceof DocumentTouch)) t = !0;
        else {
            var o = ["@media (", m.join("touch-enabled),("), "heartz", ")", "{#modernizr{top:9px;position:absolute}}"].join("");
            h(o, function (e) {
                t = 9 === e.offsetTop;
            });
        }
        return t;
    });
    var v = (function () {
        var n = e.matchMedia || e.msMatchMedia;
        return n
            ? function (e) {
                  var t = n(e);
                  return (t && t.matches) || !1;
              }
            : function (n) {
                  var t = !1;
                  return (
                      l("@media " + n + " { #modernizr { position: absolute; } }", function (n) {
                          t = "absolute" == (e.getComputedStyle ? e.getComputedStyle(n, null) : n.currentStyle).position;
                      }),
                      t
                  );
              };
    })();
    (c.mq = v), Modernizr.addTest("mediaqueries", v("only all")), s(), a(f), delete c.addTest, delete c.addAsyncTest;
    for (var y = 0; y < Modernizr._q.length; y++) Modernizr._q[y]();
    e.Modernizr = Modernizr;
})(window, document);

!(function (t, e) {
    if ("function" == typeof define && define.amd) define(["exports"], e);
    else if ("undefined" != typeof exports) e(exports);
    else {
        var n = { exports: {} };
        e(n.exports), (t.retina = n.exports);
    }
})(this, function (t) {
    "use strict";
    function e(t) {
        return Array.prototype.slice.call(t);
    }
    function n(t) {
        var e = parseInt(t, 10);
        return c < e ? c : e;
    }
    function r(t) {
        return (
            t.hasAttribute("data-no-resize") ||
                (0 === t.offsetWidth && 0 === t.offsetHeight ? (t.setAttribute("width", t.naturalWidth), t.setAttribute("height", t.naturalHeight)) : (t.setAttribute("width", t.offsetWidth), t.setAttribute("height", t.offsetHeight))),
            t
        );
    }
    function i(t, e) {
        var n = t.nodeName.toLowerCase(),
            i = document.createElement("img");
        i.addEventListener("load", function () {
            "img" === n ? r(t).setAttribute("src", e) : (t.style.backgroundImage = "url(" + e + ")");
        }),
            i.setAttribute("src", e),
            t.setAttribute(g, !0);
    }
    function o(t, e) {
        var r = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : 1,
            o = n(r);
        if (e && o > 1) {
            var u = e.replace(l, "@" + o + "x$1");
            i(t, u);
        }
    }
    function u(t, e, n) {
        c > 1 && i(t, n);
    }
    function a(t) {
        return t ? ("function" == typeof t.forEach ? t : e(t)) : "undefined" != typeof document ? e(document.querySelectorAll(h)) : [];
    }
    function f(t) {
        return t.style.backgroundImage.replace(p, "$2");
    }
    function s(t) {
        a(t).forEach(function (t) {
            if (!t.getAttribute(g)) {
                var e = "img" === t.nodeName.toLowerCase(),
                    n = e ? t.getAttribute("src") : f(t),
                    r = t.getAttribute("data-rjs"),
                    i = !isNaN(parseInt(r, 10));
                if (null === r) return;
                i ? o(t, n, r) : u(t, n, r);
            }
        });
    }
    Object.defineProperty(t, "__esModule", { value: !0 });
    var d = "undefined" != typeof window,
        c = Math.round(d ? window.devicePixelRatio || 1 : 1),
        l = /(\.[A-z]{3,4}\/?(\?.*)?)$/,
        p = /url\(('|")?([^\)'"]+)('|")?\)/i,
        h = "[data-rjs]",
        g = "data-rjs-processed";
    d &&
        (window.addEventListener("load", function () {
            s();
        }),
        (window.retinajs = s)),
        (t["default"] = s);
});