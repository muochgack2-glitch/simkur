/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  safelist: [
    // Class card colors - untuk monitoring jurnal
    'bg-blue-500',
    'bg-purple-500',
    'bg-green-500',
    'bg-indigo-500',
    'bg-pink-500',
    'bg-teal-500',
    'bg-cyan-500',
    'bg-rose-500',
    'bg-emerald-500',
    'border-blue-500',
    'border-purple-500',
    'border-green-500',
    'border-indigo-500',
    'border-pink-500',
    'border-teal-500',
    'border-cyan-500',
    'border-rose-500',
    'border-emerald-500',
    // Teacher card colors - yellow untuk partial
    'from-yellow-500',
    'to-yellow-600',
    'border-yellow-500',
    'text-yellow-600',
    'bg-yellow-200',
    'bg-yellow-50',
    'border-yellow-200',
    'bg-yellow-500',
    'text-yellow-500',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
