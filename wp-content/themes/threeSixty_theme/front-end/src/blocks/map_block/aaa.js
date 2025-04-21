// /* Add custom Js code below */
// <!-- Google tag (gtag.js) -->
// // <script async src="https://www.googletagmanager.com/gtag/js?id=AW-11460978783">
// //   </script>
// // <script>
// //   window.dataLayer = window.dataLayer || [];
// //   function gtag(){dataLayer.push(arguments);}
// //   gtag('js', new Date());
// //
// //   gtag('config', 'AW-11460978783');
// //
// //
// //
// // </script>
//
//
// // تحميل gtag.js بشكل ديناميكي
// (function() {
//   const gtagScript = document.createElement('script');
//   gtagScript.async = true;
//   gtagScript.src = 'https://www.googletagmanager.com/gtag/js?id=AW-11460978783';
//   document.head.appendChild(gtagScript);
//
//   // إعداد dataLayer وتعريف الدالة gtag
//   window.dataLayer = window.dataLayer || [];
//   function gtag() {
//     dataLayer.push(arguments);
//   }
//   window.gtag = gtag;
//
//   // تهيئة التتبع
//   gtag('js', new Date());
//   gtag('config', 'AW-11460978783');
// })();
//
//
//
// const observer = new MutationObserver(() => {
//   const spanIcons = document.querySelectorAll(".s-slider-button-icon");
//   console.log(spanIcons)
//   spanIcons.forEach(spanIcon => {
//     if (!spanIcon.dataset.svgInjected) {
//       spanIcon.innerHTML = `
//         <svg aria-hidden="true" width="15" height="15" viewBox="0 0 15 15" fill="none">
//           <path d="M12.2955 7.63456H0V6.61662H12.2955L6.39929 0.720448L7.12559 0L14.2512 7.12559L7.12559 14.2512L6.39929 13.5307L12.2955 7.63456Z" fill="#FBFAF6"></path>
//         </svg>
//       `;
//       spanIcon.dataset.svgInjected = "true";
//     }
//   });
// });
//
// // راقب الجسم بالكامل لأي تغييرات في العناصر
// observer.observe(document.body, {
//   childList: true,
//   subtree: true
// });
//
//
// function addTajawalFontLinks() {
//   const links = [
//     { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
//     { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossOrigin: 'anonymous' },
//     { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap' },
//   ];
//
//   links.forEach(data => {
//     const link = document.createElement('link');
//     link.rel = data.rel;
//     link.href = data.href;
//     if (data.crossOrigin) link.crossOrigin = data.crossOrigin;
//     document.head.appendChild(link);
//   });
// }
//
// addTajawalFontLinks();
