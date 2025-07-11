import React, { useCallback, useEffect } from 'react';
import PropTypes from 'prop-types';
import { useLocation, useNavigate } from 'react-router-dom';
import { useSnackbar } from 'notistack';

import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardHeader from '@mui/material/CardHeader';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import Grid from '@mui/material/Grid';
import Paper from '@mui/material/Paper';

import CheckIcon from '@mui/icons-material/Check';

import { useBarcodeScanner } from '../hooks/useBarcodeScanner';
import { get } from '../../api/loans';
import { scanIn } from '../../api/assets';

function AssetCard({ asset, onReturn }) {
    return (
        <Card sx={{ marginTop: 2, width: '100%' }} variant="outlined">
            <CardHeader
                action={
                    !asset.pivot.returned ?
                        <Button variant="outlined" startIcon={<CheckIcon />} onClick={onReturn}>
                            Return
                        </Button>
                        :
                        null
                }
                title={asset.name}
                subheader={`(${asset.tag})`}
            />
            <CardContent>
                <Alert variant="outlined" severity={asset.pivot.returned ? 'success' : 'warning'}>
                    {asset.pivot.returned ? 'Returned' : 'Not returned'}
                </Alert>
            </CardContent>
        </Card>
    );
}

AssetCard.propTypes = {
    asset: PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string.isRequired,
        tag: PropTypes.number.isRequired,
        description: PropTypes.string,
        pivot: PropTypes.shape({
            returned: PropTypes.number.isRequired,
        }).isRequired,
    }).isRequired,
    onReturn: PropTypes.func.isRequired
};

export default function Return() {
    const navigate = useNavigate();
    const location = useLocation();
    const { enqueueSnackbar } = useSnackbar();
    const [loan, setLoan] = React.useState(location.state?.loan);

    const fetchLoan = useCallback((id) => {
        get(id)
            .then(data => {
                setLoan(data);
            });
    }, []);

    useEffect(() => {
        if (loan?.id) {
            fetchLoan(loan.id);

            const interval = setInterval(() => {
                fetchLoan(loan.id);
            }, 5000);

            return () => clearInterval(interval);
        }
    }, [fetchLoan, loan.id]);

    const onScanComplete = useCallback(async (code) => {
        try {
            await scanIn({ tag: code });
            (new Audio('/pos-static/notify.wav')).play();

            enqueueSnackbar(`Scanned in ${code}`, {
                variant: 'success',
                autoHideDuration: 5000
            });
        } catch (e) {
            if (e.error === 'NO_OPEN_LOANS') {
                enqueueSnackbar(`Asset ${code} has no open loans`, {
                    variant: 'warning',
                    autoHideDuration: 7000
                });
                (new Audio('/pos-static/warn.wav')).play();
                return;
            }

            enqueueSnackbar(`Failed to scan in ${code}`, {
                variant: 'error',
                autoHideDuration: 7000
            });
            (new Audio('/pos-static/error.wav')).play();
        } finally {
            if (loan && loan.id) {
                fetchLoan(loan.id);
            }
        }
    }, [enqueueSnackbar, fetchLoan, loan]);

    useBarcodeScanner(onScanComplete);

    if (!loan) {
        enqueueSnackbar('Internal Error: Could not load loan.', {
            variant: 'error',
            autoHideDuration: 7000
        });
        (new Audio('/pos-static/error.wav')).play();

        navigate('/');
        return null;
    }

    return (
        <>
            <Stack
                direction="column"
                alignItems="center"
                sx={{ height: '100vh', padding: 2 }}
            >
                <Typography
                    variant="h3"
                    color={loan.assets.filter(asset => asset.pivot.returned === 1).length === loan.assets.length ? 'success' : 'white'}
                >
                    {loan.assets.filter(asset => asset.pivot.returned === 1).length}/{loan.assets.length} Items Returned
                </Typography>
                <Typography variant="h5">
                    {loan.user.forename} {loan.user.surname}
                </Typography>
                <Grid container spacing={2} sx={{ paddingTop: 2 }}>
                    {loan.assets.map(asset => (
                        <Grid item xs={3} key={asset.id}>
                            <AssetCard
                                asset={asset}
                                onReturn={async () => {
                                    await scanIn({ tag: asset.tag });
                                    fetchLoan(loan.id);
                                }}
                            />
                        </Grid>
                    ))}
                </Grid>

                <Paper
                    elevation={3}
                    sx={{ position: 'absolute', bottom: 0, left: 0, right: 0, padding: 2 }}
                >
                    <Stack
                        direction="row"
                        spacing={2}
                        justifyContent="center"
                    >
                        <Button
                            onClick={() => navigate('/')}
                            variant="outlined"
                            size="large"
                        >
                            Home
                        </Button>
                    </Stack>
                </Paper>
            </Stack>
        </>
    );
}
