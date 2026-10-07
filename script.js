/* =====================================================
   COLDCHAIN GUARD
   Review 01 - Static Website
   ===================================================== */


/* ================= DATE ================= */

function updateDate() {

    const dateElement = document.getElementById("currentDate");

    const now = new Date();

    dateElement.textContent = now.toLocaleDateString(
        "en-IN",
        {
            day: "2-digit",
            month: "short",
            year: "numeric"
        }
    );
}

updateDate();


/* ================= MAP ================= */

let map;
let routeLine;
let markers = [];

const routeCoordinates = [
    [11.0168, 76.9558], // Coimbatore
    [11.6643, 78.1460], // Salem
    [11.9401, 79.4861], // Villupuram
    [13.0827, 80.2707]  // Chennai
];


function initializeMap() {

    map = L.map("map").setView(
        [11.9, 78.8],
        7
    );

    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution:
                '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);


    routeLine = L.polyline(
        routeCoordinates,
        {
            color: "#2563eb",
            weight: 5,
            opacity: .8
        }
    ).addTo(map);


    const locations = [
        {
            name: "Coimbatore",
            status: "Origin",
            temp: "4.2°C",
            color: "#0f9f6e"
        },

        {
            name: "Salem",
            status: "Temperature Excursion",
            temp: "9.8°C",
            color: "#dc3d4b"
        },

        {
            name: "Villupuram",
            status: "Current Location",
            temp: "7.1°C",
            color: "#2563eb"
        },

        {
            name: "Chennai",
            status: "Destination",
            temp: "Expected 5°C",
            color: "#758096"
        }
    ];


    locations.forEach(
        (location, index) => {

            const marker = L.circleMarker(
                routeCoordinates[index],
                {
                    radius: 9,
                    fillColor: location.color,
                    color: "white",
                    weight: 3,
                    fillOpacity: 1
                }
            ).addTo(map);


            marker.bindPopup(`
                <strong>${location.name}</strong><br>
                ${location.status}<br>
                Temperature: ${location.temp}
            `);


            markers.push(marker);
        }
    );


    map.fitBounds(
        routeLine.getBounds(),
        {
            padding: [30, 30]
        }
    );
}


window.addEventListener(
    "load",
    initializeMap
);


function centerMap() {

    if (map && routeLine) {

        map.fitBounds(
            routeLine.getBounds(),
            {
                padding: [30, 30]
            }
        );
    }
}


/* ================= SHIPMENT ================= */

function changeShipment() {

    const selected =
        document.getElementById(
            "shipmentSelect"
        ).value;


    const temperatureElement =
        document.getElementById(
            "temperatureShipment"
        );


    if (selected === "CC-2026-001") {

        temperatureElement.textContent =
            "CC-2026-001";

        showToast(
            "Loaded vaccine shipment CC-2026-001"
        );

    }

    else if (selected === "CC-2026-002") {

        temperatureElement.textContent =
            "CC-2026-002";

        showToast(
            "Loaded dairy shipment CC-2026-002"
        );

    }

    else {

        temperatureElement.textContent =
            "CC-2026-003";

        showToast(
            "Loaded frozen food shipment CC-2026-003"
        );
    }
}


/* ================= SENSOR SIMULATION ================= */

let currentTemperature = 7.1;


function generateSensorReading() {

    const randomChange =
        (Math.random() * 5) - 2;


    currentTemperature =
        Math.max(
            -2,
            Math.min(
                14,
                currentTemperature + randomChange
            )
        );


    currentTemperature =
        Number(
            currentTemperature.toFixed(1)
        );


    updateTemperatureUI();

    updateTemperatureTime();
}


