import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import PropTypes from 'prop-types';
import dayjs from 'dayjs';
import isToday from 'dayjs/plugin/isToday';
import isTomorrow from 'dayjs/plugin/isTomorrow';
import Masonry from '@mui/lab/Masonry';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CircularProgress from '@mui/material/CircularProgress';
import CardActionArea from '@mui/material/CardActionArea';
import Divider from '@mui/material/Divider';
import { LoanItem } from '../../components/LoanCard';
import * as api from '../../api';
import { Status } from '../../api/loans';
import { Typography } from '@mui/material';

dayjs.extend(isToday);
dayjs.extend(isTomorrow);

function formatRelativeDate(input) {
    const parsedDate = dayjs(input, 'DD MMM YYYY HH:mm');
    if (parsedDate.isToday()) {
        return `Today at ${parsedDate.format('HH:mm')}`;
    } else if (parsedDate.isTomorrow()) {
        return `Tomorrow at ${parsedDate.format('HH:mm')}`;
    } else {
        return parsedDate.format('DD MMM YYYY HH:mm');
    }
}

function Item({ item }) {
    const navigate = useNavigate();

    if (item.status_id !== Status.RESERVATION && item.status_id !== Status.SETUP) {
        return null;
    }

    item.assets.map(asset => asset.type = 'asset');
    item.asset_groups.map(group => group.type = 'group');
    const lineItems = [...item.asset_groups, ...item.assets];

    return <Card key={item.id} sx={{ backgroundColor: item.status_id === Status.RESERVATION ? 'warning.main' : 'grey.500' }}>
        <CardActionArea
            onClick={() => navigate(`/collect/${item.id}`)}
        >
            <CardContent>
                <Typography variant="body2" color="#000" sx={{ textAlign: 'center' }}>
                    {item.user.forename} {item.user.surname} : {formatRelativeDate(item.start_date_time)}
                </Typography>
                {item.setup?.location?.name && <Typography variant="body2" color="#000" sx={{ textAlign: 'center' }}>
                    {item.setup?.location?.name}
                </Typography>}
                {item.details && <Typography variant="body2" color="#000" sx={{ textAlign: 'center' }}>
                    {item.details}
                </Typography>}
            </CardContent>
            {lineItems.length > 0 && <>
                <Divider />
                <CardContent>
                    <Stack direction="row" spacing={2} justifyContent="center">
                        {lineItems.map((lineItem, index) => index % 2 === 0 ? <LoanItem key={lineItem.id} item={lineItem} textColor="#000" /> : null)}
                        {lineItems.map((lineItem, index) => !(index % 2 === 0) ? <LoanItem key={lineItem.id} item={lineItem} textColor="#000" /> : null)}
                    </Stack>
                </CardContent>
            </>}
        </CardActionArea>
    </Card>;
}

Item.propTypes = {
    item: PropTypes.object.isRequired
};

export default function Collect() {
    const navigate = useNavigate();
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const timeout = setTimeout(() => {
            navigate('/');
        }, 60000);
        return () => clearTimeout(timeout);
    }, [navigate]);

    useEffect(() => {
        setLoading(true);
        function get() {
            api.loans.getReservations()
                .then(setReservations)
                .then(() => setLoading(false));
        }
        const interval = setInterval(() => {
            get();
        }, 5000);
        get();
        return () => clearInterval(interval);
    }, []);

    return (
        <Box sx={{ paddingTop: 5 }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
            >
                {loading && <CircularProgress />}
                {!loading &&  <Masonry columns={3} spacing={1} sx={{ paddingLeft: 2, paddingRight: 2 }}>
                    {reservations.filter(item => dayjs(item.start_date_time, 'DD MMM YYYY HH:mm').isToday()).map((item) => <Item item={item} key={item.id} /> )}
                </Masonry>}
                {!loading &&  <Masonry columns={3} spacing={1} sx={{ paddingLeft: 2, paddingRight: 2 }}>
                    {reservations.filter(item => !dayjs(item.start_date_time, 'DD MMM YYYY HH:mm').isToday()).map((item) => <Item item={item} key={item.id} /> )}
                </Masonry>}
                {!loading && reservations.length === 0 && <Typography variant="h5">There are no reservations</Typography>}
                <Button
                    onClick={() => navigate('/')}
                    variant="outlined"
                    sx={{ position: 'fixed', bottom: 5 }}
                >
                    Start again
                </Button>
            </Stack>
        </Box>
    );
}
