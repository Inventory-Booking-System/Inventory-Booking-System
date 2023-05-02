import React, { useState, useEffect, useMemo } from 'react';
import { createRoot } from 'react-dom/client';
import Masonry from 'masonry-layout';
import PropTypes from 'prop-types';
import '../css/signage.css';

function dateToString() {
    return (new Date()).toLocaleString('en-GB', { dateStyle: 'full', timeStyle: 'medium' });
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

function Entry({ item }) {
    const { assets, details, status_id, start_date_time, end_date_time, user, setup } = item;

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
                                {assets.map((asset, index) => index % 2 === 0 ? (asset.pivot.returned ? <div key={index} style={{ textDecoration: 'line-through' }}>{asset.name} ({asset.tag})</div> : <div key={index}>{asset.name} ({asset.tag})</div>) : null)}
                            </div>
                        </div>
                        <div className="col-6">
                            <div style={{ listStyleType: 'none' }} className="text-center">
                                {assets.map((asset, index) => !(index % 2 === 0) ? (asset.pivot.returned ? <div key={index} style={{ textDecoration: 'line-through' }}>{asset.name} ({asset.tag})</div> : <div key={index}>{asset.name} ({asset.tag})</div>) : null)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

Entry.propTypes = {
    item: PropTypes.shape({
        assets: PropTypes.array,
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
        }, 10000);

        return () => clearInterval(intervalId);
    }, []);

    useEffect(() => {
        masonry.reloadItems();
        masonry.layout();
    }, [masonry, items]);

    return items.map((item, index) => <Entry item={item} key={index} />);
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
