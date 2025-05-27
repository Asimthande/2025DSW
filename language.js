function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
includedLanguages: 'af,am,ar,az,be,bg,bn,bs,ca,ceb,co,cs,cy,da,de,el,en,eo,es,et,eu,fa,fi,fr,fy,ga,gd,gl,gu,ha,haw,he,hi,hmn,hr,ht,hu,hy,id,ig,is,it,ja,jw,ka,kk,km,kn,ko,ku,ky,la,lb,lo,lt,lv,mg,mi,mk,ml,mn,mr,ms,mt,my,ne,nl,no,ny,pa,pl,ps,pt,ro,ru,rw,sd,si,sk,sl,sm,sn,so,sq,sr,st,su,sv,sw,ta,te,tg,th,tk,tl,tr,tt,ug,uk,ur,uz,vi,wo,xh,yi,yo,zh-CN,zh-TW,zu,nr,ns,st,tn,ss,ve,ts',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}
document.addEventListener("DOMContentLoaded", function() {
  var translateContainer = document.createElement('div');
  translateContainer.id = 'google_translate_element';
  document.body.appendChild(translateContainer);

  const style = document.createElement('style');
  style.innerHTML = `
    #google_translate_element {
      position: fixed;
      bottom: 10px;
      right: 10px;
      background-color: beige;
      border-radius: 10px;
      padding: 10px;
      z-index: 9999;
      display: block;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .goog-te-combo {
      width: 200px !important;
      padding: 8px;
      font-size: 14px;
      color: #333;
      background-color: orange !important;
      border: none;
      border-radius: 5px;
    }
    .goog-te-combo option {
      background-color: beige !important;
    }
    .goog-te-menu-value {
      color: white !important;
    }
    .goog-te-menu-value span {
      color: white !important;
    }
    .goog-te-banner-frame.skiptranslate {
      display: none !important;
    }
    body {
      padding-bottom: 50px;
    }
  `;
  document.head.appendChild(style);

  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
  document.body.appendChild(script);
  var translateElement = document.getElementById('google_translate_element');
  var dropdown = document.querySelector('.goog-te-combo');

  translateElement.addEventListener('click', function() {
    if (translateElement.style.display === 'none') {
      translateElement.style.display = 'block';
    } else {
      translateElement.style.display = 'none';
    }
  });
  document.addEventListener('click', function(event) {
    if (event.target && event.target.classList.contains('goog-te-combo')) {
      translateElement.style.display = 'none';
    }
  });
  document.addEventListener('DOMContentLoaded', function() {
    const banner = document.querySelector('.goog-te-banner-frame');
    if (banner) {
      banner.style.display = 'none';
    }
  });
});
