<?php
namespace App\Views\Home\Components;
use App\Models\History\Landmark;
use App\Models\Venue;
use App\ViewModels\Home\StartingPoints;
use App\config\Secrets;
/** @var StartingPoints $startingPoints */


?>
<section class="flex flex-col lg:flex-row justify-center  gap-3 lg:gap-1 p-5 w-full lg:w-[55%] xl:w-[80%] mx-auto mb-10"
    aria-labelledby="map-heading">

    <article class="flex flex-col gap-2 justify-start flex-shrink-0">
        <header class="headers_home pb-5 text-start">
            <h1 id="map-heading" class="text-3xl md:text-4xl font-bold ">Find Your Way Around</h1>
        </header>
        <div id="map" class="rounded-xl w-full md:w-[40rem] h-[25rem] md:h-[30rem]"></div>
    </article>
    <article class="flex flex-col gap-2 flex-1 min-w-0 h-[30rem] md:h-[35rem] lg:h-[530px]">
        <header class="headers_home pb-5">
            <h1 class="text-3xl md:text-4xl font-bold">Starting points</h1>
        </header>
        <ul class="flex flex-col gap-2 flex-1 items-center  overflow-y-auto overflow-x-hidden no-scrollbar pr-2">
            <?php foreach ($startingPoints->startingPoints as $point): ?>
            <?php include __DIR__ . '/StartingPoint.php'; ?>
            <?php endforeach; ?>
        </ul>
    </article>
</section>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= Secrets::$mapsApiKey ?>&v=weekly"></script>
<script>
// Get icon configuration from SVG path
function getIconConfig(iconPath) {
    return {
        url: iconPath,
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 40),
        labelOrigin: new google.maps.Point(20, 10)
    };
}

// Initialize the map
function initMap() {
    const haarlem = {
        lat: 52.3876,
        lng: 4.6364
    };

    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 13,
        center: haarlem,
    });

    const infoWindow = new google.maps.InfoWindow();

    // Fetch markers from the backend
    fetch('/getVenues')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.markers) {
                data.markers.forEach(marker => {
                    if (marker.latitude && marker.longitude) {
                        const position = {
                            lat: parseFloat(marker.latitude),
                            lng: parseFloat(marker.longitude)
                        };

                        // Create marker with event-specific icon from iconPath
                        const gMarker = new google.maps.Marker({
                            position: position,
                            map: map,
                            title: marker.name,
                            icon: marker.iconPath ? getIconConfig(marker.iconPath) : undefined
                        });

                        // InfoWindow content with image and description
                        gMarker.addListener('click', () => {
                            let content = '<div style="max-width:250px;">';

                            // Add image if available
                            if (marker.imageUrl) {
                                content += '<img src="' + marker.imageUrl +
                                    '" style="width:100%; height:150px; object-fit:cover; border-radius:4px; margin-bottom:8px;">';
                            }

                            // Add name
                            content += '<h5 style="margin:0 0 8px 0; font-weight:bold;">' + marker
                                .name + '</h5>';

                            // Add type badge
                            const typeBadge = marker.type === 'venue' ? 'Venue' : 'Landmark';
                            content += '<span style="display:inline-block; background-color:' + (
                                    marker.type === 'venue' ? '#3b82f6' : '#10b981') +
                                '; color:white; padding:2px 8px; border-radius:4px; font-size:11px; margin-bottom:8px;">' +
                                typeBadge + '</span>';

                            // Add description
                            if (marker.description) {
                                // Strip HTML tags from description
                                const desc = marker.description.replace(/<[^>]*>/g, '').substring(0,
                                    100);
                                content += '<p style="margin:8px 0; font-size:13px;">' + desc + (
                                    marker.description.length > 100 ? '...' : '') + '</p>';
                            }

                            // Add address
                            if (marker.address) {
                                content +=
                                    '<p style="margin:8px 0 0 0; font-size:12px; color:#666;">' +
                                    marker.address + '</p>';
                            }

                            // Add Get Directions link
                            content +=
                                '<a href="https://www.google.com/maps/dir/?api=1&destination=' +
                                marker.latitude + ',' + marker.longitude +
                                '" target="_blank" style="display:block; color:#4285f4; text-decoration:none; margin-top:8px; font-size:12px;">Get directions</a>';

                            content += '</div>';

                            infoWindow.setContent(content);
                            infoWindow.open({
                                anchor: gMarker,
                                map
                            });
                        });
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching venue markers:', error);
        });
}

// Call initMap immediately when page is ready
window.addEventListener('load', initMap);
</script>