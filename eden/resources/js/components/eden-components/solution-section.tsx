import { Box, Typography } from '@mui/material';

export default function SolutionSection() {
    return (
        <Box sx={{
            display: 'flex',
            justifyContent: 'flex-start',
            alignItems: 'flex-start',
            height: '100%',
            color: '#c7c9e1',
            backgroundColor: 'black',
        }}>
            <div className="p-2">
                <Typography
                    variant="h4"
                    sx={{
                        color: '#fff',
                        textShadow: '2px 2px 4px rgba(0,0,0,0.5)'
                    }}>
                    Soution Section
                </Typography>
            </div>
        </Box>
    );
}
