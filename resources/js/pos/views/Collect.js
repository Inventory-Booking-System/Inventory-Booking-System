import React, { useEffect, useState, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
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
import Search from '../components/Search';
import LoanCard, { LoanItem } from '../../components/LoanCard';
import * as api from '../../api';
import { Typography } from '@mui/material';

dayjs.extend(isToday);
dayjs.extend(isTomorrow);

export default function Collect() {
    const navigate = useNavigate();
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(false);

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

    return (
        <Box sx={{ paddingTop: 5 }}>
            <Stack
                direction="column"
                spacing={2}
                alignItems="center"
            >
                {loading && <CircularProgress />}
                {!loading &&  <Masonry columns={3} spacing={1} sx={{ paddingLeft: 2, paddingRight: 2 }}>
                    {reservations.map((item) => {
                        if (item.status_id !== 1) {
                            return null;
                        }

                        item.assets.map(asset => asset.type = 'asset');
                        item.asset_groups.map(group => group.type = 'group');
                        const lineItems = [...item.asset_groups, ...item.assets];

                        return <Card key={item.id} sx={{ backgroundColor: 'warning.main' }}>
                            <CardActionArea
                                onClick={() => navigate(`/collect/${item.id}`)}
                            >
                                <CardContent>
                                    <Typography variant="body2" color="textPrimary" sx={{ textAlign: 'center' }}>
                                        {item.user.forename} {item.user.surname}
                                    </Typography>
                                    <Typography variant="body2" sx={{ textAlign: 'center' }}>
                                        {formatRelativeDate(item.start_date_time)}
                                    </Typography>
                                </CardContent>
                                <Divider />
                                <CardContent>
                                    <Stack direction="row" spacing={2} justifyContent="center">
                                        {lineItems.map((lineItem, index) => index % 2 === 0 ? <LoanItem key={lineItem.id} item={lineItem} /> : null)}
                                        {lineItems.map((lineItem, index) => !(index % 2 === 0) ? <LoanItem key={lineItem.id} item={lineItem} /> : null)}
                                    </Stack>
                                </CardContent>
                            </CardActionArea>
                        </Card>;
                    })}
                </Masonry>}
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
