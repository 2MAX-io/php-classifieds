(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[1629],{80862:function(t,e,r){"use strict";r(73210);var n=r(19755),s=r.n(n),i=s()(".js__messageTextarea");i.on("keypress",(function(t){13===t.which&&!t.shiftKey&&""!==i.val().trim()&&(s()(t.target.form).trigger("submit"),t.preventDefault())})),s()((function(){s()(".js__messageList").scrollTop(s()(".js__messageListMessagesBox").height()),i.length&&s()(".js__messageTextarea").trigger("focus")}))},76091:function(t,e,r){var n=r(47293),s=r(81361);t.exports=function(t){return n((function(){return!!s[t]()||"​᠎"!="​᠎"[t]()||s[t].name!==t}))}},53111:function(t,e,r){var n=r(84488),s="["+r(81361)+"]",i=RegExp("^"+s+s+"*"),u=RegExp(s+s+"*$"),o=function(t){return function(e){var r=String(n(e));return 1&t&&(r=r.replace(i,"")),2&t&&(r=r.replace(u,"")),r}};t.exports={start:o(1),end:o(2),trim:o(3)}},81361:function(t){t.exports="\t\n\v\f\r                　\u2028\u2029\ufeff"},73210:function(t,e,r){"use strict";var n=r(82109),s=r(53111).trim;n({target:"String",proto:!0,forced:r(76091)("trim")},{trim:function(){return s(this)}})}},function(t){"use strict";t.O(void 0,[2109,9755],(function(){return e=80862,t(t.s=e);var e}))}]);