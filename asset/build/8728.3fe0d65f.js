(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[8728],{18728:function(l,n,u){(function(){function l(l,n){var u,t=l.split("."),e=V;t[0]in e||!e.execScript||e.execScript("var "+t[0]);for(;t.length&&(u=t.shift());)t.length||void 0===n?e=e[u]?e[u]:e[u]={}:e[u]=n}function n(l,n){function u(){}u.prototype=n.prototype,l.M=n.prototype,l.prototype=new u,l.prototype.constructor=l,l.N=function(l,u,t){for(var e=Array(arguments.length-2),r=2;r<arguments.length;r++)e[r-2]=arguments[r];return n.prototype[u].apply(l,e)}}function u(l,n){null!=l&&this.a.apply(this,arguments)}function t(l){l.b=""}function e(l,n){return l>n?1:l<n?-1:0}function r(l,n){this.b=l,this.a={};for(var u=0;u<n.length;u++){var t=n[u];this.a[t.b]=t}}function i(l){return function(l,n){l.sort(n||e)}(l=function(l){var n,u=[],t=0;for(n in l)u[t++]=l[n];return u}(l.a),(function(l,n){return l.b-n.b})),l}function a(l,n){switch(this.b=l,this.g=!!n.v,this.a=n.c,this.i=n.type,this.h=!1,this.a){case q:case T:case U:case Y:case K:case P:case H:this.h=!0}this.f=n.defaultValue}function o(){this.a={},this.f=this.j().a,this.b=this.g=null}function s(l,n){for(var u=i(l.j()),t=0;t<u.length;t++){var e=(a=u[t]).b;if(null!=n.a[e]){l.b&&delete l.b[a.b];var r=11==a.a||10==a.a;if(a.g)for(var a=f(n,e)||[],o=0;o<a.length;o++){var p=l,c=e,h=r?a[o].clone():a[o];p.a[c]||(p.a[c]=[]),p.a[c].push(h),p.b&&delete p.b[c]}else a=f(n,e),r?(r=f(l,e))?s(r,a):d(l,e,a.clone()):d(l,e,a)}}}function f(l,n){var u=l.a[n];if(null==u)return null;if(l.g){if(!(n in l.b)){var t=l.g,e=l.f[n];if(null!=u)if(e.g){for(var r=[],i=0;i<u.length;i++)r[i]=t.b(e,u[i]);u=r}else u=t.b(e,u);return l.b[n]=u}return l.b[n]}return u}function p(l,n,u){var t=f(l,n);return l.f[n].g?t[u||0]:t}function c(l,n){var u;if(null!=l.a[n])u=p(l,n,void 0);else l:{if(void 0===(u=l.f[n]).f){var t=u.i;if(t===Boolean)u.f=!1;else if(t===Number)u.f=0;else{if(t!==String){u=new t;break l}u.f=u.h?"0":""}}u=u.f}return u}function h(l,n){return l.f[n].g?null!=l.a[n]?l.a[n].length:0:null!=l.a[n]?1:0}function d(l,n,u){l.a[n]=u,l.b&&(l.b[n]=u)}function g(l,n){var u,t=[];for(u in n)0!=u&&t.push(new a(u,n[u]));return new r(l,t)}function m(){o.call(this)}function b(){o.call(this)}function y(){o.call(this)}function v(){}function _(){}function S(){}function $(){this.a={}}function w(l){return 0==l.length||ul.test(l)}function x(l,n){if(null==n)return null;n=n.toUpperCase();var u=l.a[n];if(null==u){if(null==(u=W[n]))return null;u=(new S).a(y.j(),u),l.a[n]=u}return u}function A(l){return null==(l=Q[l])?"ZZ":l[0]}function N(l){this.H=RegExp(" "),this.C="",this.m=new u,this.w="",this.i=new u,this.u=new u,this.l=!0,this.A=this.o=this.F=!1,this.G=$.b(),this.s=0,this.b=new u,this.B=!1,this.h="",this.a=new u,this.f=[],this.D=l,this.J=this.g=E(this,this.D)}function E(l,n){var u;if(null!=n&&isNaN(n)&&n.toUpperCase()in W){if(null==(u=x(l.G,n)))throw Error("Invalid region code: "+n);u=c(u,10)}else u=0;return null!=(u=x(l.G,A(u)))?u:tl}function j(l){for(var n=l.f.length,u=0;u<n;++u){var e,r=l.f[u],i=c(r,1);if(l.w==i)return!1;e=l;var a=c(s=r,1);if(-1!=a.indexOf("|"))e=!1;else{var o;a=(a=a.replace(el,"\\d")).replace(rl,"\\d"),t(e.m),o=e;var s=c(s,2),f="999999999999999".match(a)[0];f.length<o.a.b.length?o="":o=(o=f.replace(new RegExp(a,"g"),s)).replace(RegExp("9","g")," "),0<o.length?(e.m.a(o),e=!0):e=!1}if(e)return l.w=i,l.B=al.test(p(r,4)),l.s=0,!0}return l.l=!1}function B(l,n){for(var u=[],t=n.length-3,e=l.f.length,r=0;r<e;++r){var i=l.f[r];0==h(i,3)?u.push(l.f[r]):(i=p(i,3,Math.min(t,h(i,3)-1)),0==n.search(i)&&u.push(l.f[r]))}l.f=u}function G(l){return l.l=!0,l.A=!1,l.f=[],l.s=0,t(l.m),l.w="",R(l)}function I(l){for(var n=l.a.toString(),u=l.f.length,t=0;t<u;++t){var e=l.f[t],r=c(e,1);if(new RegExp("^(?:"+r+")$").test(n))return l.B=al.test(p(e,4)),C(l,n=n.replace(new RegExp(r,"g"),p(e,2)))}return""}function C(l,n){var u=l.b.b.length;return l.B&&0<u&&" "!=l.b.toString().charAt(u-1)?l.b+" "+n:l.b+n}function R(l){var n=l.a.toString();if(3<=n.length){for(var u=l.o&&0==l.h.length&&0<h(l.g,20)?f(l.g,20)||[]:f(l.g,19)||[],t=u.length,e=0;e<t;++e){var r=u[e];0<l.h.length&&w(c(r,4))&&!p(r,6)&&null==r.a[5]||(0!=l.h.length||l.o||w(c(r,4))||p(r,6))&&il.test(c(r,2))&&l.f.push(r)}return B(l,n),0<(n=I(l)).length?n:j(l)?F(l):l.i.toString()}return C(l,n)}function F(l){var n=l.a.toString(),u=n.length;if(0<u){for(var t="",e=0;e<u;e++)t=J(l,n.charAt(e));return l.l?C(l,t):l.i.toString()}return l.b.toString()}function M(l){var n,u=l.a.toString(),e=0;return 1!=p(l.g,10)?n=!1:n="1"==(n=l.a.toString()).charAt(0)&&"0"!=n.charAt(1)&&"1"!=n.charAt(1),n?(e=1,l.b.a("1").a(" "),l.o=!0):null!=l.g.a[15]&&(n=new RegExp("^(?:"+p(l.g,15)+")"),null!=(n=u.match(n))&&null!=n[0]&&0<n[0].length&&(l.o=!0,e=n[0].length,l.b.a(u.substring(0,e)))),t(l.a),l.a.a(u.substring(e)),u.substring(0,e)}function D(l){var n=l.u.toString(),u=new RegExp("^(?:\\+|"+p(l.g,11)+")");return null!=(u=n.match(u))&&null!=u[0]&&0<u[0].length&&(l.o=!0,u=u[0].length,t(l.a),l.a.a(n.substring(u)),t(l.b),l.b.a(n.substring(0,u)),"+"!=n.charAt(0)&&l.b.a(" "),!0)}function k(l){if(0==l.a.b.length)return!1;var n,e=new u;l:{if(0!=(n=l.a.toString()).length&&"0"!=n.charAt(0))for(var r,i=n.length,a=1;3>=a&&a<=i;++a)if((r=parseInt(n.substring(0,a),10))in Q){e.a(n.substring(a)),n=r;break l}n=0}return 0!=n&&(t(l.a),l.a.a(e.toString()),"001"==(e=A(n))?l.g=x(l.G,""+n):e!=l.D&&(l.g=E(l,e)),l.b.a(""+n).a(" "),l.h="",!0)}function J(l,n){if(0<=(e=l.m.toString()).substring(l.s).search(l.H)){var u=e.search(l.H),e=e.replace(l.H,n);return t(l.m),l.m.a(e),l.s=u,e.substring(0,l.s+1)}return 1==l.f.length&&(l.l=!1),l.w="",l.i.toString()}var V=this;u.prototype.b="",u.prototype.set=function(l){this.b=""+l},u.prototype.a=function(l,n,u){if(this.b+=String(l),null!=n)for(var t=1;t<arguments.length;t++)this.b+=arguments[t];return this},u.prototype.toString=function(){return this.b};var H=1,P=2,q=3,T=4,U=6,Y=16,K=18;o.prototype.set=function(l,n){d(this,l.b,n)},o.prototype.clone=function(){var l=new this.constructor;return l!=this&&(l.a={},l.b&&(l.b={}),s(l,this)),l},n(m,o);var L=null;n(b,o);var O=null;n(y,o);var Z=null;m.prototype.j=function(){var l=L;return l||(L=l=g(m,{0:{name:"NumberFormat",I:"i18n.phonenumbers.NumberFormat"},1:{name:"pattern",required:!0,c:9,type:String},2:{name:"format",required:!0,c:9,type:String},3:{name:"leading_digits_pattern",v:!0,c:9,type:String},4:{name:"national_prefix_formatting_rule",c:9,type:String},6:{name:"national_prefix_optional_when_formatting",c:8,defaultValue:!1,type:Boolean},5:{name:"domestic_carrier_code_formatting_rule",c:9,type:String}})),l},m.j=m.prototype.j,b.prototype.j=function(){var l=O;return l||(O=l=g(b,{0:{name:"PhoneNumberDesc",I:"i18n.phonenumbers.PhoneNumberDesc"},2:{name:"national_number_pattern",c:9,type:String},9:{name:"possible_length",v:!0,c:5,type:Number},10:{name:"possible_length_local_only",v:!0,c:5,type:Number},6:{name:"example_number",c:9,type:String}})),l},b.j=b.prototype.j,y.prototype.j=function(){var l=Z;return l||(Z=l=g(y,{0:{name:"PhoneMetadata",I:"i18n.phonenumbers.PhoneMetadata"},1:{name:"general_desc",c:11,type:b},2:{name:"fixed_line",c:11,type:b},3:{name:"mobile",c:11,type:b},4:{name:"toll_free",c:11,type:b},5:{name:"premium_rate",c:11,type:b},6:{name:"shared_cost",c:11,type:b},7:{name:"personal_number",c:11,type:b},8:{name:"voip",c:11,type:b},21:{name:"pager",c:11,type:b},25:{name:"uan",c:11,type:b},27:{name:"emergency",c:11,type:b},28:{name:"voicemail",c:11,type:b},29:{name:"short_code",c:11,type:b},30:{name:"standard_rate",c:11,type:b},31:{name:"carrier_specific",c:11,type:b},33:{name:"sms_services",c:11,type:b},24:{name:"no_international_dialling",c:11,type:b},9:{name:"id",required:!0,c:9,type:String},10:{name:"country_code",c:5,type:Number},11:{name:"international_prefix",c:9,type:String},17:{name:"preferred_international_prefix",c:9,type:String},12:{name:"national_prefix",c:9,type:String},13:{name:"preferred_extn_prefix",c:9,type:String},15:{name:"national_prefix_for_parsing",c:9,type:String},16:{name:"national_prefix_transform_rule",c:9,type:String},18:{name:"same_mobile_and_fixed_line_pattern",c:8,defaultValue:!1,type:Boolean},19:{name:"number_format",v:!0,c:11,type:m},20:{name:"intl_number_format",v:!0,c:11,type:m},22:{name:"main_country_for_code",c:8,defaultValue:!1,type:Boolean},23:{name:"leading_digits",c:9,type:String},26:{name:"leading_zero_possible",c:8,defaultValue:!1,type:Boolean}})),l},y.j=y.prototype.j,v.prototype.a=function(l){throw new l.b,Error("Unimplemented")},v.prototype.b=function(l,n){if(11==l.a||10==l.a)return n instanceof o?n:this.a(l.i.prototype.j(),n);if(14==l.a){if("string"==typeof n&&z.test(n)){var u=Number(n);if(0<u)return u}return n}if(!l.h)return n;if((u=l.i)===String){if("number"==typeof n)return String(n)}else if(u===Number&&"string"==typeof n&&("Infinity"===n||"-Infinity"===n||"NaN"===n||z.test(n)))return Number(n);return n};var z=/^-?[0-9]+$/;n(_,v),_.prototype.a=function(l,n){var u=new l.b;return u.g=this,u.a=n,u.b={},u},n(S,_),S.prototype.b=function(l,n){return 8==l.a?!!n:v.prototype.b.apply(this,arguments)},S.prototype.a=function(l,n){return S.M.a.call(this,l,n)};var Q={44:["GB","GG","IM","JE"]},W={GB:[null,[null,null,"[1-357-9]\\d{9}|[18]\\d{8}|8\\d{6}",null,null,null,null,null,null,[7,9,10],[4,5,6,8]],[null,null,"(?:1(?:1(?:3[0-58]|4[0-5]|5[0-26-9]|6[0-4]|[78][0-49])|2(?:0[024-9]|1[0-7]|2[3-9]|3[3-79]|4[1-689]|[58][02-9]|6[0-47-9]|7[013-9]|9\\d)|3(?:0\\d|1[0-8]|[25][02-9]|3[02-579]|[468][0-46-9]|7[1-35-79]|9[2-578])|4(?:0[03-9]|[137]\\d|[28][02-57-9]|4[02-69]|5[0-8]|[69][0-79])|5(?:0[1-35-9]|[16]\\d|2[024-9]|3[015689]|4[02-9]|5[03-9]|7[0-35-9]|8[0-468]|9[0-57-9])|6(?:0[034689]|1\\d|2[0-35689]|[38][013-9]|4[1-467]|5[0-69]|6[13-9]|7[0-8]|9[0-24578])|7(?:0[0246-9]|2\\d|3[0236-8]|4[03-9]|5[0-46-9]|6[013-9]|7[0-35-9]|8[024-9]|9[02-9])|8(?:0[35-9]|2[1-57-9]|3[02-578]|4[0-578]|5[124-9]|6[2-69]|7\\d|8[02-9]|9[02569])|9(?:0[02-589]|[18]\\d|2[02-689]|3[1-57-9]|4[2-9]|5[0-579]|6[2-47-9]|7[0-24578]|9[2-57]))|2(?:0[01378]|3[0189]|4[017]|8[0-46-9]|9[0-2])\\d)\\d{6}|1(?:(?:2(?:0(?:46[1-4]|87[2-9])|545[1-79]|76(?:2\\d|3[1-8]|6[1-6])|9(?:7(?:2[0-4]|3[2-5])|8(?:2[2-8]|7[0-47-9]|8[3-5])))|3(?:6(?:38[2-5]|47[23])|8(?:47[04-9]|64[0157-9]))|4(?:044[1-7]|20(?:2[23]|8\\d)|6(?:0(?:30|5[2-57]|6[1-8]|7[2-8])|140)|8(?:052|87[1-3]))|5(?:2(?:4(?:3[2-79]|6\\d)|76\\d)|6(?:26[06-9]|686))|6(?:06(?:4\\d|7[4-79])|295[5-7]|35[34]\\d|47(?:24|61)|59(?:5[08]|6[67]|74)|9(?:55[0-4]|77[23]))|8(?:27[56]\\d|37(?:5[2-5]|8[239])|843[2-58])|9(?:0(?:0(?:6[1-8]|85)|52\\d)|3583|4(?:66[1-8]|9(?:2[01]|81))|63(?:23|3[1-4])|9561))\\d|7(?:(?:26(?:6[13-9]|7[0-7])|442\\d|50(?:2[0-3]|[3-68]2|76))\\d|6888[2-46-8]))\\d\\d",null,null,null,"1212345678",null,null,[9,10],[4,5,6,7,8]],[null,null,"7(?:(?:[1-3]\\d\\d|5(?:0[0-8]|[13-9]\\d|2[0-35-9])|8(?:[014-9]\\d|[23][0-8]))\\d|4(?:[0-46-9]\\d\\d|5(?:[0-689]\\d|7[0-57-9]))|7(?:0(?:0[01]|[1-9]\\d)|(?:[1-7]\\d|8[02-9]|9[0-689])\\d)|9(?:(?:[024-9]\\d|3[0-689])\\d|1(?:[02-9]\\d|1[028])))\\d{5}",null,null,null,"7400123456",null,null,[10]],[null,null,"80[08]\\d{7}|800\\d{6}|8001111",null,null,null,"8001234567"],[null,null,"(?:8(?:4[2-5]|7[0-3])|9(?:[01]\\d|8[2-49]))\\d{7}|845464\\d",null,null,null,"9012345678",null,null,[7,10]],[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"70\\d{8}",null,null,null,"7012345678",null,null,[10]],[null,null,"56\\d{8}",null,null,null,"5612345678",null,null,[10]],"GB",44,"00","0"," x",null,"0",null,null,null,[[null,"(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3",["845","8454","84546","845464"],"0$1"],[null,"(\\d{3})(\\d{4})","$1 $2",["800","8001","80011","800111","8001111"],"0$1"],[null,"(\\d{3})(\\d{6})","$1 $2",["800"],"0$1"],[null,"(\\d{4})(\\d{5,6})","$1 $2",["1(?:[2-79][02-9]|8)","1(?:[24][02-9]|3(?:[02-79]|8[0-46-9])|5(?:[04-9]|2[024-9]|3[014-689])|6(?:[02-8]|9[0-24578])|7(?:[02-57-9]|6[013-9])|8|9(?:[0235-9]|4[2-9]))","1(?:[24][02-9]|3(?:[02-79]|8(?:[0-4689]|7[0-24-9]))|5(?:[04-9]|2(?:[025-9]|4[013-9])|3(?:[014-68]|9[0-37-9]))|6(?:[02-8]|9(?:[0-2458]|7[0-25689]))|7(?:[02-57-9]|6(?:[013-79]|8[0-25689]))|8|9(?:[0235-9]|4(?:[2-57-9]|6[0-689])))"],"0$1"],[null,"(\\d{5})(\\d{4,5})","$1 $2",["1(?:38|5[23]|69|7|94)"],"0$1"],[null,"(\\d{2})(\\d{4})(\\d{4})","$1 $2 $3",["[25]|7(?:0|6[024-9])","[25]|7(?:0|6(?:[04-9]|2[356]))"],"0$1"],[null,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["[1389]"],"0$1"],[null,"(\\d{4})(\\d{6})","$1 $2",["7"],"0$1"]],null,[null,null,"76(?:0[0-2]|2[356]|4[0134]|5[49]|6[0-369]|77|81|9[39])\\d{6}",null,null,null,"7640123456",null,null,[10]],1,null,[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"(?:3[0347]|55)\\d{8}",null,null,null,"5512345678",null,null,[10]],null,null,[null,null,null,null,null,null,null,null,null,[-1]]],GG:[null,[null,null,"(?:1481|[357-9]\\d{3})\\d{6}|8\\d{6}(?:\\d{2})?",null,null,null,null,null,null,[7,9,10],[6]],[null,null,"1481[25-9]\\d{5}",null,null,null,"1481256789",null,null,[10],[6]],[null,null,"7(?:(?:781|839)\\d|911[17])\\d{5}",null,null,null,"7781123456",null,null,[10]],[null,null,"80[08]\\d{7}|800\\d{6}|8001111",null,null,null,"8001234567"],[null,null,"(?:8(?:4[2-5]|7[0-3])|9(?:[01]\\d|8[0-3]))\\d{7}|845464\\d",null,null,null,"9012345678",null,null,[7,10]],[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"70\\d{8}",null,null,null,"7012345678",null,null,[10]],[null,null,"56\\d{8}",null,null,null,"5612345678",null,null,[10]],"GG",44,"00","0",null,null,"0|([25-9]\\d{5})$","1481$1",null,null,null,null,[null,null,"76(?:0[0-2]|2[356]|4[0134]|5[49]|6[0-369]|77|81|9[39])\\d{6}",null,null,null,"7640123456",null,null,[10]],null,null,[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"(?:3[0347]|55)\\d{8}",null,null,null,"5512345678",null,null,[10]],null,null,[null,null,null,null,null,null,null,null,null,[-1]]],IM:[null,[null,null,"(?:1624|(?:[3578]\\d|90)\\d\\d)\\d{6}",null,null,null,null,null,null,[10],[6]],[null,null,"1624[5-8]\\d{5}",null,null,null,"1624756789",null,null,null,[6]],[null,null,"7(?:4576|[59]24\\d|624[0-4689])\\d{5}",null,null,null,"7924123456"],[null,null,"808162\\d{4}",null,null,null,"8081624567"],[null,null,"(?:8(?:4(?:40[49]06|5624\\d)|7(?:0624|2299)\\d)|90[0167]624\\d)\\d{3}",null,null,null,"9016247890"],[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"70\\d{8}",null,null,null,"7012345678"],[null,null,"56\\d{8}",null,null,null,"5612345678"],"IM",44,"00","0",null,null,"0|([5-8]\\d{5})$","1624$1",null,null,null,null,[null,null,null,null,null,null,null,null,null,[-1]],null,null,[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"(?:3(?:(?:08162|3\\d{4}|7(?:0624|2299))\\d|4(?:40[49]06|5624\\d))|55\\d{5})\\d{3}",null,null,null,"5512345678"],null,null,[null,null,null,null,null,null,null,null,null,[-1]]],JE:[null,[null,null,"(?:1534|(?:[3578]\\d|90)\\d\\d)\\d{6}",null,null,null,null,null,null,[10],[6]],[null,null,"1534[0-24-8]\\d{5}",null,null,null,"1534456789",null,null,null,[6]],[null,null,"7(?:(?:(?:50|82)9|937)\\d|7(?:00[378]|97[7-9]))\\d{5}",null,null,null,"7797712345"],[null,null,"80(?:07(?:35|81)|8901)\\d{4}",null,null,null,"8007354567"],[null,null,"(?:8(?:4(?:4(?:4(?:05|42|69)|703)|5(?:041|800))|7(?:0002|1206))|90(?:066[59]|1810|71(?:07|55)))\\d{4}",null,null,null,"9018105678"],[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"701511\\d{4}",null,null,null,"7015115678"],[null,null,"56\\d{8}",null,null,null,"5612345678"],"JE",44,"00","0",null,null,"0|([0-24-8]\\d{5})$","1534$1",null,null,null,null,[null,null,"76(?:0[0-2]|2[356]|4[0134]|5[49]|6[0-369]|77|81|9[39])\\d{6}",null,null,null,"7640123456"],null,null,[null,null,null,null,null,null,null,null,null,[-1]],[null,null,"(?:3(?:0(?:07(?:35|81)|8901)|3\\d{4}|4(?:4(?:4(?:05|42|69)|703)|5(?:041|800))|7(?:0002|1206))|55\\d{4})\\d{4}",null,null,null,"5512345678"],null,null,[null,null,null,null,null,null,null,null,null,[-1]]]};$.b=function(){return $.a?$.a:$.a=new $};var X={0:"0",1:"1",2:"2",3:"3",4:"4",5:"5",6:"6",7:"7",8:"8",9:"9","０":"0","１":"1","２":"2","３":"3","４":"4","５":"5","６":"6","７":"7","８":"8","９":"9","٠":"0","١":"1","٢":"2","٣":"3","٤":"4","٥":"5","٦":"6","٧":"7","٨":"8","٩":"9","۰":"0","۱":"1","۲":"2","۳":"3","۴":"4","۵":"5","۶":"6","۷":"7","۸":"8","۹":"9"},ll=RegExp("[+＋]+"),nl=RegExp("([0-9０-９٠-٩۰-۹])"),ul=/^\(?\$1\)?$/,tl=new y;d(tl,11,"NA");var el=/\[([^\[\]])*\]/g,rl=/\d(?=[^,}][^,}])/g,il=RegExp("^[-x‐-―−ー－-／  ­​⁠　()（）［］.\\[\\]/~⁓∼～]*(\\$\\d[-x‐-―−ー－-／  ­​⁠　()（）［］.\\[\\]/~⁓∼～]*)+$"),al=/[- ]/;N.prototype.K=function(){this.C="",t(this.i),t(this.u),t(this.m),this.s=0,this.w="",t(this.b),this.h="",t(this.a),this.l=!0,this.A=this.o=this.F=!1,this.f=[],this.B=!1,this.g!=this.J&&(this.g=E(this,this.D))},N.prototype.L=function(l){return this.C=function(l,n){l.i.a(n);var u,e=n;if(nl.test(e)||1==l.i.b.length&&ll.test(e)?("+"==(e=n)?(u=e,l.u.a(e)):(u=X[e],l.u.a(u),l.a.a(u)),n=u):(l.l=!1,l.F=!0),!l.l){if(!l.F)if(D(l)){if(k(l))return G(l)}else if(0<l.h.length&&(e=l.a.toString(),t(l.a),l.a.a(l.h),l.a.a(e),u=(e=l.b.toString()).lastIndexOf(l.h),t(l.b),l.b.a(e.substring(0,u))),l.h!=M(l))return l.b.a(" "),G(l);return l.i.toString()}switch(l.u.b.length){case 0:case 1:case 2:return l.i.toString();case 3:if(!D(l))return l.h=M(l),R(l);l.A=!0;default:return l.A?(k(l)&&(l.A=!1),l.b.toString()+l.a.toString()):0<l.f.length?(e=J(l,n),0<(u=I(l)).length?u:(B(l,l.a.toString()),j(l)?F(l):l.l?C(l,e):l.i.toString())):R(l)}}(this,l)},l("Cleave.AsYouTypeFormatter",N),l("Cleave.AsYouTypeFormatter.prototype.inputDigit",N.prototype.L),l("Cleave.AsYouTypeFormatter.prototype.clear",N.prototype.K)}).call("object"==typeof u.g&&u.g?u.g:window)}}]);