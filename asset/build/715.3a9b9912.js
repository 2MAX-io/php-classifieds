(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[715],{25787:function(e){e.exports=function(e,t,r){if(!(e instanceof t))throw TypeError("Incorrect "+(r?r+" ":"")+"invocation");return e}},18533:function(e,t,r){"use strict";var n=r(42092).forEach,a=r(9341)("forEach");e.exports=a?[].forEach:function(e){return n(this,e,arguments.length>1?arguments[1]:void 0)}},48457:function(e,t,r){"use strict";var n=r(49974),a=r(47908),o=r(53411),i=r(97659),u=r(17466),s=r(86135),f=r(71246);e.exports=function(e){var t,r,c,h,l,p,v=a(e),g="function"==typeof this?this:Array,m=arguments.length,d=m>1?arguments[1]:void 0,y=void 0!==d,b=f(v),w=0;if(y&&(d=n(d,m>2?arguments[2]:void 0,2)),null==b||g==Array&&i(b))for(r=new g(t=u(v.length));t>w;w++)p=y?d(v[w],w):v[w],s(r,w,p);else for(l=(h=b.call(v)).next,r=new g;!(c=l.call(h)).done;w++)p=y?o(h,d,[c.value,w],!0):c.value,s(r,w,p);return r.length=w,r}},9341:function(e,t,r){"use strict";var n=r(47293);e.exports=function(e,t){var r=[][e];return!!r&&n((function(){r.call(null,t||function(){throw 1},1)}))}},53411:function(e,t,r){var n=r(19670),a=r(99212);e.exports=function(e,t,r,o){try{return o?t(n(r)[0],r[1]):t(r)}catch(t){throw a(e),t}}},86135:function(e,t,r){"use strict";var n=r(57593),a=r(3070),o=r(79114);e.exports=function(e,t,r){var i=n(t);i in e?a.f(e,i,o(0,r)):e[i]=r}},76677:function(e,t,r){var n=r(47293);e.exports=!n((function(){return Object.isExtensible(Object.preventExtensions({}))}))},71246:function(e,t,r){var n=r(70648),a=r(97497),o=r(5112)("iterator");e.exports=function(e){if(null!=e)return e[o]||e["@@iterator"]||a[n(e)]}},18554:function(e,t,r){var n=r(19670),a=r(71246);e.exports=function(e){var t=a(e);if("function"!=typeof t)throw TypeError(String(e)+" is not iterable");return n(t.call(e))}},62423:function(e,t,r){var n=r(3501),a=r(70111),o=r(86656),i=r(3070).f,u=r(69711),s=r(76677),f=u("meta"),c=0,h=Object.isExtensible||function(){return!0},l=function(e){i(e,f,{value:{objectID:"O"+ ++c,weakData:{}}})},p=e.exports={REQUIRED:!1,fastKey:function(e,t){if(!a(e))return"symbol"==typeof e?e:("string"==typeof e?"S":"P")+e;if(!o(e,f)){if(!h(e))return"F";if(!t)return"E";l(e)}return e[f].objectID},getWeakData:function(e,t){if(!o(e,f)){if(!h(e))return!0;if(!t)return!1;l(e)}return e[f].weakData},onFreeze:function(e){return s&&p.REQUIRED&&h(e)&&!o(e,f)&&l(e),e}};n[f]=!0},97659:function(e,t,r){var n=r(5112),a=r(97497),o=n("iterator"),i=Array.prototype;e.exports=function(e){return void 0!==e&&(a.Array===e||i[o]===e)}},99212:function(e,t,r){var n=r(19670);e.exports=function(e){var t=e.return;if(void 0!==t)return n(t.call(e)).value}},590:function(e,t,r){var n=r(47293),a=r(5112),o=r(31913),i=a("iterator");e.exports=!n((function(){var e=new URL("b?a=1&b=2&c=3","http://a"),t=e.searchParams,r="";return e.pathname="c%20d",t.forEach((function(e,n){t.delete("b"),r+=n+e})),o&&!e.toJSON||!t.sort||"http://a/c%20d?a=1&c=3"!==e.href||"3"!==t.get("c")||"a=1"!==String(new URLSearchParams("?a=1"))||!t[i]||"a"!==new URL("https://a@b").username||"b"!==new URLSearchParams(new URLSearchParams("a=b")).get("a")||"xn--e1aybc"!==new URL("http://тест").host||"#%D0%B1"!==new URL("http://a#б").hash||"a1c3"!==r||"x"!==new URL("http://x",void 0).host}))},21574:function(e,t,r){"use strict";var n=r(19781),a=r(47293),o=r(81956),i=r(25181),u=r(55296),s=r(47908),f=r(68361),c=Object.assign,h=Object.defineProperty;e.exports=!c||a((function(){if(n&&1!==c({b:1},c(h({},"a",{enumerable:!0,get:function(){h(this,"b",{value:3,enumerable:!1})}}),{b:2})).b)return!0;var e={},t={},r=Symbol(),a="abcdefghijklmnopqrst";return e[r]=7,a.split("").forEach((function(e){t[e]=e})),7!=c({},e)[r]||o(c({},t)).join("")!=a}))?function(e,t){for(var r=s(e),a=arguments.length,c=1,h=i.f,l=u.f;a>c;)for(var p,v=f(arguments[c++]),g=h?o(v).concat(h(v)):o(v),m=g.length,d=0;m>d;)p=g[d++],n&&!l.call(v,p)||(r[p]=v[p]);return r}:c},12248:function(e,t,r){var n=r(31320);e.exports=function(e,t,r){for(var a in t)n(e,a,t[a],r);return e}},33197:function(e){"use strict";var t=2147483647,r=/[^\0-\u007E]/,n=/[.\u3002\uFF0E\uFF61]/g,a="Overflow: input needs wider integers to process",o=Math.floor,i=String.fromCharCode,u=function(e){return e+22+75*(e<26)},s=function(e,t,r){var n=0;for(e=r?o(e/700):e>>1,e+=o(e/t);e>455;n+=36)e=o(e/35);return o(n+36*e/(e+38))},f=function(e){var r,n,f=[],c=(e=function(e){for(var t=[],r=0,n=e.length;r<n;){var a=e.charCodeAt(r++);if(a>=55296&&a<=56319&&r<n){var o=e.charCodeAt(r++);56320==(64512&o)?t.push(((1023&a)<<10)+(1023&o)+65536):(t.push(a),r--)}else t.push(a)}return t}(e)).length,h=128,l=0,p=72;for(r=0;r<e.length;r++)(n=e[r])<128&&f.push(i(n));var v=f.length,g=v;for(v&&f.push("-");g<c;){var m=t;for(r=0;r<e.length;r++)(n=e[r])>=h&&n<m&&(m=n);var d=g+1;if(m-h>o((t-l)/d))throw RangeError(a);for(l+=(m-h)*d,h=m,r=0;r<e.length;r++){if((n=e[r])<h&&++l>t)throw RangeError(a);if(n==h){for(var y=l,b=36;;b+=36){var w=b<=p?1:b>=p+26?26:b-p;if(y<w)break;var k=y-w,R=36-w;f.push(i(u(w+k%R))),y=o(k/R)}f.push(i(u(y))),p=s(l,d,g==v),l=0,++g}}++l,++h}return f.join("")};e.exports=function(e){var t,a,o=[],i=e.toLowerCase().replace(n,".").split(".");for(t=0;t<i.length;t++)a=i[t],o.push(r.test(a)?"xn--"+f(a):a);return o.join(".")}},89554:function(e,t,r){"use strict";var n=r(82109),a=r(18533);n({target:"Array",proto:!0,forced:[].forEach!=a},{forEach:a})},82772:function(e,t,r){"use strict";var n=r(82109),a=r(41318).indexOf,o=r(9341),i=[].indexOf,u=!!i&&1/[1].indexOf(1,-0)<0,s=o("indexOf");n({target:"Array",proto:!0,forced:u||!s},{indexOf:function(e){return u?i.apply(this,arguments)||0:a(this,e,arguments.length>1?arguments[1]:void 0)}})},69600:function(e,t,r){"use strict";var n=r(82109),a=r(68361),o=r(45656),i=r(9341),u=[].join,s=a!=Object,f=i("join",",");n({target:"Array",proto:!0,forced:s||!f},{join:function(e){return u.call(o(this),void 0===e?",":e)}})},19601:function(e,t,r){var n=r(82109),a=r(21574);n({target:"Object",stat:!0,forced:Object.assign!==a},{assign:a})},43371:function(e,t,r){var n=r(82109),a=r(76677),o=r(47293),i=r(70111),u=r(62423).onFreeze,s=Object.freeze;n({target:"Object",stat:!0,forced:o((function(){s(1)})),sham:!a},{freeze:function(e){return s&&i(e)?s(u(e)):e}})},47941:function(e,t,r){var n=r(82109),a=r(47908),o=r(81956);n({target:"Object",stat:!0,forced:r(47293)((function(){o(1)}))},{keys:function(e){return o(a(e))}})},54747:function(e,t,r){var n=r(17854),a=r(48324),o=r(18533),i=r(68880);for(var u in a){var s=n[u],f=s&&s.prototype;if(f&&f.forEach!==o)try{i(f,"forEach",o)}catch(e){f.forEach=o}}},41637:function(e,t,r){"use strict";r(66992);var n=r(82109),a=r(35005),o=r(590),i=r(31320),u=r(12248),s=r(58003),f=r(24994),c=r(29909),h=r(25787),l=r(86656),p=r(49974),v=r(70648),g=r(19670),m=r(70111),d=r(70030),y=r(79114),b=r(18554),w=r(71246),k=r(5112),R=a("fetch"),U=a("Headers"),L=k("iterator"),A="URLSearchParams",S="URLSearchParamsIterator",x=c.set,E=c.getterFor(A),j=c.getterFor(S),q=/\+/g,O=Array(4),B=function(e){return O[e-1]||(O[e-1]=RegExp("((?:%[\\da-f]{2}){"+e+"})","gi"))},P=function(e){try{return decodeURIComponent(e)}catch(t){return e}},I=function(e){var t=e.replace(q," "),r=4;try{return decodeURIComponent(t)}catch(e){for(;r;)t=t.replace(B(r--),P);return t}},C=/[!'()~]|%20/g,F={"!":"%21","'":"%27","(":"%28",")":"%29","~":"%7E","%20":"+"},D=function(e){return F[e]},T=function(e){return encodeURIComponent(e).replace(C,D)},z=function(e,t){if(t)for(var r,n,a=t.split("&"),o=0;o<a.length;)(r=a[o++]).length&&(n=r.split("="),e.push({key:I(n.shift()),value:I(n.join("="))}))},$=function(e){this.entries.length=0,z(this.entries,e)},M=function(e,t){if(e<t)throw TypeError("Not enough arguments")},N=f((function(e,t){x(this,{type:S,iterator:b(E(e).entries),kind:t})}),"Iterator",(function(){var e=j(this),t=e.kind,r=e.iterator.next(),n=r.value;return r.done||(r.value="keys"===t?n.key:"values"===t?n.value:[n.key,n.value]),r})),J=function(){h(this,J,A);var e,t,r,n,a,o,i,u,s,f=arguments.length>0?arguments[0]:void 0,c=this,p=[];if(x(c,{type:A,entries:p,updateURL:function(){},updateSearchParams:$}),void 0!==f)if(m(f))if("function"==typeof(e=w(f)))for(r=(t=e.call(f)).next;!(n=r.call(t)).done;){if((i=(o=(a=b(g(n.value))).next).call(a)).done||(u=o.call(a)).done||!o.call(a).done)throw TypeError("Expected sequence with length 2");p.push({key:i.value+"",value:u.value+""})}else for(s in f)l(f,s)&&p.push({key:s,value:f[s]+""});else z(p,"string"==typeof f?"?"===f.charAt(0)?f.slice(1):f:f+"")},Q=J.prototype;u(Q,{append:function(e,t){M(arguments.length,2);var r=E(this);r.entries.push({key:e+"",value:t+""}),r.updateURL()},delete:function(e){M(arguments.length,1);for(var t=E(this),r=t.entries,n=e+"",a=0;a<r.length;)r[a].key===n?r.splice(a,1):a++;t.updateURL()},get:function(e){M(arguments.length,1);for(var t=E(this).entries,r=e+"",n=0;n<t.length;n++)if(t[n].key===r)return t[n].value;return null},getAll:function(e){M(arguments.length,1);for(var t=E(this).entries,r=e+"",n=[],a=0;a<t.length;a++)t[a].key===r&&n.push(t[a].value);return n},has:function(e){M(arguments.length,1);for(var t=E(this).entries,r=e+"",n=0;n<t.length;)if(t[n++].key===r)return!0;return!1},set:function(e,t){M(arguments.length,1);for(var r,n=E(this),a=n.entries,o=!1,i=e+"",u=t+"",s=0;s<a.length;s++)(r=a[s]).key===i&&(o?a.splice(s--,1):(o=!0,r.value=u));o||a.push({key:i,value:u}),n.updateURL()},sort:function(){var e,t,r,n=E(this),a=n.entries,o=a.slice();for(a.length=0,r=0;r<o.length;r++){for(e=o[r],t=0;t<r;t++)if(a[t].key>e.key){a.splice(t,0,e);break}t===r&&a.push(e)}n.updateURL()},forEach:function(e){for(var t,r=E(this).entries,n=p(e,arguments.length>1?arguments[1]:void 0,3),a=0;a<r.length;)n((t=r[a++]).value,t.key,this)},keys:function(){return new N(this,"keys")},values:function(){return new N(this,"values")},entries:function(){return new N(this,"entries")}},{enumerable:!0}),i(Q,L,Q.entries),i(Q,"toString",(function(){for(var e,t=E(this).entries,r=[],n=0;n<t.length;)e=t[n++],r.push(T(e.key)+"="+T(e.value));return r.join("&")}),{enumerable:!0}),s(J,A),n({global:!0,forced:!o},{URLSearchParams:J}),o||"function"!=typeof R||"function"!=typeof U||n({global:!0,enumerable:!0,forced:!0},{fetch:function(e){var t,r,n,a=[e];return arguments.length>1&&(m(t=arguments[1])&&(r=t.body,v(r)===A&&((n=t.headers?new U(t.headers):new U).has("content-type")||n.set("content-type","application/x-www-form-urlencoded;charset=UTF-8"),t=d(t,{body:y(0,String(r)),headers:y(0,n)}))),a.push(t)),R.apply(this,a)}}),e.exports={URLSearchParams:J,getState:E}},60285:function(e,t,r){"use strict";r(78783);var n,a=r(82109),o=r(19781),i=r(590),u=r(17854),s=r(36048),f=r(31320),c=r(25787),h=r(86656),l=r(21574),p=r(48457),v=r(28710).codeAt,g=r(33197),m=r(58003),d=r(41637),y=r(29909),b=u.URL,w=d.URLSearchParams,k=d.getState,R=y.set,U=y.getterFor("URL"),L=Math.floor,A=Math.pow,S="Invalid scheme",x="Invalid host",E="Invalid port",j=/[A-Za-z]/,q=/[\d+-.A-Za-z]/,O=/\d/,B=/^(0x|0X)/,P=/^[0-7]+$/,I=/^\d+$/,C=/^[\dA-Fa-f]+$/,F=/[\u0000\t\u000A\u000D #%/:?@[\\]]/,D=/[\u0000\t\u000A\u000D #/:?@[\\]]/,T=/^[\u0000-\u001F ]+|[\u0000-\u001F ]+$/g,z=/[\t\u000A\u000D]/g,$=function(e,t){var r,n,a;if("["==t.charAt(0)){if("]"!=t.charAt(t.length-1))return x;if(!(r=N(t.slice(1,-1))))return x;e.host=r}else if(G(e)){if(t=g(t),F.test(t))return x;if(null===(r=M(t)))return x;e.host=r}else{if(D.test(t))return x;for(r="",n=p(t),a=0;a<n.length;a++)r+=W(n[a],Q);e.host=r}},M=function(e){var t,r,n,a,o,i,u,s=e.split(".");if(s.length&&""==s[s.length-1]&&s.pop(),(t=s.length)>4)return e;for(r=[],n=0;n<t;n++){if(""==(a=s[n]))return e;if(o=10,a.length>1&&"0"==a.charAt(0)&&(o=B.test(a)?16:8,a=a.slice(8==o?1:2)),""===a)i=0;else{if(!(10==o?I:8==o?P:C).test(a))return e;i=parseInt(a,o)}r.push(i)}for(n=0;n<t;n++)if(i=r[n],n==t-1){if(i>=A(256,5-t))return null}else if(i>255)return null;for(u=r.pop(),n=0;n<r.length;n++)u+=r[n]*A(256,3-n);return u},N=function(e){var t,r,n,a,o,i,u,s=[0,0,0,0,0,0,0,0],f=0,c=null,h=0,l=function(){return e.charAt(h)};if(":"==l()){if(":"!=e.charAt(1))return;h+=2,c=++f}for(;l();){if(8==f)return;if(":"!=l()){for(t=r=0;r<4&&C.test(l());)t=16*t+parseInt(l(),16),h++,r++;if("."==l()){if(0==r)return;if(h-=r,f>6)return;for(n=0;l();){if(a=null,n>0){if(!("."==l()&&n<4))return;h++}if(!O.test(l()))return;for(;O.test(l());){if(o=parseInt(l(),10),null===a)a=o;else{if(0==a)return;a=10*a+o}if(a>255)return;h++}s[f]=256*s[f]+a,2!=++n&&4!=n||f++}if(4!=n)return;break}if(":"==l()){if(h++,!l())return}else if(l())return;s[f++]=t}else{if(null!==c)return;h++,c=++f}}if(null!==c)for(i=f-c,f=7;0!=f&&i>0;)u=s[f],s[f--]=s[c+i-1],s[c+--i]=u;else if(8!=f)return;return s},J=function(e){var t,r,n,a;if("number"==typeof e){for(t=[],r=0;r<4;r++)t.unshift(e%256),e=L(e/256);return t.join(".")}if("object"==typeof e){for(t="",n=function(e){for(var t=null,r=1,n=null,a=0,o=0;o<8;o++)0!==e[o]?(a>r&&(t=n,r=a),n=null,a=0):(null===n&&(n=o),++a);return a>r&&(t=n,r=a),t}(e),r=0;r<8;r++)a&&0===e[r]||(a&&(a=!1),n===r?(t+=r?":":"::",a=!0):(t+=e[r].toString(16),r<7&&(t+=":")));return"["+t+"]"}return e},Q={},Z=l({},Q,{" ":1,'"':1,"<":1,">":1,"`":1}),H=l({},Z,{"#":1,"?":1,"{":1,"}":1}),K=l({},H,{"/":1,":":1,";":1,"=":1,"@":1,"[":1,"\\":1,"]":1,"^":1,"|":1}),W=function(e,t){var r=v(e,0);return r>32&&r<127&&!h(t,e)?e:encodeURIComponent(e)},X={ftp:21,file:null,http:80,https:443,ws:80,wss:443},G=function(e){return h(X,e.scheme)},V=function(e){return""!=e.username||""!=e.password},Y=function(e){return!e.host||e.cannotBeABaseURL||"file"==e.scheme},_=function(e,t){var r;return 2==e.length&&j.test(e.charAt(0))&&(":"==(r=e.charAt(1))||!t&&"|"==r)},ee=function(e){var t;return e.length>1&&_(e.slice(0,2))&&(2==e.length||"/"===(t=e.charAt(2))||"\\"===t||"?"===t||"#"===t)},te=function(e){var t=e.path,r=t.length;!r||"file"==e.scheme&&1==r&&_(t[0],!0)||t.pop()},re=function(e){return"."===e||"%2e"===e.toLowerCase()},ne={},ae={},oe={},ie={},ue={},se={},fe={},ce={},he={},le={},pe={},ve={},ge={},me={},de={},ye={},be={},we={},ke={},Re={},Ue={},Le=function(e,t,r,a){var o,i,u,s,f,c=r||ne,l=0,v="",g=!1,m=!1,d=!1;for(r||(e.scheme="",e.username="",e.password="",e.host=null,e.port=null,e.path=[],e.query=null,e.fragment=null,e.cannotBeABaseURL=!1,t=t.replace(T,"")),t=t.replace(z,""),o=p(t);l<=o.length;){switch(i=o[l],c){case ne:if(!i||!j.test(i)){if(r)return S;c=oe;continue}v+=i.toLowerCase(),c=ae;break;case ae:if(i&&(q.test(i)||"+"==i||"-"==i||"."==i))v+=i.toLowerCase();else{if(":"!=i){if(r)return S;v="",c=oe,l=0;continue}if(r&&(G(e)!=h(X,v)||"file"==v&&(V(e)||null!==e.port)||"file"==e.scheme&&!e.host))return;if(e.scheme=v,r)return void(G(e)&&X[e.scheme]==e.port&&(e.port=null));v="","file"==e.scheme?c=me:G(e)&&a&&a.scheme==e.scheme?c=ie:G(e)?c=ce:"/"==o[l+1]?(c=ue,l++):(e.cannotBeABaseURL=!0,e.path.push(""),c=ke)}break;case oe:if(!a||a.cannotBeABaseURL&&"#"!=i)return S;if(a.cannotBeABaseURL&&"#"==i){e.scheme=a.scheme,e.path=a.path.slice(),e.query=a.query,e.fragment="",e.cannotBeABaseURL=!0,c=Ue;break}c="file"==a.scheme?me:se;continue;case ie:if("/"!=i||"/"!=o[l+1]){c=se;continue}c=he,l++;break;case ue:if("/"==i){c=le;break}c=we;continue;case se:if(e.scheme=a.scheme,i==n)e.username=a.username,e.password=a.password,e.host=a.host,e.port=a.port,e.path=a.path.slice(),e.query=a.query;else if("/"==i||"\\"==i&&G(e))c=fe;else if("?"==i)e.username=a.username,e.password=a.password,e.host=a.host,e.port=a.port,e.path=a.path.slice(),e.query="",c=Re;else{if("#"!=i){e.username=a.username,e.password=a.password,e.host=a.host,e.port=a.port,e.path=a.path.slice(),e.path.pop(),c=we;continue}e.username=a.username,e.password=a.password,e.host=a.host,e.port=a.port,e.path=a.path.slice(),e.query=a.query,e.fragment="",c=Ue}break;case fe:if(!G(e)||"/"!=i&&"\\"!=i){if("/"!=i){e.username=a.username,e.password=a.password,e.host=a.host,e.port=a.port,c=we;continue}c=le}else c=he;break;case ce:if(c=he,"/"!=i||"/"!=v.charAt(l+1))continue;l++;break;case he:if("/"!=i&&"\\"!=i){c=le;continue}break;case le:if("@"==i){g&&(v="%40"+v),g=!0,u=p(v);for(var y=0;y<u.length;y++){var b=u[y];if(":"!=b||d){var w=W(b,K);d?e.password+=w:e.username+=w}else d=!0}v=""}else if(i==n||"/"==i||"?"==i||"#"==i||"\\"==i&&G(e)){if(g&&""==v)return"Invalid authority";l-=p(v).length+1,v="",c=pe}else v+=i;break;case pe:case ve:if(r&&"file"==e.scheme){c=ye;continue}if(":"!=i||m){if(i==n||"/"==i||"?"==i||"#"==i||"\\"==i&&G(e)){if(G(e)&&""==v)return x;if(r&&""==v&&(V(e)||null!==e.port))return;if(s=$(e,v))return s;if(v="",c=be,r)return;continue}"["==i?m=!0:"]"==i&&(m=!1),v+=i}else{if(""==v)return x;if(s=$(e,v))return s;if(v="",c=ge,r==ve)return}break;case ge:if(!O.test(i)){if(i==n||"/"==i||"?"==i||"#"==i||"\\"==i&&G(e)||r){if(""!=v){var k=parseInt(v,10);if(k>65535)return E;e.port=G(e)&&k===X[e.scheme]?null:k,v=""}if(r)return;c=be;continue}return E}v+=i;break;case me:if(e.scheme="file","/"==i||"\\"==i)c=de;else{if(!a||"file"!=a.scheme){c=we;continue}if(i==n)e.host=a.host,e.path=a.path.slice(),e.query=a.query;else if("?"==i)e.host=a.host,e.path=a.path.slice(),e.query="",c=Re;else{if("#"!=i){ee(o.slice(l).join(""))||(e.host=a.host,e.path=a.path.slice(),te(e)),c=we;continue}e.host=a.host,e.path=a.path.slice(),e.query=a.query,e.fragment="",c=Ue}}break;case de:if("/"==i||"\\"==i){c=ye;break}a&&"file"==a.scheme&&!ee(o.slice(l).join(""))&&(_(a.path[0],!0)?e.path.push(a.path[0]):e.host=a.host),c=we;continue;case ye:if(i==n||"/"==i||"\\"==i||"?"==i||"#"==i){if(!r&&_(v))c=we;else if(""==v){if(e.host="",r)return;c=be}else{if(s=$(e,v))return s;if("localhost"==e.host&&(e.host=""),r)return;v="",c=be}continue}v+=i;break;case be:if(G(e)){if(c=we,"/"!=i&&"\\"!=i)continue}else if(r||"?"!=i)if(r||"#"!=i){if(i!=n&&(c=we,"/"!=i))continue}else e.fragment="",c=Ue;else e.query="",c=Re;break;case we:if(i==n||"/"==i||"\\"==i&&G(e)||!r&&("?"==i||"#"==i)){if(".."===(f=(f=v).toLowerCase())||"%2e."===f||".%2e"===f||"%2e%2e"===f?(te(e),"/"==i||"\\"==i&&G(e)||e.path.push("")):re(v)?"/"==i||"\\"==i&&G(e)||e.path.push(""):("file"==e.scheme&&!e.path.length&&_(v)&&(e.host&&(e.host=""),v=v.charAt(0)+":"),e.path.push(v)),v="","file"==e.scheme&&(i==n||"?"==i||"#"==i))for(;e.path.length>1&&""===e.path[0];)e.path.shift();"?"==i?(e.query="",c=Re):"#"==i&&(e.fragment="",c=Ue)}else v+=W(i,H);break;case ke:"?"==i?(e.query="",c=Re):"#"==i?(e.fragment="",c=Ue):i!=n&&(e.path[0]+=W(i,Q));break;case Re:r||"#"!=i?i!=n&&("'"==i&&G(e)?e.query+="%27":e.query+="#"==i?"%23":W(i,Q)):(e.fragment="",c=Ue);break;case Ue:i!=n&&(e.fragment+=W(i,Z))}l++}},Ae=function(e){var t,r,n=c(this,Ae,"URL"),a=arguments.length>1?arguments[1]:void 0,i=String(e),u=R(n,{type:"URL"});if(void 0!==a)if(a instanceof Ae)t=U(a);else if(r=Le(t={},String(a)))throw TypeError(r);if(r=Le(u,i,null,t))throw TypeError(r);var s=u.searchParams=new w,f=k(s);f.updateSearchParams(u.query),f.updateURL=function(){u.query=String(s)||null},o||(n.href=xe.call(n),n.origin=Ee.call(n),n.protocol=je.call(n),n.username=qe.call(n),n.password=Oe.call(n),n.host=Be.call(n),n.hostname=Pe.call(n),n.port=Ie.call(n),n.pathname=Ce.call(n),n.search=Fe.call(n),n.searchParams=De.call(n),n.hash=Te.call(n))},Se=Ae.prototype,xe=function(){var e=U(this),t=e.scheme,r=e.username,n=e.password,a=e.host,o=e.port,i=e.path,u=e.query,s=e.fragment,f=t+":";return null!==a?(f+="//",V(e)&&(f+=r+(n?":"+n:"")+"@"),f+=J(a),null!==o&&(f+=":"+o)):"file"==t&&(f+="//"),f+=e.cannotBeABaseURL?i[0]:i.length?"/"+i.join("/"):"",null!==u&&(f+="?"+u),null!==s&&(f+="#"+s),f},Ee=function(){var e=U(this),t=e.scheme,r=e.port;if("blob"==t)try{return new URL(t.path[0]).origin}catch(e){return"null"}return"file"!=t&&G(e)?t+"://"+J(e.host)+(null!==r?":"+r:""):"null"},je=function(){return U(this).scheme+":"},qe=function(){return U(this).username},Oe=function(){return U(this).password},Be=function(){var e=U(this),t=e.host,r=e.port;return null===t?"":null===r?J(t):J(t)+":"+r},Pe=function(){var e=U(this).host;return null===e?"":J(e)},Ie=function(){var e=U(this).port;return null===e?"":String(e)},Ce=function(){var e=U(this),t=e.path;return e.cannotBeABaseURL?t[0]:t.length?"/"+t.join("/"):""},Fe=function(){var e=U(this).query;return e?"?"+e:""},De=function(){return U(this).searchParams},Te=function(){var e=U(this).fragment;return e?"#"+e:""},ze=function(e,t){return{get:e,set:t,configurable:!0,enumerable:!0}};if(o&&s(Se,{href:ze(xe,(function(e){var t=U(this),r=String(e),n=Le(t,r);if(n)throw TypeError(n);k(t.searchParams).updateSearchParams(t.query)})),origin:ze(Ee),protocol:ze(je,(function(e){var t=U(this);Le(t,String(e)+":",ne)})),username:ze(qe,(function(e){var t=U(this),r=p(String(e));if(!Y(t)){t.username="";for(var n=0;n<r.length;n++)t.username+=W(r[n],K)}})),password:ze(Oe,(function(e){var t=U(this),r=p(String(e));if(!Y(t)){t.password="";for(var n=0;n<r.length;n++)t.password+=W(r[n],K)}})),host:ze(Be,(function(e){var t=U(this);t.cannotBeABaseURL||Le(t,String(e),pe)})),hostname:ze(Pe,(function(e){var t=U(this);t.cannotBeABaseURL||Le(t,String(e),ve)})),port:ze(Ie,(function(e){var t=U(this);Y(t)||(""==(e=String(e))?t.port=null:Le(t,e,ge))})),pathname:ze(Ce,(function(e){var t=U(this);t.cannotBeABaseURL||(t.path=[],Le(t,e+"",be))})),search:ze(Fe,(function(e){var t=U(this);""==(e=String(e))?t.query=null:("?"==e.charAt(0)&&(e=e.slice(1)),t.query="",Le(t,e,Re)),k(t.searchParams).updateSearchParams(t.query)})),searchParams:ze(De),hash:ze(Te,(function(e){var t=U(this);""!=(e=String(e))?("#"==e.charAt(0)&&(e=e.slice(1)),t.fragment="",Le(t,e,Ue)):t.fragment=null}))}),f(Se,"toJSON",(function(){return xe.call(this)}),{enumerable:!0}),f(Se,"toString",(function(){return xe.call(this)}),{enumerable:!0}),b){var $e=b.createObjectURL,Me=b.revokeObjectURL;$e&&f(Ae,"createObjectURL",(function(e){return $e.apply(b,arguments)})),Me&&f(Ae,"revokeObjectURL",(function(e){return Me.apply(b,arguments)}))}m(Ae,"URL"),a({global:!0,forced:!i,sham:!o},{URL:Ae})}}]);