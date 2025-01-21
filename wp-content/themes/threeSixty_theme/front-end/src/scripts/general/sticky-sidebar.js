import {ScrollTrigger} from "gsap/ScrollTrigger";

export function stickySidebar(sidebar,wrapper) {
  if (sidebar && wrapper) {
    ScrollTrigger.matchMedia({
      '(min-width:600px)': () => {
        ScrollTrigger.create({
          trigger: wrapper,
          start: "top 100px",
          // pin for the difference in heights between the content and the sidebar
          end: self => "+=" + (wrapper.offsetHeight - self.pin.offsetHeight),
          pin: sidebar,
          pinSpacing: false,
        });
      }
    });
  }
}
