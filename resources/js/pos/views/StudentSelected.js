import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import AssetCard from '../components/AssetCard';
import BarcodeScannerOut from '../components/BarcodeScannerOut';
import { assets as assetsApi, loans } from '../../api';

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
    const [assets, setAssets] = useState([]);
    const [selectedAssets, setSelectedAssets] = useState([]);

    useEffect(() => {
        assetsApi.getAll({
            startDateTime: Date.now(),
            endDateTime: new Date(new Date().setHours(15, 30, 0, 0)).getTime(),
        })
            .then(setAssets);
    }, []);

    return (
        <>
            <BarcodeScannerOut
                onScan={assetTag => {
                    const asset = assets.find(x => x.tag === parseInt(assetTag));
                    if (!asset) return;
                    setSelectedAssets([...selectedAssets, assetTag]);
                    scanOut({ user: location.state.user.userId, studentName: studentId, asset });
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
                            />
                        );
                    })}
                    <Button
                        onClick={() => navigate('/')}
                    >
                        Finish
                    </Button>
                </Stack>
            </Box>
        </>
    );
}
