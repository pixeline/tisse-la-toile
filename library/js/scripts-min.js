function updateViewportDimensions(){var t=window,e=document,i=e.documentElement,n=e.getElementsByTagName("body")[0],a=t.innerWidth||i.clientWidth||n.clientWidth,r=t.innerHeight||i.clientHeight||n.clientHeight;return{width:a,height:r}}function loadGravatars(){viewport=updateViewportDimensions(),viewport.width>=768&&jQuery(".comment img[data-gravatar]").each(function(){jQuery(this).attr("src",jQuery(this).attr("data-gravatar"))})}var viewport=updateViewportDimensions(),waitForFinalEvent=function(){var t={};return function(e,i,n){n||(n="Don't call this twice without a uniqueId"),t[n]&&clearTimeout(t[n]),t[n]=setTimeout(e,i)}}(),timeToWaitForLast=100;!function($){if("undefined"==typeof t)var t=jQuery("body").hasClass("home");t&&tisse_la_toile()}(jQuery),jQuery(document).ready(function($){loadGravatars()});