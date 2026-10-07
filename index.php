<?php

require_once "db.php";

/* Active shipments */
$activeResult = $conn->query("
    SELECT COUNT(*) AS total
    FROM shipments
    WHERE status = 'In Transit'
");

$activeShipments = $activeResult->fetch_assoc()['total'];


/* Latest temperature */
$tempResult = $conn->query("
    SELECT temperature
    FROM temperature_readings
    ORDER BY recorded_at DESC
    LIMIT 1
");

$latestTemperature = 0;

if ($tempResult && $tempResult->num_rows > 0) {
    $latestTemperature =
        $tempResult->fetch_assoc()['temperature'];
}


/* Open incidents */
$incidentResult = $conn->query("
    SELECT COUNT(*) AS total
    FROM incidents
    WHERE status = 'Open'
");

$openIncidents =
    $incidentResult->fetch_assoc()['total'];


/* Total shipments */
$totalResult = $conn->query("
    SELECT COUNT(*) AS total
    FROM shipments
");

$totalShipments =
    $totalResult->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ColdChain Guard</title>

<link rel="stylesheet"
href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial,Helvetica,sans-serif;
    background:#f5f7fb;
    color:#17233c;
}

/* SIDEBAR */

.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:245px;
    height:100vh;
    background:#101b30;
    color:white;
    padding:25px 16px;
    z-index:1000;
}

.logo{
    display:flex;
    align-items:center;
    gap:12px;
    padding:0 10px 30px;
}

.logo-icon{
    width:48px;
    height:48px;
    border-radius:14px;
    background:#2563eb;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

.logo h2{
    font-size:20px;
}

.logo span{
    font-size:10px;
    letter-spacing:3px;
    color:#8ea0bd;
}

.menu{
    margin-top:15px;
}

.menu-item{
    padding:15px 17px;
    margin:6px 0;
    border-radius:11px;
    color:#a9b8d0;
    cursor:pointer;
    transition:.2s;
}

.menu-item:hover,
.menu-item.active{
    background:#1d2d4b;
    color:white;
}

.menu-item.active{
    border-left:3px solid #3b82f6;
}

.status{
    position:absolute;
    bottom:40px;
    left:18px;
    right:18px;
    background:#192640;
    padding:15px;
    border-radius:12px;
    font-size:12px;
}

.dot{
    width:9px;
    height:9px;
    display:inline-block;
    border-radius:50%;
    background:#17c983;
    margin-right:7px;
}

.status p{
    color:#8092b0;
    margin-top:6px;
}

/* MAIN */

.main{
    margin-left:245px;
    padding:30px 38px;
}

.page{
    display:none;
}

.page.active{
    display:block;
}

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.label{
    font-size:11px;
    color:#2563eb;
    font-weight:bold;
    letter-spacing:3px;
    margin-bottom:7px;
}

h1{
    font-size:30px;
}

select{
    padding:11px 15px;
    border:1px solid #dce4ef;
    border-radius:9px;
    background:white;
}

button{
    border:none;
    background:#2563eb;
    color:white;
    padding:11px 17px;
    border-radius:9px;
    cursor:pointer;
    font-weight:bold;
}

button:hover{
    background:#1d4ed8;
}

/* CARDS */

.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:22px;
}

.card{
    background:white;
    border:1px solid #e3e9f2;
    border-radius:16px;
    padding:20px;
    box-shadow:0 3px 12px rgba(20,40,80,.04);
}

.card-title{
    color:#75849d;
    font-size:11px;
    letter-spacing:1px;
    margin-bottom:10px;
}

.card-value{
    font-size:27px;
    font-weight:bold;
}

.safe{
    color:#0ca66b;
}

.warning{
    color:#e58a00;
}

.danger{
    color:#e53935;
}

.muted{
    color:#71809a;
    font-size:12px;
    margin-top:7px;
}

/* DASHBOARD */

.dashboard-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
}

.activity{
    min-height:300px;
}

.activity-row{
    display:flex;
    justify-content:space-between;
    padding:16px 0;
    border-bottom:1px solid #edf1f6;
}

.badge{
    padding:5px 9px;
    border-radius:15px;
    font-size:10px;
    font-weight:bold;
}

.badge-green{
    background:#e8faf3;
    color:#079b61;
}

.badge-red{
    background:#ffeded;
    color:#d83333;
}

/* MAP */

.tracking-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
}

