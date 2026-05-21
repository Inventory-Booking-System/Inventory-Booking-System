import React, { useMemo, useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { useNavigate } from 'react-router-dom';
import { useSnackbar } from 'notistack';
import dayjs from 'dayjs';

import { styled } from '@mui/material/styles';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CircularProgress from '@mui/material/CircularProgress';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import Grid from '@mui/material/Grid';
import Divider from '@mui/material/Divider';
import Paper from '@mui/material/Paper';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogActions from '@mui/material/DialogActions';
import LoadingButton from '@mui/lab/LoadingButton';

import DeleteIcon from '@mui/icons-material/Delete';
import IconButton from '@mui/material/IconButton';
import ArrowBackIosIcon from '@mui/icons-material/ArrowBackIos';

import { LocalizationProvider } from '@mui/x-date-pickers';
import { DateCalendar } from '@mui/x-date-pickers/DateCalendar';
import { MultiSectionDigitalClock } from '@mui/x-date-pickers/MultiSectionDigitalClock';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';

import { useBarcodeScanner } from '../hooks/useBarcodeScanner';
import Keyboard from '../components/Keyboard';
import NameSearch from '../components/NameSearch';
import BookingAssetCard from '../components/BookingAssetCard';
import * as api from '../../api';

const Clock = styled(MultiSectionDigitalClock)(() => ({
    '& .MuiMultiSectionDigitalClockSection-root': {
        overflowY: 'auto',
        '&::-webkit-scrollbar': {
            display: 'none',
        },
    },
}));

function Asset({ asset, onDelete }) {

    const status = useMemo(() => {
        if (!asset?.availability) return;
        const { status: availStatus, mustReturnBefore } = asset.availability;
        if (availStatus === 'unavailable_not_returned') return 'Unavailable - previous loan not returned';
        if (availStatus === 'available') return 'Available';
        if (availStatus === 'must_return_before') return `Must be returned before ${dayjs(mustReturnBefore).format('ddd DD MMM YYYY HH:mm')}`;
        return 'Unavailable';
    }, [asset]);

    return (
        <BookingAssetCard
            title={asset.name}
            subtitle={`(${asset.tag})`}
            action={
                <IconButton onClick={onDelete}>
                    <DeleteIcon />
                </IconButton>
            }
            statusText={status}
            statusSeverity={status === 'Available' ? 'success' : (status?.startsWith('Must be returned') ? 'warning' : 'error')}
        />
    );

}

Asset.propTypes = {
    asset: PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string.isRequired,
        tag: PropTypes.number.isRequired,
        description: PropTypes.string,
        availability: PropTypes.shape({
            status: PropTypes.oneOf(['available', 'must_return_before', 'unavailable_not_returned', 'unavailable']).isRequired,
            mustReturnBefore: PropTypes.string,
        }).isRequired,
    }).isRequired,
    onDelete: PropTypes.func.isRequired,
};

