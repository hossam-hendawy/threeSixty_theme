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
  let totalPages = 1; // سيتم تحديثها بناءً على الـ API

  const prevPageBtn = block.querySelector("#prev-page");
  const nextPageBtn = block.querySelector("#next-page");
  const postContainer = block.querySelector("#post-container");
  const numbersContainer = block.querySelector(".numbers");
  const loadingSpinner = document.createElement("div");

  loadingSpinner.classList.add("loading-spinner");
  loadingSpinner.style.display = "none";
  postContainer.appendChild(loadingSpinner);

  function updateButtonStates() {
    prevPageBtn.classList.toggle("disabled", currentPage === 1);
    nextPageBtn.classList.toggle("disabled", currentPage >= totalPages);
  }

  let local = true
  let url;

  function loadPosts(page) {
    if (local) {
      url = `${window.location.origin}/threeSixty_theme/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}`;
    } else {
      url = `${window.location.origin}/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}`;
    }


    postContainer.innerHTML = "";
    postContainer.appendChild(loadingSpinner);
    loadingSpinner.style.display = "block";

    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        loadingSpinner.style.display = "none";
        postContainer.innerHTML = data.posts;

        totalPages = data.totalPages;
        currentPage = page;
        generatePagination();
        updateButtonStates();
      })
      .catch(error => {
        loadingSpinner.style.display = "none";
        console.error("Error loading posts:", error);
      });
  }

  function generatePagination() {
    numbersContainer.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {
      let numberElement = document.createElement("div");
      numberElement.classList.add("number", "text-sm", "medium", "gray-600");
      numberElement.innerText = i;

      if (i === currentPage) {
        numberElement.classList.add("active");
      }

      numberElement.addEventListener("click", () => {
        if (i !== currentPage) {
          loadPosts(i);

          block.querySelector(".bottom-content-wrapper").scrollIntoView({
            behavior: "smooth",
            block: "start"
          });
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
