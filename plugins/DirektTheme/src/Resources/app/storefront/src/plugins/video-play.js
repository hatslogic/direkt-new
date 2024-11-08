document.addEventListener('DOMContentLoaded', function () {
    const thumbnails = document.querySelectorAll('.dk-five-block-video .dk-video-thumbnail');
    const video = document.querySelectorAll('.dk-five-block-video .cta-slider__video source');
    const videoDisplay = document.querySelectorAll('.dk-five-block-video .cta-slider__video');
    
    thumbnails.forEach((e,index) => {
        e.addEventListener('click', function () {
            e.classList.add('d-none');
            const videoUrl = video[index].getAttribute('data-src');
            console.log(videoUrl);
            videoDisplay[index].setAttribute('src', videoUrl);
            videoDisplay[index].play();
            videoDisplay[index].setAttribute('controls', true);
        });
    })
});