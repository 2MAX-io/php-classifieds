(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[8402],{84947:function(t,n,r){"use strict";r(38231)},38231:function(t,n,r){"use strict";r(23157);var e=r(19755),i=r.n(e),u=r(19422);i()(".js__confirm").on("click",(function(){var t,n=i()(this);return confirm((t=n.data("confirm-message")).startsWith("trans.")?u.Z.trans(t):t)}))},84964:function(t,n,r){var e=r(5112)("match");t.exports=function(t){var n=/./;try{"/./"[t](n)}catch(r){try{return n[e]=!1,"/./"[t](n)}catch(t){}}return!1}},3929:function(t,n,r){var e=r(17854),i=r(47850),u=e.TypeError;t.exports=function(t){if(i(t))throw u("The method doesn't accept regular expressions");return t}},83009:function(t,n,r){var e=r(17854),i=r(47293),u=r(1702),a=r(41340),o=r(53111).trim,s=r(81361),c=e.parseInt,f=e.Symbol,l=f&&f.iterator,h=/^[+-]?0x/i,v=u(h.exec),g=8!==c(s+"08")||22!==c(s+"0x16")||l&&!i((function(){c(Object(l))}));t.exports=g?function(t,n){var r=o(a(t));return c(r,n>>>0||(v(h,r)?16:10))}:c},53111:function(t,n,r){var e=r(1702),i=r(84488),u=r(41340),a=r(81361),o=e("".replace),s="["+a+"]",c=RegExp("^"+s+s+"*"),f=RegExp(s+s+"*$"),l=function(t){return function(n){var r=u(i(n));return 1&t&&(r=o(r,c,"")),2&t&&(r=o(r,f,"")),r}};t.exports={start:l(1),end:l(2),trim:l(3)}},50863:function(t,n,r){var e=r(1702);t.exports=e(1..valueOf)},81361:function(t){t.exports="\t\n\v\f\r                　\u2028\u2029\ufeff"},9653:function(t,n,r){"use strict";var e=r(19781),i=r(17854),u=r(1702),a=r(54705),o=r(31320),s=r(92597),c=r(79587),f=r(47976),l=r(52190),h=r(57593),v=r(47293),g=r(8006).f,p=r(31236).f,d=r(3070).f,x=r(50863),I=r(53111).trim,m="Number",b=i.Number,N=b.prototype,E=i.TypeError,y=u("".slice),S=u("".charCodeAt),_=function(t){var n=h(t,"number");return"bigint"==typeof n?n:T(n)},T=function(t){var n,r,e,i,u,a,o,s,c=h(t,"number");if(l(c))throw E("Cannot convert a Symbol value to a number");if("string"==typeof c&&c.length>2)if(c=I(c),43===(n=S(c,0))||45===n){if(88===(r=S(c,2))||120===r)return NaN}else if(48===n){switch(S(c,1)){case 66:case 98:e=2,i=49;break;case 79:case 111:e=8,i=55;break;default:return+c}for(a=(u=y(c,2)).length,o=0;o<a;o++)if((s=S(u,o))<48||s>i)return NaN;return parseInt(u,e)}return+c};if(a(m,!b(" 0o1")||!b("0b1")||b("+0x1"))){for(var w,k=function(t){var n=arguments.length<1?0:b(_(t)),r=this;return f(N,r)&&v((function(){x(r)}))?c(Object(n),r,k):n},A=e?g(b):"MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,EPSILON,MAX_SAFE_INTEGER,MIN_SAFE_INTEGER,isFinite,isInteger,isNaN,isSafeInteger,parseFloat,parseInt,fromString,range".split(","),O=0;A.length>O;O++)s(b,w=A[O])&&!s(k,w)&&d(k,w,p(b,w));k.prototype=N,N.constructor=k,o(i,m,k)}},91058:function(t,n,r){var e=r(82109),i=r(83009);e({global:!0,forced:parseInt!=i},{parseInt:i})},4723:function(t,n,r){"use strict";var e=r(46916),i=r(27007),u=r(19670),a=r(17466),o=r(41340),s=r(84488),c=r(58173),f=r(31530),l=r(97651);i("match",(function(t,n,r){return[function(n){var r=s(this),i=null==n?void 0:c(n,t);return i?e(i,n,r):new RegExp(n)[t](o(r))},function(t){var e=u(this),i=o(t),s=r(n,e,i);if(s.done)return s.value;if(!e.global)return l(e,i);var c=e.unicode;e.lastIndex=0;for(var h,v=[],g=0;null!==(h=l(e,i));){var p=o(h[0]);v[g]=p,""===p&&(e.lastIndex=f(i,a(e.lastIndex),c)),g++}return 0===g?null:v}]}))},23123:function(t,n,r){"use strict";var e=r(22104),i=r(46916),u=r(1702),a=r(27007),o=r(47850),s=r(19670),c=r(84488),f=r(36707),l=r(31530),h=r(17466),v=r(41340),g=r(58173),p=r(50206),d=r(97651),x=r(22261),I=r(52999),m=r(47293),b=I.UNSUPPORTED_Y,N=4294967295,E=Math.min,y=[].push,S=u(/./.exec),_=u(y),T=u("".slice),w=!m((function(){var t=/(?:)/,n=t.exec;t.exec=function(){return n.apply(this,arguments)};var r="ab".split(t);return 2!==r.length||"a"!==r[0]||"b"!==r[1]}));a("split",(function(t,n,r){var u;return u="c"=="abbc".split(/(b)*/)[1]||4!="test".split(/(?:)/,-1).length||2!="ab".split(/(?:ab)*/).length||4!=".".split(/(.?)(.?)/).length||".".split(/()()/).length>1||"".split(/.?/).length?function(t,r){var u=v(c(this)),a=void 0===r?N:r>>>0;if(0===a)return[];if(void 0===t)return[u];if(!o(t))return i(n,u,t,a);for(var s,f,l,h=[],g=(t.ignoreCase?"i":"")+(t.multiline?"m":"")+(t.unicode?"u":"")+(t.sticky?"y":""),d=0,I=new RegExp(t.source,g+"g");(s=i(x,I,u))&&!((f=I.lastIndex)>d&&(_(h,T(u,d,s.index)),s.length>1&&s.index<u.length&&e(y,h,p(s,1)),l=s[0].length,d=f,h.length>=a));)I.lastIndex===s.index&&I.lastIndex++;return d===u.length?!l&&S(I,"")||_(h,""):_(h,T(u,d)),h.length>a?p(h,0,a):h}:"0".split(void 0,0).length?function(t,r){return void 0===t&&0===r?[]:i(n,this,t,r)}:n,[function(n,r){var e=c(this),a=null==n?void 0:g(n,t);return a?i(a,n,e,r):i(u,v(e),n,r)},function(t,e){var i=s(this),a=v(t),o=r(u,i,a,e,u!==n);if(o.done)return o.value;var c=f(i,RegExp),g=i.unicode,p=(i.ignoreCase?"i":"")+(i.multiline?"m":"")+(i.unicode?"u":"")+(b?"g":"y"),x=new c(b?"^(?:"+i.source+")":i,p),I=void 0===e?N:e>>>0;if(0===I)return[];if(0===a.length)return null===d(x,a)?[a]:[];for(var m=0,y=0,S=[];y<a.length;){x.lastIndex=b?0:y;var w,k=d(x,b?T(a,y):a);if(null===k||(w=E(h(x.lastIndex+(b?y:0)),a.length))===m)y=l(a,y,g);else{if(_(S,T(a,m,y)),S.length===I)return S;for(var A=1;A<=k.length-1;A++)if(_(S,k[A]),S.length===I)return S;y=m=w}}return _(S,T(a,m)),S}]}),!w,b)},23157:function(t,n,r){"use strict";var e,i=r(82109),u=r(1702),a=r(31236).f,o=r(17466),s=r(41340),c=r(3929),f=r(84488),l=r(84964),h=r(31913),v=u("".startsWith),g=u("".slice),p=Math.min,d=l("startsWith");i({target:"String",proto:!0,forced:!!(h||d||(e=a(String.prototype,"startsWith"),!e||e.writable))&&!d},{startsWith:function(t){var n=s(f(this));c(t);var r=o(p(arguments.length>1?arguments[1]:void 0,n.length)),e=s(t);return v?v(n,e,r):g(n,r,r+e.length)===e}})}},function(t){t.O(0,[2719,9755,9825,5027,9422],(function(){return n=84947,t(t.s=n);var n}));t.O()}]);
//# sourceMappingURL=admin_package_edit.cc81d69a.js.map