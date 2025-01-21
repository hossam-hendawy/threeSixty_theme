import { debounce } from "lodash/function";

const defaultOptions = {
  slidesPerView: 2,
  speed: 20000,
  spaceBetween: 20,
  breakpoints: {
    992: {
      slidesPerView: 5,
      spaceBetween: 60
    },
    600: {
      slidesPerView: 3,
      spaceBetween: 40
    }
  }
};

export function autoplaySwiper(block, options = {}) {
  const { slidesPerView, spaceBetween, breakpoints, speed } = {
    ...defaultOptions,
    ...options
  };
  const root = block.querySelector(".autoplay-swiper-cont");
  const container = block.querySelector(".autoplay-swiper");
  const wrapper = block.querySelector(".autoplay-swiper-wrapper");
  const modal = document.querySelector(".custom-modal");
  const customModalContent = document.querySelector(".custom-modal-content");
  if (!container || !root || !wrapper) return;

  container.addEventListener("click", function(event) {
    // Check if the clicked element or its parent has the class 'play-btn'
    let btn = event.target.closest(".play-btn");
    if (!btn) return;

    document.documentElement.classList.add("modal-opened");
    modal.classList.add("modal-active");

    // Clear existing content in the modal
    customModalContent.innerHTML = "";

    const modalTemplate = btn
      .closest(".autoplay-swiper-slide")
      .querySelector(".video-template");
    let clone = modalTemplate.content.cloneNode(true);
    customModalContent.appendChild(clone);
  });

  container.style.animationDuration = `${speed / 1000}s`;
  container.appendChild(wrapper.cloneNode(true));

  const points = Object.keys(breakpoints).sort((a, b) => {
    return b - a;
  });

  let activeSpaceBetween = spaceBetween;
  let activeSlidesPerView = slidesPerView;
  let activeContainerWidth = root.clientWidth;
  const partClones = [];

  const updatePartCount = () => {
    const wrapperWidth = wrapper.clientWidth;
    container.style.width = `${wrapperWidth}px`;
    const rootWidth = root.clientWidth;
    const cloneCount = Math.floor(rootWidth / wrapperWidth);

    if (cloneCount > partClones.length) {
      const addCount = cloneCount - partClones.length;
      [...Array(addCount)].forEach(() => {
        const newNode = wrapper.cloneNode(true);
        partClones.push(newNode);
        container.appendChild(newNode);
        // Reattach event listeners to the cloned node
        newNode.querySelectorAll(".play-btn").forEach(btn => {
          btn.addEventListener("click", function() {
            document.documentElement.classList.add("modal-opened");
            modal.classList.add("modal-active");
            const modalTemplate = btn
              .closest(".autoplay-swiper-slide")
              .querySelector(".video-template");
            let clone = modalTemplate.content.cloneNode(true);
            customModalContent.appendChild(clone);
          });
        });
      });
    }

    // ... rest of your code for removing clones if needed
  };

  const handleSlidesWidth = debounce(() => {
    if (points.length) {
      const currentBreakpoint = points.find(
        point => window.innerWidth >= point
      );
      if (currentBreakpoint) {
        activeSlidesPerView =
          breakpoints[currentBreakpoint].slidesPerView ||
          points.reverse().find(point => window.innerWidth >= point)
            ?.slidesPerView ||
          slidesPerView;
        activeSpaceBetween =
          breakpoints[currentBreakpoint].spaceBetween ||
          points.reverse().find(point => window.innerWidth >= point)
            ?.spaceBetween ||
          spaceBetween;
      } else {
        activeSlidesPerView = slidesPerView;
        activeSpaceBetween = spaceBetween;
      }
    }
    activeContainerWidth = root.clientWidth;
    const slideWidth =
      (activeContainerWidth - (activeSlidesPerView - 1) * activeSpaceBetween) /
      activeSlidesPerView;
    const slides = block.querySelectorAll(".autoplay-swiper-slide");
    slides.forEach((slide, index) => {
      slide.style.width = `${slideWidth}px`;
      slide.style.marginRight = `${activeSpaceBetween}px`;
    });
    updatePartCount();
  }, 200);

  handleSlidesWidth();

  const resizeHandler = () =>
    activeContainerWidth !== container.clientWidth && handleSlidesWidth();
  window.addEventListener("resize", resizeHandler);

}
