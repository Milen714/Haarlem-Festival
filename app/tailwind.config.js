/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./Views/**/*.php",
    "./public/**/*.php",
    "./src/**/*.php",
  ],
  safelist: [
    "flex-col",
    "flex-col-reverse",
    "md:flex-row",
    "md:flex-row-reverse",
  ],
  theme: {
    extend: {
      fontFamily: {
        montserrat: ['Montserrat', 'sans-serif'],
        robotomono: ['Roboto Mono', 'monospace'],
        courierprime: ['Courier Prime', 'monospace'],
        blackopsone: ['Black Ops One', 'system-ui'],
        sans: ['ui-sans-serif','system-ui','Segoe UI','Roboto','Helvetica','Arial','sans-serif'],
        serif: ['Georgia','Cambria','Times New Roman','Times','serif']
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
        'magic_nav_active': 'url("/Assets/Magic/MagicNavActiveIcon.svg")',
      },
    },
  },
  plugins: [],
}
