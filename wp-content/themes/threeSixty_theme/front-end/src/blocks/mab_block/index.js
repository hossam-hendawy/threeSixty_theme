import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const mabBlock = async (block) => {

  // add block code here
  block.querySelectorAll('.hotspot-circle').forEach(circle => {
    circle.addEventListener('click', function () {
      const hiddenContent = this.querySelector('.hidden-content');

      // إذا كان العنصر المفتوح هو نفسه الذي تم النقر عليه، فقم بإغلاقه فقط
      if (hiddenContent.classList.contains('show'))  {
        hiddenContent.classList.remove('show');
      } else {
        // إغلاق جميع العناصر المفتوحة الأخرى
        document.querySelectorAll('.hidden-content.show').forEach(content => {
          content.classList.remove('show');
        });

        // فتح العنصر الجديد
        hiddenContent.classList.add('show');
      }
    });
  });




// testing the new hidden value

    animations(block);
    imageLazyLoading(block);
};

export default mabBlock;

