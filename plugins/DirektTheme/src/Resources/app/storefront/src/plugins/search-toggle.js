const searchWrapper = document.querySelector('.header-search-module');
const searchBtn = document.querySelector('.header-search-btn');
const searchOuter = document.querySelector('.header-search');
const searchClose = document.querySelector('.header-search-close'); // closes

if(searchBtn){
    searchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        searchWrapper.classList.toggle('search-active');
        searchOuter.classList.toggle('active');
        searchOuter.querySelector('.header-search-input').focus();
    })
    searchClose.addEventListener('click', () => {
        searchOuter.classList.remove('active');
        searchWrapper.classList.remove('search-active');
    })
}