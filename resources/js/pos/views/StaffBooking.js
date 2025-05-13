import React, { useMemo, useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { useNavigate } from 'react-router-dom';
import moment from 'moment';
import { useSnackbar } from 'notistack';
import dayjs from 'dayjs';

import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardHeader from '@mui/material/CardHeader';
import CardContent from '@mui/material/CardContent';
import CardActions from '@mui/material/CardActions';
import CircularProgress from '@mui/material/CircularProgress';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import Grid from '@mui/material/Grid';
import Divider from '@mui/material/Divider';

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
import * as api from '../../api';

function Asset({ asset, onDelete }) {

    const nextLoanDate = useMemo(() => {
        if (!asset || !asset.loans) {
            return;
        }

        asset.loans.sort((a, b) => moment(a.start_date_time, 'DD MMM YYYY HH:mm').diff(moment(b.start_date_time, 'DD MMM YYYY HH:mm')));
        console.log(asset.loans);

        for (const loan of asset.loans) {
            if (moment().isBefore(moment(loan.start_date_time, 'DD MMM YYYY HH:mm'))) {
                return moment(loan.start_date_time, 'DD MMM YYYY HH:mm');
            }
        }
    }, [asset]);

    const previousLoanReturned = useMemo(() => {
        if (!asset || !asset.loans) {
            return;
        }

        asset.loans.sort((a, b) => moment(b.start_date_time, 'DD MMM YYYY HH:mm').diff(moment(a.start_date_time, 'DD MMM YYYY HH:mm')));

        for (const loan of asset.loans) {
            if (moment().isAfter(moment(loan.start_date_time, 'DD MMM YYYY HH:mm'))) {
                // status_id 5 is Completed, status_id 4 is Cancelled
                if (!loan.pivot.returned && loan.status_id !== 5 && loan.status_id !== 4) {
                    return false;
                }
            }
        }
        return true;
    }, [asset]);

    const status = useMemo(() => {
        if (!asset) {
            return;
        }
        if (!previousLoanReturned) {
            console.log('previous loan not returned');
            return 'Unavailable - previous loan not returned';
        }
        if (previousLoanReturned && !nextLoanDate) {
            console.log('previousLoanReturned, no next loan');
            return 'Available';
        }
        if (previousLoanReturned && nextLoanDate.isAfter(moment().add(1, 'hour'))) {
            console.log('previousLoanReturned, next loan after 1 hour');
            return `Must be returned before ${nextLoanDate.format('ddd DD MMM YYYY HH:mm')}`;
        }
        return 'Unavailable';
    }, [asset, previousLoanReturned, nextLoanDate]);

    return (
        <Card sx={{ marginTop: 2, width: '100%' }}>
            <CardHeader
                action={
                    <IconButton onClick={onDelete}>
                        <DeleteIcon />
                    </IconButton>
                }
                title={asset.name}
                subheader={`(${asset.tag})`}
            />
            <CardContent>
                <Alert variant="outlined" severity={status === 'Available' ? 'success' : (status?.startsWith('Must be returned') ? 'warning' : 'error')}>
                    {status}
                </Alert>
            </CardContent>
        </Card>
    );

}

Asset.propTypes = {
    asset: PropTypes.shape({
        id: PropTypes.number.isRequired,
        name: PropTypes.string.isRequired,
        tag: PropTypes.number.isRequired,
        description: PropTypes.string,
        loans: PropTypes.arrayOf(PropTypes.shape({
            start_date_time: PropTypes.string.isRequired,
            pivot: PropTypes.shape({
                returned: PropTypes.number.isRequired,
            }).isRequired,
            status_id: PropTypes.number.isRequired,
        })),
    }).isRequired,
    onDelete: PropTypes.func.isRequired,
};

export default function StaffBooking() {
    const navigate = useNavigate();
    const { enqueueSnackbar } = useSnackbar();
    const [loading, setLoading] = useState(false);
    const [users, setUsers] = useState([]);
    const [usersLoading, setUsersLoading] = useState(false);
    const [search, setSearch] = useState('');
    const [user, setUser] = useState();
    const [cart, setCart] = useState([]);
    const [dateTime, setDateTime] = useState(dayjs().add(1, 'hour').minute(0).second(0));

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
            const asset = await api.assets.get(code);
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
                    <Grid item xs={4} sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
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
                    {!user && <Grid item xs={7}>
                        <Stack
                            direction="column"
                            spacing={2}
                            alignItems="center"
                            justifyContent="flex-start"
                        >
                            <NameSearch
                                name="Staff Name"
                                onSelect={setUser}
                                options={users}
                                loading={usersLoading}
                                value={search}
                                onChange={setSearch}
                            />
                            <Keyboard
                                onChange={setSearch}
                            />
                        </Stack>
                    </Grid>}
                    {user && <Grid item xs={7}>
                        <Stack
                            direction="column"
                            spacing={4}
                            alignItems="center"
                        >
                            <Stack
                                direction="row"
                                spacing={4}
                                sx={{ width: '100%' }}
                            >
                                <Button
                                    variant='contained'
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
                            <Card>
                                <CardHeader
                                    subheader="Loan End Time"
                                />
                                <CardContent>
                                    <LocalizationProvider dateAdapter={AdapterDayjs}>
                                        <Stack
                                            direction="row"
                                            spacing={6}
                                        >
                                            <DateCalendar
                                                value={dateTime}
                                                onChange={(newValue) => setDateTime(prev => prev.set('date', newValue.date()))}
                                                disablePast
                                                sx={{ overflow: 'visible' }}
                                            />
                                            <MultiSectionDigitalClock
                                                value={dateTime}
                                                onChange={(newValue) => setDateTime(prev => prev.set('hour', newValue.hour()).set('minute', newValue.minute()))}
                                                ampm={false}
                                                sx={{ height: 232 }}
                                            />
                                        </Stack>
                                    </LocalizationProvider>
                                </CardContent>
                                <CardActions disableSpacing>
                                    <Button
                                        variant="contained"
                                        disabled={loading || !cart.length}
                                        onClick={async () => {
                                            setLoading(true);
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
                                                setLoading(false);
                                            }
                                        }}
                                    >
                                        Begin Loan
                                    </Button>
                                </CardActions>
                            </Card>
                        </Stack>
                    </Grid>}
                </Grid>
                <Button
                    onClick={() => navigate('/')}
                    variant="outlined"
                    color="error"
                    sx={{ position: 'fixed', bottom: 5 }}
                >
                    Cancel
                </Button>
            </Stack>
        </Box>
    );
}
