"use strict";

import Routing from "~/module/Routing";
import StackTrace from "stacktrace-js";

/**
 * From: \Nelmio\JsLoggerBundle\TwigExtension
 */
var level = "error";
var escapedUrl = Routing.generate("nelmio_js_logger_log");
var stackTracePath = "https://cdnjs.cloudflare.com/ajax/libs/stacktrace.js/1.3.1/stacktrace.min.js";

var basicModule = (function () {
    var basic = {};
    var oldErrorHandler = window.onerror;

    window.onerror = function (errorMsg, file, line, col, error) {
        if (oldErrorHandler) {
            oldErrorHandler(errorMsg, file, line, col, error);
        }

        if (typeof stackTraceJsModule !== "undefined" && typeof error === "object" && error !== null) {
            stackTraceJsModule.callStackTraceJs(errorMsg, file, line, col, error);
            return;
        }

        basic.callSimpleLogger(errorMsg, file, line, col, error);
    };

    basic.callSimpleLogger = function callSimpleLogger(errorMsg, file, line, col) {
        var e = encodeURIComponent;

        new Image().src =
            escapedUrl +
            "?msg=" +
            e(errorMsg) +
            "&level=" +
            level +
            "&context[file]=" +
            e(file) +
            "&context[line]=" +
            e(line) +
            "&context[column]=" +
            e(col) +
            "&context[browser]=" +
            e(navigator.userAgent) +
            "&context[page]=" +
            e(document.location.href) +
            basic.fetchCustomContext();
    };

    basic.fetchCustomContext = function fetchCustomContext() {
        var key,
            e = encodeURIComponent,
            customContext = window["nelmio_js_logger_custom_context"],
            customContextStr = "";

        if ("object" === typeof customContext) {
            for (key in customContext) {
                if (!customContext.hasOwnProperty(key)) {
                    continue;
                }
                customContextStr += "&context[" + e(key) + "]=" + e(customContext[key]);
            }
        }

        return customContextStr;
    };

    return basic;
})();

var stackTraceJsModule = (function (basicModule) {
    var stackTraceJs = {};

    stackTraceJs.appended = false;
    stackTraceJs.queue = [];

    stackTraceJs.isScriptPresent = function isScriptPresent() {
        return typeof StackTrace !== "undefined" && typeof StackTrace.fromError === "function";
    };

    stackTraceJs.sendLogData = function sendLogData(errorMsg, file, line, col, error) {
        StackTrace.fromError(error)
            .then(function (stackframes) {
                var req = new XMLHttpRequest();
                req.onerror = function (err) {
                    if (typeof console !== "undefined" && typeof console.log === "function") {
                        console.log("An error occurred while trying to log an error using stacktrace.js!", err);
                    }
                    basicModule.callSimpleLogger(errorMsg, file, line, col, error);
                };
                req.onreadystatechange = function onreadystatechange() {
                    if (req.readyState === 4) {
                        if (req.status >= 200 && req.status < 400) {
                            if (typeof console !== "undefined" && typeof console.log === "function") {
                                console.log("Error logged successfully to " + escapedUrl + ".");
                            }
                        } else {
                            if (typeof console !== "undefined" && typeof console.log === "function") {
                                console.log("POST to " + escapedUrl + " failed with status: " + req.status);
                            }
                            basicModule.callSimpleLogger(errorMsg, file, line, col, error);
                        }
                    }
                };
                req.open("post", escapedUrl);
                req.setRequestHeader("Content-Type", "application/json");
                req.send(
                    JSON.stringify({
                        stack: stackframes,
                        msg: errorMsg,
                        level: level,
                        context: {
                            file: file,
                            line: line,
                            column: col,
                            userAgent: navigator.userAgent,
                            platform: navigator.platform,
                            customContext: basicModule.fetchCustomContext(),
                        },
                    })
                );
            })
            .catch(function (err) {
                if (typeof console !== "undefined" && typeof console.log === "function") {
                    console.log("An error occurred while trying to log an error using stacktrace.js!", err);
                }
                basicModule.callSimpleLogger(
                    "An error occurred while trying to log an error using stacktrace.js: " + err.message,
                    err.fileName,
                    err.lineNumber,
                    err.columnNumber,
                    err
                );
                basicModule.callSimpleLogger(errorMsg, file, line, col, error);
            });
    };

    stackTraceJs.callStackTraceJs = function callStackTraceJs(errorMsg, file, line, col, error) {
        if (stackTraceJs.isScriptPresent()) {
            stackTraceJs.sendLogData(errorMsg, file, line, col, error);
            return;
        }

        if (!stackTraceJs.appended) {
            var script = document.createElement("script");
            script.src = stackTracePath;
            document.documentElement.appendChild(script);
            stackTraceJs.appended = true;

            script.onload = function () {
                if (stackTraceJs.isScriptPresent()) {
                    if (!this.executed) {
                        this.executed = true;
                        stackTraceJs.sendLogData(errorMsg, file, line, col, error);
                        var queue = stackTraceJs.queue;
                        for (var i in queue) {
                            if (!queue.hasOwnProperty(i)) {
                                continue;
                            }
                            var entry = queue[i];
                            stackTraceJs.sendLogData(entry[0], entry[1], entry[2], entry[3], entry[4]);
                        }
                    }
                } else {
                    console.log(script);
                    this.onerror();
                }
            };

            script.onerror = function () {
                console.log("StackTrace loading has failed, call default logger");
                basicModule.callSimpleLogger(errorMsg, file, line, col, error);
            };

            script.onreadystatechange = function () {
                var self = this;
                if (this.readyState === "complete" || this.readyState === "loaded") {
                    setTimeout(function () {
                        self.onload();
                    }, 0);
                }
            };
        } else {
            stackTraceJs.queue.push([errorMsg, file, line, col, error]);
        }
    };

    return stackTraceJs;
})(basicModule);