.map-card{
    padding:0;
    overflow:hidden;
}

.map-head{
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.live{
    background:#e8faf3;
    color:#079b61;
    padding:7px 12px;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
}

#map{
    height:510px;
    width:100%;
}

/* TIMELINE */

.timeline{
    padding:23px;
}

.timeline h2{
    margin-bottom:25px;
}

.event{
    display:flex;
    gap:14px;
    margin-bottom:26px;
    position:relative;
}

.event:not(:last-child)::after{
    content:"";
    position:absolute;
    left:14px;
    top:30px;
    height:45px;
    width:2px;
    background:#dce4ef;
}

.event-icon{
    width:30px;
    height:30px;
    min-width:30px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:12px;
    z-index:2;
}

.green{
    background:#16b878;
}

.blue{
    background:#2563eb;
}

.red{
    background:#ef4444;
}

.gray{
    background:#8390a5;
}

.event h3{
    font-size:14px;
    margin-bottom:4px;
}

.event p{
    font-size:12px;
    color:#6f7e96;
}

.event small{
    color:#96a2b5;
    font-size:10px;
}

/* INFO */

.info-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
}

.info-box{
    background:#f7f9fc;
    padding:15px;
    border-radius:10px;
}

.info-box span{
    display:block;
    font-size:10px;
    color:#77869f;
    margin-bottom:7px;
}

/* TEMPERATURE */

.temperature-big{
    text-align:center;
    padding:25px;
}

.temperature-number{
    font-size:55px;
    font-weight:bold;
    color:#2563eb;
}

.range{
    margin-top:10px;
    color:#74829a;
}

.temp-bar{
    height:12px;
    background:#e7edf5;
    border-radius:10px;
    margin:20px 0;
    overflow:hidden;
}

.temp-fill{
    height:100%;
    width:64%;
    background:#2563eb;
    border-radius:10px;
    transition:.5s;
}

/* TABLE */

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:15px;
    text-align:left;
    border-bottom:1px solid #edf1f6;
    font-size:13px;
}

th{
    color:#708099;
    font-size:11px;
}

/* INCIDENT */

.incident{
    padding:17px;
    background:#fff5f5;
    border-left:4px solid #ef4444;
    border-radius:8px;
    margin-bottom:12px;
}

.incident strong{
    color:#c72c2c;
}

.incident p{
    font-size:12px;
    color:#6e7c92;
    margin-top:5px;
}

/* RESPONSIVE */

@media(max-width:1000px){

    .sidebar{
        width:190px;
    }

    .main{
        margin-left:190px;
        padding:20px;
    }

    .cards{
        grid-template-columns:repeat(2,1fr);
    }

    .tracking-grid,
    .dashboard-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){

    .sidebar{
        display:none;
    }

    .main{
        margin-left:0;
    }

    .cards,
    .info-grid{
        grid-template-columns:1fr;
    }

}

</style>
</head>

<body>
    
  <nav style="
    background:#10213d;
    padding:15px 25px;
    display:flex;
    gap:20px;
    flex-wrap:wrap;
">

    <a href="index.php"
       style="color:white;text-decoration:none;">
       Dashboard
    </a>

    <a href="shipments.php"
       style="color:white;text-decoration:none;">
       Shipments
    </a>

    <a href="add_shipment.php"
       style="color:white;text-decoration:none;">
       Add Shipment
    </a>

    <a href="temperature.php"
       style="color:white;text-decoration:none;">
       Temperature
    </a>

    <a href="tracking.php"
       style="color:white;text-decoration:none;">
       Route Tracking
    </a>

    <a href="incidents.php"
       style="color:white;text-decoration:none;">
       Incidents & Alerts
    </a>

</nav>  

<!-- SIDEBAR -->

<aside class="sidebar">

<div class="logo">

<div class="logo-icon">❄</div>

<div>
<h2>ColdChain</h2>
<span>GUARD</span>
</div>

