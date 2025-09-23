let $ = jQuery.noConflict(); // Evita conflitti con altre librerie JS

console.log('Carousel block script loaded');

$('.carousel').slick({
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1
});