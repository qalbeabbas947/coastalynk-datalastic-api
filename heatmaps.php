<?php
// Your Datalastic API Key
$apiKey = '15df4420-d28b-4b26-9f01-13cca621d55e';
$total_port_vessels = [];
// 1. Define the center points (latitude, longitude) of our ports
$ports = [
    'Apapa' => [6.45, 3.36],
    'Tin Can Island' => [6.44, 3.34],
    'Onne' => [4.71, 7.15],
    'Calabar' => [4.95, 8.32],
];

// 2. Define the search radius (in kilometers) around each port
$searchRadiusKm = 20; // Search 10 km around each port center

// 3. Define the dimensions and resolution of our final heatmap grid
$gridWidth = 500;  // Width of the final image in pixels
$gridHeight = 300; // Height of the final image in pixels

// 4. Define the bounding box for our entire heatmap area
// This will contain all points we find. We'll calculate this dynamically.
$minLat = 90;
$maxLat = -90;
$minLon = 180;
$maxLon = -180;


// Initialize an array to hold all vessel positions
$allVesselPositions = [];

// Build the API URL
$url = "https://api.datalastic.com/api/v0/vessel_inradius?api-key=15df4420-d28b-4b26-9f01-13cca621d55e&port_uuid=2cb375dd-aea5-fc12-a639-7c15b893e250&radius=10";
$total_vessels = 0;
// Loop through each port and fetch vessels in its radius
foreach ($ports as $portName => $portCoords) {
    list($portLat, $portLon) = $portCoords;

    // Build the API URL for the vessels_in_radius endpoint
    $url = sprintf(
        "https://api.datalastic.com/api/v0/vessel_inradius?api-key=%s&lat=%f&lon=%f&radius=%d",
        urlencode($apiKey),
        $portLat,
        $portLon,
        $searchRadiusKm
    );

    // Make the API request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Enable in production

    $response = curl_exec($ch);
  

    if (curl_errno($ch)) {
        echo "cURL Error for $portName: " . curl_error($ch) . "\n";
        curl_close($ch);
        continue;
    }
    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if we got data
    if (isset($data['data']['vessels'])) {
        $vessels = $data['data']['vessels'];
        $total_port_vessels[ $portName ] = 0;
        
        // Add each vessel's position to our master list and update the overall bounding box
        foreach ($vessels as $vessel) {
            if (isset($vessel['lat']) && isset($vessel['lon'])) {
                $lat = (float)$vessel['lat'];
                $lon = (float)$vessel['lon'];

                $allVesselPositions[] = ['lat' => $lat, 'lon' => $lon];

                // Expand the overall bounding box to include this point
                $minLat = min($minLat, $lat);
                $maxLat = max($maxLat, $lat);
                $minLon = min($minLon, $lon);
                $maxLon = max($maxLon, $lon);
                $total_vessels++;
                $total_port_vessels[$portName] += 1;

            }
        }
    } else {
        echo "<br>No vessels found or API error for $portName.\n";
        if (isset($data['message'])) {
            echo "<br>Message: " . $data['message'] . "\n";
        }
    }
}

// Check if we found any vessels at all
if (empty($allVesselPositions)) {
    die('<br>No vessel data found to generate a heatmap.');
}


// 5. Define the resolution of our heatmap grid (number of cells)
// More cells = smoother but more computationally expensive heatmap
$gridColumns = 100;
$gridRows = 60;

// Initialize an empty 2D array to hold our vessel counts
$heatmapGrid = array_fill(0, $gridRows, array_fill(0, $gridColumns, 0));

// Calculate the size (in degrees) of each grid cell
$cellWidth = ($maxLon - $minLon) / $gridColumns;
$cellHeight = ($maxLat - $minLat) / $gridRows;

// Place each vessel into a grid cell
foreach ($allVesselPositions as $position) {
    $lat = $position['lat'];
    $lon = $position['lon'];

    // Calculate the grid column (x-index) and row (y-index) for this vessel
    $col = (int)floor(($lon - $minLon) / $cellWidth);
    $row = (int)floor(($lat - $minLat) / $cellHeight);

    // Ensure the calculated indices are within the grid bounds
    if ($col >= 0 && $col < $gridColumns && $row >= 0 && $row < $gridRows) {
        $heatmapGrid[$row][$col]++;
    }
}