</div>

<div class="menu">

<div class="menu-item active"
onclick="showPage('dashboard',this)">
▦ &nbsp; Dashboard
</div>

<div class="menu-item"
onclick="showPage('tracking',this)">
◈ &nbsp; Live Tracking
</div>

<div class="menu-item"
onclick="showPage('temperature',this)">
♨ &nbsp; Temperature
</div>

<div class="menu-item"
onclick="showPage('shipments',this)">
▣ &nbsp; Shipments
</div>

<div class="menu-item"
onclick="showPage('incidents',this)">
⚠ &nbsp; Incidents
</div>

<div class="menu-item"
onclick="showPage('reports',this)">
▤ &nbsp; Reports
</div>

</div>

<div class="status">

<span class="dot"></span>
<strong>System Online</strong>

<p>IoT sensor simulation active</p>

</div>

</aside>


<!-- MAIN -->

<main class="main">


<!-- ================= DASHBOARD ================= -->

<section id="dashboard" class="page active">

<div class="top">

<div>
<div class="label">COLD CHAIN MONITORING</div>
<h1>Dashboard</h1>
</div>

<select>
<option>CC-2026-001 • Vaccines</option>
</select>

</div>


<div class="cards">

<div class="card">
<div class="card-title">ACTIVE SHIPMENTS</div>
<div class="card-value"> <?php echo $activeShipments; ?></div>
<div class="muted">Currently in transit</div>
</div>

<div class="card">
<div class="card-title">TEMPERATURE</div>
<div class="card-value safe" id="dashboardTemp"> <?php echo number_format($latestTemperature, 1); ?>°C</div>
<div class="muted">Safe range: 2–8°C</div>
</div>

<div class="card">
<div class="card-title">ALERTS</div>
<div class="card-value danger"><?php echo str_pad($openIncidents, 2, "0", STR_PAD_LEFT); ?></div>
<div class="muted">Requires attention</div>
</div>

<div class="card">
<div class="card-title">ON-TIME DELIVERY</div>
<div class="card-value">96%</div>
<div class="muted">Last 30 days</div>
</div>

</div>


<div class="dashboard-grid">

<div class="card activity">

<h2>Recent Activity</h2>

<div class="activity-row">
<span>Shipment departed Coimbatore</span>
<span class="badge badge-green">08:00 AM</span>
</div>

<div class="activity-row">
<span>Checkpoint reached Salem</span>
<span class="badge badge-green">12:40 PM</span>
</div>

<div class="activity-row">
<span>Temperature excursion detected</span>
<span class="badge badge-red">02:20 PM</span>
</div>

<div class="activity-row">
<span>Vehicle reached Villupuram zone</span>
<span class="badge badge-green">04:10 PM</span>
</div>

</div>


<div class="card">

<h2>Current Shipment</h2>

<br>

<p class="muted">Shipment ID</p>
<strong>CC-2026-001</strong>

<br><br>

<p class="muted">Product</p>
<strong>Vaccines</strong>

<br><br>

<p class="muted">Destination</p>
<strong>Chennai</strong>

</div>

</div>

</section>


<!-- ================= TRACKING ================= -->

<section id="tracking" class="page">

<div class="top">

<div>
<div class="label">LIVE LOCATION</div>
<h1>Journey Tracking</h1>
</div>

<button onclick="centerRoute()">
Center Route
</button>

</div>


<div class="tracking-grid">


<div class="card map-card">

<div class="map-head">

<div>
<div class="label">LIVE VEHICLE POSITION</div>
<h2>Transportation Route</h2>
</div>

<div class="live">● LIVE</div>

</div>

<div id="map"></div>

</div>


<div class="card timeline">

<h2>Shipment Timeline</h2>


<div class="event">

<div class="event-icon green">✓</div>

<div>
<h3>Shipment Loaded</h3>
<p>Coimbatore Warehouse</p>
<small>08:00 AM • 4.2°C</small>
</div>

</div>


<div class="event">

<div class="event-icon green">✓</div>

<div>
<h3>Checkpoint Reached</h3>
<p>Salem</p>
<small>12:40 PM • 6.3°C</small>
</div>

