import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import moment from 'moment';
import Masonry from '@mui/lab/Masonry';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CircularProgress from '@mui/material/CircularProgress';
import Typography from '@mui/material/Typography';
import { useBarcodeScanner } from '../hooks/useBarcodeScanner';
import * as api from '../../api';

export default function CollectConfirm() {
    const navigate = useNavigate();
    const { loanId } = useParams();
    const [reservation, setReservation] = useState();
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(false);

    const handleScanComplete = (code) => {
        const asset = assets.find(asset => asset.tag === code);
        if (asset && asset.asset_group_id) {
            setReservation(prev => {
                const newReservation = { ...prev };
                const assetInReservation = newReservation.assets.find(asset => asset.tag === code);

                if (assetInReservation) {
                    assetInReservation.scanned = true;
                    return newReservation;
                }

                newReservation.asset_groups = newReservation.asset_groups.filter(group => group.id !== asset.asset_group_id);
                asset.scanned = true;
                newReservation.assets = [...newReservation.assets, asset];
                return newReservation;
            });
        }
    };

    useBarcodeScanner(handleScanComplete);

    useEffect(() => {
        setLoading(true);
        api.loans.getReservations()
            .then(async reservations => {
                const item = reservations.find(reservation => reservation.id === parseInt(loanId));
                setReservation(item);

                const body = await api.assets.getAll({
                    startDateTime: moment(item.start_date_time, 'DD MMM YYYY HH:mm').unix(),
                    endDateTime: moment(item.end_date_time, 'DD MMM YYYY HH:mm').unix()
                });
                setAssets(body.assets);
                setLoading(false);
            });
    }, [loanId]);

    const handleBeginLoan = async () => {
        await api.loans.update(loanId, {
            startDateTime: moment().unix(),
            endDateTime: moment(reservation.end_date_time, 'DD MMM YYYY HH:mm').unix(),
            user: reservation.user.id,
            assets: reservation.assets.map(asset => ({ id: asset.id, returned: false })),
            groups: reservation.asset_groups.map(group => ({ id: group.id, quantity: group.quantity })),
            details: reservation.details,
            reservation: false
        });
        navigate('/');
    };

    return (
        <Box sx={{ paddingTop: 5 }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
            >
                {loading && <CircularProgress />}
                {!loading && reservation &&
                    <React.Fragment>
                        <Typography variant="h5">Scan the following items:</Typography>
                        <Masonry columns={3} spacing={1} sx={{ paddingLeft: 2, paddingRight: 2 }}>
                            {reservation.asset_groups.map((group, index) => <Card key={index} sx={{ backgroundColor: 'warning.main' }}>
                                <CardContent>
                                    <Typography variant="h6" color="black">{group.name}</Typography>
                                </CardContent>
                            </Card>)}
                            {reservation.assets.map((asset, index) => <Card key={index} sx={{ backgroundColor: asset.scanned ? 'success.main' : undefined }}>
                                <CardContent>
                                    <Typography variant="h6">{asset.name} ({asset.tag})</Typography>
                                </CardContent>
                            </Card>)}
                        </Masonry>
                        <Button
                            onClick={handleBeginLoan}
                            variant="outlined"
                            color="success"
                        >
                            Begin Loan
                        </Button>
                    </React.Fragment>}
                <Button
                    onClick={() => navigate('/')}
                    variant="outlined"
                    sx={{ position: 'fixed', bottom: 5 }}
                    color="error"
                >
                    Cancel
                </Button>
            </Stack>
        </Box>
    );
}
