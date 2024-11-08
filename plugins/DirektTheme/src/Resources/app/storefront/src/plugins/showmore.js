const showMore = document.querySelectorAll('.dk-showmore');
showMore.forEach((e) => {
    e.addEventListener('click', (event) => {
        event.preventDefault();
        const titleElement = event.target.closest('.cms-element-cmsbundle-title');
        if (titleElement) {
            var showMoreText = e.getAttribute('data-show-more');  // Access the individual element
            var showLessText = e.getAttribute('data-show-less');

            titleElement.classList.toggle('show-more');
            // Toggle the text between "Show More" and "Show Less"
            if (titleElement.classList.contains('show-more')) {
                event.target.textContent = showLessText;
            } else {
                event.target.textContent = showMoreText;
            }
        }
    });
});