</div>


<div class="event">

<div class="event-icon red">!</div>

<div>
<h3>Temperature Excursion</h3>
<p>Salem Highway</p>
<small>02:20 PM • 9.8°C</small>
</div>

</div>


<div class="event">

<div class="event-icon blue">●</div>

<div>
<h3>Vehicle In Transit</h3>
<p>Near Villupuram</p>
<small>04:10 PM • 7.1°C</small>
</div>

</div>


<div class="event">

<div class="event-icon gray">4</div>

<div>
<h3>Destination</h3>
<p>Chennai Distribution Center</p>
<small>ETA 08:15 PM</small>
</div>

</div>

</div>

</div>


<div class="card" style="margin-top:20px;padding:20px">

<div class="label">ACTIVE SHIPMENT</div>

<h2>CC-2026-001</h2>

<br>

<div class="info-grid">

<div class="info-box">
<span>PRODUCT</span>
<strong>Vaccines</strong>
</div>

<div class="info-box">
<span>VEHICLE</span>
<strong>TN-38-AB-2486</strong>
</div>

<div class="info-box">
<span>DRIVER</span>
<strong>Arun Kumar</strong>
</div>

<div class="info-box">
<span>DISTANCE</span>
<strong>485 km</strong>
</div>

</div>

</div>

</section>


<!-- ================= TEMPERATURE ================= -->

<section id="temperature" class="page">

<div class="top">

<div>
<div class="label">ENVIRONMENT MONITORING</div>
<h1>Temperature Monitoring</h1>
</div>

<div class="live">● SENSOR ONLINE</div>

</div>


<div class="cards">

<div class="card temperature-big">

<div class="card-title">CURRENT TEMPERATURE</div>

<div class="temperature-number"
id="temperatureValue">
7.1°C
</div>

<div class="range">
Safe range: 2°C – 8°C
</div>

<div class="temp-bar">
<div class="temp-fill" id="tempFill"></div>
</div>

<strong class="safe" id="tempStatus">
SAFE
</strong>

</div>


<div class="card">

<div class="card-title">MINIMUM</div>
<div class="card-value">3.8°C</div>
<div class="muted">Today's reading</div>

</div>


<div class="card">

<div class="card-title">MAXIMUM</div>
<div class="card-value danger">9.8°C</div>
<div class="muted">Excursion detected</div>

</div>


<div class="card">

<div class="card-title">SAFE DURATION</div>
<div class="card-value">96%</div>
<div class="muted">Journey within range</div>

</div>

</div>


<div class="card">

<h2>Sensor Information</h2>

<br>

<table>

<tr>
<th>Sensor ID</th>
<th>Location</th>
<th>Temperature</th>
<th>Status</th>
</tr>

<tr>
<td>TEMP-001</td>
<td>Refrigerated Container</td>
<td id="tableTemp">7.1°C</td>
<td><span class="badge badge-green">NORMAL</span></td>
</tr>

<tr>
<td>TEMP-002</td>
<td>Container Door</td>
<td>6.8°C</td>
<td><span class="badge badge-green">NORMAL</span></td>
</tr>

</table>

</div>

</section>


<!-- ================= SHIPMENTS ================= -->

<section id="shipments" class="page">

<div class="top">

<div>
<div class="label">LOGISTICS</div>
<h1>Shipments</h1>
</div>

<button>+ New Shipment</button>

</div>


<div class="card">

<table>

<tr>
<th>Shipment ID</th>
<th>Product</th>
<th>Origin</th>
<th>Destination</th>
<th>Status</th>
<th>Temperature</th>
</tr>

<tr>
<td>CC-2026-001</td>
<td>Vaccines</td>
<td>Coimbatore</td>
<td>Chennai</td>
<td><span class="badge badge-green">IN TRANSIT</span></td>
<td>7.1°C</td>
</tr>

<tr>
<td>CC-2026-002</td>
<td>Insulin</td>
<td>Madurai</td>
<td>Chennai</td>
<td><span class="badge badge-green">IN TRANSIT</span></td>
<td>5.4°C</td>
</tr>

