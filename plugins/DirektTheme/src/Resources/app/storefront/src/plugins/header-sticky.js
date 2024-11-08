const  headerDiv = document.querySelector('.header');
const langDiv = document.querySelector('.top-bar-list');
// const headerMainHeight = headerDiv.clientHeight;
let lastScrollTop = 0;

document.addEventListener('DOMContentLoaded', () => {
    if (headerDiv) {
    //   document.querySelector('body').style.paddingTop = `${headerMainHeight}px`;
      window.addEventListener('scroll', () => {
          const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          if (scrollTop > 60) {
          headerDiv.classList.add('fixed');
      
          if (scrollTop < lastScrollTop)   
          {
              headerDiv.classList.add('sticky');
      
          } else {
              headerDiv.classList.remove('sticky');
              langDiv.classList.remove('show');
          }
          lastScrollTop = scrollTop;
      
          } else {
            headerDiv.classList.remove('fixed');
            headerDiv.classList.remove('sticky');
          }
      });
    }
});