import * as React from 'react';
import PropTypes from 'prop-types';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Box from '@mui/material/Box';
import Chip from '@mui/material/Chip';
import Stack from '@mui/material/Stack';
import Divider from '@mui/material/Divider';
import Typography from '@mui/material/Typography';

export default function AssetCard({ asset, endDateTime, overdue }) {
    return (
        <Card variant="outlined" sx={{ width: 500 }}>
            <CardContent>
                <Box sx={{ pb: 2 }}>
                    <Stack direction="row" justifyContent="space-between" alignItems="center">
                        <Typography gutterBottom variant="h3" component="div">
                            {asset.name}
                        </Typography>
                        <Typography gutterBottom variant="h4" component="div">
                            {asset.tag}
                        </Typography>
                    </Stack>
                    {!asset.available && <Chip color="error" label="Not available: Scan again to confirm" size="medium" sx={{ marginBottom: 1 }} />}
                    <Typography color="text.secondary" variant="h6">
                        {overdue ? 'Booking overdue.' : 'Please return by the end of the day.'}
                    </Typography>
                </Box>
                <Divider />
                <Box sx={{ pt: 2 }}>
                    <Typography gutterBottom variant="h5">
                        Booking end
                    </Typography>
                    <Stack direction="row" spacing={1}>
                        <Chip label={endDateTime} size="medium" color={overdue ? 'error' : 'primary'} />
                    </Stack>
                </Box>
            </CardContent>
        </Card>
    );
}

AssetCard.propTypes = {
    asset: PropTypes.shape({
        name: PropTypes.string.isRequired,
        tag: PropTypes.number.isRequired,
        available: PropTypes.bool,
    }).isRequired,
    endDateTime: PropTypes.string,
    overdue: PropTypes.bool
};