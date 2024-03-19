import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { useSnackbar } from 'notistack';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
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

    useEffect(() => {
        assetsApi.getAll({
            startDateTime: Math.round(Date.now() / 1000),
            endDateTime: Math.round(new Date(new Date().setHours(15, 30, 0, 0)).getTime() / 1000),
        })
            .then(setAssets);

        getOpenLoans(studentId)
            .then(loans => {
                setExistingLoans(loans);
                if (loans.length) {
                    console.log(loans);
                    enqueueSnackbar('Student already has an open booking', {
                        variant: 'error',
                        autoHideDuration: 5000
                    });
                    (new Audio('/pos-static/error.wav')).play();
                }
            });
    }, [enqueueSnackbar, navigate, studentId]);

    if (existingLoans === null) {
        return (
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={2}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4">{studentId}</Typography>
                    <Box sx={{ display: 'flex' }}>
                        <CircularProgress />
                    </Box>
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

    if (existingLoans.length) {
        return (
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={2}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4">{studentId}</Typography>
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
                onScan={async assetTag => {
                    const asset = assets.find(x => x.tag === parseInt(assetTag));
                    if (!asset) return;

                    /**
                     * If the asset is not available and is not already pending, add it to the pending list.
                     */
                    if (!asset.available && !pendingAssets.includes(asset)) {
                        setPendingAssets([...pendingAssets, asset]);
                        setSelectedAssets([...selectedAssets, assetTag]);
                        (new Audio('/pos-static/warn.wav')).play();
                        return;
                    }

                    /**
                     * If the asset is pending, remove it from the pending list and set it to available.
                     */
                    if (pendingAssets.includes(asset)) {
                        const index = pendingAssets.indexOf(asset);
                        pendingAssets.splice(index, 1);
                        setPendingAssets(pendingAssets);

                        asset.available = true;
                        setAssets([...assets]);

                        scanOut({ user: location.state.user.userId, studentName: studentId, asset });
                        (new Audio('/pos-static/ding.wav')).play();
                        return;
                    }

                    setSelectedAssets([...selectedAssets, assetTag]);
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
                }}
            />
            <Box sx={{ paddingTop: 5 }}>
                <Stack
                    direction="column"
                    spacing={2}
                    alignItems="center"
                    justifyContent="center"
                >
                    <Typography variant="h4">{studentId}</Typography>
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