function updateTemperatureUI() {

    const tempElement =
        document.getElementById(
            "currentTemperature"
        );


    const statusElement =
        document.getElementById(
            "temperatureStatus"
        );


    const marker =
        document.getElementById(
            "temperatureMarker"
        );


    tempElement.textContent =
        currentTemperature.toFixed(1);


    /*
        Vaccine safe range:
        2°C - 8°C
    */


    if (
        currentTemperature >= 2 &&
        currentTemperature <= 8
    ) {

        statusElement.textContent =
            "● NORMAL";

        statusElement.className =
            "badge green-badge";

        marker.style.background =
            "#2563eb";

        showToast(
            `Temperature normal: ${currentTemperature}°C`
        );

    }

    else if (
        currentTemperature > 8 &&
        currentTemperature <= 10
    ) {

        statusElement.textContent =
            "● WARNING";

        statusElement.className =
            "badge yellow-badge";

        marker.style.background =
            "#e7a52a";

        showToast(
            `Warning: ${currentTemperature}°C`
        );

    }

    else {

        statusElement.textContent =
            "● CRITICAL";

        statusElement.className =
            "badge red-badge";

        marker.style.background =
            "#dc3d4b";

        showToast(
            `Critical temperature: ${currentTemperature}°C`
        );
    }


    /*
        Convert temperature
        approximately into
        meter position.
    */

    let percentage =
        ((currentTemperature + 20) / 40) * 100;


    percentage =
        Math.max(
            0,
            Math.min(
                100,
                percentage
            )
        );


    marker.style.left =
        `${percentage}%`;
}


function updateTemperatureTime() {

    const timeElement =
        document.getElementById(
            "lastUpdated"
        );


    timeElement.textContent =
        new Date().toLocaleTimeString(
            "en-IN",
            {
                hour: "2-digit",
                minute: "2-digit"
            }
        );


    const timelineTemp =
        document.getElementById(
            "timelineTemp"
        );


    timelineTemp.textContent =
        `${new Date().toLocaleTimeString(
            "en-IN",
            {
                hour: "2-digit",
                minute: "2-digit"
            }
        )} • ${currentTemperature}°C`;
}


/* ================= MODALS ================= */

const modal =
    document.getElementById("modal");

const modalContent =
    document.getElementById("modalContent");


function openModal(content) {

    modalContent.innerHTML =
        content;

    modal.classList.add("show");
}


function closeModal() {

    modal.classList.remove("show");
}


modal.addEventListener(
    "click",
    function(event) {

        if (event.target === modal) {
            closeModal();
        }

    }
);


/* ================= RISK DETAILS ================= */

function openRiskDetails() {

    openModal(`

        <h2>Shipment Risk Assessment</h2>

        <p>
            The risk score evaluates temperature
            deviations, route delays, excursions
            and handling conditions.
        </p>

        <div class="modal-data">

            <div>
                <span>Temperature Risk</span>
                <strong>24 / 40</strong>
            </div>

            <div>
                <span>Route Delay</span>
                <strong>8 / 25</strong>
            </div>

            <div>
                <span>Temperature Excursions</span>
                <strong>10 / 20</strong>
            </div>

            <div>
                <span>Handling Risk</span>
                <strong>0 / 15</strong>
            </div>

            <div>
                <span>Total Risk Score</span>
                <strong>42 / 100 — MEDIUM</strong>
            </div>

        </div>

    `);
}


/* ================= NOTIFICATIONS ================= */

function showNotifications() {

    openModal(`

        <h2>Notifications</h2>

        <div class="modal-data">

            <div>
                <span>Critical</span>
                <strong>1 temperature alert</strong>
            </div>

            <div>
                <span>Warning</span>
                <strong>1 route delay</strong>
            </div>

            <div>
                <span>Information</span>
                <strong>1 checkpoint reminder</strong>
            </div>

        </div>

    `);
}


/* ================= ALERTS ================= */

function viewAllAlerts() {

    openModal(`

        <h2>Temperature Alerts</h2>

        <p>
            Recent temperature events detected
            by the ColdChain Guard monitoring engine.
        </p>

        <div class="modal-data">

            <div>
                <span>CC-2026-004</span>
                <strong>12.1°C — Critical</strong>
            </div>

            <div>
                <span>CC-2026-001</span>
                <strong>9.8°C — Warning</strong>
            </div>

            <div>
                <span>CC-2026-002</span>
                <strong>3.4°C — Normal</strong>
            </div>

        </div>

    `);
}


