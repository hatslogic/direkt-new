// const showMore = document.querySelectorAll('.dk-showmore');
// showMore.forEach((e) => {
//     e.addEventListener('click', (event) => {
//         event.preventDefault();
//         const titleElement = event.target.closest('.cms-element-cmsbundle-title');
//         if (titleElement) {
//             var showMoreText = e.getAttribute('data-show-more');  // Access the individual element
//             var showLessText = e.getAttribute('data-show-less');

//             titleElement.classList.toggle('show-more');
//             // Toggle the text between "Show More" and "Show Less"
//             if (titleElement.classList.contains('show-more')) {
//                 event.target.textContent = showLessText;
//             } else {
//                 event.target.textContent = showMoreText;
//             }
//         }
//     });
// });

const toggleReadMore = (buttonClass, toggleDivClass) => {
    const buttons = document.querySelectorAll(`.${buttonClass}`);
    buttons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const toggleElement = event.target.closest(`.${toggleDivClass}`);
            if (toggleElement) {
                const showMoreText = button.dataset.showMore; // Use dataset for cleaner attribute access
                const showLessText = button.dataset.showLess;

                toggleElement.classList.toggle('show-more');

                // Toggle the text between "Show More" and "Show Less"
                button.textContent = toggleElement.classList.contains('show-more') 
                    ? showLessText 
                    : showMoreText;
            }
        });
    });
};

// Call the function for each case
toggleReadMore('dk-showmore', 'cms-element-cmsbundle-title');
toggleReadMore('dk-description-readmore', 'cms-element-text');

