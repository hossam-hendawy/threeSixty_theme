import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {accordion} from "../../scripts/general/accordion";


export default async (footer = document) => {
  const media = window.matchMedia('(max-width: 599px)');

  if (media.matches) {
    accordion(footer.querySelectorAll(".accordion"))
  }

  animations(footer);
  imageLazyLoading(footer);
};