export default function StaffBooking() {
    const navigate = useNavigate();
    const { enqueueSnackbar } = useSnackbar();
    const [loading, setLoading] = useState(false);
    const [submitLoading, setSubmitLoading] = useState(false);
    const [users, setUsers] = useState([]);
    const [usersLoading, setUsersLoading] = useState(false);
    const [search, setSearch] = useState('');
    const [user, setUser] = useState();
    const [cart, setCart] = useState([]);
    const [dateTime, setDateTime] = useState(dayjs().add(1, 'hour').minute(0).second(0));
    const [cancelDialogOpen, setCancelDialogOpen] = useState(false);

    useEffect(() => {
        setUsersLoading(true);
        api.users.getAll()
            .then(users => {
                setUsers(users.map(user => ({
                    userId: user.id,
                    label: `${user.forename} ${user.surname}`
                })));
            })
            .finally(() => setUsersLoading(false));
    }, []);

    const handleScanComplete = async (code) => {
        setLoading(true);
        try {
            if (cart.find(item => item.tag === code)) {
                throw new Error(`Asset ${code} already in cart`);
            }
            const asset = await api.assets.getAvailability(code);
            if (!asset || asset.error) {
                throw new Error(`Asset ${code} not found`);
            }
            (new Audio('/pos-static/ding.wav')).play();

            setCart((prev) => {
                const newCart = [...prev];
                const index = newCart.findIndex((item) => item.tag === asset.tag);
                if (index !== -1) {
                    newCart[index] = asset;
                } else {
                    newCart.push(asset);
                }
                return newCart;
            });
        } catch (err) {
            enqueueSnackbar(err.message, {
                variant: 'error',
                autoHideDuration: 5000
            });
            (new Audio('/pos-static/error.wav')).play();
        } finally {
            setLoading(false);
        }
    };

    useBarcodeScanner(handleScanComplete);

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

    const handleSaveLoan = async () => {
        setSubmitLoading(true);
        try {
            const startDateTime = dayjs().unix();
            const endDateTime = dateTime.unix();
            const resp = await api.loans.create({
                user: user.userId,
                assets: cart.map(asset => ({
                    id: asset.id,
                    returned: false
                })),
                startDateTime: startDateTime,
                endDateTime: endDateTime,
                details: '',
                reservation: false
            });
            if (!resp.ok) {
                throw new Error('Failed to create loan');
            }
            enqueueSnackbar('Loan created', {
                variant: 'success',
                autoHideDuration: 5000
            });
            navigate('/');
        } catch (err) {
            enqueueSnackbar(err.message, {
                variant: 'error',
                autoHideDuration: 5000
            });
        } finally {
            setSubmitLoading(false);
        }
    };

    return (
        <Box sx={{ paddingTop: 5, paddingLeft: 5, paddingRight: 5, height: '100vh' }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
            >
                <Typography variant="h4">
                    New Staff Loan
                </Typography>
                <Grid container spacing={2}>
                    <Grid item xs={3} sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                        <Typography variant="h5">
                            Cart
                        </Typography>
                        {!cart.length && <Alert severity="info" variant="outlined" sx={{ mt: 2 }}>
                            Scan your first item
                        </Alert>}
                        {cart.map(asset => (
                            <Asset
                                key={asset.id}
                                asset={asset}
                                onDelete={() => {
                                    setCart((prev) => prev.filter(item => item.tag !== asset.tag));
                                }}
                            />
                        ))}
                        {loading && <CircularProgress />}
                    </Grid>
                    <Grid item xs={1}>
                        <Divider orientation="vertical" />
                    </Grid>
                    {!user && <Grid item xs={8}>
                        <Stack
                            direction="column"
                            spacing={2}
                            alignItems="center"
                            justifyContent="flex-start"
                        >
                            <NameSearch
                                sx={{ zoom: 1.6 }}
                                name="Staff Name"
                                onSelect={setUser}
                                options={users}
                                loading={usersLoading}
                                value={search}
                                onChange={setSearch}
                            />
                            <Keyboard
                                sx={{ zoom: 1.5 }}
                                onChange={setSearch}
                            />
                        </Stack>
                    </Grid>}
                    {user && <Grid item xs={7}>
                        <Stack
                            direction="column"
                            spacing={8}
                            alignItems="center"
                        >
                            <Stack
                                direction="row"
                                spacing={4}
                                sx={{ width: '100%' }}
                            >
                                <Button
                                    variant='outlined'
                                    startIcon={<ArrowBackIosIcon />}
                                    onClick={() => {
                                        setUser();
                                    }}
                                >
                                    Back
                                </Button>
                                <Typography variant="h5">{user.label}</Typography>
                                <Box />
                            </Stack>
                            <Stack
                                direction="row"
                                spacing={6}
                            >
                                <Card sx={{ zoom: 1.4 }} elevation={0} variant="outlined">
                                    <CardContent>
                                        <Typography gutterBottom variant="h6" component="div">
                                            Select End Date
                                        </Typography>
                                        <LocalizationProvider dateAdapter={AdapterDayjs}>
                                            <DateCalendar
                                                value={dateTime}
                                                onChange={(newValue) => setDateTime(prev => newValue.hour(prev.hour()).minute(prev.minute()).second(prev.second()))}
                                                disablePast
                                                sx={{ overflow: 'visible' }}
                                            />
                                        </LocalizationProvider>
                                    </CardContent>
                                </Card>
                                <Card sx={{ zoom: 1.4 }} elevation={0} variant="outlined">
                                    <CardContent>
                                        <Typography gutterBottom variant="h6" component="div">
                                            Select End Time
                                        </Typography>
                                        <LocalizationProvider dateAdapter={AdapterDayjs}>
                                            <Clock
                                                value={dateTime}
                                                onChange={(newValue) => setDateTime(prev => prev.set('hour', newValue.hour()).set('minute', newValue.minute()))}
                                                ampm={false}
                                                sx={{ height: 232, marginTop: 4 }}
                                            />
                                        </LocalizationProvider>
                                    </CardContent>
                                </Card>
                            </Stack>
                        </Stack>
                    </Grid>}
                </Grid>
                {!!cart.length && <Alert severity="warning" variant="outlined">
                    You have unsaved changes.
                </Alert>}
            </Stack>
            <Paper
                elevation={3}
                sx={{ position: 'absolute', bottom: 0, left: 0, right: 0, padding: 2 }}
            >
                <Stack
                    direction="row"
                    spacing={4}
                    justifyContent="center"
                >
                    <Button
                        onClick={handleCancel}
                        variant="outlined"
                        color="error"
                        size="large"
                    >
                        Cancel
                    </Button>
                    <LoadingButton
                        onClick={handleSaveLoan}
                        variant="contained"
                        color="success"
                        size="large"
                        disabled={loading || !cart.length}
                        loading={submitLoading}
                    >
                        Save Loan
                    </LoadingButton>
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
