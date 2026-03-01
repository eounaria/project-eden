import { Box, Grid, Typography } from '@mui/material';

export default function Impact() {
    return (
        <Box sx={{
            display: 'flex',
            justifyContent: 'flex-start',
            alignItems: 'flex-start',
            height: '100vh',
            color: '#c7c9e1',
            background: "linear-gradient(180deg, #000000 30%, #255527 50%, #357a38 100%)",
        }}>
            <div className="p-2">
                <Typography
                    variant="h4"
                    sx={{
                        color: '#fff',
                        textShadow: '2px 2px 4px rgba(0,0,0,0.5)'
                    }}>
                    Impact
                </Typography>
            </div>
        </Box>
    );
}
