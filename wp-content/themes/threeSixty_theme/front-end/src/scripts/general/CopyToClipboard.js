export function copyToClipboard(container = document) {
  const singlePage = container.querySelector('body.single-post');

  if (singlePage) {
    function Clipboard(text) {
      navigator.clipboard.writeText(text).then(function () {
      }, function (err) {
      });
    }

    const copyButton = container.querySelector('.copy-link');
    const copiedText = container.querySelector('.copied-text');

    if (copyButton) {
      copyButton.addEventListener('click', (event) => {
        event.preventDefault();
        Clipboard(window.location.href);
        copiedText.classList.add('active');
        setTimeout(() => {
          copiedText.classList.remove('active');
        }, 3000);
      });
    }
  }
}

