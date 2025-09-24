let slideIndex = 1;
let slideTimer; // for auto-slide

showSlides(slideIndex);

// Neste/forrige
function plusSlides(n) {
  showSlides(slideIndex += n);
  resetTimer();
}

// Klikk på dot
function currentSlide(n) {
  showSlides(slideIndex = n);
  resetTimer();
}

// Viser slides og dots
function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");

  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}

  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";

  // Automatisk loop
  clearTimeout(slideTimer);
  slideTimer = setTimeout(() => plusSlides(1), 3000); // 3 sekunder per slide
}

// Reset timer hvis brukeren klikker
function resetTimer() {
  clearTimeout(slideTimer);
  slideTimer = setTimeout(() => plusSlides(1), 3000);
}
