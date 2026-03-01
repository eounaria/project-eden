import { Box, Typography } from '@mui/material';
import SupplyMovements from './supply-movements-chart';

export default function ProblemSection() {
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
                    Problem Section
                </Typography>
            </div>
        </Box>
    );
}
