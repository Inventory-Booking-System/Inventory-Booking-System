import React, { useState, useEffect, useMemo } from 'react';
import { createRoot } from 'react-dom/client';
import Masonry from 'masonry-layout';
import PropTypes from 'prop-types';
import '../css/signage.css';

function dateToString() {
    return (new Date()).toLocaleString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function Header() {
    const [date, setDate] = useState(dateToString());

    useEffect(() => {
        const interval = setInterval(() => {
            setDate(dateToString());
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    return (
        <div className="row">
            <div className="col-6">
                <h1 className="text-center" style={{ color: 'white' }}>Inventory Booking System</h1>
            </div>
            <div  className="col-6">
                <h1 className="text-center" style={{ color: 'white' }}>{date}</h1>
            </div>
        </div>
    );
}

function Entry({ assets = [], groups = [], details, status_id, start_date_time, end_date_time, user, setup }) {
    assets.map(asset => asset.type = 'asset');
    groups.map(group => group.type = 'group');
    const items = [...groups, ...assets];

    const cardClass = useMemo(() => {
        switch(status_id) {
            case 0:
                return 'bg-success';
            case 1:
                return 'bg-warning';
            case 2:
                return 'bg-danger';
            case 3:
                return 'bg-secondary';
        }
    }, [status_id]);

    if (status_id > 3) {
        return null;
    }

    const renderItem = ((item, index) => {
        return (
            <div
                key={index}
                style={{
                    textDecoration: item.pivot.returned ? 'line-through' : undefined,
                    background: item.type === 'group' ? 'rgba(0,0,0,0.2)' : undefined,
                    borderRadius: item.type === 'group' ? '.25rem' : undefined,
                    color: item.type === 'group' ? '#fff' : undefined
                }}
            >
                {item.name} {item.type === 'group' ? `(x${item.pivot.quantity})` : `(${item.tag})`}
            </div>
        );
    });

    return (
        <div className="col-md-4">
            <div className={`card ${cardClass} w-100`}>
                <div className="card-header text-center">{user.forename} {user.surname} : {status_id === 2 ? end_date_time : start_date_time.split(' ')[3]}</div>
                <div className="card-body p-1 ">
                    <div className="row mb-2">
                        {setup?.location?.name && <div className="col-12 text-center truncate">
                            {setup.location.name}
                        </div>}
                        <div className="col-12 text-center truncate">
                            {details}
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-6">
                            <div style={{ listStyleType: 'none' }} className="text-center">
                                {items.map((item, index) => index % 2 === 0 ? renderItem(item, index) : null)}
                            </div>
                        </div>
                        <div className="col-6">
                            <div style={{ listStyleType: 'none' }} className="text-center">
                                {items.map((item, index) => !(index % 2 === 0) ? renderItem(item, index) : null)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

Entry.propTypes = {
    assets: PropTypes.array,
    groups: PropTypes.array,
    details: PropTypes.string,
    status_id: PropTypes.number,
    start_date_time: PropTypes.string,
    end_date_time: PropTypes.string,
    user: PropTypes.shape({
        forename: PropTypes.string,
        surname: PropTypes.string
    }),
    setup: PropTypes.shape({
        location: PropTypes.shape({
            name: PropTypes.string
        })
    })
};

function App() {
    const [masonry] = useState(new Masonry(document.querySelector('#masonry'), {
        percentPosition: true,
        transitionDuration: 0
    }));
    const [items, setItems] = useState([]);

    useEffect(() => {
        async function get() {
            const resp = await fetch('/api/signage');
            const data = await resp.json();
            setItems(data);
            if (document.querySelector('#loading')) {
                document.querySelector('#loading').remove();
            }
        }
        get();

        const intervalId = setInterval(() => {
            get();
        }, 5000);

        return () => clearInterval(intervalId);
    }, []);

    useEffect(() => {
        masonry.reloadItems();
        masonry.layout();
    }, [masonry, items]);

    return items.map((item, index) => <Entry
        assets={item.assets}
        groups={item.asset_groups}
        details={item.details}
        status_id={item.status_id}
        start_date_time={item.start_date_time}
        end_date_time={item.end_date_time}
        user={item.user}
        setup={item.setup}
        key={index}
    />);
}

createRoot(document.getElementById('header')).render(<Header />);
createRoot(document.getElementById('masonry')).render(<App />);

let scrollDirection = 1;
async function pageScroll() {
    window.scrollBy(0, scrollDirection);
    if ( (window.scrollY === 0) || (window.innerHeight + window.scrollY) >= document.body.offsetHeight ) {
        scrollDirection = -1 * scrollDirection;

        await new Promise(resolve => {
            setTimeout(resolve, 5000);
        });
    }
    setTimeout(pageScroll, 10);
}
pageScroll();