/* ================= INCIDENT ================= */

function viewIncident(id) {

    let title = "";
    let description = "";

    if (id === "INC-001") {

        title =
            "Temperature Excursion — INC-001";

        description =
            "Shipment CC-2026-004 reached 12.1°C, exceeding the permitted range. Manual inspection is recommended.";

    }

    else if (id === "INC-002") {

        title =
            "Route Delay — INC-002";

        description =
            "Shipment CC-2026-001 is currently 24 minutes behind its planned schedule.";

    }

    else {

        title =
            "Checkpoint Missed — INC-003";

        description =
            "Shipment CC-2026-003 has not reported at the expected checkpoint.";

    }


    openModal(`

        <h2>${title}</h2>

        <p>${description}</p>

        <div class="modal-data">

            <div>
                <span>Incident ID</span>
                <strong>${id}</strong>
            </div>

            <div>
                <span>Status</span>
                <strong>Open</strong>
            </div>

            <div>
                <span>Recommended Action</span>
                <strong>Investigate shipment</strong>
            </div>

        </div>

    `);
}


/* ================= NEW SHIPMENT ================= */

function openShipmentModal() {

    openModal(`

        <h2>Create New Shipment</h2>

        <p>
            Review 01 prototype form.
            Database integration will be added in Stage 2.
        </p>

        <div class="modal-data">

            <div>
                <span>Shipment ID</span>
                <strong>CC-2026-005</strong>
            </div>

            <div>
                <span>Product</span>
                <strong>Vaccines</strong>
            </div>

            <div>
                <span>Origin</span>
                <strong>Coimbatore</strong>
            </div>

            <div>
                <span>Destination</span>
                <strong>Chennai</strong>
            </div>

        </div>

        <button
            class="primary-btn"
            style="margin-top:18px;width:100%"
            onclick="closeModal();showToast('Prototype shipment created')"
        >
            Create Shipment
        </button>

    `);
}


/* ================= REPORT ================= */

function generateReport() {

    openModal(`

        <h2>Shipment Health Report</h2>

        <p>
            ColdChain Guard evaluates the complete
            transportation journey to determine
            shipment safety.
        </p>

        <div class="modal-data">

            <div>
                <span>Shipment</span>
                <strong>CC-2026-001</strong>
            </div>

            <div>
                <span>Journey</span>
                <strong>Coimbatore → Chennai</strong>
            </div>

            <div>
                <span>Temperature Excursions</span>
                <strong>1</strong>
            </div>

            <div>
                <span>Maximum Temperature</span>
                <strong>9.8°C</strong>
            </div>

            <div>
                <span>Route Delay</span>
                <strong>24 minutes</strong>
            </div>

            <div>
                <span>Final Risk</span>
                <strong>MEDIUM — 42/100</strong>
            </div>

        </div>

    `);
}


/* ================= TOAST ================= */

function showToast(message) {

    const existing =
        document.querySelector(".toast");

    if (existing) {
        existing.remove();
    }


    const toast =
        document.createElement("div");


    toast.className = "toast";

    toast.textContent =
        message;


    toast.style.position =
        "fixed";

    toast.style.bottom =
        "25px";

    toast.style.right =
        "25px";

    toast.style.background =
        "#101a2d";

    toast.style.color =
        "white";

    toast.style.padding =
        "12px 17px";

    toast.style.borderRadius =
        "9px";

    toast.style.fontSize =
        "11px";

    toast.style.fontWeight =
        "600";

    toast.style.zIndex =
        "5000";

    toast.style.boxShadow =
        "0 10px 30px rgba(0,0,0,.2)";


    document.body.appendChild(toast);


    setTimeout(
        () => {
            toast.remove();
        },
        2500
    );
}


/* ================= NAVIGATION ================= */

const navLinks =
    document.querySelectorAll(".nav-link");


navLinks.forEach(
    link => {

        link.addEventListener(
            "click",
            () => {

                navLinks.forEach(
                    item =>
                        item.classList.remove(
                            "active"
                        )
                );

                link.classList.add("active");

            }
        );

    }
);
