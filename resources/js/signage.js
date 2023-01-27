import Masonry from 'masonry-layout';
import { render } from 'preact';
import { useState, useEffect, useMemo } from 'preact/hooks';
import { html } from 'htm/preact';

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

    return html`
        <div class="row">
            <div class="col-6">
                <h1 class="text-center" style="color:white">Inventory Booking System</h1>
            </div>
            <div  class="col-6">
                <h1 class="text-center" style="color:white">${date}</h1>
            </div>
        </div>
    `;
}

function Entry({ item }) {
    const { assets, details, status_id, start_date_time, user, setup } = item;
    
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

    return html`
        <div class="col-md-4">

            <div class="card ${cardClass} w-100">
                <div class="card-header text-center">${user.forename} ${user.surname} : ${start_date_time.split(' ')[3]}</div>
                <div class="card-body p-1 ">
                    <div class="row mb-2">
                        ${setup?.location?.name && html`<div class="col-12 text-center truncate">
                            ${setup.location.name}
                        </div>`}
                        <div class="col-12 text-center truncate">
                            ${details}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div style="list-style-type: none;" class="text-center">
                                ${assets.map((asset, index) => index % 2 === 0 ? (asset.pivot.returned ? html`<div style="text-decoration: line-through;">${asset.name} (${asset.tag})</div>` : html`<div>${asset.name} (${asset.tag})</div>`) : null)}
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="list-style-type: none;" class="text-center">
                                ${assets.map((asset, index) => !(index % 2 === 0) ? (asset.pivot.returned ? html`<div style="text-decoration: line-through;">${asset.name} (${asset.tag})</div>` : html`<div>${asset.name} (${asset.tag})</div>`) : null)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
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
        }, 10000);

        return () => clearInterval(intervalId);
    }, []);

    useEffect(() => {
        masonry.reloadItems();
        masonry.layout();
        
    }, [masonry, items]);

    return html`
        ${items.map((item) => html`<${Entry} item=${item} />`)}
    `;
}

render(html`<${Header} />`, document.querySelector('#header'));
render(html`<${App} />`, document.querySelector('#masonry'));