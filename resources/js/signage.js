import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Masonry from 'masonry-layout';
import LoanCard from './components/LoanCard';
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

    return items.map((item, index) => <LoanCard
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
