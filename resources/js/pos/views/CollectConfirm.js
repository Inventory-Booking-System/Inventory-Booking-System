import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import moment from 'moment';
import Masonry from '@mui/lab/Masonry';
import { useSnackbar } from 'notistack';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Paper from '@mui/material/Paper';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogActions from '@mui/material/DialogActions';
import Typography from '@mui/material/Typography';
import IconButton from '@mui/material/IconButton';
import LoadingButton from '@mui/lab/LoadingButton';
import DeleteIcon from '@mui/icons-material/Delete';
import { useBarcodeScanner } from '../hooks/useBarcodeScanner';
import BookingAssetCard from '../components/BookingAssetCard';
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
    const [cancelDialogOpen, setCancelDialogOpen] = useState(false);

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

    const handleCancel = () => {
        setCancelDialogOpen(true);
    };

    const handleConfirmCancel = () => {
        setCancelDialogOpen(false);
        navigate('/');
    };

    const handleCloseCancelDialog = () => {
        setCancelDialogOpen(false);
    };

    const handleDeleteGroupItem = (groupId) => {
        setReservation(prev => {
            if (!prev) {
                return prev;
            }

            const nextReservation = {
                ...prev,
                asset_groups: prev.asset_groups.map(group => ({
                    ...group,
                    pivot: { ...group.pivot }
                }))
            };

            const group = nextReservation.asset_groups.find(item => item.id === groupId);
            if (!group || !group.pivot?.quantity) {
                return prev;
            }

            group.pivot.quantity -= 1;
            if (group.pivot.quantity <= 0) {
                nextReservation.asset_groups = nextReservation.asset_groups.filter(item => item.id !== groupId);
            }

            return nextReservation;
        });
    };

    const handleDeleteAssetItem = (assetId, assetTag) => {
        setReservation(prev => {
            if (!prev) {
                return prev;
            }

            return {
                ...prev,
                assets: prev.assets.filter(asset => {
                    if (assetId != null && asset.id != null) {
                        return asset.id !== assetId;
                    }
                    return asset.tag !== assetTag;
                })
            };
        });
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
                        <Typography variant="h4">Scan the following items:</Typography>
                        <Masonry columns={3} spacing={1} sx={{ paddingLeft: 2, paddingRight: 2 }}>
                            {reservation.asset_groups.map((group, index) => {
                                let cards = [];
                                for (let i = 0; i < group.pivot.quantity; i++) {
                                    cards.push(
                                        <BookingAssetCard
                                            key={`${group.id}-${index}-${i}`}
                                            title={group.name}
                                            statusText="Pending scan"
                                            statusSeverity={reservation.status_id === Status.RESERVATION ? 'warning' : 'info'}
                                            action={
                                                <IconButton onClick={() => handleDeleteGroupItem(group.id)}>
                                                    <DeleteIcon />
                                                </IconButton>
                                            }
                                        />
                                    );
                                }
                                return cards;
                            })}
                            {reservation.assets.map((asset, index) => (
                                <BookingAssetCard
                                    key={asset.id || `${asset.tag}-${index}`}
                                    title={asset.name}
                                    subtitle={`(${asset.tag})`}
                                    statusText={asset.scanned ? 'Scanned' : 'Pending scan'}
                                    statusSeverity={asset.scanned ? 'success' : 'warning'}
                                    action={
                                        <IconButton onClick={() => handleDeleteAssetItem(asset.id, asset.tag)}>
                                            <DeleteIcon />
                                        </IconButton>
                                    }
                                />
                            ))}
                        </Masonry>
                    </React.Fragment>}
            </Stack>
            <Paper
                elevation={3}
                sx={{ position: 'fixed', bottom: 0, left: 0, right: 0, padding: 2 }}
            >
                <Stack
                    direction="row"
                    spacing={4}
                    justifyContent="center"
                >
                    <Button
                        onClick={handleCancel}
                        variant="outlined"
                        size="large"
                        color="error"
                    >
                        Cancel
                    </Button>
                    {!loading && reservation && <LoadingButton
                        onClick={handleBeginLoan}
                        variant="outlined"
                        color="success"
                        size="large"
                        loading={submitLoading}
                    >
                        Save {reservation.status_id === Status.RESERVATION ? 'Loan' : 'Setup'}
                    </LoadingButton>}
                </Stack>
            </Paper>
            <Dialog
                open={cancelDialogOpen}
                onClose={handleCloseCancelDialog}
                aria-labelledby="cancel-dialog-title"
                aria-describedby="cancel-dialog-description"
            >
                <DialogTitle id="cancel-dialog-title">Confirm Cancel</DialogTitle>
                <DialogContent>
                    <DialogContentText id="cancel-dialog-description">
                        Are you sure you want to cancel? Any scanned items will be lost.
                    </DialogContentText>
                </DialogContent>
                <DialogActions>
                    <Button onClick={handleCloseCancelDialog}>No</Button>
                    <Button onClick={handleConfirmCancel} color="error" autoFocus>
                        Yes, cancel
                    </Button>
                </DialogActions>
            </Dialog>
        </Box>
    );
}
