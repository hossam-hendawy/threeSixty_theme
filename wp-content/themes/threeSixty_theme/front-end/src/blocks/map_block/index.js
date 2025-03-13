import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
/**
 * @author HOSSAM
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const mapBlock = async (block) => {

  block.querySelectorAll('.hotspot-circle').forEach(circle => {
    circle.addEventListener('click', function () {
      const hiddenContent = this.querySelector('.hidden-content');

      // إذا كان العنصر المفتوح هو نفسه الذي تم النقر عليه، فقم بإغلاقه فقط
      if (hiddenContent.classList.contains('show'))  {
        hiddenContent.classList.remove('show');
      } else {
        // إغلاق جميع العناصر المفتوحة الأخرى
        block.querySelectorAll('.hidden-content.show').forEach(content => {
          content.classList.remove('show');
        });

        // فتح العنصر الجديد
        hiddenContent.classList.add('show');
      }
    });
  });

    animations(block);
    imageLazyLoading(block);
};

export default mapBlock;