// Find the maximum vessel count in any single cell to normalize our colors
$maxCount = 0;
foreach ($heatmapGrid as $row) {
    $rowMax = max($row);
    if ($rowMax > $maxCount) {
        $maxCount = $rowMax;
    }
}

// Create a truecolor image with transparency
$im = imagecreatetruecolor($gridWidth, $gridHeight);
imagesavealpha($im, true);
$transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $transparent);

// Create a color palette (from transparent blue to intense red)
$colorPalette = [];
if ($maxCount > 0) {
    for ($i = 0; $i <= 100; $i++) {
        $intensity = $i / 100;
        $r = (int)min(255, round(255 * $intensity)); // Red increases
        $g = 0; // No green
        $b = (int)max(0, round(255 * (1 - $intensity))); // Blue decreases
        $alpha = (int)(50 * (1 - $intensity*0.5)); // More intense = less transparent
        $colorPalette[$i] = imagecolorallocatealpha($im, $r, $g, $b, $alpha);
    }
}

// Calculate the size of each cell in pixels
$cellWidthPx = $gridWidth / $gridColumns;
$cellHeightPx = $gridHeight / $gridRows;

// Draw each cell of the heatmap
for ($row = 0; $row < $gridRows; $row++) {
    for ($col = 0; $col < $gridColumns; $col++) {
        $count = $heatmapGrid[$row][$col];
        if ($count > 0) {
            // Normalize the count to an intensity index (0-100)
            $intensityIndex = (int)min(100, ($count / $maxCount) * 100);
            $color = $colorPalette[$intensityIndex];

            // Calculate the pixel coordinates for this cell
            $x1 = (int)($col * $cellWidthPx);
            $y1 = (int)($row * $cellHeightPx);
            $x2 = (int)(($col + 1) * $cellWidthPx - 1);
            $y2 = (int)(($row + 1) * $cellHeightPx - 1);

            // Draw the cell as a filled rectangle
            imagefilledrectangle($im, $x1, $y1, $x2, $y2, $color);
        }
    }
}

// Mark the port locations on the map for reference
$portColor = imagecolorallocate($im, 255, 255, 0); // Yellow
foreach ($ports as $portName => $portCoords) {
    list($portLat, $portLon) = $portCoords;
    $x = (int)(($portLon - $minLon) / ($maxLon - $minLon) * $gridWidth);
    $y = (int)(($portLat - $minLat) / ($maxLat - $minLat) * $gridHeight);
    imagefilledellipse($im, $x, $y, 8, 8, $portColor); // Draw a dot
    imagestring($im, 2, $x+5, $y-5, $portName, $portColor); // Label the port
}

// Save the image
$heatmapImagePath = 'nigerian_ports_heatmap_radius.png';
imagepng($im, $heatmapImagePath);
imagedestroy($im);


// Optional: Save the data for further analysis or web visualization
$outputData = [
    'vessel_positions' => $allVesselPositions,
    'bbox' => [$minLon, $minLat, $maxLon, $maxLat],
    'ports' => $ports
];
file_put_contents('heatmap_data.json', json_encode($outputData));

// Initialize arrays
$allVesselPositions = [];
$features = []; // For GeoJSON

// Fetch data for each port
foreach ($ports as $portName => $portCoords) {
    list($portLat, $portLon) = $portCoords;

    $url = sprintf(
        "https://api.datalastic.com/api/v0/vessel_inradius?api-key=%s&lat=%f&lon=%f&radius=%d",
        urlencode($apiKey),
        $portLat,
        $portLon,
        $searchRadiusKm
    );

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['data']['vessels'])) {
        foreach ($data['data']['vessels'] as $vessel) {
            if (isset($vessel['lat']) && isset($vessel['lon'])) {
                $lat = (float)$vessel['lat'];
                $lon = (float)$vessel['lon'];
                
                // Add to all positions array
                $allVesselPositions[] = ['lat' => $lat, 'lon' => $lon];
                
                // Create a GeoJSON feature for each vessel
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$lon, $lat]
                    ],
                    'properties' => [
                        'name' => $vessel['name'] ?? 'Unknown',
                        'mmsi' => $vessel['mmsi'] ?? 'N/A',
                        'port' => $portName,
                        'timestamp' => $vessel['last_position_epoch'] ?? time()
                    ]
                ];
            }
        }
    }
}

