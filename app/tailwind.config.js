/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./Views/**/*.php",
    "./public/**/*.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        montserrat: ['Montserrat', 'sans-serif'],
      },
      content: {
        'arrow_right': 'url("/Assets/Nav/ArrowRightNav.svg")',
        'arrow_down': 'url("/Assets/Nav/ArrowDownNav.svg")',
        'accessibility_icon': 'url("/Assets/Nav/AccessibilityMenIcon.svg")',
        'footer_phone': 'url("/Assets/Nav/FooterPhone.svg")',
        'footer_mail': 'url("/Assets/Nav/FooterMail.svg")',
        'footer_location': 'url("/Assets/Nav/FooterLocation.svg")',
        'footer_facebook': 'url("/Assets/Nav/FooterFacebook.svg")',
        'footer_instagram': 'url("/Assets/Nav/FooterInstagram.svg")',
        'footer_twitter': 'url("/Assets/Nav/FooterTwitter.svg")',
      },
    },
  },
  plugins: [],
}
