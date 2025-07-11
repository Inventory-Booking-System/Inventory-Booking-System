import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Paper from '@mui/material/Paper';
import Keyboard from '../components/Keyboard';
import * as api from '../../api';
import NameSearch from '../components/NameSearch';

export default function Student() {
    const navigate = useNavigate();
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState('');

    useEffect(() => {
        const timeout = setTimeout(() => {
            navigate('/');
        }, 60000);
        return () => clearTimeout(timeout);
    }, [navigate]);

    useEffect(() => {
        setLoading(true);
        api.users.getUsersWithPosAccess()
            .then(users => {
                setUsers(users.map(user => ({
                    userId: user.booking_authoriser_user_id,
                    label: `${user.forename} ${user.surname}`
                })));
            })
            .finally(() => setLoading(false));
    }, []);

    return (
        <Box sx={{ pt: 5, pb: 10, height: '100vh' }}>
            <Stack
                direction="column"
                alignItems="center"
                justifyContent="space-between"
                sx={{ height: '100%' }}
            >
                <NameSearch
                    sx={{ zoom: 1.6 }}
                    name="Enter your Name"
                    onSelect={(user) => navigate(user.label, { state: { user } })}
                    options={users}
                    loading={loading}
                    value={search}
                />
                <Keyboard
                    sx={{ zoom: 1.6 }}
                    onChange={value => {
                        console.log('Keyboard value', value);
                        setSearch(value);
                    }}
                />
            </Stack>
            <Paper
                elevation={3}
                sx={{ position: 'absolute', bottom: 0, left: 0, right: 0, padding: 2 }}
            >
                <Stack
                    direction="row"
                    spacing={2}
                    justifyContent="center"
                >
                    <Button
                        onClick={() => navigate('/')}
                        variant="outlined"
                        size="large"
                    >
                        Start again
                    </Button>
                </Stack>
            </Paper>
        </Box>
    );
}