<tr>
<td>CC-2026-003</td>
<td>Blood Samples</td>
<td>Salem</td>
<td>Coimbatore</td>
<td><span class="badge badge-green">DELIVERED</span></td>
<td>4.8°C</td>
</tr>

<tr>
<td>CC-2026-004</td>
<td>Vaccines</td>
<td>Chennai</td>
<td>Trichy</td>
<td><span class="badge badge-red">ALERT</span></td>
<td>9.2°C</td>
</tr>

</table>

</div>

</section>


<!-- ================= INCIDENTS ================= -->

<section id="incidents" class="page">

<div class="top">

<div>
<div class="label">RISK MANAGEMENT</div>
<h1>Temperature Incidents</h1>
</div>

</div>


<div class="card">

<div class="incident">

<strong>⚠ Critical Temperature Excursion</strong>

<p>
Shipment CC-2026-001 exceeded the safe temperature
limit near Salem Highway.
</p>

<p>
Recorded temperature: <b>9.8°C</b> |
Safe range: 2–8°C |
Duration: 18 minutes
</p>

</div>


<div class="incident">

<strong>⚠ Temperature Warning</strong>

<p>
Shipment CC-2026-004 is currently at 9.2°C.
Immediate inspection recommended.
</p>

</div>

</div>

</section>


<!-- ================= REPORTS ================= -->

<section id="reports" class="page">

<div class="top">

<div>
<div class="label">ANALYTICS</div>
<h1>Cold Chain Reports</h1>
</div>

<button onclick="alert('Report generation will be connected to PHP/MySQL in Stage 2.')">
Generate Report
</button>

</div>


<div class="cards">

<div class="card">

<div class="card-title">TOTAL SHIPMENTS</div>
<div class="card-value">248</div>

</div>

<div class="card">

<div class="card-title">SUCCESSFUL DELIVERIES</div>
<div class="card-value safe">239</div>

</div>

<div class="card">

<div class="card-title">TEMPERATURE INCIDENTS</div>
<div class="card-value danger">09</div>

</div>

<div class="card">

<div class="card-title">COMPLIANCE</div>
<div class="card-value">96.4%</div>

</div>

</div>


<div class="card">

<h2>Monthly Performance</h2>

<br>

<table>

<tr>
<th>Metric</th>
<th>January</th>
<th>February</th>
<th>March</th>
</tr>

<tr>
<td>Shipments</td>
<td>74</td>
<td>82</td>
<td>92</td>
</tr>

<tr>
<td>Safe Deliveries</td>
<td>71</td>
<td>79</td>
<td>89</td>
</tr>

<tr>
<td>Incidents</td>
<td>3</td>
<td>3</td>
<td>3</td>
</tr>

</table>

</div>

</section>


</main>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<script>

/* =====================================
   PAGE NAVIGATION
===================================== */

function showPage(pageId, element){

    document.querySelectorAll(".page").forEach(page=>{
        page.classList.remove("active");
    });

    document.getElementById(pageId).classList.add("active");

    document.querySelectorAll(".menu-item").forEach(item=>{
        item.classList.remove("active");
    });

    element.classList.add("active");

    if(pageId === "tracking"){

        setTimeout(()=>{
            initializeMap();
        },100);

    }

}


/* =====================================
   MAP DATA
===================================== */

const locations = {

    coimbatore:{
        name:"Coimbatore",
        lat:11.0168,
        lng:76.9558,
        temp:"4.2°C",
        time:"08:00 AM"
    },

    salem:{
        name:"Salem",
        lat:11.6643,
        lng:78.1460,
        temp:"6.3°C",
        time:"12:40 PM"
    },

    excursion:{
        name:"Salem Highway",
        lat:11.7300,
        lng:78.4300,
        temp:"9.8°C",
        time:"02:20 PM"
    },

    villupuram:{
        name:"Villupuram",
        lat:11.9401,
        lng:79.4861,
        temp:"7.1°C",
        time:"04:10 PM"
    },

    chennai:{
        name:"Chennai",
        lat:13.0827,
        lng:80.2707,
        temp:"--",
        time:"08:15 PM"
    }

};


