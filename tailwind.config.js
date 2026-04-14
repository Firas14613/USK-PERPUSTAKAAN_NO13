import preset from './vendor/filament/support/tailwind.config.preset'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#00236f',
                'primary-container': '#1e3a8a',
                'primary-fixed': '#dce1ff',
                'primary-fixed-dim': '#b6c4ff',
                'on-primary': '#ffffff',
                'on-primary-fixed': '#00164e',
                'on-primary-fixed-variant': '#264191',
                'on-primary-container': '#90a8ff',

                surface: '#f7f9fb',
                'surface-bright': '#f7f9fb',
                'surface-dim': '#d8dadc',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f2f4f6',
                'surface-container': '#eceef0',
                'surface-container-high': '#e6e8ea',
                'surface-container-highest': '#e0e3e5',
                'surface-variant': '#e0e3e5',

                'on-surface': '#191c1e',
                'on-surface-variant': '#444651',

                outline: '#757682',
                'outline-variant': '#c5c5d3',

                secondary: '#505f76',
                'secondary-fixed': '#d3e4fe',
                'secondary-fixed-dim': '#b7c8e1',
                'on-secondary': '#ffffff',
                'on-secondary-fixed': '#0b1c30',
                'on-secondary-fixed-variant': '#38485d',
                'secondary-container': '#d0e1fb',
                'on-secondary-container': '#54647a',

                tertiary: '#4b1c00',
                'tertiary-container': '#6e2c00',
                'tertiary-fixed': '#ffdbcb',
                'tertiary-fixed-dim': '#ffb691',
                'on-tertiary': '#ffffff',
                'on-tertiary-container': '#f39461',
                'on-tertiary-fixed': '#341100',
                'on-tertiary-fixed-variant': '#773205',

                error: '#ba1a1a',
                'error-container': '#ffdad6',
                'on-error': '#ffffff',
                'on-error-container': '#93000a',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                headline: ['Inter', 'ui-sans-serif', 'system-ui'],
                body: ['Inter', 'ui-sans-serif', 'system-ui'],
                label: ['Inter', 'ui-sans-serif', 'system-ui'],
            },
            boxShadow: {
                ambient: '0 20px 40px rgba(0, 35, 111, 0.06)',
            },
        },
    },
    plugins: [forms, typography],
}
