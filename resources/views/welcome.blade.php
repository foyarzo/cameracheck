<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario con Ubicación y Fotos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Agrega el script de Leaflet para los mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-lg w-full">
        <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Formulario</h1>

        <form action="{{ route('form.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <!-- Campo para el Nombre -->
            <div>
                <label for="name" class="block text-gray-700 font-semibold mb-2">Nombre</label>
                <input type="text" id="name" name="name"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300"
                    placeholder="Tu nombre" required>
            </div>

            <!-- Ubicación -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Ubicación Actual</label>
                <div id="map" class="w-full h-64 rounded-md"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
            </div>

            <!-- Captura de Fotos -->
            <div>
                <label for="photos" class="block text-gray-700 font-semibold mb-2">Capturar Fotos</label>
                <div class="flex items-center space-x-4">
                    <video id="video" autoplay class="w-32 h-32 bg-gray-300 rounded-md"></video>
                    <canvas id="canvas" class="hidden w-32 h-32 bg-gray-300 rounded-md"></canvas>
                </div>
                <button type="button" id="captureButton"
                    class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-md">
                    Tomar Foto
                </button>
                <div id="photoPreview" class="grid grid-cols-2 gap-4 mt-4"></div>
                <input type="hidden" id="photos" name="photos">
            </div>

            <!-- Botón Enviar -->
            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-md">
                Enviar
            </button>
        </form>
    </div>

    <script>
        // Configurar Leaflet Map
        const map = L.map('map').setView([0, 0], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        let marker;

        // Obtener geolocalización
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const { latitude, longitude } = position.coords;
                    map.setView([latitude, longitude], 13);
                    marker = L.marker([latitude, longitude]).addTo(map);
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                },
                () => alert('No se pudo obtener la ubicación.'),
                { enableHighAccuracy: true }
            );
        } else {
            alert('Geolocalización no soportada por tu navegador.');
        }

        // Configurar acceso a la cámara
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureButton = document.getElementById('captureButton');
        const photoPreview = document.getElementById('photoPreview');
        const photosInput = document.getElementById('photos');
        const context = canvas.getContext('2d');
        let photos = [];

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                alert('No se pudo acceder a la cámara: ' + err.message);
            });

        captureButton.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const photoData = canvas.toDataURL('image/png');
            photos.push(photoData);

            // Mostrar la foto en la previsualización
            const img = document.createElement('img');
            img.src = photoData;
            img.alt = 'Foto';
            img.className = 'w-full h-32 object-cover rounded-md';
            photoPreview.appendChild(img);

            // Actualizar input oculto con las fotos
            photosInput.value = JSON.stringify(photos);
        });
    </script>
</body>
</html>
