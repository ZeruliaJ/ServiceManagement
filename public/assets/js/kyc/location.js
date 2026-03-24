function isCreateMode() {
    return !($('#is_edit').length && $('#is_edit').val() === '1');
}
function fetchGeolocation(onSuccess = null, onError = null)
{
    if (navigator.geolocation)
    {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;

                if (typeof onSuccess === 'function') {
                    onSuccess(position);
                }
            },
            function(error) {
                console.error("Error fetching geolocation: ", error);

                if (typeof onError === 'function') {
                    onError(error);
                }
            }
        );
    }
    else
    {
        Swal.fire({
            title: window.Lang.oops,
            text: window.Lang.geolocation_not_supported,
            icon: "warning"
        });

        if (typeof onError === 'function') {
            onError();
        }
    }
}

function fetchGeolocationFromButton(btn)
{
    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerText = window.Lang.fetching_location;

    fetchGeolocation(
        () => {
            btn.innerText = window.Lang.location_fetched;
            btn.disabled = false;
            btn.innerText = originalText;
            Swal.fire({
                title: window.Lang.location_fetched,
                text: window.Lang.location_updated_successfully,
                icon: "success"
            });
        },
        () => {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    );
}

function initMap()
{
    var latitude  = parseFloat(document.getElementById('latitude')?.value);
    var longitude = parseFloat(document.getElementById('longitude')?.value);
    var businessName = document.getElementById('business_name')?.value || 'Business Location';

    if (isNaN(latitude) || isNaN(longitude)) {
        console.error('Invalid latitude or longitude', latitude, longitude);
        return;
    }

    // Initialize Mapbox map with satellite-streets style for better view
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/satellite-streets-v12',
        center: [longitude, latitude],
        zoom: 17,
        pitch: 45,
        bearing: 0
    });

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl(), 'top-right');

    // Add fullscreen control
    map.addControl(new mapboxgl.FullscreenControl(), 'top-right');

    // Create custom marker element
    const markerEl = document.createElement('div');
    markerEl.className = 'custom-marker';
    markerEl.innerHTML = `
        <div style="
            background: linear-gradient(135deg, #ED1B24 0%, #273D80 100%);
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(237, 27, 36, 0.4);
            position: relative;
            min-width: max-content;
        ">
            <div style="
                color: #fff;
                font-weight: 600;
                font-size: 13px;
                display: flex;
                align-items: center;
                gap: 6px;
            ">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                ${businessName}
            </div>
            <div style="
                position: absolute;
                bottom: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 0;
                border-left: 8px solid transparent;
                border-right: 8px solid transparent;
                border-top: 8px solid #273D80;
            "></div>
        </div>
    `;

    // Add marker with popup
    const popup = new mapboxgl.Popup({
        offset: [0, -10],
        closeButton: true,
        closeOnClick: false
    }).setHTML(`
        <div style="padding: 15px; min-width: 200px;">
            <h6 style="margin: 0 0 10px 0; font-weight: 700; color: #273D80; font-size: 15px;">
                ${businessName}
            </h6>
            <div style="display: flex; align-items: center; gap: 6px; color: #6c757d; font-size: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>${latitude.toFixed(6)}, ${longitude.toFixed(6)}</span>
            </div>
            <a href="https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}"
               target="_blank"
               style="
                   display: inline-flex;
                   align-items: center;
                   gap: 6px;
                   margin-top: 12px;
                   padding: 8px 14px;
                   background: linear-gradient(135deg, #ED1B24 0%, #273D80 100%);
                   color: #fff;
                   text-decoration: none;
                   border-radius: 6px;
                   font-size: 12px;
                   font-weight: 500;
               ">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                </svg>
                Get Directions
            </a>
        </div>
    `);

    new mapboxgl.Marker(markerEl)
        .setLngLat([longitude, latitude])
        .setPopup(popup)
        .addTo(map);

    // Add 3D buildings layer when style loads
    map.on('style.load', () => {
        const layers = map.getStyle().layers;
        const labelLayerId = layers.find(
            (layer) => layer.type === 'symbol' && layer.layout['text-field']
        )?.id;

        map.addLayer(
            {
                'id': '3d-buildings',
                'source': 'composite',
                'source-layer': 'building',
                'filter': ['==', 'extrude', 'true'],
                'type': 'fill-extrusion',
                'minzoom': 15,
                'paint': {
                    'fill-extrusion-color': '#aaa',
                    'fill-extrusion-height': ['get', 'height'],
                    'fill-extrusion-base': ['get', 'min_height'],
                    'fill-extrusion-opacity': 0.6
                }
            },
            labelLayerId
        );
    });
}

if(isCreateMode())
{
    fetchGeolocation();
}
