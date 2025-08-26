@extends('home.default')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">SERU Training Course - Map Practice</h1>

    <div class="flex gap-4 mb-4">
        <button id="draw-toggle" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Draw</button>
        <button id="undo-last" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Back</button>
        <button id="delete-all" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete</button>
        <button id="edit-path" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Edit</button>
        <button id="go-home" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Home</button>
    </div>

    <div id="map" class="rounded-lg shadow" style="height: 600px;"></div>
</div>
@endsection


@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush


@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map').setView([51.5455, -0.0545], 19); // Zoomed maximum close

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 20
            }).addTo(map);

            const greenIcon = new L.Icon({
                iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                iconSize: [32, 32],
            });

            const redIcon = new L.Icon({
                iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                iconSize: [32, 32],
            });

            // ✅ Points brought closer together
            const startMarker = L.marker([51.546530205419046 , -0.055044293403625495], {icon: greenIcon}).addTo(map).bindPopup('Start');
            const endMarker = L.marker([51.54636674023215 , -0.05557000637054444], {icon: redIcon}).addTo(map).bindPopup('End');


            let isDrawing = false;
            let points = [];
            let currentLine;

            const drawButton = document.getElementById('draw-toggle');
            const undoButton = document.getElementById('undo-last');
            const deleteButton = document.getElementById('delete-all');
            const editButton = document.getElementById('edit-path');
            const homeButton = document.getElementById('go-home');

            map.on('click', function (e) {
                console.log('Clicked at: ', e.latlng.lat, e.latlng.lng);
                // alert('Latitude: ' + e.latlng.lat + '\nLongitude: ' + e.latlng.lng);
            });


            drawButton.addEventListener('click', function () {
                isDrawing = !isDrawing;
                if (isDrawing) {
                    drawButton.textContent = 'Stop';
                    drawButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                    drawButton.classList.add('bg-red-500', 'hover:bg-red-600');
                    points = [];
                    if (currentLine) {
                        map.removeLayer(currentLine);
                        currentLine = null;
                    }
                    alert('Click as many points as needed. Click Stop when finished.');
                } else {
                    drawButton.textContent = 'Draw';
                    drawButton.classList.remove('bg-red-500', 'hover:bg-red-600');
                    drawButton.classList.add('bg-green-500', 'hover:bg-green-600');
                    if (points.length > 1) {
                        if (currentLine) {
                            map.removeLayer(currentLine);
                        }
                        currentLine = L.polyline(points, {color: 'blue', weight: 4}).addTo(map);
                    }
                    points = [];
                }
            });

            undoButton.addEventListener('click', function () {
                if (!isDrawing || points.length === 0) return;
                points.pop(); // Remove last point
                if (currentLine) {
                    map.removeLayer(currentLine);
                }
                if (points.length > 0) {
                    currentLine = L.polyline(points, {color: 'blue', weight: 4}).addTo(map);
                } else {
                    currentLine = null;
                }
            });

            deleteButton.addEventListener('click', function () {
                points = [];
                if (currentLine) {
                    map.removeLayer(currentLine);
                    currentLine = null;
                }
            });

            editButton.addEventListener('click', function () {
                if (!isDrawing) {
                    isDrawing = true;
                    drawButton.textContent = 'Stop';
                    drawButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                    drawButton.classList.add('bg-red-500', 'hover:bg-red-600');
                    alert('Continue clicking to add points to the current path.');
                }
            });

            homeButton.addEventListener('click', function () {
                map.setView([51.54642011668429 , -0.05568534135818482], 19);
                startMarker.openPopup();
                endMarker.openPopup();
            });


            map.on('click', function (e) {
                if (!isDrawing) return;

                points.push([e.latlng.lat, e.latlng.lng]);

                if (currentLine) {
                    map.removeLayer(currentLine);
                }

                currentLine = L.polyline(points, {color: 'blue', weight: 4}).addTo(map);
            });
        });
    </script>
@endpush
