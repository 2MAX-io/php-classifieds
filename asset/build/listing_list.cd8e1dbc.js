"use strict";(self.webpackChunksymfony=self.webpackChunksymfony||[]).push([[6179],{79075:function(e,n,t){t.d(n,{Z:function(){return o}});var o={DATA_FOR_JS:"dataForJs",CSRF_TOKEN:"csrfToken",CSRF_HEADER:"x-csrf-token",LANGUAGE_ISO:"languageIso",COUNTRY_ISO:"countryIso",BASE_URL:"baseUrl",SUCCESS:"success",ERROR:"error",USERNAME:"username",LISTING_ID:"listingId",CATEGORY_ID:"categoryId",LISTING_LIST:"listingList",LISTING_FILES:"listingFiles",CUSTOM_FIELD:"customField",PACKAGE_LIST:"packageList",SHOW_CONTACT_HTML:"showContactHtml",SHOW_LISTING_PREVIEW_FOR_OWNER:"showListingPreviewForOwner",PAYMENT_APP_TOKEN:"paymentAppToken",POLICE_LOG_TEXT:"policeLogText",MAP_LOCATION_COORDINATES:"mapLocationCoordinates",MAP_DEFAULT_LATITUDE:"mapDefaultLatitude",MAP_DEFAULT_LONGITUDE:"mapDefaultLongitude",MAP_DEFAULT_ZOOM:"mapDefaultZoom",LONGITUDE:"longitude",LATITUDE:"latitude",ZOOM:"zoom",THOUSAND_SEPARATOR:"thousandSeparator",NUMERAL_DECIMAL_MARK:"numeralDecimalMark",OBSERVED:"observed"}},70471:function(e,n,t){t.d(n,{ZP:function(){return a}});var o,a=null;null===a&&(o=document.getElementById("js__dataForJs"),a=null===o?{}:JSON.parse(atob(o.value))),a=a},1138:function(e,n,t){var o=t(19755),a=t.n(o);a()(".js__removeOnClick").on("click",(function(){a()(this).remove()}))},69954:function(e,n,t){t(89554),t(41539),t(54747),document.querySelectorAll(".js__selectChangeUrl").forEach((function(e){e.addEventListener("change",(function(){window.location=this.options[e.selectedIndex].value}))}))},30363:function(e,n,t){t(41539),t(88674),t(69826);var o=t(19755),a=t.n(o),r=t(79075),s=t(70471),i=t(49117);a()(".js__observe").on("click",(function(){var e=a()(this),n=e.attr("data-listing-id"),t=!(0|e.attr("data-observed")),o=new FormData;o.append(r.Z.LISTING_ID,n),o.append(r.Z.OBSERVED,t?"1":"0"),fetch(i.Z.generate("app_user_observed_listing_set"),{method:"post",body:o,headers:{"X-CSRF-Token":s.ZP[r.Z.CSRF_TOKEN]}}).then((function(e){return e.json()})).then((function(n){if(!0===n[r.Z.SUCCESS]){e.attr("data-observed",0|n[r.Z.OBSERVED]);var t=e;t.is(".svg")||(t=t.find(a()(".svg"))),n[r.Z.OBSERVED]?t.removeClass("svg-heart-outline").addClass("svg-heart"):t.removeClass("svg-heart").addClass("svg-heart-outline")}}))}))},37434:function(e,n,t){t(65601),t(1138),t(69954),t(30363)},69826:function(e,n,t){var o=t(82109),a=t(42092).find,r=t(51223),s="find",i=!0;s in[]&&Array(1).find((function(){i=!1})),o({target:"Array",proto:!0,forced:i},{find:function(e){return a(this,e,arguments.length>1?arguments[1]:void 0)}}),r(s)}},function(e){e.O(0,[2719,9755,9825,5027,9125,1014,9117],(function(){return n=37434,e(e.s=n);var n}));e.O()}]);
//# sourceMappingURL=listing_list.cd8e1dbc.js.map