// Create GeoJSON structure
$geoJSON = [
    'type' => 'FeatureCollection',
    'features' => $features
];

// Save data for Leaflet
file_put_contents('vessel_data.geojson', json_encode($geoJSON));

// Also save port locations
$portFeatures = [];
foreach ($ports as $portName => $coords) {
    $portFeatures[] = [
        'type' => 'Feature',
        'geometry' => [
            'type' => 'Point',
            'coordinates' => [$coords[1], $coords[0]] // [lon, lat]
        ],
        'properties' => [
            'name' => $portName,
            'type' => 'port'
        ]
    ];
}

$portsGeoJSON = [
    'type' => 'FeatureCollection',
    'features' => $portFeatures
];

file_put_contents('ports_data.geojson', json_encode($portsGeoJSON));

echo "Data generated successfully!\n";
echo "Run the map.html file to view the interactive heatmap.\n";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nigerian Ports Traffic Heatmap</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
</head>
<body>
    <div class="datalastic-container">
        <header>
            <h1>Nigerian Ports Traffic Heatmap</h1>
            <p class="subtitle">Real-time vessel traffic analysis for major Nigerian ports</p>
        </header>
        
        <div class="controls">
            <div class="port-selector">
                <button class="port-button active" data-port="all">All Ports</button>
                <button class="port-button" data-port="apapa">Apapa</button>
                <button class="port-button" data-port="tincan">Tin Can Island</button>
                <button class="port-button" data-port="onne">Onne</button>
                <button class="port-button" data-port="calabar">Calabar</button>
            </div>
            
            <div class="view-options">
                <button class="view-button active" data-view="heatmap">Heatmap</button>
                <button class="view-button" data-view="vessels">Vessels</button>
                <button class="view-button" data-view="ports">Ports</button>
            </div>
        </div>
        
        <div class="dashboard">
            <div class="stats-panel">
                <h2>Port Statistics</h2>
                <div class="stat-item">
                    <div class="stat-label">Total Vessels Tracked</div>
                    <div class="stat-value" id="total-vessels"><?php echo $total_vessels;?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Last Updated</div>
                    <div class="stat-value" id="last-updated"><?php echo date("h:i:s A");?></div>
                </div>
            </div>
            
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
        
        <div class="info-panel">
            <h2>Port Information</h2>
            <div class="port-info">
                <div class="port-card">
                    <h3>Apapa Port</h3>
                    <div class="port-stat">
                        <span>UN/LOCODE:</span>
                        <span>NGAPP</span>
                    </div>
                    <div class="port-stat">
                        <span>Vessels:</span>
                        <span id="apapa-vessels"><?php echo $total_port_vessels["Apapa"];?></span> 
                    </div>
                    
                </div>
                <div class="port-card">
                    <h3>Tin Can Island Port</h3>
                    <div class="port-stat">
                        <span>UN/LOCODE:</span>
                        <span>NGTIN</span>
                    </div>
                    <div class="port-stat">
                        <span>Vessels:</span>
                        <span id="tincan-vessels"><?php echo $total_port_vessels["Tin Can Island"];?></span>
                    </div>
                </div>
                
                <div class="port-card">
                    <h3>Onne Port</h3>
                    <div class="port-stat">
                        <span>UN/LOCODE:</span>
                        <span>NGONN</span>
                    </div>
                    <div class="port-stat">
                        <span>Vessels:</span>
                        <span id="onne-vessels"><?php echo $total_port_vessels["Onne"];?></span>
                    </div>
                </div>
                
                <div class="port-card">
                    <h3>Calabar Port</h3>
                    <div class="port-stat">
                        <span>UN/LOCODE:</span>
                        <span>NGCBQ</span>
                    </div>
                    <div class="port-stat">
                        <span>Vessels:</span>
                        <span id="calabar-vessels"><?php echo $total_port_vessels["Calabar"];?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    
    <script>
        // Initialize the map centered on Nigeria
        const map = L.map('map').setView([6.5, 5.0], 7);

        // Add base layers
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri'
        });

        // Define port locations
        const ports = {
            'Apapa': { coords: [6.45, 3.36], vessels: 18 },
            'Tin Can Island': { coords: [6.44, 3.34], vessels: 15 },
            'Onne': { coords: [4.71, 7.15], vessels: 7 },
            'Calabar': { coords: [4.95, 8.32], vessels: 2 }
        };

        // Simulated vessel data (in a real scenario, this would come from the Datalastic API)
        const vesselData = {
            type: 'FeatureCollection',
            features: []
        };
        
        <?php foreach( $features as $feature ) { ?>
                vesselData.features.push({
                    type: '<?php echo $feature['type'];?>',
                    geometry: {
                        type: '<?php echo $feature['geometry']['type'];?>',
                        coordinates: [<?php echo $feature['geometry']['coordinates'][0];?>, <?php echo $feature['geometry']['coordinates'][1];?>]
                    },
                    properties: {
                        name: "<?php echo $feature['properties']['name'];?>",
                        mmsi: "<?php echo $feature['properties']['mmsi'];?>",
                        port: "<?php echo $feature['properties']['port'];?>",
                        timestamp: "<?php echo $feature['properties']['timestamp'];?>"
                    }
                });
        <?php } ?>
        // Convert to heatmap points
        const heatPoints = vesselData.features.map(feature => [
            feature.geometry.coordinates[1],
            feature.geometry.coordinates[0],
            0.5
        ]);

        // Create heatmap layer
        const heatLayer = L.heatLayer(heatPoints, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'}
        }).addTo(map);

        // Create vessel markers layer
        const vesselsLayer = L.layerGroup();
        
        vesselData.features.forEach(feature => {
            const marker = L.circleMarker([
                feature.geometry.coordinates[1],
                feature.geometry.coordinates[0]
            ], {
                radius: 4,
                fillColor: "#ff7800",
                color: "#000",
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            });
            
            const props = feature.properties;
            marker.bindPopup(`
                <strong>${props.name}</strong><br>
                MMSI: ${props.mmsi}<br>
                Near: ${props.port} Port<br>
                Last Update: ${new Date(props.timestamp * 1000).toLocaleString()}
            `);
            
            vesselsLayer.addLayer(marker);
        });

        // Create port markers layer
        const portsLayer = L.layerGroup();
        
        
        Object.entries(ports).forEach(([name, data]) => {
            const marker = L.marker(data.coords, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            });
            
            marker.bindPopup(`<strong>${name} Port</strong>`);
            portsLayer.addLayer(marker);
        });
        
        // Add ports layer by default
        portsLayer.addTo(map);

        // Layer control
        const baseMaps = {
            "OpenStreetMap": osmLayer,
            "Satellite": satelliteLayer
        };

        const overlayMaps = {
            "Heatmap": heatLayer,
            "Vessels": vesselsLayer,
            "Ports": portsLayer
        };

        L.control.layers(baseMaps, overlayMaps).addTo(map);

        // Add legend
        const legend = L.control({position: 'bottomright'});
        legend.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'info legend');
            div.innerHTML = `
                <h4>Traffic Density</h4>
                <div class="legend">
                    <i style="background: blue;"></i> Low<br>
                    <i style="background: lime;"></i> Medium<br>
                    <i style="background: red;"></i> High
                </div>
                <h4>Map Features</h4>
                <div class="legend">
                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" style="width: 15px; height: 25px;"> Port<br>
                    <span style="color: #ff7800;">●</span> Individual Vessel
                </div>
            `;
            return div;
        };
        legend.addTo(map);

        // Set up button handlers
        document.querySelectorAll('.port-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.port-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const port = this.dataset.port;
                if (port === 'all') {
                    map.setView([6.5, 5.0], 7);
                } else {
                    const portName = port.replace(/(^\w|\s\w)/g, l => l.toUpperCase()).replace('-', ' ');
                    const portCoords = ports[portName];
                    if (portCoords) {
                        map.setView(portCoords.coords, 12);
                    }
                }
            });
        });
        
        document.querySelectorAll('.view-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.view-button').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const view = this.dataset.view;
                
                // Toggle layers based on view
                if (view === 'heatmap') {
                    map.addLayer(heatLayer);
                    map.removeLayer(vesselsLayer);
                    map.addLayer(portsLayer);
                } else if (view === 'vessels') {
                    map.removeLayer(heatLayer);
                    map.addLayer(vesselsLayer);
                    map.addLayer(portsLayer);
                } else if (view === 'ports') {
                    map.removeLayer(heatLayer);
                    map.removeLayer(vesselsLayer);
                    map.addLayer(portsLayer);
                }
            });
        });
    </script>
</body>
</html>