let map;
let routeLine;
let vehicleMarker;
let mapLoaded=false;


/* =====================================
   INITIALIZE MAP
===================================== */

function initializeMap(){

    if(mapLoaded){

        setTimeout(()=>{
            map.invalidateSize();
        },200);

        return;
    }


    map = L.map("map");


    /*
       ESRI MAP TILES

       This avoids the blocked tile issue
       from the previous map.
    */

    L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}",
        {
            attribution:
            "Tiles © Esri | Data © OpenStreetMap contributors",
            maxZoom:19
        }
    ).addTo(map);


    const routeCoordinates=[

        [
            locations.coimbatore.lat,
            locations.coimbatore.lng
        ],

        [
            locations.salem.lat,
            locations.salem.lng
        ],

        [
            locations.excursion.lat,
            locations.excursion.lng
        ],

        [
            locations.villupuram.lat,
            locations.villupuram.lng
        ],

        [
            locations.chennai.lat,
            locations.chennai.lng
        ]

    ];


    /* ROUTE */

    routeLine=L.polyline(
        routeCoordinates,
        {
            color:"#2563eb",
            weight:5,
            opacity:.9
        }
    ).addTo(map);


    /* MARKER FUNCTION */

    function marker(location,color,symbol){

        const icon=L.divIcon({

            className:"",

            html:
            `<div style="
                width:34px;
                height:34px;
                background:${color};
                border:3px solid white;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                color:white;
                font-weight:bold;
                box-shadow:0 3px 10px rgba(0,0,0,.3);
            ">${symbol}</div>`,

            iconSize:[34,34],

            iconAnchor:[17,17]

        });


        const m=L.marker(
            [location.lat,location.lng],
            {icon:icon}
        ).addTo(map);


        m.bindPopup(`

            <b>${location.name}</b>

            <br><br>

            Temperature:
            <b>${location.temp}</b>

            <br>

            Time:
            ${location.time}

        `);


        return m;

    }


    /* ORIGIN */

    marker(
        locations.coimbatore,
        "#16b878",
        "✓"
    );


    /* SALEM */

    marker(
        locations.salem,
        "#16b878",
        "✓"
    );


    /* TEMPERATURE INCIDENT */

    marker(
        locations.excursion,
        "#ef4444",
        "!"
    );


    /* CURRENT VEHICLE */

    vehicleMarker=marker(
        locations.villupuram,
        "#2563eb",
        "●"
    );


    /* DESTINATION */

    marker(
        locations.chennai,
        "#718096",
        "4"
    );


    /* INCIDENT AREA */

    L.circle(

        [
            locations.excursion.lat,
            locations.excursion.lng
        ],

        {
            radius:12000,
            color:"#ef4444",
            fillColor:"#ef4444",
            fillOpacity:.12
        }

    ).addTo(map);


    /* FIT ROUTE */

    map.fitBounds(
        routeLine.getBounds(),
        {
            padding:[30,30]
        }
    );


    mapLoaded=true;

}


/* =====================================
   CENTER ROUTE
===================================== */

function centerRoute(){

    if(!mapLoaded){

        initializeMap();

        setTimeout(centerRoute,300);

        return;

    }

    map.fitBounds(
        routeLine.getBounds(),
        {
            padding:[30,30],
            animate:true
        }
    );

}


/* =====================================
   TEMPERATURE SIMULATION
===================================== */

let temperature=7.1;


setInterval(()=>{

    temperature += (Math.random()-.5)*.4;

    temperature=Math.max(
        5.5,
        Math.min(8.2,temperature)
    );


    document.getElementById(
        "temperatureValue"
    ).innerText=
        temperature.toFixed(1)+"°C";


    document.getElementById(
        "dashboardTemp"
    ).innerText=
        temperature.toFixed(1)+"°C";


    document.getElementById(
        "tableTemp"
    ).innerText=
        temperature.toFixed(1)+"°C";


},5000);


/* =====================================
   INITIALIZE DASHBOARD
===================================== */

window.addEventListener(
    "load",
    ()=>{
        console.log(
            "ColdChain Guard loaded successfully"
        );
    }
);

</script>

</body>
</html>
