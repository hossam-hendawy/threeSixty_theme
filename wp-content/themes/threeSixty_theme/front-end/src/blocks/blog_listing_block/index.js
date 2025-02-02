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
  const numbersContainer = block.querySelector(".numbers"); // Pagination numbers container

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
        return response.json();
      })
      .then(data => {
        postContainer.innerHTML = data.posts; // Inject posts into the container
        totalPages = data.totalPages; // Update total pages from response

        currentPage = page;
        generatePagination(); // Regenerate pagination buttons
        updateButtonStates(); // Update button states
      })
      .catch(error => console.error("Error loading posts:", error));
  }

  function generatePagination() {
    numbersContainer.innerHTML = ""; // Clear existing numbers

    for (let i = 1; i <= totalPages; i++) {
      let numberElement = document.createElement("div");
      numberElement.classList.add("number", "text-sm", "medium", "gray-600");
      numberElement.innerText = i;

      if (i === currentPage) {
        numberElement.classList.add("active"); // Highlight active page
      }

      numberElement.addEventListener("click", () => {
        if (i !== currentPage) {
          loadPosts(i);
        }
      });

      numbersContainer.appendChild(numberElement);
    }
  }

  nextPageBtn.addEventListener("click", function () {
    if (currentPage < totalPages) {
      loadPosts(currentPage + 1);
    }
  });

  prevPageBtn.addEventListener("click", function () {
    if (currentPage > 1) {
      loadPosts(currentPage - 1);
    }
  });

  loadPosts(currentPage);

  animations(block);
  imageLazyLoading(block);
};

export default blogListingBlock;
