import React, { useState, useEffect, useCallback, useRef } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useSnackbar } from 'notistack';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import Paper from '@mui/material/Paper';
import CircularProgress from '@mui/material/CircularProgress';
import Alert from '@mui/material/Alert';
import AssetCard from '../components/AssetCard';
import BarcodeScannerOut from '../components/BarcodeScannerOut';
import { assets as assetsApi, loans } from '../../api';

function getDefaultEndDateTime() {
    const now = new Date();
    const end = new Date(now);
    end.setHours(15, 30, 0, 0);

    if (now.getTime() > end.getTime()) {
        end.setDate(end.getDate() + 1);
    }

    return Math.round(end.getTime() / 1000);
}

async function getOpenLoans(userId) {
    const openLoans = [];
    const allLoans = await loans.getAll();
    for (const loan of allLoans) {
        if (loan.user.id === userId) {
            openLoans.push(loan);
        }
    }
    return openLoans;
}

async function scanOut({ user, authorisedBy, asset }) {
    return loans.create({
        user,
        assets: [{
            id: asset.id,
            returned: false
        }],
        authorisedBy,
        reservation: false,
        startDateTime: Math.round(Date.now() / 1000),
        endDateTime: getDefaultEndDateTime()
    });
}

export default function StudentSelected() {
    const navigate = useNavigate();
    const location = useLocation();
    const { userId, authorisedByUserId, label: studentName } = location.state.user;
    const { enqueueSnackbar } = useSnackbar();
    const [assets, setAssets] = useState([]);
    const [selectedAssets, setSelectedAssets] = useState([]);
    const [existingLoans, setExistingLoans] = useState(null);
    const [scannerReady, setScannerReady] = useState(false);

    const assetsRef = useRef(assets);
    const selectedAssetsRef = useRef(selectedAssets);

    useEffect(() => {
        assetsRef.current = assets;
        selectedAssetsRef.current = selectedAssets;
    }, [assets, selectedAssets]);

    useEffect(() => {
        assetsApi.getAll({
            startDateTime: Math.round(Date.now() / 1000),
            endDateTime: getDefaultEndDateTime(),
        })
            .then(data => setAssets(data.assets))
            .then(() => setScannerReady(true))
            .catch(error => {
                enqueueSnackbar(error.message, {
                    variant: 'error',
                    autoHideDuration: 5000
                });
            });

        getOpenLoans(userId)
            .then(loans => {
                setExistingLoans(loans);
            });
    }, [enqueueSnackbar, userId]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            navigate('/');
        }, 60000);
        return () => clearTimeout(timeout);
    }, [navigate]);

    const onScan = useCallback(async assetTag => {
        const asset = assetsRef.current.find(x => x.tag === parseInt(assetTag));
        if (!asset) return;

        /**
         * If user already has an open loan, don't allow them to book out another
         */
        if (existingLoans?.length) {
            (new Audio('/pos-static/error.wav')).play();
            return;
        }

        /**
         * Don't allow more than one asset to be scanned
         */
        if (selectedAssetsRef.current.length > 0) {
            enqueueSnackbar('You can only borrow 1 item at a time.', {
                variant: 'warning',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/warn.wav')).play();
            return;
        }

        /**
         * If the asset is not available, show snackbar and don't add to selected assets.
         */
        if (!asset.available) {
            enqueueSnackbar(`Asset ${asset.tag} is unavailable. Try a different item.`, {
                variant: 'warning',
                autoHideDuration: 7000
            });
            (new Audio('/pos-static/error.wav')).play();
            return;
        }

        setSelectedAssets([...selectedAssetsRef.current, assetTag]);
        try {
            await scanOut({ user: userId, authorisedBy: authorisedByUserId, asset });
            (new Audio('/pos-static/ding.wav')).play();
        } catch (error) {
            enqueueSnackbar(error.message, {
                variant: 'error',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/error.wav')).play();
        }
    }, [enqueueSnackbar, existingLoans?.length, authorisedByUserId, userId]);

    if (existingLoans?.length) {
        return (
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={4}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4" gutterBottom>
                        New Student Loan
                    </Typography>
                    <Typography variant="h3" gutterBottom>{studentName}</Typography>
                    <Alert
                        severity="error"
                        variant="filled"
                        sx={{ transform: 'scale(1.25)' }}
                    >
                        You have an existing loan - you cannot borrow another item until it has been returned.
                    </Alert>
                    {existingLoans.map(loan => loan.assets.map(asset => {
                        asset.available = true;
                        return (
                            <AssetCard
                                key={asset.tag}
                                asset={asset}
                                endDateTime={loan.end_date_time}
                                overdue
                            />
                        );
                    }))}
                </Stack>
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
                            color="error"
                            size="large"
                        >
                            Cancel
                        </Button>
                    </Stack>
                </Paper>
            </Box>
        );
    }

    return (
        <>
            <BarcodeScannerOut
                assets={assets}
                onScan={onScan}
            />
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={4}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4" gutterBottom>
                        New Student Loan
                    </Typography>
                    <Typography variant="h3">{studentName}</Typography>

                    {(!scannerReady || existingLoans === null) &&
                    <Stack direction="column" alignItems="center" spacing={2}>
                        <Alert severity="warning" variant="outlined">
                            Please wait...
                        </Alert>
                        <CircularProgress />
                    </Stack>}

                    {(!selectedAssets.length && scannerReady && existingLoans !== null) &&
                    <Stack direction="column" alignItems="center" spacing={2}>
                        <Alert severity="success" variant="outlined">
                            Scanner ready.
                        </Alert>
                    </Stack>}

                    {selectedAssets.map(assetTag => {
                        const asset = assets.find(x => x.tag === parseInt(assetTag));
                        if (!asset) return;
                        return (
                            <AssetCard
                                key={assetTag}
                                asset={asset}
                                endDateTime="15:30"
                            />
                        );
                    })}
                </Stack>
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
                            color={selectedAssets.length ? 'primary' : 'error'}
                        >
                            {selectedAssets.length ? 'Finish' : 'Cancel'}
                        </Button>
                    </Stack>
                </Paper>
            </Box>
        </>
    );
}
