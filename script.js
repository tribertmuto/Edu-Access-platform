let index = 0;
const images = [
  "https://via.placeholder.com/800x200?text=Learn+New+Skills",
  "https://via.placeholder.com/800x200?text=Anytime+Anywhere",
  "https://via.placeholder.com/800x200?text=Interactive+Courses"
];

function slideShow() {
  document.getElementById("slide").src = images[index];
  index = (index + 1) % images.length;
  setTimeout(slideShow, 3000);
}
window.onload = slideShow;

function validateForm(event) {
  const email = document.getElementById("email").value;
  const error = document.getElementById("error");
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!regex.test(email)) {
    error.textContent = "Invalid email format";
    event.preventDefault();
  }
}
