import './styles/admin.scss';
import {adminAcf} from "./scripts/admin-acf";

let loaded = false;

async function onLoad() {

  if (document.readyState === 'interactive' && !loaded) {
    loaded = true;
    adminAcf();
  }
}

onLoad();

document.onreadystatechange = function () {
  onLoad();
};
