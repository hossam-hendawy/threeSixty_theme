import './style.scss';
import { imageLazyLoading } from "../../scripts/functions/imageLazyLoading";
import { animations } from "../../scripts/general/animations";

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const blogListingBlock = async (block) => {
  let currentPage = 1;
  let totalPages = 1; // Default value, will be updated from the API

  const prevPageBtn = block.querySelector("#prev-page");
  const nextPageBtn = block.querySelector("#next-page");
  const postContainer = document.getElementById("post-container");

  function updateButtonStates() {
    prevPageBtn.classList.toggle("disabled", currentPage === 1);
    nextPageBtn.classList.toggle("disabled", currentPage >= totalPages);
  }

  function loadPosts(page) {
    let url = `${window.location.origin}/threeSixty_theme/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}`;

    console.log("Fetching posts from:", url);

    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json(); // Expecting JSON now
      })
      .then(data => {
        postContainer.innerHTML = data.posts; // Inject posts into the container
        document.getElementById("current-page").innerText = page;

        totalPages = data.totalPages; // Update total pages from response
        updateButtonStates(); // Update button states
      })
      .catch(error => console.error("Error loading posts:", error));
  }

  nextPageBtn.addEventListener("click", function () {
    if (currentPage < totalPages) {
      currentPage++;
      loadPosts(currentPage);
    }
  });

  prevPageBtn.addEventListener("click", function () {
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
