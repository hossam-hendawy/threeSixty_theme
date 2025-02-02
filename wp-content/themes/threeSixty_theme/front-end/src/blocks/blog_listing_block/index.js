import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const blogListingBlock = async (block) => {

  let currentPage = 1;

  function loadPosts(page) {
    let url = `${window.location.origin}/threeSixty_theme/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}`;

    console.log("Fetching posts from:", url);

    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.text();
      })
      .then(data => {
        document.getElementById("post-container").innerHTML = data;
        document.getElementById("current-page").innerText = page;
      })
      .catch(error => console.error("Error loading posts:", error));
  }

  block.querySelector("#next-page").addEventListener("click", function () {
    currentPage++;
    loadPosts(currentPage);
  });

  block.querySelector("#prev-page").addEventListener("click", function () {
    if (currentPage > 1) {
      currentPage--;
      loadPosts(currentPage);
    }
  });

  loadPosts(currentPage);




  animations(block);
    imageLazyLoading(block);
};

export default blogListingBlock;

