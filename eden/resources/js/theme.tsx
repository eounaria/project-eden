import { createTheme } from '@mui/material/styles';

const edenTheme = createTheme({
    palette: {
        background: {
            default: '#2e6a30',
            paper: '#c7c9e1',
        },
        primary: {
            light: '#5d945f',
            main: '#357a38',
            dark: '#255527',
            contrastText: '#fff',
        },
        secondary: {
            light: '#255355',
            main: '#35777a',
            dark: '#5d9294',
            contrastText: '#000',
        },
    },
    typography: {
        fontFamily: "'Inter', sans-serif",

        h1: {
            fontFamily: "'Helvetica', sans-serif",
        },

        h4: {
            fontFamily: "'monospace', sans-serif",
        },
    },
});

export default edenTheme;
