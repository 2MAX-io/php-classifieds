(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[9117],{82313:function(e){"use strict";e.exports=JSON.parse('{"base_url":"","routes":{"app_admin_category_save_order":{"tokens":[["text","/admin/red5/category/save-order"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_admin_category_custom_fields_save_order":{"tokens":[["text","/admin/red5/category/custom-fields/save-order"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_admin_custom_field_save_order":{"tokens":[["text","/admin/red5/custom-field/save-order"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_admin_custom_field_options_save_order":{"tokens":[["text","/admin/red5/custom-field-option/save-options-order"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_listing_show":{"tokens":[["variable","/","[^/]++","slug"],["variable","/","[^/]++","id"],["text","/l"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"app_listing_contact_data":{"tokens":[["text","/private/listing/show-contact-data"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_file_upload":{"tokens":[["text","/private/file-upload"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_map_image_cache":{"tokens":[["text",".png"],["variable","_","\\\\d+","y"],["variable","_","\\\\d+","x"],["variable","/","\\\\d+","z"],["text","/static/cache/map"]],"defaults":[],"requirements":{"z":"\\\\d+","x":"\\\\d+","y":"\\\\d+"},"hosttokens":[],"methods":[],"schemes":[]},"app_listing_get_custom_fields":{"tokens":[["text","/listing/get-custom-fields"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_user_listing_file_remove":{"tokens":[["text","/user/listing/file/remove"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":["POST"],"schemes":[]},"app_user_observed_listing_set":{"tokens":[["text","/user/listing/observed"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"app_payment_status_refresh":{"tokens":[["text","/user/payment/await-payment-confirmation/refresh"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"app_payment_await_confirmation_redirect":{"tokens":[["variable","/","[^/]++","paymentAppToken"],["text","/user/payment/await-payment-confirmation/redirect-destination"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"bazinga_jstranslation_js":{"tokens":[["variable",".","js|json","_format"],["variable","/","[\\\\w]+","domain"],["text","/zzzz/translations"]],"defaults":{"domain":"messages","_format":"js"},"requirements":{"_format":"js|json","domain":"[\\\\w]+"},"hosttokens":[],"methods":["GET"],"schemes":[]},"nelmio_js_logger_log":{"tokens":[["text","/zzzz/nelmio-js-logger/log"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]},"app_public_dir":{"tokens":[["text","/"]],"defaults":[],"requirements":[],"hosttokens":[],"methods":[],"schemes":[]}},"prefix":"","host":"classifieds.localhost","port":"","scheme":"http","locale":[]}')},49117:function(e,t,o){"use strict";o(15306),o(74916);var s=o(76187),n=o.n(s),r=o(70471),i=o(79075);function a(e,t){for(var o=0;o<t.length;o++){var s=t[o];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(e,s.key,s)}}n().setRoutingData(o(82313));var u=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e)}var t,o,s;return t=e,s=[{key:"generate",value:function(e,t){return r.ZP[i.Z.BASE_URL]+n().generate(e,t).replace(/^\/+/,"")}}],(o=null)&&a(t.prototype,o),s&&a(t,s),e}();t.Z=u},76187:function(e,t,o){var s,n,r,i;function a(e){return(a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}o(19601),o(82526),o(41817),o(41539),o(32165),o(78783),o(66992),o(33948),o(43371),o(24603),o(74916),o(39714),o(89554),o(54747),o(82772),o(47941),o(69600),o(15306),i=function(){"use strict";var e=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var o=arguments[t];for(var s in o)Object.prototype.hasOwnProperty.call(o,s)&&(e[s]=o[s])}return e},t="function"==typeof Symbol&&"symbol"===a(Symbol.iterator)?function(e){return a(e)}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":a(e)},o=function(){function e(e,t){for(var o=0;o<t.length;o++){var s=t[o];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(e,s.key,s)}}return function(t,o,s){return o&&e(t.prototype,o),s&&e(t,s),t}}();function s(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var n=function(){function n(e,t){s(this,n),this.context_=e||{base_url:"",prefix:"",host:"",port:"",scheme:"",locale:""},this.setRoutes(t||{})}return o(n,[{key:"setRoutingData",value:function(e){this.setBaseUrl(e.base_url),this.setRoutes(e.routes),"prefix"in e&&this.setPrefix(e.prefix),"port"in e&&this.setPort(e.port),"locale"in e&&this.setLocale(e.locale),this.setHost(e.host),this.setScheme(e.scheme)}},{key:"setRoutes",value:function(e){this.routes_=Object.freeze(e)}},{key:"getRoutes",value:function(){return this.routes_}},{key:"setBaseUrl",value:function(e){this.context_.base_url=e}},{key:"getBaseUrl",value:function(){return this.context_.base_url}},{key:"setPrefix",value:function(e){this.context_.prefix=e}},{key:"setScheme",value:function(e){this.context_.scheme=e}},{key:"getScheme",value:function(){return this.context_.scheme}},{key:"setHost",value:function(e){this.context_.host=e}},{key:"getHost",value:function(){return this.context_.host}},{key:"setPort",value:function(e){this.context_.port=e}},{key:"getPort",value:function(){return this.context_.port}},{key:"setLocale",value:function(e){this.context_.locale=e}},{key:"getLocale",value:function(){return this.context_.locale}},{key:"buildQueryParams",value:function(e,o,s){var n=this,r=void 0,i=new RegExp(/\[\]$/);if(o instanceof Array)o.forEach((function(o,r){i.test(e)?s(e,o):n.buildQueryParams(e+"["+("object"===(void 0===o?"undefined":t(o))?r:"")+"]",o,s)}));else if("object"===(void 0===o?"undefined":t(o)))for(r in o)this.buildQueryParams(e+"["+r+"]",o[r],s);else s(e,o)}},{key:"getRoute",value:function(e){var t=[this.context_.prefix+e,e+"."+this.context_.locale,this.context_.prefix+e+"."+this.context_.locale,e];for(var o in t)if(t[o]in this.routes_)return this.routes_[t[o]];throw new Error('The route "'+e+'" does not exist.')}},{key:"generate",value:function(t,o){var s=arguments.length>2&&void 0!==arguments[2]&&arguments[2],r=this.getRoute(t),i=o||{},a=e({},i),u="",c=!0,l="",f=void 0===this.getPort()||null===this.getPort()?"":this.getPort();if(r.tokens.forEach((function(e){if("text"===e[0])return u=n.encodePathComponent(e[1])+u,void(c=!1);if("variable"!==e[0])throw new Error('The token type "'+e[0]+'" is not supported.');var o=r.defaults&&e[3]in r.defaults;if(!1===c||!o||e[3]in i&&i[e[3]]!=r.defaults[e[3]]){var s=void 0;if(e[3]in i)s=i[e[3]],delete a[e[3]];else{if(!o){if(c)return;throw new Error('The route "'+t+'" requires the parameter "'+e[3]+'".')}s=r.defaults[e[3]]}if(!0!==s&&!1!==s&&""!==s||!c){var l=n.encodePathComponent(s);"null"===l&&null===s&&(l=""),u=e[1]+l+u}c=!1}else o&&e[3]in a&&delete a[e[3]]})),""===u&&(u="/"),r.hosttokens.forEach((function(e){var t=void 0;"text"!==e[0]?"variable"===e[0]&&(e[3]in i?(t=i[e[3]],delete a[e[3]]):r.defaults&&e[3]in r.defaults&&(t=r.defaults[e[3]]),l=e[1]+t+l):l=e[1]+l})),u=this.context_.base_url+u,r.requirements&&"_scheme"in r.requirements&&this.getScheme()!=r.requirements._scheme){var h=l||this.getHost();u=r.requirements._scheme+"://"+h+(h.indexOf(":"+f)>-1||""===f?"":":"+f)+u}else if(void 0!==r.schemes&&void 0!==r.schemes[0]&&this.getScheme()!==r.schemes[0]){var m=l||this.getHost();u=r.schemes[0]+"://"+m+(m.indexOf(":"+f)>-1||""===f?"":":"+f)+u}else l&&this.getHost()!==l+(l.indexOf(":"+f)>-1||""===f?"":":"+f)?u=this.getScheme()+"://"+l+(l.indexOf(":"+f)>-1||""===f?"":":"+f)+u:!0===s&&(u=this.getScheme()+"://"+this.getHost()+(this.getHost().indexOf(":"+f)>-1||""===f?"":":"+f)+u);if(Object.keys(a).length>0){var d=void 0,p=[],_=function(e,t){t=null===(t="function"==typeof t?t():t)?"":t,p.push(n.encodeQueryComponent(e)+"="+n.encodeQueryComponent(t))};for(d in a)this.buildQueryParams(d,a[d],_);u=u+"?"+p.join("&")}return u}}],[{key:"getInstance",value:function(){return r}},{key:"setData",value:function(e){n.getInstance().setRoutingData(e)}},{key:"customEncodeURIComponent",value:function(e){return encodeURIComponent(e).replace(/%2F/g,"/").replace(/%40/g,"@").replace(/%3A/g,":").replace(/%21/g,"!").replace(/%3B/g,";").replace(/%2C/g,",").replace(/%2A/g,"*").replace(/\(/g,"%28").replace(/\)/g,"%29").replace(/'/g,"%27")}},{key:"encodePathComponent",value:function(e){return n.customEncodeURIComponent(e).replace(/%3D/g,"=").replace(/%2B/g,"+").replace(/%21/g,"!").replace(/%7C/g,"|")}},{key:"encodeQueryComponent",value:function(e){return n.customEncodeURIComponent(e).replace(/%3F/g,"?")}}]),n}();n.Route,n.Context;var r=new n;return{Router:n,Routing:r}}(),n=[],s=i.Routing,void 0===(r="function"==typeof s?s.apply(t,n):s)||(e.exports=r)}}]);
//# sourceMappingURL=9117.b819bcd6.js.map