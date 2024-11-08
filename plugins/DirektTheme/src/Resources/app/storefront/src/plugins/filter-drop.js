document.addEventListener('click', (event) => {
    // Show the filter panel when the filter button is clicked
    if (event.target.closest('.cms-block-product-listing .filter-panel-wrapper-toggle')) {
        event.stopPropagation();
        const filterPanel = document.querySelector('.filter-panel-wrapper');
        filterPanel.classList.toggle('active');
    }

    // Close the filter panel when the close button is clicked
    if (event.target.closest('.filter-panel-offcanvas-close')) {
        const filterPanel = document.querySelector('.filter-panel-wrapper');
        filterPanel.classList.remove('active');
    }
});
