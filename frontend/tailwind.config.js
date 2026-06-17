/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ['class'],
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  safelist: [
    // Brand colour utilities – guaranteed even when dynamically composed
    { pattern: /^(bg|text|border|ring|fill)-(vert|foret|jaune|bleu|rouge|menthe)$/ },
    // Typography scale tokens
    { pattern: /^text-(titre1|titre2|titre3|titre4|titre5|body|btn)(-(md))?$/ },
    // Font families
    'font-luckiest', 'font-bryndan', 'font-patrick', 'font-nunito',
  ],
  theme: {
    container: {
      center: true,
      padding: '2rem',
      screens: { '2xl': '1400px' },
    },
    extend: {
      fontFamily: {
        luckiest: ['Luckiest Guy', 'cursive'],
        bryndan: ['Bryndan Write', 'cursive'],
        patrick: ['Patrick Hand', 'sans-serif'],
        nunito: ['Nunito', 'sans-serif'],
      },
      // Design-system typography scale (from Figma)
      // Mobile: titre1=24 titre2=20 titre3/4=16 body=12/20 btn=14 titre5=10
      // Desktop: titre1-md=46/121.4% titre2-md=32 titre3-md=20 titre4-md=24 body-md=20/20
      fontSize: {
        'titre1':    ['1.5rem',   { lineHeight: 'normal' }],
        'titre2':    ['1.25rem',  { lineHeight: 'normal' }],
        'titre3':    ['1rem',     { lineHeight: 'normal' }],
        'titre4':    ['1rem',     { lineHeight: 'normal' }],
        'body':      ['0.75rem',  { lineHeight: '1.25rem' }],
        'btn':       ['0.875rem', { lineHeight: 'normal' }],
        'titre5':    ['0.625rem', { lineHeight: 'normal' }],
        // Desktop variants (use with md: prefix)
        'titre1-md': ['2.875rem', { lineHeight: '1.214' }],
        'titre2-md': ['2rem',     { lineHeight: 'normal' }],
        'titre3-md': ['1.25rem',  { lineHeight: 'normal' }],
        'titre4-md': ['1.5rem',   { lineHeight: 'normal' }],
        'body-md':   ['1.25rem',  { lineHeight: '1.25rem' }],
      },
      colors: {
        vert: '#01BF63',
        foret: '#013D22',
        jaune: '#FFB000',
        bleu: '#0094FF',
        rouge: '#FE3B2F',
        menthe: '#F2FBEF',
        border: 'hsl(var(--border))',
        input: 'hsl(var(--input))',
        ring: 'hsl(var(--ring))',
        background: 'hsl(var(--background))',
        foreground: 'hsl(var(--foreground))',
        primary: {
          DEFAULT: 'hsl(var(--primary))',
          foreground: 'hsl(var(--primary-foreground))',
        },
        secondary: {
          DEFAULT: 'hsl(var(--secondary))',
          foreground: 'hsl(var(--secondary-foreground))',
        },
        destructive: {
          DEFAULT: 'hsl(var(--destructive))',
          foreground: 'hsl(var(--destructive-foreground))',
        },
        muted: {
          DEFAULT: 'hsl(var(--muted))',
          foreground: 'hsl(var(--muted-foreground))',
        },
        accent: {
          DEFAULT: 'hsl(var(--accent))',
          foreground: 'hsl(var(--accent-foreground))',
        },
        popover: {
          DEFAULT: 'hsl(var(--popover))',
          foreground: 'hsl(var(--popover-foreground))',
        },
        card: {
          DEFAULT: 'hsl(var(--card))',
          foreground: 'hsl(var(--card-foreground))',
        },
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
    },
  },
  plugins: [],
}
