import {debounce} from "./debounce";

export function getHeightOfViewPort() {
// Then we set the value in the --vh custom property to the root of the document
    const setHeight = () => document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);

    setHeight();

    window.addEventListener('resize', debounce(function () {
        setHeight();
    }, 500));
}