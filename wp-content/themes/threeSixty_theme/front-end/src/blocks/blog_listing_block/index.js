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
  let totalPages = 1; // سيتم تحديثها بناءً على الـ API

  const prevPageBtn = block.querySelector("#prev-page");
  const nextPageBtn = block.querySelector("#next-page");
  const postContainer = document.getElementById("post-container");
  const numbersContainer = block.querySelector(".numbers"); // أرقام الصفحات
  const loadingSpinner = document.createElement("div"); // إنشاء عنصر السبينر

  // إضافة السبينر للـ DOM داخل postContainer
  loadingSpinner.classList.add("loading-spinner");
  loadingSpinner.style.display = "none";
  postContainer.appendChild(loadingSpinner);

  function updateButtonStates() {
    prevPageBtn.classList.toggle("disabled", currentPage === 1);
    nextPageBtn.classList.toggle("disabled", currentPage >= totalPages);
  }

  function loadPosts(page) {
    let url = `${window.location.origin}/threeSixty_theme/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}`;

    postContainer.innerHTML = ""; // تفريغ المحتوى الحالي
    postContainer.appendChild(loadingSpinner); // إضافة السبينر
    loadingSpinner.style.display = "block"; // إظهار السبينر

    console.log("Fetching posts from:", url);

    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        loadingSpinner.style.display = "none"; // إخفاء السبينر بعد التحميل
        postContainer.innerHTML = data.posts; // عرض البوستات

        totalPages = data.totalPages; // تحديث إجمالي الصفحات
        currentPage = page;
        generatePagination(); // تحديث أزرار الأرقام
        updateButtonStates(); // تحديث حالة الأزرار
      })
      .catch(error => {
        loadingSpinner.style.display = "none"; // إخفاء السبينر في حالة الخطأ
        console.error("Error loading posts:", error);
      });
  }

  function generatePagination() {
    numbersContainer.innerHTML = ""; // تفريغ أزرار الأرقام

    for (let i = 1; i <= totalPages; i++) {
      let numberElement = document.createElement("div");
      numberElement.classList.add("number", "text-sm", "medium", "gray-600");
      numberElement.innerText = i;

      if (i === currentPage) {
        numberElement.classList.add("active"); // تمييز الصفحة الحالية
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
