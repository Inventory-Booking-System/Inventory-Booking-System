import React, { useState, useEffect, useCallback, useRef } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { useSnackbar } from 'notistack';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
import Alert from '@mui/material/Alert';
import AssetCard from '../components/AssetCard';
import BarcodeScannerOut from '../components/BarcodeScannerOut';
import { assets as assetsApi, loans } from '../../api';

async function getOpenLoans(studentName) {
    const openLoans = [];
    const allLoans = await loans.getAll();
    for (const loan of allLoans) {
        if (loan.details === studentName) {
            openLoans.push(loan);
        }
    }
    return openLoans;
}

async function scanOut({ user, studentName, asset }) {
    return loans.create({
        user,
        assets: [{
            id: asset.id,
            returned: false
        }],
        details: studentName,
        reservation: false,
        startDateTime: Math.round(Date.now() / 1000),
        endDateTime:  Math.round(new Date(new Date().setHours(15, 30, 0, 0)).getTime() / 1000)
    });
}

export default function StudentSelected() {
    const { studentId } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    const { enqueueSnackbar } = useSnackbar();
    const [assets, setAssets] = useState([]);
    const [selectedAssets, setSelectedAssets] = useState([]);
    const [pendingAssets, setPendingAssets] = useState([]);
    const [existingLoans, setExistingLoans] = useState(null);
    const [scannerReady, setScannerReady] = useState(false);

    const assetsRef = useRef(assets);
    const pendingAssetsRef = useRef(pendingAssets);
    const selectedAssetsRef = useRef(selectedAssets);

    useEffect(() => {
        assetsRef.current = assets;
        pendingAssetsRef.current = pendingAssets;
        selectedAssetsRef.current = selectedAssets;
    }, [assets, pendingAssets, selectedAssets]);

    useEffect(() => {
        assetsApi.getAll({
            startDateTime: Math.round(Date.now() / 1000),
            endDateTime: Math.round(new Date(new Date().setHours(15, 30, 0, 0)).getTime() / 1000),
        })
            .then(data => setAssets(data.assets))
            .then(() => setScannerReady(true))
            .catch(error => {
                enqueueSnackbar(error.message, {
                    variant: 'error',
                    autoHideDuration: 5000
                });
            });

        getOpenLoans(studentId)
            .then(loans => {
                setExistingLoans(loans);
                if (loans.length) {
                    console.log(loans);
                    enqueueSnackbar('Return your previous item before booking another.', {
                        variant: 'error',
                        autoHideDuration: 5000
                    });
                    (new Audio('/pos-static/error.wav')).play();
                }
            });
    }, [enqueueSnackbar, studentId]);

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
         * If the asset is not available and is not already pending, add it to the pending list.
         */
        if (!asset.available && !pendingAssetsRef.current.includes(asset)) {
            setPendingAssets([...pendingAssetsRef.current, asset]);
            setSelectedAssets([...selectedAssetsRef.current, assetTag]);
            (new Audio('/pos-static/warn.wav')).play();
            return;
        }

        /**
         * If the asset has just been scanned, do not add it to the selected
         * list again.
         */
        if (selectedAssetsRef.current.includes(assetTag)) {
            enqueueSnackbar('Asset has already been scanned.', {
                variant: 'warning',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/warn.wav')).play();
            return;
        }

        /**
         * If the asset is pending, remove it from the pending list and set it to available.
         */
        if (pendingAssetsRef.current.includes(asset)) {
            const index = pendingAssetsRef.current.indexOf(asset);
            pendingAssetsRef.current.splice(index, 1);
            setPendingAssets(pendingAssetsRef.current);

            asset.available = true;
            setAssets([...assetsRef.current]);

            scanOut({ user: location.state.user.userId, studentName: studentId, asset });
            (new Audio('/pos-static/ding.wav')).play();
            return;
        }

        setSelectedAssets([...selectedAssetsRef.current, assetTag]);
        try {
            await scanOut({ user: location.state.user.userId, studentName: studentId, asset });
            (new Audio('/pos-static/ding.wav')).play();
        } catch (error) {
            enqueueSnackbar(error.message, {
                variant: 'error',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/error.wav')).play();
        }
    }, [enqueueSnackbar, existingLoans?.length, location.state.user.userId, studentId]);

    if (existingLoans?.length) {
        return (
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={2}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4">{studentId}</Typography>
                    <Typography variant="h5">Return this item before booking another:</Typography>
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
                    <Button
                        onClick={() => navigate('/')}
                        variant="outlined"
                        sx={{ position: 'fixed', bottom: 5 }}
                    >
                        Cancel
                    </Button>
                </Stack>
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
                    spacing={2}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4">{studentId}</Typography>

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
                    <Button
                        onClick={() => navigate('/')}
                        variant="outlined"
                        sx={{ position: 'fixed', bottom: 5 }}
                        color={selectedAssets.length ? 'primary' : 'error'}
                    >
                        {selectedAssets.length ? 'Finish' : 'Cancel'}
                    </Button>
                </Stack>
            </Box>
        </>
    );
}
