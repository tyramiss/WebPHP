(function(a){if(typeof define==='function'&&define.amd){define(['jquery'],a)}else if(typeof exports==='object'){a(require('jquery'))}else{a(jQuery)}}(function($){var k=/\+/g;function encode(s){return m.raw?s:encodeURIComponent(s)}function decode(s){return m.raw?s:decodeURIComponent(s)}function stringifyCookieValue(a){return encode(m.json?JSON.stringify(a):String(a))}function parseCookieValue(s){if(s.indexOf('"')===0){s=s.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,'\\')}try{s=decodeURIComponent(s.replace(k,' '));return m.json?JSON.parse(s):s}catch(e){}}function read(s,a){var b=m.raw?s:parseCookieValue(s);return $.isFunction(a)?a(b):b}var m=$.cookie=function(a,b,c){if(b!==undefined&&!$.isFunction(b)){c=$.extend({},m.defaults,c);if(typeof c.expires==='number'){var d=c.expires,t=c.expires=new Date();t.setTime(+t+d*864e+5)}return(document.cookie=[encode(a),'=',stringifyCookieValue(b),c.expires?'; expires='+c.expires.toUTCString():'',c.path?'; path='+c.path:'',c.domain?'; domain='+c.domain:'',c.secure?'; secure':''].join(''))}var e=a?undefined:{};var f=document.cookie?document.cookie.split('; '):[];for(var i=0,l=f.length;i<l;i++){var g=f[i].split('=');var h=decode(g.shift());var j=g.join('=');if(a&&a===h){e=read(j,b);break}if(!a&&(j=read(j))!==undefined){e[h]=j}}return e};m.defaults={};$.removeCookie=function(a,b){if($.cookie(a)===undefined){return false}$.cookie(a,'',$.extend({},b,{expires:-1}));return!$.cookie(a)}}));