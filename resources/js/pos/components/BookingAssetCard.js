import React from 'react';
import PropTypes from 'prop-types';
import Card from '@mui/material/Card';
import CardHeader from '@mui/material/CardHeader';
import CardContent from '@mui/material/CardContent';
import Alert from '@mui/material/Alert';

export default function BookingAssetCard({ title, subtitle, statusText, statusSeverity, action, sx }) {
    return (
        <Card sx={{ marginTop: 2, width: '100%', ...sx }} variant="outlined">
            <CardHeader
                action={action}
                title={title}
                subheader={subtitle}
            />
            {statusText && (
                <CardContent>
                    <Alert variant="outlined" severity={statusSeverity}>
                        {statusText}
                    </Alert>
                </CardContent>
            )}
        </Card>
    );
}

BookingAssetCard.propTypes = {
    title: PropTypes.string.isRequired,
    subtitle: PropTypes.string,
    statusText: PropTypes.string,
    statusSeverity: PropTypes.oneOf(['error', 'warning', 'info', 'success']),
    action: PropTypes.node,
    sx: PropTypes.object,
};

BookingAssetCard.defaultProps = {
    subtitle: '',
    statusText: '',
    statusSeverity: 'info',
    action: null,
    sx: {},
};