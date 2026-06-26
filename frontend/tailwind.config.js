/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        biblio: {
          brown: '#5C4033',
          walnut: '#7A5C43',
          gold: '#C5A46D',
        },
        paper: {
          ivory: '#F8F5F0',
          parchment: '#EFE7DC',
          border: '#E2D7C6'
        },
        text: {
          primary: '#2D2A26',
          secondary: '#6B655E'
        }
      },
      fontFamily: {
        'playfair': ['Playfair Display', 'serif'],
        'cormorant': ['Cormorant Garamond', 'serif'],
        'merriweather': ['Merriweather', 'serif'],
        'source-serif': ['Source Serif Pro', 'serif'],
        'inter': ['Inter', 'sans-serif']
      },
      boxShadow: {
        'paper': '0 2px 8px rgba(0,0,0,.04), 0 8px 20px rgba(0,0,0,.06)',
      },
      borderRadius: {
        'card': '18px'
      }
    }
  },
  plugins: [],
}
