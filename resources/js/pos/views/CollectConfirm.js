import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import moment from 'moment';
import Masonry from '@mui/lab/Masonry';
import { useSnackbar } from 'notistack';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CircularProgress from '@mui/material/CircularProgress';
import Typography from '@mui/material/Typography';
import LoadingButton from '@mui/lab/LoadingButton';
import { useBarcodeScanner } from '../hooks/useBarcodeScanner';
import * as api from '../../api';
import { Status } from '../../api/loans';

export default function CollectConfirm() {
    const navigate = useNavigate();
    const { loanId } = useParams();
    const { enqueueSnackbar } = useSnackbar();
    const [reservation, setReservation] = useState();
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(false);
    const [submitLoading, setSubmitLoading] = useState(false);

    const handleScanComplete = (code) => {
        const asset = assets.find(asset => asset.tag === code);
        if (!asset) {
            enqueueSnackbar(`Asset ${code} not found.`, {
                variant: 'error',
                autoHideDuration: 8000
            });
            (new Audio('/pos-static/error.wav')).play();
            return;
        }
        if (!asset.available) {
            enqueueSnackbar(`Asset ${asset.tag} is not available.`, {
                variant: 'error',
                autoHideDuration: 8000
            });
            (new Audio('/pos-static/error.wav')).play();
            return;
        }
        if (asset && asset.asset_group_id) {
            setReservation(prev => {
                const newReservation = { ...prev };
                const assetInReservation = newReservation.assets.find(asset => asset.tag === code);

                if (assetInReservation) {
                    assetInReservation.scanned = true;
                    return newReservation;
                }

                const groupToRemove = newReservation.asset_groups.find(group => group.id === asset.asset_group_id);
                if (groupToRemove?.pivot?.quantity) groupToRemove.pivot.quantity -= 1;
                if (groupToRemove?.pivot?.quantity === 0) {
                    newReservation.asset_groups = newReservation.asset_groups.filter(group => group.id !== asset.asset_group_id);
                }
                asset.scanned = true;
                newReservation.assets = [...newReservation.assets, asset];
                return newReservation;
            });
            (new Audio('/pos-static/ding.wav')).play();
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
        setSubmitLoading(true);
        try {
            if (reservation.status_id === Status.RESERVATION) {
                const resp = await api.loans.update(loanId, {
                    startDateTime: moment().unix(),
                    endDateTime: moment(reservation.end_date_time, 'DD MMM YYYY HH:mm').unix(),
                    user: reservation.user.id,
                    assets: reservation.assets.map(asset => ({ id: asset.id, returned: false })),
                    groups: reservation.asset_groups.map(group => ({ id: group.id, quantity: group.pivot.quantity })),
                    details: reservation.details,
                    reservation: false
                });
                if (!resp.ok) {
                    throw new Error((await resp.json()));
                }
            } else {
                const resp = await api.setups.patch(reservation.setup.id, {
                    assets: reservation.assets.map(asset => ({ id: asset.id, returned: false })),
                    groups: reservation.asset_groups.map(group => ({ id: group.id, quantity: group.pivot.quantity }))
                });
                if (!resp.ok) {
                    throw new Error((await resp.json()));
                }
            }
            navigate('/');
        } catch (e) {
            console.error(e);
            enqueueSnackbar('An error occurred while modifying the booking.', {
                variant: 'error',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/error.wav')).play();
        }
        setSubmitLoading(false);
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
                            {reservation.asset_groups.map((group, index) => {
                                let cards = [];
                                for (let i = 0; i < group.pivot.quantity; i++) {
                                    cards.push(<Card key={index} sx={{ backgroundColor: reservation.status_id === Status.RESERVATION ? 'warning.main' : 'grey.500' }}>
                                        <CardContent>
                                            <Typography variant="h6" color="black">{group.name}</Typography>
                                        </CardContent>
                                    </Card>);
                                }
                                return cards;
                            })}
                            {reservation.assets.map((asset, index) => <Card key={index} sx={{ backgroundColor: asset.scanned ? 'success.main' : undefined }}>
                                <CardContent>
                                    <Typography variant="h6">{asset.name} ({asset.tag})</Typography>
                                </CardContent>
                            </Card>)}
                        </Masonry>
                        <LoadingButton
                            onClick={handleBeginLoan}
                            variant="outlined"
                            color="success"
                            loading={submitLoading}
                        >
                            Begin {reservation.status_id === Status.RESERVATION ? 'Loan' : 'Setup'}
                        </LoadingButton>
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
