"use strict";(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[6431],{93365:function(e,n,t){t(32564);var r=t(70471),a=t(79075),s=t(49117),i=t(19422),o=t(19755);function f(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}var u=new(function(){function e(){var n,t,r;!function(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}(this,e),r=0,(t="refreshCount")in(n=this)?Object.defineProperty(n,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):n[t]=r}var n,t,c;return n=e,(t=[{key:"init",value:function(){u.refresh()}},{key:"refresh",value:function(){u.refreshCount++;var e=r.ZP[a.Z.PAYMENT_APP_TOKEN];o.ajax(s.Z.generate("app_payment_status_refresh",[]),{type:"POST",dataType:"json",data:{paymentAppToken:e}}).done((function(n){if(o("#js__lastRefreshTime").text(n.lastRefreshTime),!0===n.paymentComplete){var t=o("#js__paymentStatus");t.addClass("text-success"),t.removeClass("text-danger"),t.text(i.Z.trans("trans.Success, redirecting...")),window.location.href=s.Z.generate("app_payment_await_confirmation_redirect",{paymentAppToken:e})}else{var r=800;u.refreshCount>300&&(r=5e3),u.refreshCount>1800&&(r=6e4),setTimeout((function(){u.refresh()}),r)}})).fail((function(){setTimeout((function(){u.refresh()}),5e3)}))}}])&&f(n.prototype,t),c&&f(n,c),e}());u.init()}},function(e){e.O(0,[2719,9755,9825,5027,9465,9422,9117],(function(){return n=93365,e(e.s=n);var n}));e.O()}]);
//# sourceMappingURL=payment_await_confirmation.c3c12b34.js.map